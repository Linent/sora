<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Google_Calendar_Sync' ) ) {
    /**
     * Class YITH_WCBK_Google_Calendar_Sync
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Google_Calendar_Sync {
        /** @var bool|YITH_WCBK_Google_Calendar */
        public $google_calendar;

        /** @var  YITH_WCBK_Google_Calendar_Sync */
        private static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Google_Calendar_Sync
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Google_Calendar_Sync constructor.
         */
        private function __construct() {
            $this->google_calendar = YITH_WCBK_Google_Calendar::get_instance();

            if ( $this->is_enabled() ) {
                if ( $this->google_calendar->is_synchronize_on_creation_enabled() ) {
                    add_action( 'yith_wcbk_booking_created', array( $this, 'sync_booking' ), 10, 1 );
                    add_action( 'yith_wcbk_google_calendar_booking_sync_on_creation', array( $this, 'sync_booking' ), 10, 1 );
                }

                if ( $this->google_calendar->is_synchronize_on_status_update_enabled() ) {
                    add_action( 'yith_wcbk_booking_status_changed', array( $this, 'sync_booking' ), 10, 1 );
                    add_action( 'yith_wcbk_google_calendar_booking_sync_on_status_update', array( $this, 'sync_booking' ), 10, 1 );
                }

                if ( $this->google_calendar->is_synchronize_on_update_enabled() ) {
                    add_action( 'yith_wcbk_google_calendar_booking_sync_on_update', array( $this, 'sync_booking' ), 10, 1 );
                }

                if ( $this->google_calendar->is_synchronize_on_deletion_enabled() ) {
                    add_action( 'delete_post', array( $this, 'sync_booking_on_deletion' ), 10, 1 );
                }

                add_action( 'yith_wcbk_google_calendar_booking_sync', array( $this, 'sync_booking' ), 10, 1 );

                if ( is_admin() ) {
                    add_action( 'wp_loaded', array( $this, 'handle_actions' ), 90 );

                    add_filter( 'yith_wcbk_booking_custom_columns', array( $this, 'add_google_sync_column' ) );
                    add_action( 'yith_wcbk_booking_render_custom_columns', array( $this, 'render_google_sync_column' ), 10, 3 );
                }
            }
        }

        /**
         * Add Google Sync column in Booking WP List
         *
         * @param array $columns
         * @return array
         */
        public function add_google_sync_column( $columns ) {
            $text                              = __( 'Google Calendar Sync', 'yith-booking-for-woocommerce' );
            $columns[ 'google-calendar-sync' ] = "<span class='yith-wcbk-google-calendar-sync-head tips' data-tip='{$text}'>{$text}</span>";;

            return $columns;
        }

        /**
         * print Google Sync column in Booking WP List
         *
         * @param string            $column
         * @param int               $post_id
         * @param YITH_WCBK_Booking $booking
         */
        public function render_google_sync_column( $column, $post_id, $booking ) {
            if ( 'google-calendar-sync' === $column ) {
                $sync_status = 'not-sync';
                $date        = '';
                $label       = __( 'not synchronized', 'yith-booking-for-woocommerce' );
                if ( $booking->google_calendar_last_update ) {
                    $sync_status = 'sync';
                    $date        = date_i18n( wc_date_format() . ' ' . wc_time_format(), $booking->google_calendar_last_update + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
                    $label       = __( 'synchronized', 'yith-booking-for-woocommerce' );
                }

                $tip = $label;
                $tip .= $date ? '<br />' . $date : '';

                echo "<span class='yith-wcbk-google-calendar-sync-status $sync_status dashicons dashicons-update tips' data-tip='$tip'></span>";
            }
        }

        /**
         * get the action url
         *
         * @param string $action
         * @param array  $args
         * @param string $url
         * @return string
         */
        public function get_action_url( $action, $args = array(), $url = '' ) {
            $args         = !!$args && is_array( $args ) ? $args : array();
            $default_args = array(
                'yith-wcbk-google-calendar-sync-action' => $action,
                '_wpnonce'                              => wp_create_nonce( 'yith-wcbk-google-calendar-sync' ),
                '_wp_http_referer'                      => urlencode( $_SERVER[ 'REQUEST_URI' ] )
            );

            $args = wp_parse_args( $args, $default_args );

            $url = !!$url ? $url : admin_url();

            return add_query_arg( $args, $url );
        }


        /**
         * handle actions
         */
        public function handle_actions() {
            if ( !empty( $_REQUEST[ 'yith-wcbk-google-calendar-sync-action' ] )
                 && isset( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'yith-wcbk-google-calendar-sync' ) ) {
                $redirect_url = wp_get_referer();
                $action       = $_REQUEST[ 'yith-wcbk-google-calendar-sync-action' ];
                switch ( $action ) {
                    case 'sync-booking':
                        if ( !empty( $_REQUEST[ 'booking_id' ] ) ) {
                            $booking_id = $_REQUEST[ 'booking_id' ];
                            do_action( 'yith_wcbk_google_calendar_booking_sync', $booking_id );
                        }
                        break;

                    case 'force-sync-all-bookings':
                    case 'sync-new-bookings':
                        if ( 'force-sync-all-bookings' === $action ) {
                            YITH_WCBK()->background_processes->google_calendar_sync->push_to_queue( array( $this, 'delete_last_update_meta_for_all_products' ) );
                        }
                        YITH_WCBK()->background_processes->google_calendar_sync->push_to_queue( array( $this, 'sync_new_bookings' ) );
                        YITH_WCBK()->background_processes->google_calendar_sync->save()->dispatch();
                        $redirect_url = admin_url( 'admin.php?page=yith_wcbk_panel&tab=google-calendar' );
                        break;
                }

                wp_safe_redirect( $redirect_url );
            }
        }

        /**
         * delete last update meta for all products
         */
        public function delete_last_update_meta_for_all_products() {
            global $wpdb;
            $post_meta_table = $wpdb->prefix . 'postmeta';
            $wpdb->delete( $post_meta_table, array( 'meta_key' => '_google_calendar_last_update' ), array( '%s' ) );
        }

        /**
         * Synchronize new bookings
         */
        public function sync_new_bookings() {
            $args     = array(
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => '_google_calendar_last_update',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key'     => '_google_calendar_last_update',
                        'value'   => 1,
                        'compare' => '<'
                    ),
                )
            );
            $bookings = YITH_WCBK()->booking_helper->get_bookings( $args, 'ids' );
            if ( !!$bookings ) {
                foreach ( $bookings as $booking_id ) {
                    do_action( 'yith_wcbk_google_calendar_booking_sync', $booking_id );
                }
            }
        }

        /**
         * is enabled?
         *
         * @return bool
         */
        public function is_enabled() {
            return !!$this->google_calendar && $this->google_calendar->is_calendar_sync_enabled();
        }

        /**
         * Sync booking
         *
         * @param YITH_WCBK_Booking $booking
         */
        public function sync_booking( $booking ) {
            $booking = yith_get_booking( $booking );
            $allowed = 'yith_wcbk_booking_status_changed' !== current_action() || !doing_action( 'save_post' );

            if ( apply_filters( 'yith_wcbk_google_calendar_allow_booking_sync', $allowed, $booking ) ) {
                $sync_result = $this->google_calendar->sync_booking_event( $booking );
                if ( $sync_result ) {
                    switch ( $sync_result ) {
                        case 'created':
                            $note = __( 'Google Calendar: event successfully created', 'yith-booking-for-woocommerce' );
                            break;

                        case 'updated':
                            $note = __( 'Google Calendar: event successfully updated', 'yith-booking-for-woocommerce' );
                            break;

                        default:
                            $note = sprintf( __( 'Google Calendar: event successfully %s', 'yith-booking-for-woocommerce' ), $sync_result );
                            break;
                    }

                    if ( apply_filters( 'yith_wcbk_google_calendar_add_note_in_booking_on_sync', $this->google_calendar->is_add_note_on_sync_enabled(), $booking ) ) {
                        $booking->add_note( 'google-calendar', $note );
                    }

                    $booking->set( 'google_calendar_last_update', time() );
                } else {
                    yith_wcbk_add_log( sprintf( 'Sync Booking #%s failed!', $booking->get_id() ), YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GOOGLE_CALENDAR );
                }
            }
        }

        /**
         * Sync booking on deletion
         *
         * @param int $post_id
         */
        public function sync_booking_on_deletion( $post_id ) {
            if ( YITH_WCBK_Post_Types::$booking === get_post_type( $post_id ) ) {
                $booking = yith_get_booking( $post_id );
                if ( $booking ) {
                    $sync_result = $this->google_calendar->delete_booking_event( $booking );
                    if ( $sync_result ) {
                        if ( apply_filters( 'yith_wcbk_google_calendar_add_note_in_booking_on_sync', $this->google_calendar->is_add_note_on_sync_enabled(), $booking ) ) {
                            $note = __( 'Google Calendar: event successfully deleted', 'yith-booking-for-woocommerce' );
                            $booking->add_note( 'google-calendar', $note );
                        }
                    } else {
                        yith_wcbk_add_log( sprintf( 'Deleting Booking #%s failed!', $booking->get_id() ), YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GOOGLE_CALENDAR );
                    }
                }
            }
        }
    }
}