<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Admin_Assets' ) ) {
    /**
     * Class YITH_WCBK_Admin_Assets
     * register and enqueue styles and scripts in Admin
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Admin_Assets {

        /** @var YITH_WCBK_Admin_Assets */
        private static $_instance;

        /**
         * Singleton Implementation
         *
         * @return YITH_WCBK_Admin_Assets
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Admin_Assets constructor.
         */
        private function __construct() {
            add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ), 11 );
            add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ), 11 );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 11 );

            add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ), 99, 1 );
        }


        /**
         * Register Styles
         */
        public function register_styles() {
            wp_register_style( 'yith-wcbk-admin-fields', YITH_WCBK_ASSETS_URL . '/css/admin/admin-fields.css', array( 'yith-plugin-fw-fields' ), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-admin-settings-sections', YITH_WCBK_ASSETS_URL . '/css/admin/admin-settings-sections.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-admin', YITH_WCBK_ASSETS_URL . '/css/admin/admin.css', array( 'yith-wcbk-admin-fields', 'yith-wcbk-admin-settings-sections' ), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-admin-rtl', YITH_WCBK_ASSETS_URL . '/css/admin/admin-rtl.css', array(), YITH_WCBK_VERSION );

            wp_register_style( 'yith-wcbk-admin-booking', YITH_WCBK_ASSETS_URL . '/css/admin/admin-booking.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-admin-booking-calendar', YITH_WCBK_ASSETS_URL . '/css/admin/admin-booking-calendar.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-admin-booking-search-form', YITH_WCBK_ASSETS_URL . '/css/admin/admin-booking-search-form.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-admin-global', YITH_WCBK_ASSETS_URL . '/css/admin/admin-global.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-admin-integrations', YITH_WCBK_ASSETS_URL . '/css/admin/admin-integrations.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-admin-logs', YITH_WCBK_ASSETS_URL . '/css/admin/admin-logs.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-admin-service-taxonomy', YITH_WCBK_ASSETS_URL . '/css/admin/admin-service-taxonomy.css', array(), YITH_WCBK_VERSION );
            wp_register_style( 'yith-wcbk-admin-orders', YITH_WCBK_ASSETS_URL . '/css/admin/admin-orders.css', array(), YITH_WCBK_VERSION );
        }

        /**
         * Register Scripts
         */
        public function register_scripts() {
            $suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            $wcbk_admin = array(
                'prod_type'                    => YITH_WCBK_Product_Post_Type_Admin::$prod_type,
                'loader_svg'                   => yith_wcbk_print_svg( 'loader', false ),
                'i18n_delete_log_confirmation' => esc_js( __( 'Are you sure you want to delete logs?', 'yith-booking-for-woocommerce' ) ),
                'i18n_untitled'                => __( 'Untitled', 'yith-booking-for-woocommerce' ),
                'i18n_leave_page_confirmation' => __( "The changes you made will be lost if you navigate away from this page.", 'yith-booking-for-woocommerce' ),
                'i18n_copied'                  => __( 'Copied!', 'yith-booking-for-woocommerce' ),
                'i18n_durations'               => array(
                    'month' => array(
                        'singular_unit' => yith_wcbk_get_duration_unit_label( 'month', 1 ),
                        'plural_unit'   => yith_wcbk_get_duration_unit_label( 'month', 2 ),
                        'singular'      => yith_wcbk_get_duration_label_string( 'month' ),
                        'plural'        => yith_wcbk_get_duration_label_string( 'month', true ),
                        'singular_qty'  => yith_wcbk_get_duration_label_string( 'month', false, true ),
                        'plural_qty'    => yith_wcbk_get_duration_label_string( 'month', true, true ),
                    ),
                    'day'   => array(
                        'singular_unit' => yith_wcbk_get_duration_unit_label( 'day', 1 ),
                        'plural_unit'   => yith_wcbk_get_duration_unit_label( 'day', 2 ),
                        'singular'      => yith_wcbk_get_duration_label_string( 'day' ),
                        'plural'        => yith_wcbk_get_duration_label_string( 'day', true ),
                        'singular_qty'  => yith_wcbk_get_duration_label_string( 'day', false, true ),
                        'plural_qty'    => yith_wcbk_get_duration_label_string( 'day', true, true ),
                    ),
                    'hour'  => array(
                        'singular_unit' => yith_wcbk_get_duration_unit_label( 'hour', 1 ),
                        'plural_unit'   => yith_wcbk_get_duration_unit_label( 'hour', 2 ),
                        'singular'      => yith_wcbk_get_duration_label_string( 'hour' ),
                        'plural'        => yith_wcbk_get_duration_label_string( 'hour', true ),
                        'singular_qty'  => yith_wcbk_get_duration_label_string( 'hour', false, true ),
                        'plural_qty'    => yith_wcbk_get_duration_label_string( 'hour', true, true ),
                    ),

                    'minute' => array(
                        'singular_unit' => yith_wcbk_get_duration_unit_label( 'minute', 1 ),
                        'plural_unit'   => yith_wcbk_get_duration_unit_label( 'minute', 2 ),
                        'singular'      => yith_wcbk_get_duration_label_string( 'minute' ),
                        'plural'        => yith_wcbk_get_duration_label_string( 'minute', true ),
                        'singular_qty'  => yith_wcbk_get_duration_label_string( 'minute', false, true ),
                        'plural_qty'    => yith_wcbk_get_duration_label_string( 'minute', true, true ),
                    ),
                )
            );

            wp_register_script( 'yith-wcbk-admin', YITH_WCBK_ASSETS_URL . '/js/admin/admin' . $suffix . '.js', array( 'jquery', 'jquery-tiptip', 'yith-wcbk-datepicker' ), YITH_WCBK_VERSION, true );
            wp_register_script( 'yith-wcbk-admin-booking-availability-rules', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-availability-rules' . $suffix . '.js', array( 'jquery', 'yith-wcbk-datepicker' ), YITH_WCBK_VERSION, true );
            wp_register_script( 'yith-wcbk-admin-booking-price-rules', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-price-rules' . $suffix . '.js', array( 'jquery', 'yith-wcbk-datepicker' ), YITH_WCBK_VERSION, true );
            wp_register_script( 'yith-wcbk-admin-booking-bulk-actions', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-bulk-actions' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
            wp_register_script( 'yith-wcbk-admin-booking-calendar', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-calendar' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ), YITH_WCBK_VERSION, true );
            wp_register_script( 'yith-wcbk-admin-booking-create', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-create' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
            wp_register_script( 'yith-wcbk-admin-booking-edit-services', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-edit-services' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
            wp_register_script( 'yith-wcbk-admin-booking-meta-boxes', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-meta-boxes' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
            wp_register_script( 'yith-wcbk-admin-booking-product', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-product' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'yith-wcbk-datepicker', 'jquery-ui-sortable', 'google-maps' ), YITH_WCBK_VERSION, true );
            wp_register_script( 'yith-wcbk-admin-booking-search-form', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-search-form' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), YITH_WCBK_VERSION, true );
            wp_register_script( 'yith-wcbk-admin-booking-settings-sections', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-settings-sections' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), YITH_WCBK_VERSION, true );
            wp_register_script( 'yith-wcbk-admin-prevent-leave-on-changes', YITH_WCBK_ASSETS_URL . '/js/admin/admin-prevent-leave-on-changes' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );

            wp_register_script( 'yith-wcbk-enhanced-select', YITH_WCBK_ASSETS_URL . '/js/admin/enhanced-select' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );

            $google_maps_key = get_option( 'yith-wcbk-google-maps-api-key', '' );
            $google_maps_key = !!$google_maps_key ? "&key=$google_maps_key" : '';
            wp_register_script( 'google-maps', "//maps.google.com/maps/api/js?libraries=places$google_maps_key", false, '3' );


            // --------------------------
            // Localize
            // --------------------------
            wp_localize_script( 'yith-wcbk-admin', 'wcbk_admin', $wcbk_admin );
            wp_localize_script( 'yith-wcbk-admin-booking-settings-sections', 'wcbk_admin', $wcbk_admin );
            wp_localize_script( 'yith-wcbk-admin-booking-product', 'wcbk_admin', $wcbk_admin );
            wp_localize_script( 'yith-wcbk-admin-booking-create', 'wcbk_admin', $wcbk_admin );
            wp_localize_script( 'yith-wcbk-admin-prevent-leave-on-changes', 'wcbk_admin', $wcbk_admin );
            wp_localize_script( 'yith-wcbk-enhanced-select', 'yith_wcbk_enhanced_select_params', array(
                'ajax_url'              => admin_url( 'admin-ajax.php' ),
                'search_bookings_nonce' => wp_create_nonce( 'search-bookings' ),
                'search_orders_nonce'   => wp_create_nonce( 'search-orders' ),
            ) );

            wp_localize_script( 'yith-wcbk-admin-booking-bulk-actions', 'wcbk_bulk_actions', array(
                'actions' => apply_filters( 'yith_wcbk_booking_bulk_actions', array(
                    'export_to_csv' => __( 'Export to CSV', 'yith-booking-for-woocommerce' ),
                    'export_to_ics' => __( 'Export to ICS', 'yith-booking-for-woocommerce' ),
                ) ),
            ) );
        }


        /**
         * Enqueue scripts and styles
         */
        public function enqueue() {
            global $wp_scripts;

            // Everywhere
            wp_enqueue_style( 'yith-wcbk-admin-global' );

            // Booking adming screen ids and Settings Panels
            if ( $this->is( yith_wcbk_booking_admin_screen_ids() ) || $this->is( 'settings' ) ) {
                $jquery_version = isset( $wp_scripts->registered[ 'jquery-ui-core' ]->ver ) ? $wp_scripts->registered[ 'jquery-ui-core' ]->ver : '1.9.2';

                wp_enqueue_script( 'jquery-tiptip' );
                wp_enqueue_script( 'jquery-ui-datepicker' );
                wp_enqueue_script( 'yith-wcbk-admin' );
                wp_enqueue_script( 'yith-wcbk-enhanced-select' );

                wp_enqueue_style( 'yith-wcbk-admin' );
                wp_enqueue_style( 'yith-wcbk-datepicker' );
                wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
                if ( 'simple' === get_option( 'yith-wcbk-booking-style', 'simple' ) ) {
                    wp_enqueue_style( 'yith-wcbk-simple-style' );
                }
            }

            // Booking Create
            if ( $this->is( YITH_WCBK_Booking_Create::$screen_id ) ) {
                wp_enqueue_script( 'yith-wcbk-admin-booking-create' );

                wp_enqueue_script( 'yith-wcbk-booking-form' );
                wp_enqueue_style( 'yith-wcbk-booking-form' );
            }

            // Calendar
            if ( $this->is( 'yith_booking_page_yith-wcbk-booking-calendar' ) ) {
                wp_enqueue_script( 'yith-wcbk-admin-booking-calendar' );

                wp_enqueue_style( 'yith-wcbk-admin-booking-calendar' );
            }

            // Booking WP List
            if ( $this->is( 'edit-' . YITH_WCBK_Post_Types::$booking ) ) {
                wp_enqueue_script( 'yith-wcbk-admin-booking-bulk-actions' );
            }

            // Booking object
            if ( $this->is( YITH_WCBK_Post_Types::$booking ) ) {
                global $post;
                $post_id = !!$post && isset( $post->ID ) ? $post->ID : '';

                $params = array(
                    'post_id'                   => $post_id,
                    'add_booking_note_nonce'    => wp_create_nonce( 'add-booking-note' ),
                    'delete_booking_note_nonce' => wp_create_nonce( 'delete-booking-note' ),
                    'i18n_delete_note'          => __( 'Are you sure you want to delete this note? This action cannot be undone.', 'yith-booking-for-woocommerce' ),
                );

                wp_localize_script( 'yith-wcbk-admin-booking-meta-boxes', 'wcbk_admin_booking_meta_boxes', $params );
                wp_enqueue_script( 'yith-wcbk-admin-booking-meta-boxes' );

                wp_enqueue_style( 'yith-wcbk-admin-booking' );
            }

            // Service List
            if ( $this->is( 'edit-' . YITH_WCBK_Post_Types::$service_tax ) ) {
                wp_enqueue_script( 'yith-wcbk-admin-booking-edit-services' );

                wp_enqueue_style( 'yith-wcbk-admin-service-taxonomy' );
            }

            // Search Form
            if ( $this->is( YITH_WCBK_Post_Types::$search_form ) ) {
                wp_enqueue_script( 'yith-wcbk-admin-booking-search-form' );

                wp_enqueue_style( 'yith-wcbk-admin-booking-search-form' );
            }

            // Integrations TAB
            if ( $this->is( 'settings', 'integrations' ) ) {
                wp_enqueue_style( 'yith-wcbk-admin-integrations' );

            }

            // Logs TAB
            if ( $this->is( 'settings', 'logs' ) ) {
                wp_enqueue_style( 'yith-wcbk-admin-logs' );
            }

            // Edit Product
            if ( $this->is( 'product' ) ) {
                wp_enqueue_script( 'google-maps' );
                wp_enqueue_script( 'yith-wcbk-admin-booking-availability-rules' );
                wp_enqueue_script( 'yith-wcbk-admin-booking-price-rules' );
                wp_enqueue_script( 'yith-wcbk-admin-booking-product' );
                wp_enqueue_script( 'yith-wcbk-admin-booking-settings-sections' );
            }

            // Edit Order
            if ( $this->is( 'shop_order' ) ) {
                wp_enqueue_style( 'yith-wcbk-admin-orders' );
            }

            // Settings Availability Tab
            if ( $this->is( 'settings', 'availability-rules' ) ) {
                wp_enqueue_script( 'yith-wcbk-admin-booking-availability-rules' );
                wp_enqueue_script( 'yith-wcbk-admin-booking-settings-sections' );
                wp_enqueue_script( 'yith-wcbk-admin-prevent-leave-on-changes' );
            }

            // Settings Costs Tab
            if ( $this->is( 'settings', 'price-rules' ) ) {
                wp_enqueue_script( 'yith-wcbk-admin-booking-price-rules' );
                wp_enqueue_script( 'yith-wcbk-admin-booking-settings-sections' );
                wp_enqueue_script( 'yith-wcbk-admin-prevent-leave-on-changes' );
            }

            if ( is_rtl() ) {
                wp_enqueue_style( 'yith-wcbk-admin-rtl' );
            }
        }

        /**
         * Add custom screen ids to standard WC
         *
         * @access public
         * @param array $screen_ids
         * @return array
         */
        public function add_screen_ids( $screen_ids ) {
            $screen_ids[] = 'yith_booking_page_yith-wcbk-booking-calendar';
            $screen_ids[] = YITH_WCBK_Booking_Create::$screen_id;
            $screen_ids[] = YITH_WCBK_Post_Types::$booking;
            $screen_ids[] = 'edit-' . YITH_WCBK_Post_Types::$booking;
            $screen_ids[] = 'edit-' . YITH_WCBK_Post_Types::$service_tax;

            return $screen_ids;
        }

        /**
         * which is the current page?
         *
         * @param array|string $id
         * @param string       $arg
         * @return bool
         */
        public function is( $id, $arg = '' ) {
            $panel_page = 'yith_wcbk_panel';
            $screen     = get_current_screen();

            switch ( $id ) {
                case 'settings':
                    if ( strpos( $screen->id, 'page_' . $panel_page ) > 0 ) {
                        if ( !!$arg ) {
                            return isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === $arg;
                        }

                        return true;
                    }

                    return false;
                    break;
                default:
                    if ( is_array( $id ) ) {
                        return in_array( $screen->id, $id );
                    } elseif ( $id === $screen->id ) {
                        return true;
                    }
                    break;
            }

            return false;
        }
    }
}