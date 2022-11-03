<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Search_Form_Frontend' ) ) {
    /**
     * Class YITH_WCBK_Search_Form_Frontend
     * handle Booking Forms in frontend
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Search_Form_Frontend {
        /** @var YITH_WCBK_Search_Form_Frontend */
        private static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_WCBK_Search_Form_Frontend
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_WCBK_Search_Form_Frontend constructor.
         */
        private function __construct() {
            add_action( 'yith_wcbk_booking_search_form_print_field', array( $this, 'print_field' ), 10, 3 );

            add_action( 'pre_get_posts', array( $this, 'filter_search_results_in_shop' ) );
            add_filter( 'woocommerce_loop_product_link', array( $this, 'add_booking_data_in_search_result_links' ), 10, 2 );
            add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_booking_data_in_search_result_links' ), 10, 2 );
            add_filter( 'woocommerce_get_price_html', array( $this, 'show_price_based_on_search_params' ), 10, 2 );
        }

        /**
         * is searching?
         *
         * @return bool
         * @since 2.1.9
         */
        public function is_search() {
            return isset( $_REQUEST[ 'yith-wcbk-booking-search' ] ) && $_REQUEST[ 'yith-wcbk-booking-search' ] === 'search-bookings';
        }

        /**
         * show prices in Shop page based on search parameters
         *
         * @param $price_html
         * @param $product
         * @return string
         * @since 2.1.9
         */
        public function show_price_based_on_search_params( $price_html, $product ) {
            if ( $this->is_search() && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
                $request                                                                  = $_REQUEST;
                $request[ 'add-to-cart' ]                                                 = $product->get_id();
                $booking_data                                                             = YITH_WCBK_Cart::get_booking_data_from_request( $request );
                $booking_data[ YITH_WCBK_Search_Form_Helper::RESULT_KEY_IN_BOOKING_DATA ] = true;

                if ( isset( $booking_data[ 'person_types' ] ) ) {
                    if ( $product->has_people_types_enabled() ) {
                        $booking_data[ 'person_types' ] = yith_wcbk_booking_person_types_to_list( $booking_data[ 'person_types' ] );
                    } else {
                        unset( $booking_data[ 'person_types' ] );
                    }
                }

                $the_price  = $product->calculate_price( $booking_data );
                $price_html = wc_price( $the_price );
            }

            return $price_html;
        }

        /**
         * add booking data in product links when showing results in Shop Page
         *
         * @param string             $permalink
         * @param WC_Product_Booking $product
         * @return string
         * @since 2.0.6
         */
        public function add_booking_data_in_search_result_links( $permalink, $product ) {
            if ( $this->is_search() && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) && !$product->has_time() ) {
                $booking_request                 = $_REQUEST;
                $booking_request[ 'product_id' ] = $product->get_id();
                if ( isset( $booking_request[ 'services' ] ) ) {
                    $booking_request[ 'booking_services' ] = $booking_request[ 'services' ];
                }
                $booking_data         = YITH_WCBK_Cart::get_booking_data_from_request( $booking_request );
                $key                  = YITH_WCBK_Search_Form_Helper::RESULT_KEY_IN_BOOKING_DATA;
                $booking_data[ $key ] = true;
                $permalink            = $product->get_permalink_with_data( $booking_data );
            }
            return $permalink;
        }

        /**
         * filter search results in shop
         *
         * @param WP_Query $query
         */
        public function filter_search_results_in_shop( $query ) {
            if ( $query->is_main_query() && isset( $_REQUEST[ 'yith-wcbk-booking-search' ] ) && $_REQUEST[ 'yith-wcbk-booking-search' ] === 'search-bookings' ) {
                $product_ids = YITH_WCBK()->search_form_helper->search_booking_products( $_REQUEST );

                if ( !$product_ids )
                    $product_ids = array( 0 );

                $query->set( 'post__in', $product_ids );
            }
        }

        /**
         * @param string                $field_name
         * @param array                 $field_data
         * @param YITH_WCBK_Search_Form $search_form
         */
        public function print_field( $field_name, $field_data, $search_form ) {
            $template = $field_name;

            if ( !empty( $field_data[ 'type' ] ) ) {
                $template .= '-' . $field_data[ 'type' ];
            }

            $template .= '.php';

            $args = array(
                'field_name'  => $field_name,
                'field_data'  => $field_data,
                'search_form' => $search_form,
            );

            wc_get_template( 'booking/search-form/fields/' . $template, $args, '', YITH_WCBK_TEMPLATE_PATH );
        }
    }
}