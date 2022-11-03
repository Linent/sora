<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Post_Types' ) ) {
    /**
     * Class YITH_WCBK_Post_Types
     * handle post types
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Post_Types {

        /**
         * Booking Post Type
         *
         * @var string
         * @static
         */
        public static $booking = 'yith_booking';

        /**
         * Person Type Post Type
         *
         * @var string
         * @static
         */
        public static $person_type = 'ywcbk-person-type';

        /**
         * Search Form Post Type
         *
         * @var string
         * @static
         */
        public static $search_form = 'ywcbk-search-form';

        /**
         * Extra Cost Post Type
         *
         * @var string
         * @static
         */
        public static $extra_cost = 'ywcbk-extra-cost';

        /**
         * Service Tax
         *
         * @var string
         * @static
         */
        public static $service_tax = 'yith_booking_service';

        /**
         * Hook in methods.
         */
        public static function init() {
            add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
            add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
            add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );

            add_filter( 'woocommerce_data_stores', array( __CLASS__, 'register_data_stores' ), 10, 1 );
        }

        /**
         * Register core post types.
         */
        public static function register_post_types() {
            if ( post_type_exists( self::$booking ) ) {
                return;
            }

            do_action( 'yith_wcbk_register_post_type' );

            // Booking

            $labels = array(
                'name'               => __( 'Bookings', 'yith-booking-for-woocommerce' ),
                'singular_name'      => __( 'Booking', 'yith-booking-for-woocommerce' ),
                'add_new'            => __( 'Add Booking', 'yith-booking-for-woocommerce' ),
                'add_new_item'       => __( 'Add New Booking', 'yith-booking-for-woocommerce' ),
                'edit'               => __( 'Edit', 'yith-booking-for-woocommerce' ),
                'edit_item'          => __( 'Edit Booking', 'yith-booking-for-woocommerce' ),
                'new_item'           => __( 'New Booking', 'yith-booking-for-woocommerce' ),
                'view'               => __( 'View Booking', 'yith-booking-for-woocommerce' ),
                'view_item'          => __( 'View Booking', 'yith-booking-for-woocommerce' ),
                'search_items'       => __( 'Search Bookings', 'yith-booking-for-woocommerce' ),
                'not_found'          => __( 'No bookings found', 'yith-booking-for-woocommerce' ),
                'not_found_in_trash' => __( 'No bookings found in trash', 'yith-booking-for-woocommerce' ),
                'parent'             => __( 'Parent Bookings', 'yith-booking-for-woocommerce' ),
                'menu_name'          => _x( 'Bookings', 'Admin menu name', 'yith-booking-for-woocommerce' ),
                'all_items'          => __( 'All Bookings', 'yith-booking-for-woocommerce' ),
            );

            $booking_post_type_args = array(
                'label'               => __( 'Booking', 'yith-booking-for-woocommerce' ),
                'labels'              => $labels,
                'description'         => __( 'This is where bookings are stored.', 'yith-booking-for-woocommerce' ),
                'public'              => false,
                'show_ui'             => true,
                'capability_type'     => self::$booking,
                'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
                'map_meta_cap'        => true,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'show_in_menu'        => true,
                'hierarchical'        => false,
                'show_in_nav_menus'   => false,
                'rewrite'             => false,
                'query_var'           => false,
                'supports'            => array( '' ),
                'has_archive'         => false,
                'menu_icon'           => 'dashicons-calendar',
            );

            register_post_type( self::$booking, apply_filters( 'yith_wcbk_register_post_type_booking', $booking_post_type_args ) );


            // Person Type

            $labels = array(
                'menu_name'          => _x( 'People', 'Admin menu name', 'yith-booking-for-woocommerce' ),
                'all_items'          => __( 'People', 'yith-booking-for-woocommerce' ),
                'name'               => __( 'People', 'yith-booking-for-woocommerce' ),
                'singular_name'      => __( 'Person', 'yith-booking-for-woocommerce' ),
                'add_new'            => __( 'Add new type', 'yith-booking-for-woocommerce' ),
                'add_new_item'       => __( 'New type', 'yith-booking-for-woocommerce' ),
                'edit_item'          => __( 'Edit type', 'yith-booking-for-woocommerce' ),
                'view_item'          => __( 'View this type', 'yith-booking-for-woocommerce' ),
                'not_found'          => __( 'Type not found', 'yith-booking-for-woocommerce' ),
                'not_found_in_trash' => __( 'Type not found in trash', 'yith-booking-for-woocommerce' )
            );

            $person_type_args = array(
                'labels'              => $labels,
                'public'              => false,
                'show_ui'             => true,
                'menu_position'       => 10,
                'exclude_from_search' => true,
                'capability_type'     => self::$person_type,
                'map_meta_cap'        => true,
                'rewrite'             => true,
                'has_archive'         => true,
                'hierarchical'        => false,
                'show_in_nav_menus'   => false,
                'supports'            => array( 'title', 'editor', 'thumbnail' ),
                'show_in_menu'        => 'edit.php?post_type=' . self::$booking
            );

            register_post_type( self::$person_type, $person_type_args );


            // Search Form

            $labels = array(
                'menu_name'          => _x( 'Search Forms', 'Admin menu name', 'yith-booking-for-woocommerce' ),
                'all_items'          => __( 'Search Forms', 'yith-booking-for-woocommerce' ),
                'name'               => __( 'Search Forms', 'yith-booking-for-woocommerce' ),
                'singular_name'      => __( 'Search form', 'yith-booking-for-woocommerce' ),
                'add_new'            => __( 'Add search form', 'yith-booking-for-woocommerce' ),
                'add_new_item'       => __( 'New search form', 'yith-booking-for-woocommerce' ),
                'edit_item'          => __( 'Edit search form', 'yith-booking-for-woocommerce' ),
                'view_item'          => __( 'View search form', 'yith-booking-for-woocommerce' ),
                'not_found'          => __( 'Search form not found', 'yith-booking-for-woocommerce' ),
                'not_found_in_trash' => __( 'Search form not found in trash', 'yith-booking-for-woocommerce' )
            );

            $search_form_args = array(
                'labels'              => $labels,
                'public'              => false,
                'show_ui'             => true,
                'menu_position'       => 10,
                'exclude_from_search' => true,
                'capability_type'     => self::$search_form,
                'map_meta_cap'        => true,
                'rewrite'             => true,
                'has_archive'         => true,
                'hierarchical'        => false,
                'show_in_nav_menus'   => false,
                'supports'            => array( 'title' ),
                'show_in_menu'        => 'edit.php?post_type=' . self::$booking
            );

            register_post_type( self::$search_form, $search_form_args );


            // Extra Cost

            $labels = array(
                'menu_name'          => _x( 'Extra Costs', 'Admin menu name', 'yith-booking-for-woocommerce' ),
                'all_items'          => __( 'Extra Costs', 'yith-booking-for-woocommerce' ),
                'name'               => __( 'Extra Costs', 'yith-booking-for-woocommerce' ),
                'singular_name'      => __( 'Extra Cost', 'yith-booking-for-woocommerce' ),
                'add_new'            => __( 'Add New Extra Cost', 'yith-booking-for-woocommerce' ),
                'add_new_item'       => __( 'New Extra Cost', 'yith-booking-for-woocommerce' ),
                'edit_item'          => __( 'Edit Extra Cost', 'yith-booking-for-woocommerce' ),
                'view_item'          => __( 'View this extra cost', 'yith-booking-for-woocommerce' ),
                'not_found'          => __( 'Extra cost not found', 'yith-booking-for-woocommerce' ),
                'not_found_in_trash' => __( 'Extra cost not found in trash', 'yith-booking-for-woocommerce' )
            );

            $extra_cost_args = array(
                'labels'              => $labels,
                'public'              => false,
                'show_ui'             => true,
                'menu_position'       => 10,
                'exclude_from_search' => true,
                'capability_type'     => self::$extra_cost,
                'map_meta_cap'        => true,
                'rewrite'             => true,
                'has_archive'         => true,
                'hierarchical'        => false,
                'show_in_nav_menus'   => false,
                'supports'            => array( 'title', 'editor', 'thumbnail' ),
                'show_in_menu'        => 'edit.php?post_type=' . self::$booking
            );

            register_post_type( self::$extra_cost, $extra_cost_args );
        }

        /**
         * Register our custom post statuses, used for order status.
         */
        public static function register_post_status() {
            foreach ( yith_wcbk_get_booking_statuses() as $status_slug => $status_label ) {
                $status_slug = 'bk-' . $status_slug;

                register_post_status( $status_slug, array(
                    'label'                     => $status_label,
                    'public'                    => true,
                    'exclude_from_search'       => false,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( $status_label . ' <span class="count">(%s)</span>', $status_label . ' <span class="count">(%s)</span>', 'yith-booking-for-woocommerce' )
                ) );
            }
        }

        /**
         * Register core taxonomies.
         */
        public static function register_taxonomies() {
            if ( taxonomy_exists( self::$service_tax ) ) {
                return;
            }

            register_taxonomy( self::$service_tax, apply_filters( 'yith_wcbk_taxonomy_objects_booking_service', array(
                'product',
                self::$booking
            ) ), apply_filters( 'yith_wcbk_taxonomy_args_booking_service', array(
                'hierarchical'      => true,
                'label'             => __( 'Booking Services', 'yith-booking-for-woocommerce' ),
                'labels'            => array(
                    'name'                       => __( 'Booking Services', 'yith-booking-for-woocommerce' ),
                    'singular_name'              => __( 'Booking Service', 'yith-booking-for-woocommerce' ),
                    'menu_name'                  => _x( 'Booking Services', 'Admin menu name', 'yith-booking-for-woocommerce' ),
                    'all_items'                  => __( 'All Booking Services', 'yith-booking-for-woocommerce' ),
                    'edit_item'                  => __( 'Edit Booking Service', 'yith-booking-for-woocommerce' ),
                    'view_item'                  => __( 'View Booking Service', 'yith-booking-for-woocommerce' ),
                    'update_item'                => __( 'Update Booking Service', 'yith-booking-for-woocommerce' ),
                    'add_new_item'               => __( 'Add New Booking Service', 'yith-booking-for-woocommerce' ),
                    'new_item_name'              => __( 'New Booking Service Name', 'yith-booking-for-woocommerce' ),
                    'parent_item'                => __( 'Parent Booking Service', 'yith-booking-for-woocommerce' ),
                    'parent_item_colon'          => __( 'Parent Booking Service:', 'yith-booking-for-woocommerce' ),
                    'search_items'               => __( 'Search Booking Services', 'yith-booking-for-woocommerce' ),
                    'separate_items_with_commas' => __( 'Separate booking services with commas', 'yith-booking-for-woocommerce' ),
                    'add_or_remove_items'        => __( 'Add or remove booking services', 'yith-booking-for-woocommerce' ),
                    'choose_from_most_used'      => __( 'Choose among the most popular booking services', 'yith-booking-for-woocommerce' ),
                    'not_found'                  => __( 'No booking service found.', 'yith-booking-for-woocommerce' ),
                ),
                'show_ui'           => true,
                'query_var'         => true,
                'show_in_nav_menus' => false,
                //'meta_box_cb'       => 'post_categories_meta_box',
                'show_admin_column' => true,
                'capabilities'      => array(
                    'manage_terms' => 'manage_' . self::$service_tax . 's',
                    'edit_terms'   => 'edit_' . self::$service_tax . 's',
                    'delete_terms' => 'delete' . self::$service_tax . 's',
                    'assign_terms' => 'assign' . self::$service_tax . 's',
                ),
                'rewrite'           => true,
            ) ) );
        }

        /**
         * Add capabilities to Admin and Shop Manager
         */
        public static function add_capabilities() {
            $admin            = get_role( 'administrator' );
            $shop_manager     = get_role( 'shop_manager' );
            $capability_types = array(
                self::$booking         => 'post',
                self::$person_type     => 'post',
                self::$search_form     => 'post',
                self::$extra_cost      => 'post',
                self::$service_tax     => 'tax',
                'yith_create_booking'  => 'single',
                'yith_manage_bookings' => 'single',
            );

            foreach ( $capability_types as $capability_type => $type ) {
                $caps = array();
                switch ( $type ) {
                    case 'post':
                        $caps = array(
                            'edit_post'              => "edit_{$capability_type}",
                            'delete_post'            => "delete_{$capability_type}",
                            'edit_posts'             => "edit_{$capability_type}s",
                            'edit_others_posts'      => "edit_others_{$capability_type}s",
                            'publish_posts'          => "publish_{$capability_type}s",
                            'read_private_posts'     => "read_private_{$capability_type}s",
                            'delete_posts'           => "delete_{$capability_type}s",
                            'delete_private_posts'   => "delete_private_{$capability_type}s",
                            'delete_published_posts' => "delete_published_{$capability_type}s",
                            'delete_others_posts'    => "delete_others_{$capability_type}s",
                            'edit_private_posts'     => "edit_private_{$capability_type}s",
                            'edit_published_posts'   => "edit_published_{$capability_type}s",
                            'create_posts'           => "create_{$capability_type}s",
                        );

                        if ( YITH_WCBK_Post_Types::$booking === $capability_type )
                            unset( $caps[ 'create_posts' ] );

                        break;

                    case 'tax':
                        $caps = array(
                            'manage_terms' => 'manage_' . $capability_type . 's',
                            'edit_terms'   => 'edit_' . $capability_type . 's',
                            'delete_terms' => 'delete' . $capability_type . 's',
                            'assign_terms' => 'assign' . $capability_type . 's',
                        );
                        break;
                    case 'single':
                        $caps = array( $capability_type );
                }

                foreach ( $caps as $key => $cap ) {
                    if ( $admin )
                        $admin->add_cap( $cap );

                    if ( $shop_manager )
                        $shop_manager->add_cap( $cap );
                }
            }
        }

        /**
         * Register data stores
         *
         * @param array $data_stores
         * @return array
         */
        public static function register_data_stores( $data_stores ) {
            $data_stores[ 'product-booking' ] = 'YITH_WCBK_Product_Booking_Data_Store_CPT';
            return $data_stores;
        }
    }
}