<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Tools' ) ) {
    /**
     * Class YITH_WCBK_Tools
     * handle tools available in YITh Plugins > Booking > Tools
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Tools {
        /** @var YITH_WCBK_Tools */
        private static $_instance;

        private $_redirect_notices = array();

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Tools
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Tools constructor.
         */
        private function __construct() {
            add_action( 'wp_loaded', array( $this, 'handle_actions' ), 90 );

            add_action( 'admin_notices', array( $this, 'print_notices' ) );
        }

        /**
         * Handle all actions
         */
        public function handle_actions() {
            $action = !empty( $_REQUEST[ 'yith_wcbk_tools_action' ] ) ? $_REQUEST[ 'yith_wcbk_tools_action' ] : false;
            $method = 'handle_action_' . sanitize_key( $action );
            if ( $action && is_callable( array( $this, $method ) ) ) {
                $this->$method();
            }
            $this->redirect();
        }

        /**
         * redirect to proper page if the redirect is set in request
         */
        public function redirect() {
            if ( isset( $_REQUEST[ 'yith_wcbk_tools_redirect' ] ) ) {
                $redirect = $_REQUEST[ 'yith_wcbk_tools_redirect' ];
                if ( $this->_redirect_notices ) {
                    $redirect = add_query_arg( array( 'yith_wcbk_tools_notices' => $this->_redirect_notices ), $redirect );
                }
                wp_safe_redirect( $redirect );
                exit;
            }
        }

        /**
         * Handles Sync Booking Product Prices
         */
        public function handle_action_sync_booking_product_prices() {
            if ( isset( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'yith-wcbk-sync-booking-prices' ) ) {
                $success = yith_wcbk_sync_booking_product_prices();
                if ( $success ) {
                    $this->add_notice_before_redirect( 'price-sync-done' );
                } else {
                    $this->add_notice_before_redirect( 'price-sync-no-bookings' );
                }
            }
        }

        /**
         * add notice before redirect
         *
         * @param string $key
         */
        public function add_notice_before_redirect( $key ) {
            $this->_redirect_notices[] = $key;
            $this->_redirect_notices   = array_unique( $this->_redirect_notices );
        }

        /**
         * return an array containing the available notices
         *
         * @return array
         */
        public function get_notices() {
            return array(
                'price-sync-done'        => array(
                    'message' => __( 'Booking product prices synchronized correctly!', 'yith-booking-for-woocommerce' ),
                    'type'    => 'info'
                ),
                'price-sync-no-bookings' => array(
                    'message' => __( 'You don\'t have any booking product in your store.', 'yith-booking-for-woocommerce' ),
                    'type'    => 'warning'
                )
            );
        }

        /**
         * return the notice array by the key
         *
         * @param string $key
         *
         * @return bool|mixed
         */
        public function get_notice( $key ) {
            $notices = $this->get_notices();
            return array_key_exists( $key, $notices ) ? $notices[ $key ] : false;
        }

        /**
         * print notices
         */
        public function print_notices() {
            if ( !empty( $_REQUEST[ 'yith_wcbk_tools_notices' ] ) && is_array( $_REQUEST[ 'yith_wcbk_tools_notices' ] ) ) {
                $notices = $_REQUEST[ 'yith_wcbk_tools_notices' ];
                foreach ( $notices as $notice_key ) {
                    if ( $notice = $this->get_notice( $notice_key ) ) {
                        if ( !empty( $notice[ 'message' ] ) ) {
                            $message = $notice[ 'message' ];
                            $type    = $notice[ 'type' ] ? $notice[ 'type' ] : 'info';
                            yith_wcbk_print_notice( $message, $type, true );
                        }
                    }
                }
            }

        }
    }
}