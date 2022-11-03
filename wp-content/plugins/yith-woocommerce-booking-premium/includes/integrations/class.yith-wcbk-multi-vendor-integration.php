<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Multi_Vendor_Integration
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.7
 */
class YITH_WCBK_Multi_Vendor_Integration extends YITH_WCBK_Integration {
    /** @var YITH_WCBK_Multi_Vendor_Integration */
    protected static $_instance;

    public static $vendor_service_meta = 'yith_shop_vendor';

    public static $vendors_data = array();

    /** @var bool */
    public $filter_vendor_services_enabled = true;

    /**
     * Constructor
     *
     * @param bool $plugin_active
     * @param bool $integration_active
     * @access protected
     */
    protected function __construct( $plugin_active, $integration_active ) {
        parent::__construct( $plugin_active, $integration_active );

        if ( $this->has_plugin_active() ) {
            /* - - - A D M I N   B O O K I N G   M A N A G E M E N T - - - */
            add_action( 'yith_wcbk_multi-vendor_add_on_active_status_change', array( $this, 'add_remove_booking_capabilities_for_vendor' ) );
        } else {
            return;
        }

        /* - - - B O O K I N G S - - - */
        add_filter( 'manage_' . YITH_WCBK_Post_Types::$booking . '_posts_columns', array( $this, 'remove_vendor_column_in_booking_for_vendors' ), 20 );
        add_filter( 'yith_wck_booking_helper_count_booked_bookings_in_period_get_post_args', array( $this, 'suppress_vendor_filter' ), 10, 1 );

        if ( $this->is_active() ) {
            /* - - - B O O K I N G S - - - */
            add_action( 'yith_wcbk_booking_created', array( $this, 'add_vendor_taxonomy_to_booking' ), 10, 3 );

            /* - - - O R D E R S - - - */
            add_filter( 'yith_wcbk_order_check_order_for_booking', array( $this, 'not_check_for_booking_in_parent_orders_with_suborders' ), 10, 3 );
            add_action( 'yith_wcmv_checkout_order_processed', array( YITH_WCBK()->orders, 'check_order_for_booking' ), 999, 1 ); // check (sub)orders for booking
            add_filter( 'yith_wcbk_order_bookings_related_to_order', array( $this, 'add_bookings_related_to_suborders' ), 10, 2 );
            add_filter( 'yith_wcbk_booking_details_order_id', array( $this, 'show_parent_order_id' ) );
            add_filter( 'yith_wcbk_email_booking_details_order_id', array( $this, 'show_parent_order_id_in_emails' ), 10, 5 );
            add_filter( 'yith_wcbk_pdf_booking_details_order_id', array( $this, 'show_parent_order_id_in_pdf' ), 10, 3 );

            /* - - - S E R V I C E S - - - */
            if ( is_admin() ) {
                add_action( 'pre_get_terms', array( $this, 'filter_vendor_services' ) );
                add_filter( 'wp_unique_term_slug', array( $this, 'unique_term_slug_for_vendors' ), 10, 3 );
                add_filter( 'pre_get_terms', array( $this, 'filter_services_by_vendor_or_admin_when_creating_services' ) );
            }
            add_action( 'yith_wcbk_service_fields_set', array( $this, 'set_vendor_in_services' ), 10, 1 );
            add_filter( 'yith_wcbk_service_tax_get_service_taxonomy_fields', array( $this, 'add_vendor_info_in_services' ) );
            add_action( 'after-' . YITH_WCBK_Post_Types::$service_tax . '-table', array( $this, 'add_vendor_filter_in_services' ) );
            add_filter( 'manage_edit-' . YITH_WCBK_Post_Types::$service_tax . '_columns', array( $this, 'add_vendor_column_in_services' ) );
            add_filter( 'manage_' . YITH_WCBK_Post_Types::$service_tax . '_custom_column', array( $this, 'print_vendor_column_in_services' ), 10, 3 );
            add_filter( 'yith_wcmv_disable_post', array( $this, 'allow_editing_services' ), 20 );

            /* - - - E X T E R N A L S   I N   C A L E N D A R - - - */
            $show_externals = 'yes' === get_option( 'yith-wcbk-external-calendars-show-externals-in-calendar', 'no' );
            if ( $show_externals ) {
                add_filter( 'yith_wcbk_calendar_booking_list_bookings', array( $this, 'filter_external_bookings_in_calendar' ) );
            }


            /* - - - E M A I L - - - */
            add_filter( 'woocommerce_email_classes', array( $this, 'add_email_classes' ), 20 );
        } else {
            // Hide Booking Products in Admin, if integration is not active
            add_filter( 'product_type_selector', array( $this, 'remove_booking_in_product_type_selector_for_vendors' ), 999 );
            add_action( 'init', array( $this, 'remove_booking_data_panels_for_vendors' ), 999 );

        }
    }

