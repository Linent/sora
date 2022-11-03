<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Cron' ) ) {
    /**
     * Class YITH_WCBK_Cron
     * handle Cron processes
     *
     * @since  2.0.0
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Cron {

        /** @var YITH_WCBK_Cron */
        private static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Cron
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        private function __construct() {
            add_action( 'yith_wcbk_check_reject_pending_confirmation_bookings', array( $this, 'check_reject_pending_confirmation_bookings' ) );
            add_action( 'yith_wcbk_check_complete_paid_bookings', array( $this, 'check_complete_paid_bookings' ) );

            add_action( 'wp_loaded', array( $this, 'set_cron' ), 30 );
        }

        /**
         * Set cron
         */
        public function set_cron() {
            if ( !wp_next_scheduled( 'yith_wcbk_check_reject_pending_confirmation_bookings' ) ) {
                wp_schedule_event( time(), 'daily', 'yith_wcbk_check_reject_pending_confirmation_bookings' );
            }

            if ( !wp_next_scheduled( 'yith_wcbk_check_complete_paid_bookings' ) ) {
                wp_schedule_event( time(), 'daily', 'yith_wcbk_check_complete_paid_bookings' );
            }
        }


        /**
         * check if reject pending confirmation bookings
         */
        public function check_reject_pending_confirmation_bookings() {
            $after = absint( get_option( 'yith-wcbk-reject-pending-confirmation-bookings-after', '' ) );
            if ( !!$after ) {
                $after_day = $after - 1;

                $args     = array(
                    'post_status' => array( 'bk-pending-confirm' ),
                    'date_query'  => array(
                        array(
                            'before' => date( 'Y-m-d H:i:s', strtotime( "now -$after_day day midnight" ) )
                        )
                    )
                );
                $bookings = YITH_WCBK_Booking_Helper()->get_bookings( $args );

                if ( !!$bookings ) {
                    foreach ( $bookings as $booking ) {
                        $booking->update_status( 'unconfirmed', sprintf( __( 'Automatically reject booking after %d day(s) from creating', 'yith-booking-for-woocommerce' ), $after ) );
                    }
                }
            }
        }

        /**
         * check if reject pending confirmation bookings
         */
        public function check_complete_paid_bookings() {
            $after = get_option( 'yith-wcbk-complete-paid-bookings-after', '' );
            if ( $after !== '' ) {
                $after_day = $after - 1;
                $sign      = $after_day < 0 ? '+' : '-';

                $args     = array(
                    'post_status'  => array( 'bk-paid' ),
                    'meta_key'     => '_to',
                    'meta_value'   => strtotime( "now {$sign}{$after_day} day midnight" ),
                    'meta_compare' => '<'
                );
                $bookings = YITH_WCBK_Booking_Helper()->get_bookings( $args );

                if ( !!$bookings ) {
                    foreach ( $bookings as $booking ) {
                        $booking->update_status( 'completed', sprintf( __( 'Automatically complete booking after %d day(s) from End Date', 'yith-booking-for-woocommerce' ), $after ) );
                    }
                }
            }
        }

    }
}