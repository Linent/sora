<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Booking_External_Sources' ) ) {
    /**
     * Class YITH_WCBK_Booking_External_Sources
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since 2.0.0
     */
    class YITH_WCBK_Booking_External_Sources {
        /** @var YITH_WCBK_Booking_External_Sources */
        private static $_instance;

        /** @var array */
        private $_sources = array();

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Booking_External_Sources
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Booking_External_Sources constructor.
         */
        private function __construct() {
            $sources = array(
                'yith-booking' => array(
                    'search' => 'YITH Booking',
                    'name'   => 'YITH Booking'
                ),
                'booking-com'  => array(
                    'search' => 'Booking.com',
                    'name'   => 'Booking.com'
                ),
                'airbnb'       => array(
                    'search' => 'Airbnb',
                    'name'   => 'Airbnb'
                )
            );

            $this->_sources = apply_filters( 'yith_wcbk_booking_external_sources', $sources );
        }

        /**
         * get the source name
         *
         * @param string $source_slug
         *
         * @return string mixed
         */
        public function get_name( $source_slug ) {
            return array_key_exists( $source_slug, $this->_sources ) ? $this->_sources[ $source_slug ][ 'name' ] : $source_slug;
        }

        /**
         * get the source slug from the search string
         *
         * @param string $string
         *
         * @return string
         */
        public function get_slug_from_string( $string ) {
            $slug = $string;
            foreach ( $this->_sources as $source_slug => $source ) {
                if ( strpos( $string, $source[ 'search' ] ) !== false ) {
                    $slug = $source_slug;
                    break;
                }
            }

            return sanitize_key( $slug );
        }

        /**
         * get the source name from the search string
         *
         * @param string $string
         *
         * @return string
         */
        public function get_name_from_string( $string ) {
            $slug = $this->get_slug_from_string( $string );

            return $this->is_valid_source( $slug ) ? $this->get_name( $slug ) : $string;
        }

        /**
         * is a valid source?
         *
         * @param string $source_slug
         *
         * @return bool
         */
        public function is_valid_source( $source_slug ) {
            return array_key_exists( $source_slug, $this->_sources );
        }
    }
}

/**
 * Unique access to instance of YITH_WCBK_Booking_External_Sources class
 *
 * @return YITH_WCBK_Booking_External_Sources
 * @since 2.0.0
 */
function YITH_WCBK_Booking_External_Sources() {
    return YITH_WCBK_Booking_External_Sources::get_instance();
}