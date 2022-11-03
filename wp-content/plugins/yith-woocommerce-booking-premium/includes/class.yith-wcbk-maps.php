<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Maps' ) ) {
    /**
     * Class YITH_WCBK_Maps
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Maps {
        /** @var YITH_WCBK_Maps */
        private static $_instance;

        /** @var string */
        public $geocode_json_url = "https://maps.googleapis.com/maps/api/geocode/json";

        /** @var string */
        public $api_key         = "";
        public $geocode_api_key = "";

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Maps
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Maps constructor.
         */
        private function __construct() {
            $this->api_key         = get_option( 'yith-wcbk-google-maps-api-key', '' );
            $this->geocode_api_key = get_option( 'yith-wcbk-google-maps-geocode-api-key', '' );
        }

        /**
         * Get location coordinate by an address
         *
         * @param string $address
         * @return array
         */
        public function get_location_by_address( $address = '' ) {
            $location = array();
            if ( $address && ( $this->api_key || $this->geocode_api_key ) ) {
                $data = $this->get_data_by_address( $address );

                if ( isset( $data[ 'status' ] ) && $data[ 'status' ] === 'OK' && isset( $data[ 'results' ][ 0 ][ 'geometry' ][ 'location' ] ) ) {
                    $location = $data[ 'results' ][ 0 ][ 'geometry' ][ 'location' ];
                } else {
                    $error = sprintf( 'Error while getting Google Map Location by address %s', print_r( $data, true ) );
                    yith_wcbk_add_log( $error, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GOOGLE_MAPS );
                }
            }

            return $location;
        }

        /**
         * Get data by address
         *
         * @param string $address
         * @return array|mixed|object
         */
        public function get_data_by_address( $address = '' ) {
            $data = array();
            if ( $address ) {
                $address = str_replace( ' ', '+', $address );

                $place_detail_args = array(
                    'address' => $address,
                );

                if ( $this->geocode_api_key ) {
                    $place_detail_args[ 'key' ] = $this->geocode_api_key;
                }

                $place_detail_url = add_query_arg( $place_detail_args, $this->geocode_json_url );

                if ( $json = wp_remote_fopen( $place_detail_url ) ) {
                    if ( $decoded = json_decode( $json, true ) ) {
                        $data = $decoded;
                    }
                }
            }

            return $data;
        }

        /**
         * Calculate distance between two coordinates
         *
         * @param $c1
         * @param $c2
         * @return bool|int
         */
        public function calculate_distance( $c1, $c2 ) {
            if ( isset( $c1[ 'lat' ] ) && isset( $c1[ 'lng' ] ) && isset( $c2[ 'lat' ] ) && isset( $c2[ 'lng' ] ) ) {
                $deglen = 110.25;
                $x      = $c1[ 'lat' ] - $c2[ 'lat' ];
                $y      = ( $c1[ 'lng' ] - $c2[ 'lng' ] ) * cos( $c2[ 'lat' ] );

                return $deglen * sqrt( $x * $x + $y * $y );
            }

            return false;
        }

    }
}