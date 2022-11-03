<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_AJAX' ) ) {
    /**
     * Class YITH_WCBK_AJAX
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_AJAX {

        /** @var YITH_WCBK_AJAX */
        private static $_instance;

        /** @var bool */
        public $testing = false;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_AJAX
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_AJAX constructor.
         */
        private function __construct() {
            $ajax_actions = array(
                'json_search_order',
                'json_search_booking_products',
                'get_product_booking_form',
                'mark_booking_status',
                'search_booking_products',
                'search_booking_products_paged',
                'add_booking_note',
                'delete_booking_note',
                'get_booking_data',
                'get_booking_available_times',
                'get_product_not_available_dates',
                'create_people_type',
                'create_extra_cost',
                'create_service',
            );

            foreach ( $ajax_actions as $ajax_action ) {
                add_action( 'wp_ajax_yith_wcbk_' . $ajax_action, array( $this, $ajax_action ) );
                add_action( 'wp_ajax_nopriv_yith_wcbk_' . $ajax_action, array( $this, $ajax_action ) );
            }
        }

        /**
         * Start Booking AJAX call
         *
         * @param string $context admin or frontend?
         */
        private function _ajax_start( $context = 'admin' ) {
            error_reporting( 0 );

            !defined( 'YITH_WCBK_DOING_AJAX' ) && define( 'YITH_WCBK_DOING_AJAX', true );
            if ( 'admin' === $context ) {
                !defined( 'YITH_WCBK_DOING_AJAX_ADMIN' ) && define( 'YITH_WCBK_DOING_AJAX_ADMIN', true );
            } elseif ( 'frontend' === $context ) {
                !defined( 'YITH_WCBK_DOING_AJAX_FRONTEND' ) && define( 'YITH_WCBK_DOING_AJAX_FRONTEND', true );
            }
        }

        /**
         * Add booking note via ajax.
         */
        public function add_booking_note() {
            $this->_ajax_start();

            check_ajax_referer( 'add-booking-note', 'security' );

            if ( !current_user_can( 'edit_' . YITH_WCBK_Post_Types::$booking . 's' ) ) {
                wp_die( -1 );
            }

            $post_id   = absint( $_POST[ 'post_id' ] );
            $note      = wp_kses_post( trim( stripslashes( $_POST[ 'note' ] ) ) );
            $note_type = $_POST[ 'note_type' ];

            $note_classes = 'note ' . $note_type;

            if ( $post_id > 0 && $booking = yith_get_booking( $post_id ) ) {
                $note_id = $booking->add_note( $note_type, $note );

                echo '<li rel="' . esc_attr( $note_id ) . '" class="' . esc_attr( $note_classes ) . '">';
                echo '<div class="note_content">';
                echo wpautop( wptexturize( $note ) );
                echo '</div><p class="meta"><a href="#" class="delete-booking-note">' . __( 'Delete note', 'yith-booking-for-woocommerce' ) . '</a></p>';
                echo '</li>';
            }
            wp_die();
        }

        /**
         * Create People Type via ajax.
         */
        public function create_people_type() {
            $this->_ajax_start();

            check_ajax_referer( 'create-people-type', 'security' );

            if ( !current_user_can( 'edit_' . YITH_WCBK_Post_Types::$person_type . 's' ) || !current_user_can( 'create_' . YITH_WCBK_Post_Types::$person_type . 's' ) ) {
                return $this->send_json( array( 'message' => __( 'Error: something went wrong', 'yith-booking-for-woocommerce' ) ) );
            }

            $title   = $_POST[ 'title' ];
            $post_id = wp_insert_post( array(
                                           'post_title'  => $title,
                                           'post_type'   => YITH_WCBK_Post_Types::$person_type,
                                           'post_status' => 'publish'
                                       ) );

            if ( $post_id && is_wp_error( $post_id ) ) {
                $error_message = sprintf( __( 'Error: %s', 'yith-booking-for-woocommerce' ), $post_id->get_error_message() );
                return $this->send_json( array( 'message' => $error_message ) );
            }

            if ( $post_id ) {
                return $this->send_json( array( 'id' => $post_id, 'title' => $title ) );
            }
            return $this->send_json( array( 'message' => __( 'Error: something went wrong', 'yith-booking-for-woocommerce' ) ) );
        }

        /**
         * Create Extra Cost via ajax.
         */
        public function create_extra_cost() {
            $this->_ajax_start();

            check_ajax_referer( 'create-extra-cost', 'security' );

            if ( !current_user_can( 'edit_' . YITH_WCBK_Post_Types::$extra_cost . 's' ) || !current_user_can( 'create_' . YITH_WCBK_Post_Types::$extra_cost . 's' ) ) {
                return $this->send_json( array( 'message' => __( 'Error: something went wrong', 'yith-booking-for-woocommerce' ) ) );
            }

            $title   = $_POST[ 'title' ];
            $post_id = wp_insert_post( array(
                                           'post_title'  => $title,
                                           'post_type'   => YITH_WCBK_Post_Types::$extra_cost,
                                           'post_status' => 'publish'
                                       ) );

            if ( $post_id && is_wp_error( $post_id ) ) {
                $error_message = sprintf( __( 'Error: %s', 'yith-booking-for-woocommerce' ), $post_id->get_error_message() );
                return $this->send_json( array( 'message' => $error_message ) );
            }

            if ( $post_id ) {
                return $this->send_json( array( 'id' => $post_id, 'title' => $title ) );
            }
            return $this->send_json( array( 'message' => __( 'Error: something went wrong', 'yith-booking-for-woocommerce' ) ) );
        }

        /**
         * Create Service via ajax.
         */
        public function create_service() {
            $this->_ajax_start();

            check_ajax_referer( 'create-service', 'security' );

            if ( !current_user_can( 'manage_' . YITH_WCBK_Post_Types::$service_tax . 's' ) ) {
                return $this->send_json( array( 'message' => __( 'Error: something went wrong', 'yith-booking-for-woocommerce' ) ) );
            }

            $title       = $_POST[ 'title' ];
            $description = $_POST[ 'description' ];
            $result      = wp_insert_term( $title, YITH_WCBK_Post_Types::$service_tax, array( 'description' => $description ) );

            if ( $result && is_wp_error( $result ) ) {
                $error_message = sprintf( __( 'Error: %s', 'yith-booking-for-woocommerce' ), $result->get_error_message() );
                return $this->send_json( array( 'message' => $error_message ) );
            }

            $term_id = $result && isset( $result[ 'term_id' ] ) ? $result[ 'term_id' ] : false;

            if ( $term_id ) {
                return $this->send_json( array( 'id' => $term_id, 'title' => $title, array( 'debug' => array( 'id' => $term_id, 'title' => $title ) ) ) );
            }

            return $this->send_json( array( 'message' => __( 'Error: something went wrong', 'yith-booking-for-woocommerce' ) ) );
        }

        /**
         * Delete booking note via ajax.
         */
        public function delete_booking_note() {
            $this->_ajax_start();

            check_ajax_referer( 'delete-booking-note', 'security' );

            if ( !current_user_can( 'edit_' . YITH_WCBK_Post_Types::$booking . 's' ) ) {
                wp_die( -1 );
            }

            $note_id = (int) $_POST[ 'note_id' ];

            if ( $note_id > 0 ) {
                yith_wcbk_delete_booking_note( $note_id );
            }
            wp_die();
        }

        /**
         * Order Search
         */
        public function json_search_order() {
            $this->_ajax_start();

            global $wpdb;
            ob_start();

            check_ajax_referer( 'search-orders', 'security' );

            $term = wc_clean( stripslashes( $_GET[ 'term' ] ) );

            if ( empty( $term ) ) {
                die();
            }

            $found_orders = array();

            $term = apply_filters( 'yith_wcbk_json_search_order_number', $term );

            $query_orders = $wpdb->get_results( $wpdb->prepare( "
			SELECT ID, post_title FROM {$wpdb->posts} AS posts
			WHERE posts.post_type = 'shop_order'
			AND posts.ID LIKE %s
		", '%' . $term . '%' ) );

            if ( $query_orders ) {
                foreach ( $query_orders as $item ) {
                    $order_number              = apply_filters( 'yith_wcbk_order_number', '#' . $item->ID, $item->ID );
                    $found_orders[ $item->ID ] = $order_number . ' &ndash; ' . esc_html( $item->post_title );
                }
            }

            return $this->send_json( $found_orders );
        }

        /**
         * Booking Products Search
         */
        public function json_search_booking_products() {
            $this->_ajax_start();

            ob_start();
            check_ajax_referer( 'search-bookings', 'security' );

            $search_term = isset( $_REQUEST[ 'term' ][ 'term' ] ) ? $_REQUEST[ 'term' ][ 'term' ] : $_REQUEST[ 'term' ];

            $term    = (string) wc_clean( stripslashes( $search_term ) );
            $exclude = array();

            if ( empty( $term ) ) {
                die();
            }

            if ( !empty( $_REQUEST[ 'exclude' ] ) ) {
                $exclude = array_map( 'intval', explode( ',', $_REQUEST[ 'exclude' ] ) );
            }


            $found_products = array();

            if ( $booking_term = get_term_by( 'slug', 'booking', 'product_type' ) ) {

                $posts_in = array_unique( (array) get_objects_in_term( $booking_term->term_id, 'product_type' ) );

                if ( sizeof( $posts_in ) > 0 ) {

                    $args = array(
                        'post_type'        => 'product',
                        'post_status'      => 'publish',
                        'numberposts'      => -1,
                        'orderby'          => 'title',
                        'order'            => 'asc',
                        'post_parent'      => 0,
                        'suppress_filters' => 0,
                        'include'          => $posts_in,
                        's'                => $term,
                        'fields'           => 'ids',
                        'exclude'          => $exclude,
                    );

                    $posts = get_posts( $args );

                    if ( !empty( $posts ) ) {
                        foreach ( $posts as $post ) {
                            $product = wc_get_product( $post );

                            if ( !current_user_can( 'read_product', $post ) ) {
                                continue;
                            }

                            $found_products[ $post ] = rawurldecode( $product->get_formatted_name() );
                        }
                    }
                }
            }

            $found_products = apply_filters( 'yith_wcbk_json_search_found_booking_products', $found_products );

            return $this->send_json( $found_products );
        }

        /**
         * Get the product booking form
         */
        public function get_product_booking_form() {
            $this->_ajax_start();

            if ( isset( $_POST[ 'product_id' ] ) ) {
                $product = wc_get_product( absint( $_POST[ 'product_id' ] ) );
                $args    = array(
                    'show_price'      => true,
                    'additional_data' => array(
                        'bk_page' => 'create_booking'
                    ),
                );
                do_action( 'yith_wcbk_booking_form', $product, $args );
            }
            die();
        }

        /**
         * Mark an order with a status.
         */
        public function mark_booking_status() {
            $this->_ajax_start();

            $booking_id = isset( $_REQUEST[ 'booking_id' ] ) ? absint( $_GET[ 'booking_id' ] ) : false;

            if ( $booking_id && current_user_can( 'edit_' . YITH_WCBK_Post_Types::$booking, $booking_id ) ) {
                $status = sanitize_text_field( $_REQUEST[ 'status' ] );

                if ( yith_wcbk_is_a_booking_status( $status ) && $booking_id ) {
                    $booking = yith_get_booking( $booking_id );
                    $booking->update_status( $status );
                }
            }

            wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=' . YITH_WCBK_Post_Types::$booking ) );
            die();
        }

        /**
         * Search Forms: search booking products
         */
        public function search_booking_products() {
            $this->_ajax_start( 'frontend' );

            check_ajax_referer( 'search-booking-products', 'security' );

            if ( isset( $_REQUEST[ 'yith-wcbk-booking-search' ] ) && $_REQUEST[ 'yith-wcbk-booking-search' ] === 'search-bookings' ) {

                $this->set_in_search_form_const();

                $from         = isset( $_REQUEST[ 'from' ] ) ? $_REQUEST[ 'from' ] : '';
                $to           = isset( $_REQUEST[ 'to' ] ) ? $_REQUEST[ 'to' ] : '';
                $persons      = isset( $_REQUEST[ 'persons' ] ) ? $_REQUEST[ 'persons' ] : 1;
                $person_types = isset( $_REQUEST[ 'person_types' ] ) ? $_REQUEST[ 'person_types' ] : array();
                $services     = isset( $_REQUEST[ 'services' ] ) ? $_REQUEST[ 'services' ] : array();

                if ( !!$person_types && is_array( $person_types ) ) {
                    $persons = array_sum( array_values( $person_types ) );
                }

                $product_ids = YITH_WCBK()->search_form_helper->search_booking_products( $_REQUEST );

                if ( !$product_ids ) {
                    $no_bookings_available_text = __( 'No booking available for this search', 'yith-booking-for-woocommerce' );
                    echo apply_filters( 'yith_wcbk_search_booking_products_no_bookings_available_text', $no_bookings_available_text );
                    do_action( 'yith_wcbk_search_booking_products_no_bookings_available_after' );
                    die();
                }

                $current_page = 1;

                $args     = array(
                    'post_type'           => 'product',
                    'ignore_sticky_posts' => 1,
                    'no_found_rows'       => 1,
                    'posts_per_page'      => apply_filters( 'yith_wcbk_ajax_search_booking_products_posts_per_page', 12 ),
                    'paged'               => $current_page,
                    'post__in'            => $product_ids,
                    'orderby'             => 'post__in',
                    'meta_query'          => WC()->query->get_meta_query(),
                );
                $args     = apply_filters( 'yith_wcbk_ajax_search_booking_products_query_args', $args, $product_ids );
                $products = new WP_Query( $args );

                $booking_request = array(
                    'from'             => $from,
                    'to'               => $to,
                    'persons'          => $persons,
                    'person_types'     => $person_types,
                    'booking_services' => $services,
                );

                wc_get_template( 'booking/search-form/results/results.php', compact( 'booking_request', 'products', 'product_ids', 'current_page' ), '', YITH_WCBK_TEMPLATE_PATH );
            }

            die();
        }

        /**
         * Search Forms: search booking products paged
         */
        public function search_booking_products_paged() {
            $this->_ajax_start( 'frontend' );

            if ( !empty( $_REQUEST[ 'product_ids' ] ) && !empty( $_REQUEST[ 'booking_request' ] ) && !empty( $_REQUEST[ 'page' ] ) ) {
                $this->set_in_search_form_const();

                $product_ids     = $_REQUEST[ 'product_ids' ];
                $booking_request = $_REQUEST[ 'booking_request' ];
                $current_page    = absint( $_REQUEST[ 'page' ] );

                $args = array(
                    'post_type'           => 'product',
                    'ignore_sticky_posts' => 1,
                    'no_found_rows'       => 1,
                    'posts_per_page'      => apply_filters( 'yith_wcbk_ajax_search_booking_products_posts_per_page', 12 ),
                    'paged'               => $current_page,
                    'post__in'            => $product_ids,
                    'meta_query'          => WC()->query->get_meta_query(),
                );
                $args = apply_filters( 'yith_wcbk_ajax_search_booking_products_query_args', $args, $product_ids );

                $products = new WP_Query( $args );

                wc_get_template( 'booking/search-form/results/results-list.php', compact( 'products', 'booking_request' ), '', YITH_WCBK_TEMPLATE_PATH );
            }

            die();
        }

        /**
         * define Search Form Results constant
         */
        public function set_in_search_form_const() {
            if ( !defined( 'YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS' ) ) {
                define( 'YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS', true );
            }
        }

        /**
         * Get booking data as Availability and price
         *
         * @param array|string $request
         * @return array
         */
        public function get_booking_data( $request = '' ) {
            $this->_ajax_start( 'frontend' );

            $booking_data = false;
            $request      = is_array( $request ) ? $request : $_POST;
            $request      = apply_filters( 'yith_wcbk_ajax_booking_data_request', $request );

            if ( empty( $request[ 'product_id' ] ) || empty( $request[ 'from' ] ) || ( empty( $request[ 'duration' ] ) && empty( $request[ 'to' ] ) ) ) {

                $booking_data = array( 'error' => _x( 'Required POST variable not set', 'Error', 'yith-booking-for-woocommerce' ) );

            } else {

                $date_helper = YITH_WCBK_Date_Helper();
                $product_id  = $request[ 'product_id' ];
                /** @var WC_Product_Booking $product */
                $product = wc_get_product( $product_id );


                if ( $product ) {
                    $from = strtotime( $request[ 'from' ] );
                    if ( isset( $request[ 'to' ] ) ) {
                        $to = strtotime( $request[ 'to' ] );
                    } else {
                        $duration = absint( $request[ 'duration' ] ) * $product->get_duration();
                        if ( $product->is_full_day() ) {
                            $duration -= 1;
                        }
                        $to = $date_helper->get_time_sum( $from, $duration, $product->get_duration_unit() );
                    }


                    $is_available_args = YITH_WCBK_Cart::get_booking_data_from_request( $request );
                    $is_available_args = apply_filters( 'yith_wcbk_product_form_get_booking_data_available_args',
                                                        $is_available_args,
                                                        $product,
                                                        $request );

                    $is_available_args[ 'return' ] = 'array';

                    $availability = $product->is_available( $is_available_args );
                    $is_available = $availability[ 'available' ];

                    $bookable_args = array(
                        'product'               => $product,
                        'bookable'              => $is_available,
                        'from'                  => $from,
                        'to'                    => $to,
                        'non_available_reasons' => $availability[ 'non_available_reasons' ]
                    );
                    ob_start();
                    wc_get_template( 'single-product/add-to-cart/bookable.php', $bookable_args, '', YITH_WCBK_TEMPLATE_PATH );
                    $message = ob_get_clean();


                    if ( $is_available ) {
                        $show_totals = YITH_WCBK()->settings->show_totals();
                        $totals      = $product->calculate_totals( $request, $show_totals );
                        $price       = $product->calculate_price_from_totals( $totals );
                        $price       = apply_filters( 'yith_wcbk_booking_product_calculated_price', $price, $request, $product );
                        $price_html  = $product->get_calculated_price_html( $price );
                        if ( $show_totals ) {
                            ob_start();
                            wc_get_template( 'single-product/add-to-cart/booking-form/totals-list.php', compact( 'totals', 'price_html', 'product' ), '', YITH_WCBK_TEMPLATE_PATH );
                            $totals_html = ob_get_clean();
                        } else {
                            $totals_html = '';
                        }
                    } else {
                        $totals      = array();
                        $totals_html = '';
                        $price_html  = apply_filters( 'yith_wcbk_product_form_not_bookable_price_html', yith_wcbk_get_label( 'not-bookable' ), $request, $bookable_args );
                    }

                    $booking_data = array(
                        'is_available' => $is_available,
                        'totals'       => $totals,
                        'totals_html'  => $totals_html,
                        'price'        => $price_html,
                        'message'      => $message,
                    );

                    $booking_data = apply_filters( 'yith_wcbk_product_form_get_booking_data', $booking_data, $product, $bookable_args, $request );
                }
            }

            if ( !$booking_data ) {
                $booking_data = array( 'error' => _x( 'Product not found', 'Error', 'yith-booking-for-woocommerce' ) );
            }

            return $this->send_json( $booking_data );
        }


        /**
         * Get booking available times
         *
         * @param array|string $request
         * @return array|bool
         * @since 2.0.0
         */
        public function get_booking_available_times( $request = '' ) {
            $data    = false;
            $request = is_array( $request ) ? $request : $_POST;
            $request = apply_filters( 'yith_wcbk_ajax_booking_available_times_request', $request );

            if ( empty( $request[ 'product_id' ] ) || empty( $request[ 'from' ] ) || empty( $request[ 'duration' ] ) ) {

                $data = array( 'error' => _x( 'Required POST variable not set', 'Error', 'yith-booking-for-woocommerce' ) );

            } else {

                $product_id = $request[ 'product_id' ];
                /** @var WC_Product_Booking $product */
                $product = wc_get_product( $product_id );

                if ( $product && $product instanceof WC_Product_Booking ) {
                    $time_data      = $product->create_availability_time_array( $request[ 'from' ], $request[ 'duration' ] );
                    $time_data_html = '<option value="">' . __( 'Select Time', 'yith-booking-for-woocommerce' ) . '</option>';

                    $default_start_time = $product->get_default_start_time();
                    $first              = true;

                    foreach ( $time_data as $time ) {
                        $formatted_time = date( wc_time_format(), strtotime( $time ) );
                        $formatted_time = apply_filters( 'yith_wcbk_ajax_booking_available_times_formatted_time', $formatted_time, $time, $product );
                        $selected       = 'first-available' === $default_start_time ? selected( $first, true, false ) : '';
                        $first          = false;

                        $time_data_html .= "<option value='$time' $selected>$formatted_time</option>";
                    }

                    $data = array(
                        'time_data'      => $time_data,
                        'time_data_html' => $time_data_html,
                    );

                    if ( !$time_data ) {
                        $data[ 'time_data_html' ] = '<option value="">' . __( 'No time available', 'yith-booking-for-woocommerce' ) . '</option>';
                    }
                }

            }

            if ( false === $data ) {
                $data = array( 'error' => _x( 'Product not found', 'Error', 'yith-booking-for-woocommerce' ) );
            }

            return $this->send_json( $data );
        }

        public function get_product_not_available_dates( $request = '' ) {
            $data    = false;
            $request = is_array( $request ) ? $request : $_POST;
            $request = apply_filters( 'yith_wcbk_ajax_product_not_available_dates_request', $request );

            if ( empty( $request[ 'product_id' ] ) ) {

                $data = array( 'error' => _x( 'Required POST variable not set', 'Error', 'yith-booking-for-woocommerce' ) );

            } else {
                $product_id = $request[ 'product_id' ];

                /** @var WC_Product_Booking $product */
                $product = wc_get_product( $product_id );

                if ( $product && $product instanceof WC_Product_Booking ) {
                    $date_info_args = array();
                    if ( !empty( $request[ 'month_to_load' ] ) && !empty( $request[ 'year_to_load' ] ) ) {
                        $month_to_load  = $request[ 'month_to_load' ];
                        $year_to_load   = $request[ 'year_to_load' ];
                        $start          = "$year_to_load-$month_to_load-01";
                        $date_info_args = array( 'start' => $start );
                    }
                    $date_info = yith_wcbk_get_booking_form_date_info( $product, $date_info_args );

                    $data = array(
                        'not_available_dates' => $product->get_not_available_dates( $date_info[ 'current_year' ], $date_info[ 'current_month' ], $date_info[ 'next_year' ], $date_info[ 'next_month' ], 'day' ),
                        'year_to_load'        => $date_info[ 'next_year' ],
                        'month_to_load'       => $date_info[ 'next_month' ],
                    );
                }

            }

            if ( false === $data ) {
                $data = array( 'error' => _x( 'Product not found', 'Error', 'yith-booking-for-woocommerce' ) );
            }

            return $this->send_json( $data );
        }


        /**
         * send JSON or return if testing
         *
         * @param array $data
         * @return array|bool
         */
        public function send_json( $data ) {
            if ( $this->testing ) {
                return $data;
            } else {
                wp_send_json( $data );

                return false;
            }
        }


    }
}