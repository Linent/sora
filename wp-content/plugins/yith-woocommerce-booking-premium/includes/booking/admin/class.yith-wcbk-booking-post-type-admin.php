<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Booking_Post_Type_Admin' ) ) {
    /**
     * Class YITH_WCBK_Booking_Post_Type_Helper
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Booking_Post_Type_Admin {

        /** @var YITH_WCBK_Booking_Post_Type_Admin */
        protected static $_instance;

        public $booking_post_type;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Booking_Post_Type_Admin
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Booking_Post_Type_Helper constructor.
         */
        public function __construct() {
            // Manage Booking List columns
            add_filter( 'manage_' . YITH_WCBK_Post_Types::$booking . '_posts_columns', array( $this, 'manage_booking_list_columns' ) );
            add_action( 'manage_' . YITH_WCBK_Post_Types::$booking . '_posts_custom_column', array( $this, 'render_booking_list_columns' ), 10, 2 );
            add_filter( 'manage_edit-' . YITH_WCBK_Post_Types::$booking . '_sortable_columns', array( $this, 'manage_booking_sortable_columns' ), 10, 1 );
            add_filter( 'default_hidden_columns', array( $this, 'default_booking_hidden_columns' ), 10, 2 );
            add_action( 'pre_get_posts', array( $this, 'booking_orderby' ) );

            // Booking bulk actions
            add_action( 'load-edit.php', array( $this, 'bulk_action' ) );

            // get the PDF
            add_action( 'init', array( $this, 'get_pdf' ) );

            // Booking Search
            add_filter( 'get_search_query', array( $this, 'booking_search_label' ) );
            add_filter( 'query_vars', array( $this, 'add_custom_query_var' ) );
            add_action( 'parse_query', array( $this, 'booking_search' ) );

            // Remove Row Actions for Bookings
            add_filter( 'post_row_actions', array( $this, 'customize_booking_row_actions' ), 10, 2 );

            // set primary column to booking
            add_filter( 'list_table_primary_column', array( $this, 'list_table_primary_column' ), 10, 2 );
        }

        /**
         * Set Default hidden columns in Booking WP List
         *
         * @param array     $hidden
         * @param WP_Screen $screen
         * @return array
         * @since 2.0.0
         */
        public function default_booking_hidden_columns( $hidden, $screen ) {
            if ( 'edit-' . YITH_WCBK_Post_Types::$booking === $screen->id ) {
                $hidden[] = 'order';
                $hidden[] = 'user';
                $hidden[] = 'duration';
                $hidden[] = 'taxonomy-yith_booking_service';
                $hidden[] = 'persons';
                $hidden[] = 'booking_date';
            }

            return $hidden;
        }

        /**
         * Booking Orderby for sorting in WP List
         *
         * @param WP_Query $query
         */
        public function booking_orderby( $query ) {
            if ( !is_admin() )
                return;

            $orderby = $query->get( 'orderby' );

            switch ( $orderby ) {
                case 'order':
                    $query->set( 'meta_key', '_order_id' );
                    $query->set( 'orderby', 'meta_value_num' );
                    break;
                case 'from':
                    $query->set( 'meta_key', '_from' );
                    $query->set( 'orderby', 'meta_value_num' );
                    break;
                case 'to':
                    $query->set( 'meta_key', '_to' );
                    $query->set( 'orderby', 'meta_value_num' );
                    break;
                case 'persons':
                    $query->set( 'meta_key', '_persons' );
                    $query->set( 'orderby', 'meta_value_num' );
                    break;

            }
        }

        /**
         * Process the new bulk actions for bookings.
         */
        public function bulk_action() {
            if ( !isset( $_REQUEST[ 'post' ] ) ) {
                return;
            }
            $wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
            $action        = $wp_list_table->current_action();

            switch ( $action ) {
                case 'export_to_csv':
                    $post_ids = array_map( 'absint', (array) $_REQUEST[ 'post' ] );
                    YITH_WCBK()->exporter->download_csv( $post_ids );
                    break;

                case 'export_to_ics':
                    $post_ids = array_map( 'absint', (array) $_REQUEST[ 'post' ] );
                    YITH_WCBK()->exporter->download_ics( $post_ids );
                    break;

                default:
            }
        }

        /**
         * Remove Booking Actions
         *
         * @param array   $actions An array of row action links. Defaults are
         *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
         *                         'Delete Permanently', 'Preview', and 'View'.
         * @param WP_Post $post    The post object.
         * @return array
         */
        public function customize_booking_row_actions( $actions, $post ) {
            if ( $post->post_type === YITH_WCBK_Post_Types::$booking ) {
                $new_actions = array();
                if ( isset( $actions[ 'edit' ] ) )
                    $new_actions[ 'edit' ] = $actions[ 'edit' ];

                if ( isset( $actions[ 'trash' ] ) )
                    $new_actions[ 'trash' ] = $actions[ 'trash' ];

                $actions = apply_filters( 'yith_wcbk_booking_row_actions', $new_actions, $post );
            }

            return $actions;
        }

        /**
         * Get the pdf for booking
         */
        public function get_pdf() {
            if ( isset( $_REQUEST[ 'action' ] ) && !empty( $_REQUEST[ 'booking-id' ] ) ) {
                $booking_id = absint( $_REQUEST[ 'booking-id' ] );
                switch ( $_REQUEST[ 'action' ] ) {
                    case 'get-booking-pdf-customer':
                        YITH_WCBK()->exporter->generate_pdf( $booking_id, false );
                        break;

                    case 'get-booking-pdf-admin':
                        YITH_WCBK()->exporter->generate_pdf( $booking_id, true );
                        break;
                }
            }
        }

        /**
         * set primary column to booking
         *
         * @param $default
         * @param $screen_id
         * @return string
         */
        public function list_table_primary_column( $default, $screen_id ) {
            if ( 'edit-' . YITH_WCBK_Post_Types::$booking === $screen_id ) {
                return 'booking';
            }

            return $default;
        }

        /**
         * Manage columns column in Booking List
         *
         * @param array $columns
         * @return array
         */
        public function manage_booking_list_columns( $columns ) {
            $has_date  = isset( $columns[ 'date' ] );
            $date_text = $has_date ? $columns[ 'date' ] : '';
            if ( $has_date ) {
                unset( $columns[ 'date' ] );
            }
            unset( $columns[ 'title' ] );

            $new_columns[ 'cb' ] = $columns[ 'cb' ];
            unset( $columns[ 'cb' ] );

            $new_columns[ 'booking' ]        = __( 'Booking', 'yith-booking-for-woocommerce' );
            $new_columns[ 'booking_status' ] = __( 'Status', 'yith-booking-for-woocommerce' );
            $new_columns[ 'order' ]          = __( 'Order', 'yith-booking-for-woocommerce' );
            $new_columns[ 'user' ]           = __( 'User', 'yith-booking-for-woocommerce' );
            $new_columns[ 'duration' ]       = __( 'Duration', 'yith-booking-for-woocommerce' );
            $new_columns[ 'from' ]           = __( 'From', 'yith-booking-for-woocommerce' );
            $new_columns[ 'to' ]             = __( 'To', 'yith-booking-for-woocommerce' );
            $new_columns[ 'persons' ]        = __( 'People', 'yith-booking-for-woocommerce' );

            $new_columns = array_merge( $new_columns, $columns );

            $new_columns = array_merge( $new_columns, apply_filters( 'yith_wcbk_booking_custom_columns', array() ) );

            if ( $has_date ) {
                $new_columns[ 'booking_date' ] = $date_text;
            }
            $new_columns[ 'booking_actions' ] = __( 'Actions', 'yith-booking-for-woocommerce' );

            return $new_columns;
        }

        /**
         * Manage sortable columns in Booking List
         *
         * @param $sortable_columns
         * @return array
         * @since  1.2.1
         */
        public function manage_booking_sortable_columns( $sortable_columns ) {
            $sortable_columns[ 'booking' ]      = 'ID';
            $sortable_columns[ 'order' ]        = 'order';
            $sortable_columns[ 'from' ]         = 'from';
            $sortable_columns[ 'to' ]           = 'to';
            $sortable_columns[ 'persons' ]      = 'persons';
            $sortable_columns[ 'booking_date' ] = 'date';

            return $sortable_columns;
        }

        /**
         * Render columns in Booking List
         *
         * @param string $column
         * @param int    $post_id
         */
        public function render_booking_list_columns( $column, $post_id ) {
            /**
             * @var YITH_WCBK_Booking $booking
             */
            $booking = yith_get_booking( $post_id );
            if ( $booking ) {
                $post = $booking->post;
                switch ( $column ) {
                    case 'booking_status':
                        $status      = $booking->get_status();
                        $status_text = $booking->get_status_text();

                        echo "<span class='yith-booking-status {$status}'>{$status_text}</span>";
                        break;
                    case 'booking':
                        $booking_edit_link         = get_edit_post_link( $post_id );
                        $booking_product_edit_link = get_edit_post_link( $booking->product_id );
                        $booking_title             = get_the_title( $booking->product_id );
                        $booking_title             = $booking_title ? $booking_title : $booking->title;
                        $booking_id                = "<strong>#{$post_id}</strong> ";

                        $booking_title = "<a href='{$booking_product_edit_link}'>$booking_title</a>";
                        $booking_id    = "<a href='{$booking_edit_link}'>$booking_id</a>";

                        echo sprintf( __( '%s of %s', 'yith-booking-for-woocommerce' ), $booking_id, $booking_title );
                        break;
                    case 'order':
                        $order_id = $booking->order_id;
                        if ( $order_id > 0 ) {
                            if ( $the_order = wc_get_order( $order_id ) ) {
                                $the_order_user_id = yit_get_prop( $the_order, 'user_id' );
                                $user_info         = !empty( $the_order_user_id ) ? get_userdata( $the_order_user_id ) : false;

                                if ( !!$user_info ) {
                                    $username = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

                                    if ( $user_info->first_name || $user_info->last_name ) {
                                        $username .= esc_html( sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
                                    } else {
                                        $username .= esc_html( ucfirst( $user_info->display_name ) );
                                    }

                                    $username .= '</a>';
                                } else {
                                    if ( yit_get_prop( $the_order, 'billing_first_name' ) || yit_get_prop( $the_order, 'billing_last_name' ) ) {
                                        $username = trim( sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), yit_get_prop( $the_order, 'billing_first_name' ), yit_get_prop( $the_order, 'billing_last_name' ) ) );
                                    } else {
                                        $username = __( 'Guest', 'woocommerce' );
                                    }
                                }

                                printf( _x( '%s by %s', 'Order number by X', 'woocommerce' ), '<a href="' . admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ) . '" class="row-title"><strong>#' . esc_attr( $the_order->get_order_number() ) . '</strong></a>', $username );

                                if ( $billing_email = yit_get_prop( $the_order, 'billing_email' ) ) {
                                    echo '<small class="meta email"><a href="' . esc_url( 'mailto:' . $billing_email ) . '">' . esc_html( $billing_email ) . '</a></small>';
                                }

                                printf( '<mark class="order-status %s"><span>%s</span></mark>',
                                        esc_attr( sanitize_html_class( 'status-' . $the_order->get_status() ) ),
                                        esc_html( wc_get_order_status_name( $the_order->get_status() ) ) );
                            } else {
                                printf( _x( '#%s (deleted)', 'Deleted Order:#123 (deleted)', 'yith-booking-for-woocommerce' ), $order_id );
                            }
                        } else {
                            echo '&ndash;';
                        }
                        break;
                    case 'user':
                        $user_id = $booking->user_id;
                        if ( $user_id > 0 && $user_info = get_userdata( $user_id ) ) {
                            $username = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

                            if ( $user_info->first_name || $user_info->last_name ) {
                                $username .= esc_html( sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
                            } else {
                                $username .= esc_html( ucfirst( $user_info->display_name ) );
                            }

                            $username .= '</a>';
                            echo $username;

                            echo '<small class="meta email"><a href="' . esc_url( 'mailto:' . $user_info->user_email ) . '">' . esc_html( $user_info->user_email ) . '</a></small>';
                        } else {
                            echo '&ndash;';
                        }
                        break;
                    case 'duration':
                        echo $booking->get_duration_html();
                        break;
                    case 'from':
                        echo $booking->get_formatted_date( 'from' );
                        break;
                    case 'to':
                        echo $booking->get_formatted_date( 'to' );
                        break;
                    case 'persons':
                        $person_types_html = $booking->get_person_types_html();
                        echo "<span class='tips' data-tip='{$person_types_html}'>{$booking->persons}</span>";
                        break;
                    case 'booking_actions':
                        ?><p>
                        <?php
                        do_action( 'yith_wcbk_admin_booking_actions_start', $booking );

                        $actions = array();

                        if ( $booking->has_status( 'unpaid' ) ) {
                            $actions[ 'paid' ] = array(
                                'url'    => $booking->get_mark_action_url( 'paid' ),
                                'name'   => __( 'Paid', 'yith-booking-for-woocommerce' ),
                                'action' => "paid",
                            );
                        } elseif ( $booking->has_status( 'pending-confirm' ) ) {
                            $actions[ 'confirmed' ]   = array(
                                'url'    => $booking->get_mark_action_url( 'confirmed' ),
                                'name'   => __( 'Confirm', 'yith-booking-for-woocommerce' ),
                                'action' => "confirmed",
                            );
                            $actions[ 'unconfirmed' ] = array(
                                'url'    => $booking->get_mark_action_url( 'unconfirmed' ),
                                'name'   => __( 'Reject', 'yith-booking-for-woocommerce' ),
                                'action' => "unconfirmed",
                            );
                        }

                        $actions[ 'view' ] = array(
                            'url'    => admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
                            'name'   => __( 'View', 'yith-booking-for-woocommerce' ),
                            'action' => "view",
                        );

                        $actions[ 'get-pdf-customer' ] = array(
                            'url'    => add_query_arg( array(
                                                           'action'     => 'get-booking-pdf-customer',
                                                           'booking-id' => $post_id,
                                                       ), admin_url() ),
                            'name'   => __( 'Customer PDF', 'yith-booking-for-woocommerce' ),
                            'action' => "get-pdf-customer",
                        );

                        $actions[ 'get-pdf-admin' ] = array(
                            'url'    => add_query_arg( array(
                                                           'action'     => 'get-booking-pdf-admin',
                                                           'booking-id' => $post_id,
                                                       ), admin_url() ),
                            'name'   => __( 'Admin PDF', 'yith-booking-for-woocommerce' ),
                            'action' => "get-pdf-admin",
                        );


                        $actions = apply_filters( 'yith_wcbk_admin_booking_actions', $actions, $booking );

                        foreach ( $actions as $action ) {
                            printf( '<a class="button tips %s" href="%s" data-tip="%s">%s</a>', esc_attr( $action[ 'action' ] ), esc_url( $action[ 'url' ] ), esc_attr( $action[ 'name' ] ), esc_attr( $action[ 'name' ] ) );
                        }

                        do_action( 'yith_wcbk_admin_booking_actions_end', $booking );
                        ?>
                        </p><?php
                        break;
                    case 'booking_date':
                        global $mode;
                        if ( '0000-00-00 00:00:00' === $post->post_date ) {
                            $t_time    = $h_time = __( 'Unpublished' );
                            $time_diff = 0;
                        } else {
                            $t_time = get_the_time( __( 'Y/m/d g:i:s a' ) );
                            $m_time = $post->post_date;
                            $time   = get_post_time( 'G', true, $post );

                            $time_diff = time() - $time;

                            if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
                                $h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
                            } else {
                                $h_time = mysql2date( __( 'Y/m/d' ), $m_time );
                            }
                        }
                        if ( 'excerpt' === $mode ) {
                            echo apply_filters( 'yith_wcbk_booking_date_column_time', $t_time, $post, 'date', $mode );
                        } else {
                            echo '<abbr title="' . $t_time . '">' . apply_filters( 'yith_wcbk_booking_date_column_time', $h_time, $post, 'date', $mode ) . '</abbr>';
                        }
                        break;
                }

                do_action( 'yith_wcbk_booking_render_custom_columns', $column, $post_id, $booking );
            }
        }

        /**
         * Booking Search
         *
         * @param WP_Query $wp
         */
        public function booking_search( $wp ) {
            global $pagenow, $wpdb;

            if ( 'edit.php' != $pagenow || empty( $wp->query_vars[ 's' ] ) || $wp->query_vars[ 'post_type' ] != YITH_WCBK_Post_Types::$booking ) {
                return;
            }

            $order_search_fields = array(
                '_order_key',
                '_billing_company',
                '_billing_address_1',
                '_billing_address_2',
                '_billing_city',
                '_billing_postcode',
                '_billing_country',
                '_billing_state',
                '_billing_email',
                '_billing_phone',
                '_shipping_address_1',
                '_shipping_address_2',
                '_shipping_city',
                '_shipping_postcode',
                '_shipping_country',
                '_shipping_state',
            );

            $user_search_fields = array(
                'user_login',
                'user_nicename',
                'user_email',
                'display_name',
            );

            $search_booking_id = str_replace( 'Booking #', '', $_GET[ 's' ] );

            // Search bookings
            if ( is_numeric( $search_booking_id ) ) {
                $post_ids = array_unique( array_merge(
                                              $wpdb->get_col(
                                                  $wpdb->prepare( "SELECT DISTINCT p1.post_id FROM {$wpdb->postmeta} p1 WHERE p1.meta_value LIKE '%%%d%%';", absint( $search_booking_id ) )
                                              ),
                                              array( absint( $search_booking_id ) )
                                          ) );
            } else {
                $post_ids = array_unique( array_merge(
                                              $wpdb->get_col(
                                                  $wpdb->prepare( "
						SELECT DISTINCT booking_meta.post_id
						FROM {$wpdb->postmeta} AS booking_meta
						INNER JOIN {$wpdb->postmeta} AS order_meta ON order_meta.post_id = booking_meta.meta_value AND booking_meta.meta_key = '_order_id'
						INNER JOIN {$wpdb->postmeta} AS order_meta2 ON order_meta2.post_id = order_meta.post_id
						WHERE
							( order_meta.meta_key = '_billing_first_name' AND order_meta2.meta_key = '_billing_last_name' AND CONCAT(order_meta.meta_value, ' ', order_meta2.meta_value) LIKE '%%%s%%' )
						OR
							( order_meta.meta_key = '_shipping_first_name' AND order_meta2.meta_key = '_shipping_last_name' AND CONCAT(order_meta.meta_value, ' ', order_meta2.meta_value) LIKE '%%%s%%' )
						OR
							( order_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $order_search_fields ) ) . "') AND order_meta.meta_value LIKE '%%%s%%' )
						",
                                                                  wc_clean( $_GET[ 's' ] ), wc_clean( $_GET[ 's' ] ), wc_clean( $_GET[ 's' ] )
                                                  )
                                              ),
                                              $wpdb->get_col(
                                                  $wpdb->prepare( "
						SELECT DISTINCT booking_meta.post_id
						FROM {$wpdb->postmeta} AS booking_meta
						INNER JOIN {$wpdb->users} AS user_data ON user_data.ID = booking_meta.meta_value AND booking_meta.meta_key = '_user_id'
						WHERE
							( user_data.user_login LIKE '%%%s%%' )
						OR
						    ( user_data.user_nicename LIKE '%%%s%%' )
						OR
						    ( user_data.user_email LIKE '%%%s%%' )
						OR
						    ( user_data.display_name LIKE '%%%s%%' )
						",
                                                                  wc_clean( $_GET[ 's' ] ), wc_clean( $_GET[ 's' ] ), wc_clean( $_GET[ 's' ] ), wc_clean( $_GET[ 's' ] )
                                                  )
                                              ) ) );
            }

            if ( is_array( $post_ids ) ) {
                // Remove s - we don't want to search booking name
                unset( $wp->query_vars[ 's' ] );

                // so we know we're doing this
                $wp->query_vars[ 'booking_search' ] = true;

                // Search by found posts
                $wp->query_vars[ 'post__in' ] = array_merge( $post_ids, array( 0 ) );
            }
        }

        /**
         * Change the label when searching bookings.
         *
         * @param mixed $query
         * @return string
         */
        public function booking_search_label( $query ) {
            global $pagenow, $typenow;

            if ( 'edit.php' != $pagenow ) {
                return $query;
            }

            if ( $typenow != YITH_WCBK_Post_Types::$booking ) {
                return $query;
            }

            if ( !get_query_var( 'booking_search' ) ) {
                return $query;
            }

            return wp_unslash( $_GET[ 's' ] );
        }

        /**
         * Query vars for custom searches.
         *
         * @param mixed $public_query_vars
         * @return array
         */
        public function add_custom_query_var( $public_query_vars ) {
            $public_query_vars[] = 'booking_search';

            return $public_query_vars;
        }
    }
}

return YITH_WCBK_Booking_Post_Type_Admin::get_instance();