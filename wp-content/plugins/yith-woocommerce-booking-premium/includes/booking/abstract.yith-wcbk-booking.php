<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Booking_Abstract' ) ) {
    /**
     * Class YITH_WCBK_Booking_Abstract
     *
     * @abstract
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  2.0.0
     */
    abstract class YITH_WCBK_Booking_Abstract {

        /** @var int ID of the booking */
        public $id;

        /**
         * set function.
         *
         * @param string $property
         * @param mixed  $value
         *
         * @return bool|int
         */
        abstract public function set( $property, $value );

        /**
         * return the Booking ID
         *
         * @return int
         */
        abstract public function get_id();

        /**
         * return the hook prefix
         *
         * @return string
         */
        public function get_hook_prefix() {
            return 'yith_wcbk_booking_';
        }

        /**
         * Get the name
         *
         * @return string
         */
        public function get_name() {
            $name = sprintf( _x( 'Booking #%s', 'Booking name', 'yith-booking-for-woocommerce' ), $this->id );
            return apply_filters( $this->get_hook_prefix() . 'get_name', $name, $this );
        }

        /**
         * Get the title
         *
         * @return string
         */
        abstract public function get_title();

        /**
         * Get the duration of booking including duration unit
         */
        abstract public function get_duration_html();

        /**
         * Check if the booking is valid
         *
         * @return bool
         */
        abstract public function is_valid();

        /**
         * Check if the booking is external
         *
         * @return bool
         */
        public function is_external() {
            return false;
        }

        /**
         * Get the edit link
         *
         * @return string
         */
        abstract public function get_edit_link();

        /**
         * Return the status
         *
         * @return string
         */
        abstract public function get_status();

        /**
         * Return string for status
         *
         * @return string
         */
        abstract public function get_status_text();

        /**
         * return the product ID
         *
         * @return int
         */
        abstract public function get_product_id();

        /**
         * Return string for dates
         *
         * @param string $date_type the type of date : from | to
         *
         * @access public
         * @since  1.0.0
         *
         * @return string
         */
        public function get_formatted_date( $date_type ) {
            $format = wc_date_format();
            $format .= $this->has_time() ? ( ' ' . wc_time_format() ) : '';

            return apply_filters( $this->get_hook_prefix() . 'get_formatted_date', date_i18n( $format, $this->$date_type ), $date_type, $this );
        }

        /**
         *
         * check if the booking can change status to $status
         *
         * @param $status
         *
         * @return bool
         */
        abstract public function can_be( $status );

        /**
         * return true if the booking has time
         *
         * @return bool
         */
        abstract public function has_time();

        /**
         * Checks the booking status against a passed in status.
         *
         * @param string $status
         *
         * @return bool
         */
        abstract public function has_status( $status );
    }
}