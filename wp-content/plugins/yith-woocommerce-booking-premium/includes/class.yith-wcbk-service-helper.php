<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Service_Helper' ) ) {
    /**
     * Class YITH_WCBK_Service_Helper
     * helper for services
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Service_Helper {
        /** @var YITH_WCBK_Service_Helper */
        private static $_instance;

        /**@var string the service taxonomy name */
        public $taxonomy_name;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Service_Helper
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Service_Helper constructor.
         */
        private function __construct() {
            $this->taxonomy_name = YITH_WCBK_Post_Types::$service_tax;
        }

        /**
         * Get all services by arguments
         *
         * @param array  $args argument for get_terms
         * @param string $return the object type returned
         *
         * @return WP_Post[]|YITH_WCBK_Service[]|bool|string
         */
        public function get_services( $args = array(), $return = 'services' ) {
            $default_args = array(
                'hide_empty' => false,
            );

            $args = wp_parse_args( $args, $default_args );

            $is_id_name_assoc = isset( $args[ 'fields' ] ) && $args[ 'fields' ] === 'id=>name';
            if ( $is_id_name_assoc ) {
                $return = 'terms';
            }

            $args[ 'taxonomy' ] = $this->taxonomy_name;
            $services           = YITH_WCBK()->wp->get_terms( $args );

            switch ( $return ) {
                case 'terms':
                    return $services;
                    break;
                case 'services':
                default:
                    return $this->parse_services_from_terms( $services );
            }
        }

        /**
         * parse terms and return array of YITH_WCBK_Service
         *
         * @param WP_Term|WP_Term[] $service_terms the terms
         *
         * @return YITH_WCBK_Service[]|bool
         */
        public function parse_services_from_terms( $service_terms ) {
            if ( !empty( $service_terms ) ) {
                $service_terms = (array) $service_terms;
                $services      = array();
                foreach ( $service_terms as $term ) {
                    $service    = yith_get_booking_service( $term->term_id );
                    $services[] = $service;
                }

                return $services;
            }

            return false;
        }
    }
}