    /**
     * Suppress filters for booking post type to avoid issues when retrieving booking product availability through AJAX.
     * This way when the plugin search for "bookings" it'll retrieve all bookings regardless the vendor
     *
     * @param array $args
     * @since 2.1.4
     * @see   YITH_WCMV_Addons_Compatibility::filter_vendor_post_types (since 3.3.7)
     * @return array
     */
    public function suppress_vendor_filter( $args ) {
        $args[ 'yith_wcmv_addons_suppress_filter' ] = true;
        return $args;
    }

    /**
     * Filter externals in calendar to show the vendor ones only
     *
     * @param  YITH_WCBK_Booking[]|YITH_WCBK_Booking_External[] $bookings
     * @return YITH_WCBK_Booking[]|YITH_WCBK_Booking_External[]
     */
    public function filter_external_bookings_in_calendar( $bookings ) {
        if ( function_exists( 'yith_get_vendor' ) ) {
            $vendor = yith_get_vendor( 'current', 'user' );
            if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
                $vendor_product_ids = $vendor->get_products();
                foreach ( $bookings as $key => $booking ) {
                    if ( $booking->is_external() && !in_array( $booking->get_product_id(), $vendor_product_ids ) ) {
                        unset( $bookings[ $key ] );
                    }
                }
            }
        }
        return $bookings;
    }

    /**
     * remove booking data panels in product for vendors if the integration is not active
     */
    public function remove_booking_data_panels_for_vendors() {
        if ( function_exists( 'yith_get_vendor' ) ) {
            $vendor = yith_get_vendor( 'current', 'user' );
            if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
                $product_cpt = YITH_WCBK_Product_Post_Type_Admin::get_instance();
                remove_filter( 'woocommerce_product_data_tabs', array( $product_cpt, 'product_booking_tabs' ), 10 );
                remove_action( 'woocommerce_product_options_general_product_data', array( $product_cpt, 'add_options_to_general_product_data' ), 10 );
            }
        }
    }

    /**
     * @param array $columns
     * @return array
     */
    public function remove_vendor_column_in_booking_for_vendors( $columns ) {
        if ( function_exists( 'yith_get_vendor' ) && function_exists( 'YITH_Vendors' ) && isset( $columns[ 'taxonomy-' . YITH_Vendors()->get_taxonomy_name() ] ) ) {
            $vendor = yith_get_vendor( 'current', 'user' );
            if ( $vendor->is_valid() && $vendor->has_limited_access() )
                unset( $columns[ 'taxonomy-' . YITH_Vendors()->get_taxonomy_name() ] );

        }

        return $columns;
    }

    /**
     * @param int               $order_id
     * @param YITH_WCBK_Booking $booking
     * @param bool              $sent_to_admin
     * @param string            $plain_text
     * @param WC_Email          $email
     * @return mixed
     */
    public function show_parent_order_id_in_emails( $order_id, $booking, $sent_to_admin, $plain_text, $email ) {
        if ( !$email instanceof YITH_WCBK_Email_Booking_Status ) {
            return $this->show_parent_order_id( $order_id );
        }

        return $order_id;
    }

    /**
     * @param int               $order_id
     * @param YITH_WCBK_Booking $booking
     * @param bool              $is_admin
     * @return mixed
     */
    public function show_parent_order_id_in_pdf( $order_id, $booking, $is_admin ) {
        if ( !$is_admin ) {
            return $this->show_parent_order_id( $order_id );
        }

        return $order_id;
    }

    /**
     * @param int $order_id
     * @return mixed
     */
    public function show_parent_order_id( $order_id ) {
        $parent_id = wp_get_post_parent_id( $order_id );

        return !!$parent_id ? $parent_id : $order_id;
    }

    /**
     * Add booking related to suborders to display them in parent order details
     *
     * @param array    $bookings
     * @param WC_Order $order
     * @return array
     */
    public function add_bookings_related_to_suborders( $bookings, $order ) {
        $suborder_ids = YITH_Vendors()->orders->get_suborder( yit_get_prop( $order, 'id' ) );

        if ( !!$bookings || !is_array( $bookings ) )
            $bookings = array();

        if ( !!$suborder_ids && is_array( $suborder_ids ) ) {
            foreach ( $suborder_ids as $suborder_id ) {
                $suborder_bookings = YITH_WCBK()->booking_helper->get_bookings_by_order( $suborder_id );
                if ( !!$suborder_bookings && is_array( $suborder_bookings ) )
                    $bookings = array_merge( $bookings, $suborder_bookings );
            }
        }

        return $bookings;
    }

    /**
     * add email classes to woocommerce
     *
     * @param array $emails
     * @return array
     * @access public
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    public function add_email_classes( $emails ) {
        $emails[ 'YITH_WCBK_Email_Vendor_New_Booking' ]    = include YITH_WCBK_DIR . '/includes/emails/class.yith-wcbk-email-vendor-new-booking.php' ;
        $emails[ 'YITH_WCBK_Email_Booking_Status_Vendor' ] = include YITH_WCBK_DIR . '/includes/emails/class.yith-wcbk-email-booking-status-vendor.php' ;

        return $emails;
    }

    /**
     * Remove booking product type in product type selector for vendors
     *
     * @param array $types
     * @return array
     */
    public function remove_booking_in_product_type_selector_for_vendors( $types ) {
        if ( function_exists( 'yith_get_vendor' ) && isset( $types[ YITH_WCBK_Product_Post_Type_Admin::$prod_type ] ) ) {
            $vendor = yith_get_vendor( 'current', 'user' );
            if ( $vendor->is_valid() && $vendor->has_limited_access() )
                unset( $types[ YITH_WCBK_Product_Post_Type_Admin::$prod_type ] );

        }

        return $types;
    }

    /**
     * disable check for bookings in orders with suborders
     *
     * @param bool  $check
     * @param int   $order_id
     * @param array $posted
     * @return bool
     */
    public function not_check_for_booking_in_parent_orders_with_suborders( $check, $order_id, $posted ) {
        if ( $has_suborders = !!get_post_meta( $order_id, 'has_sub_order', true ) ) {
            // parent order
            return false;
        }

        return $check;
    }

    /**
     * Add vendor taxonomy to booking when it's created
     *
     * @param YITH_WCBK_Booking $booking
     */
    public function add_vendor_taxonomy_to_booking( $booking ) {
        if ( $booking->product_id ) {
            $vendor = yith_get_vendor( $booking->product_id, 'product' );

            if ( $vendor->is_valid() ) {
                wp_set_object_terms( $booking->id, $vendor->term->slug, $vendor->term->taxonomy, false );
            }
        }
    }

    /**
     * add/remove booking capabilities for vendor
     * based on "integration is active or not"
     */
    public function add_remove_booking_capabilities_for_vendor( $activation ) {
        $action = 'yes' === $activation ? 'add' : 'remove';
        if ( $vendor_role = get_role( YITH_Vendors()->get_role_name() ) ) {

            $booking_post_type = YITH_WCBK_Post_Types::$booking;
            $booking_caps      = array(
                'edit_post'            => "edit_{$booking_post_type}",
                'edit_posts'           => "edit_{$booking_post_type}s",
                'edit_others_posts'    => "edit_others_{$booking_post_type}s",
                'read_private_posts'   => "read_private_{$booking_post_type}s",
                'edit_private_posts'   => "edit_private_{$booking_post_type}s",
                'edit_published_posts' => "edit_published_{$booking_post_type}s",
            );

            $service_caps = array(
                'manage_terms' => 'manage_' . YITH_WCBK_Post_Types::$service_tax . 's',
                'edit_terms'   => 'edit_' . YITH_WCBK_Post_Types::$service_tax . 's',
                'delete_terms' => 'delete' . YITH_WCBK_Post_Types::$service_tax . 's',
                'assign_terms' => 'assign' . YITH_WCBK_Post_Types::$service_tax . 's',
            );

            $caps = array_merge( $booking_caps, $service_caps );

            foreach ( $caps as $key => $cap ) {
                if ( 'add' === $action )
                    $vendor_role->add_cap( $cap );
                elseif ( 'remove' === $action )
                    $vendor_role->remove_cap( $cap );

            }
        }
    }

    /**
     * Filter services by vendor or admin to allow creating vendor services with the same name of admin services
     *
     * @param WP_Term_Query $term_query
     */
    public function filter_services_by_vendor_or_admin_when_creating_services( $term_query ) {
        if ( isset( $_REQUEST[ 'yith_booking_service_data' ], $_REQUEST[ 'yith_booking_service_data' ][ 'yith_shop_vendor' ] ) && $this->filter_vendor_services_enabled && function_exists( 'yith_get_vendor' ) && isset( $term_query->query_vars[ 'taxonomy' ] ) && array( YITH_WCBK_Post_Types::$service_tax ) === $term_query->query_vars[ 'taxonomy' ] ) {
            $vendor_id = absint( $_REQUEST[ 'yith_booking_service_data' ][ 'yith_shop_vendor' ] );
            $vendor    = $vendor_id ? yith_get_vendor( $vendor_id ) : false;
            if ( $vendor && $vendor->is_valid() ) {
                $meta_query = array(
                    array( 'key' => self::$vendor_service_meta, 'value' => $vendor->id )
                );
            } else {
                // Admin
                $meta_query = array(
                    array(
                        'relation' => 'OR',
                        array( 'key' => self::$vendor_service_meta, 'value' => '' ),
                        array( 'key' => self::$vendor_service_meta, 'compare' => 'NOT EXISTS' ),
                    )
                );
            }
            if ( !empty( $term_query->query_vars[ 'meta_query' ] ) && is_array( $term_query->query_vars[ 'meta_query' ] ) )
                $meta_query = array_merge( $meta_query, $term_query->query_vars[ 'meta_query' ] );

            $term_query->query_vars[ 'meta_query' ] = $meta_query;
        }
    }

    /**
     * filter the vendor services
     *
     * @param WP_Term_Query $term_query
     */
    public function filter_vendor_services( $term_query ) {
        global $pagenow;
        if ( $this->filter_vendor_services_enabled && function_exists( 'yith_get_vendor' ) && isset( $term_query->query_vars[ 'taxonomy' ] ) && array( YITH_WCBK_Post_Types::$service_tax ) === $term_query->query_vars[ 'taxonomy' ] ) {
            $vendor                             = yith_get_vendor( 'current', 'user' );
            $is_vendor                          = $vendor->is_valid() && $vendor->has_limited_access();
            $is_service_edit_page_filter_vendor = 'edit-tags.php' === $pagenow && isset( $_GET[ 'taxonomy' ] ) && YITH_WCBK_Post_Types::$service_tax === $_GET[ 'taxonomy' ] && !empty( $_GET[ self::$vendor_service_meta ] );

            if ( $is_vendor || $is_service_edit_page_filter_vendor ) {
                if ( $is_vendor ) {
                    $vendor_id = $vendor->id;
                } else {
                    // $is_service_edit_page_filter_vendor
                    $vendor_id = $_GET[ self::$vendor_service_meta ];
                }

                if ( $vendor_id !== 'mine' ) {
                    $meta_query = array(
                        array( 'key' => self::$vendor_service_meta, 'value' => $vendor_id )
                    );
                } else {
                    $meta_query = array(
                        array(
                            'relation' => 'OR',
                            array( 'key' => self::$vendor_service_meta, 'value' => '' ),
                            array( 'key' => self::$vendor_service_meta, 'compare' => 'NOT EXISTS' ),
                        )
                    );
                }

                if ( !empty( $term_query->query_vars[ 'meta_query' ] ) && is_array( $term_query->query_vars[ 'meta_query' ] ) )
                    $meta_query = array_merge( $meta_query, $term_query->query_vars[ 'meta_query' ] );

                $term_query->query_vars[ 'meta_query' ] = $meta_query;
            }
        }
    }

    /**
     * Filter unique term slug to allows Vendor to add services with the same name of the admin services
     *
     * @param $slug
     * @param $term
     * @param $original_slug
     * @return string
     * @since 1.0.14
     */
    public function unique_term_slug_for_vendors( $slug, $term, $original_slug ) {
        if ( isset( $term->taxonomy ) && YITH_WCBK_Post_Types::$service_tax === $term->taxonomy ) {
            remove_filter( 'wp_unique_term_slug', array( $this, __FUNCTION__ ), 10 );
            $this->filter_vendor_services_enabled = false;

            $slug = wp_unique_term_slug( $original_slug, $term );

            add_filter( 'wp_unique_term_slug', array( $this, __FUNCTION__ ), 10, 3 );
            $this->filter_vendor_services_enabled = true;
        }

        return $slug;
    }

    /**
     * add Vendor ID in services
     *
     * @param YITH_WCBK_Service $service
     */
    public function set_vendor_in_services( $service ) {
        $vendor = yith_get_vendor( 'current', 'user' );
        if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
            $service->set( self::$vendor_service_meta, $vendor->id );
        }
    }

    /**
     * allow editing single service for vendors
     *
     * @param $is_post
     * @since 2.0.9
     * @return bool
     */
    public function allow_editing_services( $is_post ) {
        global $pagenow;

        $is_edit_tag         = 'edit-tags.php' == $pagenow;
        $is_edit_action      = !empty( $_POST[ 'action' ] ) && 'editedtag' == $_POST[ 'action' ];
        $is_booking_taxonomy = !empty( $_POST[ 'taxonomy' ] ) && YITH_WCBK_Post_Types::$service_tax == $_POST[ 'taxonomy' ];

        if ( $is_edit_tag && $is_edit_action && $is_booking_taxonomy ) {
            $is_post = false;
        }

        return $is_post;
    }


    /**
     * Add Vendor info in sevices to show vendor dropdown
     *
     * @param $info
     * @return mixed
     */
    public function add_vendor_info_in_services( $info ) {
        $vendor = yith_get_vendor( 'current', 'user' );
        if ( !$vendor->is_valid() || !$vendor->has_limited_access() ) {
            $vendors = self::get_vendors( array( 'fields' => 'id=>name' ) );

            if ( !$vendors || !is_array( $vendors ) )
                $vendors = array();

            $vendors[ '' ] = __( 'None', 'yith-booking-for-woocommerce' );
            asort( $vendors );

            $info[ self::$vendor_service_meta ] = array(
                'title'   => __( 'Vendor', 'yith-booking-for-woocommerce' ),
                'type'    => 'select',
                'default' => '',
                'options' => $vendors,
                'desc'    => ''
            );
        }

        return $info;
    }

    /**
     * Add vendor filter form and dropdown in services
     */
    public function add_vendor_filter_in_services() {
        $vendor = yith_get_vendor( 'current', 'user' );
        if ( !$vendor->is_valid() || !$vendor->has_limited_access() ) {
            $vendors = self::get_vendors( array( 'fields' => 'id=>name' ) );
            if ( !$vendors || !is_array( $vendors ) )
                $vendors = array();

            $vendors[ '' ]     = __( 'All', 'yith-booking-for-woocommerce' );
            $vendors[ 'mine' ] = __( 'Mine', 'yith-booking-for-woocommerce' );

            asort( $vendors );

            echo '<div class="yith-wcbk-services-filter-by-vendor-form yith-wcbk-move alignleft actions" data-after=".tablenav.top > .bulkactions">';
            echo '<form method="get">';
            if ( !empty( $_GET ) ) {
                foreach ( $_GET as $key => $value ) {
                    if ( self::$vendor_service_meta === $key )
                        continue;
                    echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />';
                }
            }

            $selected_vendor = isset( $_REQUEST[ self::$vendor_service_meta ] ) ? $_REQUEST[ self::$vendor_service_meta ] : '';

            echo '<select name="' . self::$vendor_service_meta . '">';
            foreach ( $vendors as $vendor_id => $vendor_name ) {
                $selected = selected( $vendor_id == $selected_vendor, true );
                echo "<option value='{$vendor_id}' $selected>$vendor_name</option>";
            }
            echo '</select>';

            echo '<input type="submit" class="button" value="' . __( 'Filter by Vendor', 'yith-booking-for-woocommerce' ) . '">';

            echo '</form>';
            echo '</div>';
        }
    }

    /**
     * Add Vendor column in services
     *
     * @param string $columns the column
     * @return array The columns list
     * @use   manage_{$this->screen->id}_columns filter
     */
    public function add_vendor_column_in_services( $columns ) {
        $vendor = yith_get_vendor( 'current', 'user' );
        if ( !$vendor->is_valid() || !$vendor->has_limited_access() )
            $columns[ 'service_vendor' ] = __( 'Vendor', 'yith-booking-for-woocommerce' );

        return $columns;
    }

    /**
     * Print Vendor column in services
     *
     * @param string $custom_column Filter value
     * @param string $column_name   Column name
     * @param int    $term_id       The term id
     * @internal param \The $columns columns
     * @return array The columns list
     * @use      manage_{$this->screen->taxonomy}_custom_column filter
     */
    public function print_vendor_column_in_services( $custom_column, $column_name, $term_id ) {
        $service = yith_get_booking_service( $term_id );
        if ( 'service_vendor' === $column_name ) {
            $vendor_meta = self::$vendor_service_meta;
            $vendor_id   = absint( $service->$vendor_meta );
            if ( !!$vendor_id ) {
                $vendor = yith_get_vendor( $vendor_id );
                if ( $vendor->is_valid() ) {
                    $link        = add_query_arg( array( self::$vendor_service_meta => $vendor->id ) );
                    $vendor_name = $vendor->name;
                    $title       = sprintf( _x( 'Filter by %s', 'Filter by Vendor name', 'yith-booking-for-woocommerce' ), $vendor_name );
                    echo "<a href='$link' title='$title'>$vendor_name</a>";
                }
            }
        }
    }

    /**
     * get Vendors
     *
     * @param array $args
     * @return array|int|WP_Error
     */
    public static function get_vendors( $args = array() ) {
        $hash = !!$args ? md5( implode( ' ', $args ) ) : 0;
        if ( !isset( self::$vendors_data[ $hash ] ) ) {

            $default_args = array(
                'fields'     => 'id',
                'hide_empty' => false
            );

            $args               = wp_parse_args( $args, $default_args );
            $args[ 'taxonomy' ] = YITH_Vendor::$taxonomy;

            self::$vendors_data[ $hash ] = YITH_WCBK()->wp->get_terms( $args );
        }

        return self::$vendors_data[ $hash ];
    }
}