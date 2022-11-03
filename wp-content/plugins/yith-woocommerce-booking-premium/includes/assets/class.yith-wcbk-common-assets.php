<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Common_Assets' ) ) {
    /**
     * Class YITH_WCBK_Common_Assets
     * Register and enqueue styles and scripts in Admin and in Frontend
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     * @since    2.0.0
     */
    class YITH_WCBK_Common_Assets {

        /** @var  YITH_WCBK_Common_Assets */
        private static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Common_Assets
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Common_Assets constructor.
         */
        private function __construct() {
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        }

        /**
         * Styles
         */
        public function enqueue_styles() {
            wp_register_style( 'yith-wcbk-people-selector', YITH_WCBK_ASSETS_URL . '/css/people-selector.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-date-range-picker', YITH_WCBK_ASSETS_URL . '/css/date-range-picker.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-simple-style', YITH_WCBK_ASSETS_URL . '/css/simple-style.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-datepicker', YITH_WCBK_ASSETS_URL . '/css/datepicker.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-fields', YITH_WCBK_ASSETS_URL . '/css/fields.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-booking-form', YITH_WCBK_ASSETS_URL . '/css/booking-form.css', array( 'yith-wcbk-fields', 'yith-wcbk-people-selector', 'yith-wcbk-date-range-picker' ), YITH_WCBK_VERSION );
        }

        public static function get_bk_global_params( $context = 'common' ) {
            $bk = array(
                'ajaxurl'    => admin_url( 'admin-ajax.php' ),
                'loader_svg' => yith_wcbk_print_svg( 'loader', false ),
                'settings'   => array(
                    'check_min_max_duration_in_calendar' => YITH_WCBK()->settings->check_min_max_duration_in_calendar() ? 'yes' : 'no',
                    'datepickerFormat'                   => YITH_WCBK()->settings->get_date_picker_format()
                )
            );

            return apply_filters( 'yith_wcbk_assets_bk_global_params', $bk, $context );
        }

        /**
         * Scripts
         */
        public function enqueue_scripts() {
            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            $bk = self::get_bk_global_params();

            wp_register_script( 'yith-wcbk-people-selector', YITH_WCBK_ASSETS_URL . '/js/yith-wcbk-people-selector' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
            wp_localize_script( 'yith-wcbk-people-selector', 'yith_people_selector_params', apply_filters( 'yith_wcbk_js_people_selector_params', array(
                'i18n_zero_person'  => __( 'select people', 'yith-booking-for-woocommerce' ),
                'i18n_one_person'   => __( '1 person', 'yith-booking-for-woocommerce' ),
                'i18n_more_persons' => __( '%s persons', 'yith-booking-for-woocommerce' ),
            ) ) );

            wp_register_script( 'yith-wcbk-monthpicker', YITH_WCBK_ASSETS_URL . '/js/monthpicker' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
            wp_register_script( 'yith-wcbk-datepicker', YITH_WCBK_ASSETS_URL . '/js/datepicker' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-blockui', 'yith-wcbk-dates' ), YITH_WCBK_VERSION, true );
            wp_localize_script( 'yith-wcbk-datepicker', 'bk', $bk );
            wp_localize_script( 'yith-wcbk-datepicker', 'yith_wcbk_datepicker_params', array(
                'i18n_clear' => __( 'Clear', 'yith-booking-for-woocommerce' )
            ) );
            wp_localize_script( 'yith-wcbk-people-selector', 'bk', $bk );


            wp_register_script( 'yith-wcbk-dates', YITH_WCBK_ASSETS_URL . '/js/yith-wcbk-dates' . $suffix . '.js', array(), YITH_WCBK_VERSION, true );

            wp_register_script( 'yith-wcbk-fields', YITH_WCBK_ASSETS_URL . '/js/fields' . $suffix . '.js', array( 'jquery-tiptip' ), YITH_WCBK_VERSION, true );

            wp_register_script( 'yith-wcbk-booking-form', YITH_WCBK_ASSETS_URL . '/js/booking_form' . $suffix . '.js', array( 'jquery', 'yith-wcbk-dates', 'yith-wcbk-datepicker', 'yith-wcbk-monthpicker', 'yith-wcbk-people-selector' ), YITH_WCBK_VERSION, true );
            wp_localize_script( 'yith-wcbk-booking-form', 'yith_booking_form_params', apply_filters( 'yith_booking_form_params', array(
                'ajaxurl'                                 => admin_url( 'admin-ajax.php' ),
                'is_admin'                                => is_admin(),
                'show_empty_date_time_messages'           => 'no',
                'update_form_on_load'                     => 'no',
                'ajax_update_non_available_dates_on_load' => get_option( 'yith-wcbk-ajax-update-non-available-dates-on-load', 'no' ),
                'i18n_empty_duration'                     => __( 'Select a duration', 'yith-booking-for-woocommerce' ),
                'i18n_empty_date'                         => __( 'Select a date', 'yith-booking-for-woocommerce' ),
                'i18n_empty_date_for_time'                => __( 'Select a date to choose the time', 'yith-booking-for-woocommerce' ),
                'i18n_empty_time'                         => __( 'Select Time', 'yith-booking-for-woocommerce' ),
                'i18n_min_persons'                        => __( 'Minimum people: %s', 'yith-booking-for-woocommerce' ),
                'i18n_max_persons'                        => __( 'Maximum people: %s', 'yith-booking-for-woocommerce' ),
                'i18n_min_duration'                       => __( 'Minimum duration: %s', 'yith-booking-for-woocommerce' ),
                'i18n_max_duration'                       => __( 'Maximum duration: %s', 'yith-booking-for-woocommerce' ),
                'i18n_days'                               => array(
                    'singular' => yith_wcbk_get_duration_label_string( 'day' ),
                    'plural'   => yith_wcbk_get_duration_label_string( 'day', true ),
                ),
                'price_first_only'                        => 'yes',
                'dom'                                     => array(
                    'product_container' => '.product',
                    'price'             => '.price'
                )
            ) ) );

            wp_localize_script( 'yith-wcbk-booking-form', 'bk', $bk );
        }
    }
}