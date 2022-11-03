<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Booking_External' ) ) {
    /**
     * Class YITH_WCBK_Booking_External
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  2.0.0
     */
    class YITH_WCBK_Booking_External extends YITH_WCBK_Booking_Abstract {

        private $data = array();

        private static $_instances = array();
        private        $_current_instance;

        /**
         * __get function.
         *
         * @param string $key
         * @return mixed
         */
        public function __get( $key ) {
            if ( isset( $this->data[ $key ] ) )
                return $this->data[ $key ];

            return '';
        }

        /**
         * __isset function.
         *
         * @param string $key
         * @return mixed
         */
        public function __isset( $key ) {
            return isset( $this->data[ $key ] );
        }

        public function __construct( $args = array() ) {
            $this->data = array_merge( $this->data, self::get_defaults() );
            $this->data = wp_parse_args( $args, $this->data );

            if ( isset( self::$_instances[ $this->get_product_id() ] ) ) {
                $this->_current_instance = ++self::$_instances[ $this->get_product_id() ];
            } else {
                self::$_instances[ $this->get_product_id() ] = 1;
                $this->_current_instance                     = 1;
            }
        }

        /**
         * return the hook prefix
         *
         * @return string
         */
        public function get_hook_prefix() {
            return 'yith_wcbk_booking_external_';
        }

        /**
         * Check if the booking is external
         *
         * @return bool
         */
        public function is_external() {
            return true;
        }

        /**
         * __set function.
         *
         * @param string $property
         * @param mixed  $value
         * @return bool|int
         */
        public function set( $property, $value ) {
            $this->data[ $property ] = $value;

            return true;
        }

        /**
         * return the Booking ID
         *
         * @return int
         */
        public function get_id() {
            return $this->get_product_id() . '-' . $this->data[ 'id' ];
        }

        /**
         * return the from
         *
         * @return string
         */
        public function get_from() {
            return $this->data[ 'from' ];
        }

        /**
         * return the to
         *
         * @return string
         */
        public function get_to() {
            return $this->data[ 'to' ];
        }

        /**
         * return the description
         *
         * @return string
         */
        public function get_description() {
            return $this->data[ 'description' ];
        }

        /**
         * return the summary
         *
         * @return string
         */
        public function get_summary() {
            return $this->data[ 'summary' ];
        }

        /**
         * return the location
         *
         * @return string
         */
        public function get_location() {
            return $this->data[ 'location' ];
        }

        /**
         * return the uid
         *
         * @return string
         */
        public function get_uid() {
            return $this->data[ 'uid' ];
        }

        /**
         * return the calendar_name
         *
         * @return string
         */
        public function get_calendar_name() {
            return $this->data[ 'calendar_name' ];
        }

        /**
         * return the date
         *
         * @return string
         */
        public function get_date() {
            return $this->data[ 'date' ];
        }

        /**
         * return the source
         *
         * @return string
         */
        public function get_source() {
            return $this->data[ 'source' ];
        }

        /**
         * return the source slug
         *
         * @return string
         */
        public function get_source_slug() {
            return YITH_WCBK_Booking_External_Sources()->get_slug_from_string( $this->get_source() );
        }

        /**
         * return the product ID
         *
         * @return int
         */
        public function get_product_id() {
            return $this->data[ 'product_id' ];
        }

        /**
         * Get the title
         *
         * @return string
         */
        public function get_title() {
            switch ( $this->get_source_slug() ) {
                case 'airbnb':
                    $title = $this->get_summary();
                    break;

                default:
                    $product_id = $this->get_product_id();
                    $product    = wc_get_product( $product_id );
                    $title      = !!$product ? $product->get_title() : sprintf( __( 'External of #%s product', 'yith-booking-for-woocommerce' ), $product_id );
            }

            return $title;
        }

        /**
         * Get the duration of booking including duration unit
         */
        public function get_duration_html() {
            return '';
        }

        /**
         * Get the edit link
         *
         * @return string
         */
        public function get_edit_link() {
            return '';
        }

        /**
         * return true if the booking has time
         *
         * @return bool
         */
        public function has_time() {
            return $this->get_to() - $this->get_from() < DAY_IN_SECONDS;
        }

        /**
         * Check if the booking is valid
         *
         * @return bool
         */
        public function is_valid() {
            return !!$this->get_product_id() && !!$this->get_id();
        }

        /**
         * Check if the booking is valid
         *
         * @return bool
         */
        public function is_completed() {
            $now = strtotime( 'now midnight' );

            return $this->get_from() < $now && $this->get_to() < $now;
        }


        /**
         * Return the status
         *
         * @return string
         */
        public function get_status() {
            return 'external';
        }

        /**
         * Return string for status
         *
         * @return string
         */
        public function get_status_text() {
            return __( 'External', 'yith-booking-for-woocommerce' );
        }

        /**
         * check if the booking can change status to $status
         *
         * @param $status
         * @return bool
         */
        public function can_be( $status ) {
            return false;
        }

        /**
         * Checks the booking status against a passed in status.
         *
         * @param string $status
         * @return bool
         */
        public function has_status( $status ) {
            return 'external' === $status;
        }

        public static function get_defaults() {
            return array(
                'id'            => '',
                'product_id'    => '',
                'from'          => '',
                'to'            => '',
                'description'   => '',
                'summary'       => '',
                'location'      => '',
                'uid'           => '',
                'calendar_name' => '',
                'source'        => '',
                'date'          => '',
            );
        }
    }
}

if ( !function_exists( 'yith_wcbk_booking_external' ) ) {
    function yith_wcbk_booking_external( $args ) {
        return $args instanceof YITH_WCBK_Booking_External ? $args : new YITH_WCBK_Booking_External( $args );
    }
}

if ( !function_exists( ' yith_wcbk_array_to_external_booking' ) ) {
    /**
     * @param $args
     * @return YITH_WCBK_Booking_External
     * @deprecated since 2.1 | use yith_wcbk_booking_external instead
     */
    function yith_wcbk_array_to_external_booking( $args ) {
        yith_wcbk_deprecated_function( 'yith_wcbk_array_to_external_booking', '2.1', 'yith_wcbk_booking_external' );
        return yith_wcbk_booking_external( $args );
    }
}