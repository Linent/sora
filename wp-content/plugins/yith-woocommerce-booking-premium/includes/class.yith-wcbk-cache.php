<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Cache' ) ) {
    /**
     * Class YITH_WCBK_Cache
     *
     * @since  2.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Cache {
        /** @var YITH_WCBK_Cache */
        private static $_instance;

        /** @var string the prefix */
        private $prefix = 'yith_wcbk_';

        /** @var int transient expiration in seconds */
        public $transient_expiration = MONTH_IN_SECONDS;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Cache
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Cache constructor.
         */
        private function __construct() {
        }

        /**
         * is cache disabled?
         *
         * @deprecated since 2.0.5: use YITH_WCBK_Cache::is_enabled() instead
         * @return bool
         */
        public function no_cache() {
            return !$this->is_enabled();
        }

        /**
         * is cache enabled?
         *
         * @since 2.0.5
         * @return bool
         */
        public function is_enabled() {
            return YITH_WCBK()->settings->is_cache_enabled();
        }

        /**
         * get the transient key
         *
         * @param string|array $key
         *
         * @return string
         */
        public function get_transient_key( $key ) {
            if ( is_array( $key ) ) {
                $key_string = '';
                if ( isset( $key[ 'function' ] ) ) {
                    $key_string .= $key[ 'function' ] . '_';
                    unset( $key[ 'function' ] );
                }
                $key_string_to_encode = '';
                foreach ( $key as $current_key => $current_value ) {
                    $key_string_to_encode .= $current_key . '_';
                    if ( is_array( $current_value ) || is_object( $current_value ) ) {
                        $key_string_to_encode .= json_encode( $current_value ) . '_';
                    } else {
                        $key_string_to_encode .= (string) $current_value . '_';
                    }
                }
                $key = $key_string . md5( $key_string_to_encode );
            }

            return (string) $key;
        }

        /**
         * return the transient name
         *
         * @param string $object_type
         * @param string $id
         *
         * @return string
         */
        public function get_transient_name( $object_type, $id = '' ) {
            return $this->prefix . $object_type . '_' . md5( $id );
        }

        /**
         * delete the object transient
         *
         * @param string $object_type
         * @param int    $id
         *
         * @return bool
         */
        public function delete_object_transient( $object_type, $id ) {
            $transient_name = $this->get_transient_name( $object_type, $id );

            return delete_transient( $transient_name );
        }

        /**
         * get the object data
         *
         * @param string       $object_type
         * @param int          $id
         * @param string|array $key
         *
         * @return mixed
         */
        public function get_object_data( $object_type, $id, $key = '' ) {
            if ( !$this->is_enabled() ) {
                return null;
            }
            $id = apply_filters( 'yith_wcbk_cache_get_object_data_object_id', $id, $object_type, $key );
            $id = apply_filters( "yith_wcbk_cache_get_object_data_{$object_type}_id", $id, $object_type, $key );

            $transient_data = get_transient( $this->get_transient_name( $object_type, $id ) );
            if ( '' === $key ) {
                $data = $transient_data;
            } else {
                $key  = $this->get_transient_key( $key );
                $data = null;
                if ( !!$transient_data && array_key_exists( $key, $transient_data ) ) {
                    $data = $transient_data[ $key ];
                }
            }

            return $data;
        }

        /**
         * set the object data
         *
         * @param string       $object_type
         * @param int          $id
         * @param array|string $key
         * @param mixed        $value
         *
         * @return bool
         */
        public function set_object_data( $object_type, $id, $key, $value ) {
            if ( !$this->is_enabled() ) {
                return null;
            }
            $key            = $this->get_transient_key( $key );
            $transient_name = $this->get_transient_name( $object_type, $id );
            $transient_data = get_transient( $transient_name );
            $transient_data = !!$transient_data && is_array( $transient_data ) ? $transient_data : array();

            $transient_data[ $key ] = $value;

            return set_transient( $transient_name, $transient_data, $this->transient_expiration );
        }

        /**
         * delete the object data
         *
         * @param string $object_type
         * @param int    $id
         * @param string $key
         *
         * @return bool
         */
        public function delete_object_data( $object_type, $id, $key = '' ) {
            if ( !$this->is_enabled() ) {
                return null;
            }
            if ( '' === $key ) {
                $response = $this->delete_object_transient( $object_type, $id );
            } else {
                $key            = $this->get_transient_key( $key );
                $transient_name = $this->get_transient_name( $object_type, $id );
                $transient_data = get_transient( $transient_name );
                $transient_data = !!$transient_data && is_array( $transient_data ) ? $transient_data : array();
                if ( isset( $transient_data[ $key ] ) ) {
                    unset( $transient_data[ $key ] );
                }

                $response = set_transient( $transient_name, $transient_data, $this->transient_expiration );
            }

            do_action( "yith_wcbk_cache_delete_{$object_type}_data", $id, $key, $response );
            do_action( "yith_wcbk_cache_delete_object_data", $object_type, $id, $key, $response );

            return $response;
        }


        /**
         * get the product data
         *
         * @param int    $id
         * @param string $key
         *
         * @return mixed
         */
        public function get_product_data( $id, $key = '' ) {
            return $this->get_object_data( 'product', $id, $key );
        }

        /**
         * @param int          $id
         * @param array|string $key
         * @param mixed        $value
         *
         * @return bool
         */
        public function set_product_data( $id, $key, $value ) {
            return $this->set_object_data( 'product', $id, $key, $value );
        }

        /**
         * @param int    $id
         * @param string $key
         *
         * @return bool
         */
        public function delete_product_data( $id, $key = '' ) {
            return $this->delete_object_data( 'product', $id, $key );
        }

        /**
         * get the booking data
         *
         * @param int    $id
         * @param string $key
         *
         * @return mixed
         */
        public function get_booking_data( $id, $key = '' ) {
            return $this->get_object_data( 'booking', $id, $key );
        }

        /**
         * set the booking data
         *
         * @param int    $id
         * @param string $key
         * @param mixed  $value
         *
         * @return bool
         */
        public function set_booking_data( $id, $key, $value ) {
            return $this->set_object_data( 'booking', $id, $key, $value );
        }

        /**
         * @param int    $id
         * @param string $key
         *
         * @return bool
         */
        public function delete_booking_data( $id, $key = '' ) {
            return $this->delete_object_data( 'booking', $id, $key );
        }
    }
}

if ( !function_exists( 'YITH_WCBK_Cache' ) ) {
    function YITH_WCBK_Cache() {
        return YITH_WCBK_Cache::get_instance();
    }
}