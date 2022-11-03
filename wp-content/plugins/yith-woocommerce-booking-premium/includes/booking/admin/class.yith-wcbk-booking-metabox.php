<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Booking_Metabox' ) ) {
    /**
     * Class YITH_WCBK_Booking_Metabox
     *
     * handle metaboxes for booking object
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Booking_Metabox {

        /** @var string booking post type */
        public $booking_post_type;

        /** @var array */
        public $synchronized_bookings = array();

        /** @var YITH_WCBK_Booking_Metabox */
        protected static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Booking_Metabox
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Booking_Metabox constructor.
         */
        private function __construct() {
            $this->booking_post_type = YITH_WCBK_Post_Types::$booking;

            add_action( 'admin_menu', array( $this, 'remove_publish_box' ) );
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'save_booking' ), 10, 3 );
        }

        /**
         * Remove publish box from edit booking
         */
        public function remove_publish_box() {
            remove_meta_box( 'submitdiv', $this->booking_post_type, 'side' );
        }

        /**
         * Add meta boxes to edit booking page
         *
         * @param string $post_type Post type.
         */
        public function add_meta_boxes( $post_type ) {

            if ( $post_type !== $this->booking_post_type ) {
                return;
            }

            $metaboxes = $this->_get_metaboxes();

            if ( empty( $metaboxes ) || !is_array( $metaboxes ) ) {
                return;
            }

            foreach ( $metaboxes as $metabox ) {
                add_meta_box( $metabox[ 'id' ], $metabox[ 'title' ], array(
                    $this,
                    'meta_box_print',
                ), $this->booking_post_type, $metabox[ 'context' ], $metabox[ 'priority' ] );
            }
        }

        /**
         * get the metaboxes
         *
         * @access protected
         * @return array
         */
        protected function _get_metaboxes() {

            $metaboxes = array(
                10 => array(
                    'id'       => 'yith-booking-notes',
                    'title'    => __( 'Booking notes', 'yith-booking-for-woocommerce' ),
                    'context'  => 'side',
                    'priority' => 'default',
                ),
                20 => array(
                    'id'       => 'yith-booking-actions',
                    'title'    => __( 'Booking actions', 'yith-booking-for-woocommerce' ),
                    'context'  => 'side',
                    'priority' => 'high',
                ),
                30 => array(
                    'id'       => 'yith-booking-data',
                    'title'    => __( 'Booking data', 'yith-booking-for-woocommerce' ),
                    'context'  => 'normal',
                    'priority' => 'high',
                ),
            );

            $metaboxes =  apply_filters( 'yith_wcbk_booking_metaboxes_array', $metaboxes );
            ksort($metaboxes);

            return $metaboxes;
        }

        /**
         * Print metaboxes content
         *
         * @param WP_Post $post Post object.
         * @param array   $metabox
         */
        public function meta_box_print( $post, $metabox ) {

            if ( !isset( $metabox[ 'id' ] ) ) {
                return;
            }

            switch ( $metabox[ 'id' ] ) {
                case 'yith-booking-notes':
                    $booking = yith_get_booking( $post->ID );
                    $notes   = $booking->get_notes();
                    include YITH_WCBK_VIEWS_PATH . 'metaboxes/html-booking-meta-notes.php';
                    break;
                case 'yith-booking-actions':
                    $booking = yith_get_booking( $post->ID );
                    include YITH_WCBK_VIEWS_PATH . 'metaboxes/html-booking-meta-actions.php';
                    break;
                case 'yith-booking-data':
                    $booking = yith_get_booking( $post->ID );
                    include YITH_WCBK_VIEWS_PATH . 'metaboxes/html-booking-meta-data.php' ;
                    break;
                default :
                    do_action( 'yith_wcbk_booking_' . $metabox[ 'id' ] . '_print', $post );
                    break;
            }
        }

        /**
         * Save meta on save post
         *
         * @param int $post_id
         */
        public function save_booking( $post_id, $post, $update ) {
            global $wpdb;
            if ( get_post_type( $post_id ) !== YITH_WCBK_Post_Types::$booking || !$update )
                return;

            $booking = yith_get_booking( $post_id );

            if ( $booking ) {
                // CREATION DATE
                if ( isset( $_POST[ 'yith_booking_date' ] ) && isset( $_POST[ 'yith_booking_date_hour' ] ) && isset( $_POST[ 'yith_booking_date_minute' ] ) ) {
                    $post_date = strtotime( $_POST[ 'yith_booking_date' ] . ' ' . (int) $_POST[ 'yith_booking_date_hour' ] . ':' . (int) $_POST[ 'yith_booking_date_minute' ] . ':00' );
                    $post_date = date_i18n( 'Y-m-d H:i:s', $post_date );

                    $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_date = %s, post_date_gmt = %s WHERE ID = %s", $post_date, get_gmt_from_date( $post_date ), $post_id ) );
                }

                // STATUS
                if ( isset( $_POST[ 'yith_booking_status' ] ) ) {
                    $status = $_POST[ 'yith_booking_status' ];
                    $booking->update_status( $status );
                }

                // ORDER
                if ( isset( $_POST[ 'yith_booking_order' ] ) ) {
                    $order_id = absint( $_POST[ 'yith_booking_order' ] );
                    $booking->set( 'order_id', $order_id );
                }

                // USER
                if ( isset( $_POST[ 'yith_booking_user' ] ) ) {
                    $user_id = absint( $_POST[ 'yith_booking_user' ] );
                    $booking->set( 'user_id', $user_id );
                }

                // DATE
                if ( isset( $_POST[ 'yith_booking_from' ] ) ) {
                    $from = strtotime( $_POST[ 'yith_booking_from' ] );
                    $booking->set( 'from', $from );
                }
                if ( isset( $_POST[ 'yith_booking_to' ] ) ) {
                    $to = strtotime( $_POST[ 'yith_booking_to' ] );
                    if ( $booking->is_all_day() ) {
                        $to = strtotime( '23:59:59', $to );
                    }
                    $booking->set( 'to', $to );
                }
                if ( isset( $_POST[ 'yith_booking_from' ] ) || isset( $_POST[ 'yith_booking_to' ] ) ) {
                    $booking->set( 'duration', $booking->calculate_duration() );
                }

                // PERSONS
                if ( $booking->has_person_types() ) {
                    if ( !empty( $_POST[ 'yith_booking_person_type' ] ) ) {
                        $person_types      = $booking->person_types;
                        $post_person_types = $_POST[ 'yith_booking_person_type' ];

                        $total_persons = 0;

                        foreach ( $person_types as $key => $person_type ) {
                            $person_type_id = $person_type[ 'id' ];
                            if ( isset( $post_person_types[ $person_type_id ] ) ) {
                                $person_types[ $key ][ 'number' ] = absint( $post_person_types[ $person_type_id ] );
                            }

                            $total_persons += $person_type[ 'number' ];
                        }
                        $booking->set( 'person_types', $person_types );
                        $booking->set( 'persons', $total_persons );
                    }
                } else {
                    if ( isset( $_POST[ 'yith_booking_persons' ] ) ) {
                        $persons = absint( $_POST[ 'yith_booking_persons' ] );
                        $booking->set( 'persons', $persons );
                    }
                }

                if ( isset( $_POST[ 'yith_booking_service_quantities' ] ) ) {
                    $service_quantities = $_POST[ 'yith_booking_service_quantities' ] ;
                    $booking->set( 'service_quantities', $service_quantities );
                }

                if ( !in_array( $booking->get_id(), $this->synchronized_bookings ) ) {
                    do_action( 'yith_wcbk_google_calendar_booking_sync_on_update', $booking->get_id() );
                    $this->synchronized_bookings[] = $booking->get_id();
                }

                if ( !empty( $booking->product_id ) ) {
                    YITH_WCBK_Cache()->delete_product_data( $booking->product_id );

                    // Trigger background update data
                    YITH_WCBK()->background_processes->schedule_product_data_update( $booking->product_id );
                }
            }
        }
    }
}

return YITH_WCBK_Booking_Metabox::get_instance();