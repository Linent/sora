<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Settings' ) ) {
    /**
     * Class YITH_WCBK_Settings
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Settings {
        /** @var YITH_WCBK_Settings */
        private static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Settings
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Settings constructor.
         */
        private function __construct() {
            if ( is_admin() ) {
                add_action( 'init', array( $this, 'save_settings' ) );
            }
        }

        /**
         * Save Settings
         */
        public function save_settings() {
            if ( isset( $_POST[ 'yith-wcbk-cache-check-for-transient-creation' ] ) ) {
                if ( isset( $_POST[ 'yith-wcbk-cache-enabled' ] ) && 'yes' === $_POST[ 'yith-wcbk-cache-enabled' ] ) {
                    delete_transient( 'yith-wcbk-cache-disabled' );
                } else {
                    set_transient( 'yith-wcbk-cache-disabled', '1', DAY_IN_SECONDS );
                }
            }

            if ( empty( $_POST[ 'yith_wcbk_nonce' ] ) || !wp_verify_nonce( $_POST[ 'yith_wcbk_nonce' ], 'yith_wcbk_settings_fields' ) ) {
                return;
            }

            $page = isset( $_REQUEST[ 'yith-wcbk-settings-page' ] ) ? $_REQUEST[ 'yith-wcbk-settings-page' ] : '';

            if ( 'global-availability-rules' === $page ) {
                $ranges = isset( $_POST[ 'yith_booking_global_availability_range' ] ) ? $_POST[ 'yith_booking_global_availability_range' ] : array();
                $ranges = !!$ranges ? $ranges : array();
                update_option( 'yith_wcbk_booking_global_availability_ranges', $ranges );
                yith_wcbk_delete_data_for_booking_products();
            }

            if ( 'global-price-rules' === $page ) {
                $ranges = isset( $_POST[ 'yith_booking_global_cost_ranges' ] ) ? $_POST[ 'yith_booking_global_cost_ranges' ] : array();
                $ranges = !!$ranges ? $ranges : array();
                update_option( 'yith_wcbk_booking_global_cost_ranges', $ranges );
                yith_wcbk_sync_booking_product_prices();
            }
        }

        /**
         * retrieves the global availability rules
         *
         * @return YITH_WCBK_Availability_Rule[]
         * @deprecated since 2.1 | use YITH_WCBK_Settings::get_global_availability_rules() instead
         */
        public function get_global_availability_ranges() {
            return $this->get_global_availability_rules();
        }

        /**
         * retrieves the global availability range array
         *
         * @return YITH_WCBK_Availability_Rule[]
         * @since 2.1
         */
        public function get_global_availability_rules() {
            $rules = get_option( 'yith_wcbk_booking_global_availability_ranges', array() );

            if ( !!$rules && is_array( $rules ) ) {
                $rules = array_map( 'yith_wcbk_availability_rule', $rules );
            }

            return !!$rules && is_array( $rules ) ? $rules : array();
        }


        /**
         * retrieves the global cost range array
         *
         * @return array the array of ranges as StdClass
         * @deprecated since 2.1 | use YITH_WCBK_Settings::get_global_availability_rules() instead
         */
        public function get_global_cost_ranges() {
            return $this->get_global_price_rules();
        }

        /**
         * retrieves the global price rules
         *
         * @return YITH_WCBK_Price_Rule[]
         * @since 2.1
         */
        public function get_global_price_rules() {
            $rules = get_option( 'yith_wcbk_booking_global_cost_ranges', array() );

            if ( !!$rules && is_array( $rules ) ) {
                $rules = array_map( 'yith_wcbk_price_rule', $rules );
            }

            return !!$rules && is_array( $rules ) ? $rules : array();
        }

        /**
         * get settings related to Booking plugin
         *
         * @param      $key
         * @param bool $default
         * @return mixed
         */
        public function get( $key, $default = false ) {
            return get_option( 'yith-wcbk-' . $key, $default );
        }

        /**
         * return true if showing booking form requires login
         *
         * @return bool
         * @since 1.0.5
         */
        public function show_booking_form_to_logged_users_only() {
            return $this->get( 'show-booking-form-to-logged-users-only', 'no' ) === 'yes';
        }

        /**
         * return true if check min max duration in calendar is enabled
         *
         * @return bool
         * @since 2.0.3
         */
        public function check_min_max_duration_in_calendar() {
            return $this->get( 'check-min-max-duration-in-calendar', 'yes' ) === 'yes';
        }

        /**
         * return true if people selector is enabled
         *
         * @return bool
         */
        public function is_people_selector_enabled() {
            return $this->get( 'people-selector-enabled', 'yes' ) === 'yes';
        }

        /**
         * return true if unique calendar range picker is enabled
         *
         * @return bool
         */
        public function is_unique_calendar_range_picker_enabled() {
            return $this->get( 'unique-calendar-range-picker-enabled', 'yes' ) === 'yes';
        }

        /**
         * return true if date-picker is displayed inline
         *
         * @return bool
         */
        function display_date_picker_inline() {
            return $this->get( 'display-date-picker-inline', 'no' ) === 'yes';
        }

        /**
         * return true if show included services is enabled
         *
         * @return bool
         */
        function show_included_services() {
            return $this->get( 'show-included-services', 'yes' ) === 'yes';
        }

        /**
         * return true if show totals is enabled
         *
         * @return bool
         */
        function show_totals() {
            return $this->get( 'show-totals', 'no' ) === 'yes';
        }

        /**
         * return the number of months to show in calendar
         *
         * @return int
         */
        function get_months_loaded_in_calendar() {
            $months = absint( $this->get( 'months-loaded-in-calendar', 12 ) );
            $months = min( 12, max( 1, $months ) );

            return $months;
        }

        /**
         * return true if cache is enabled
         *
         * @since 2.0.5
         * @return bool
         */
        public function is_cache_enabled() {
            return apply_filters( 'yith_wcbk_is_cache_enabled', !get_transient( 'yith-wcbk-cache-disabled' ) );
        }

        /**
         * return date picker format
         *
         * @return bool
         * @since 2.1.4
         */
        public function get_date_picker_format() {
            return $this->get( 'date-picker-format', 'yy-mm-dd' );
        }
    }
}