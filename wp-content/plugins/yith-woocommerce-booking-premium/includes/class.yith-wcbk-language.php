<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Language' ) ) {
    /**
     * Class YITH_WCBK_Language
     * handle booking labels
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Language {

        /** @var YITH_WCBK_Language */
        private static $_instance;

        /** @var array */
        private $_labels = array();

        /** @var array */
        private $_default_labels = array();

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Language
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Language constructor.
         */
        private function __construct() {
            $this->_default_labels = apply_filters( 'yith_wcbk_language_default_labels', array(
                'add-to-cart'          => _x( 'Add to cart', 'Text of add-to-cart-button for booking products', 'yith-booking-for-woocommerce' ),
                'additional-services'  => __( 'Additional Services', 'yith-booking-for-woocommerce' ),
                'bookable'             => __( 'Bookable', 'yith-booking-for-woocommerce' ),
                'booking-services'     => __( 'Booking Services', 'yith-booking-for-woocommerce' ),
                'check-in'             => __( 'Check-in', 'yith-booking-for-woocommerce' ),
                'check-out'            => __( 'Check-out', 'yith-booking-for-woocommerce' ),
                'dates'                => __( 'Dates', 'yith-booking-for-woocommerce' ),
                'duration'             => __( 'Duration', 'yith-booking-for-woocommerce' ),
                'end-date'             => __( 'End date', 'yith-booking-for-woocommerce' ),
                'from'                 => __( 'From', 'yith-booking-for-woocommerce' ),
                'included-services'    => __( 'Included Services', 'yith-booking-for-woocommerce' ),
                'not-bookable'         => __( 'Not-bookable', 'yith-booking-for-woocommerce' ),
                'people'               => __( 'People', 'yith-booking-for-woocommerce' ),
                'read-more'            => _x( 'Read more', 'Add-to-cart button text for booking products', 'yith-booking-for-woocommerce' ),
                'request-confirmation' => _x( 'Request Confirmation', 'Add-to-cart button text for booking products', 'yith-booking-for-woocommerce' ),
                'start-date'           => __( 'Start date', 'yith-booking-for-woocommerce' ),
                'services'             => __( 'Services', 'yith-booking-for-woocommerce' ),
                'time'                 => __( 'Time', 'yith-booking-for-woocommerce' ),
                'to'                   => __( 'To', 'yith-booking-for-woocommerce' ),
                'total-people'         => __( 'Total people', 'yith-booking-for-woocommerce' ),
            ) );

            asort( $this->_default_labels );
        }

        /**
         * get a label
         *
         * @param $key
         * @return string
         */
        public function get_label( $key ) {
            if ( isset( $this->_labels[ $key ] ) )
                return $this->_labels[ $key ];

            $label = get_option( 'yith-wcbk-label-' . sanitize_text_field( $key ), '' );
            $label = !$label ? $this->get_default_label( $key ) : call_user_func( '__', $label, 'yith-booking-for-woocommerce' );
            
            $this->_labels[ $key ] = apply_filters( 'yith_wcbk_language_get_label', $label, $key );

            return $this->_labels[ $key ];
        }

        /**
         * get the default label
         *
         * @param $key
         * @return string
         */
        public function get_default_label( $key ) {
            return isset( $this->_default_labels[ $key ] ) ? $this->_default_labels[ $key ] : '';
        }

        /**
         * get the default label array
         *
         * @return array
         */
        public function get_default_labels() {
            return $this->_default_labels;
        }
    }
}