<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Request_A_Quote_Integration
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.1
 */
class YITH_WCBK_Request_A_Quote_Integration extends YITH_WCBK_Integration {
    /** @var YITH_WCBK_Request_A_Quote_Integration */
    protected static $_instance;

    /**
     * Constructor
     *
     * @param bool $plugin_active
     * @param bool $integration_active
     * @access protected
     */
    protected function __construct( $plugin_active, $integration_active ) {
        parent::__construct( $plugin_active, $integration_active );

        add_filter( 'ywraq_add_item', array( $this, 'add_booking_to_quote' ), 10, 2 );

        add_filter( 'yith_ywraq_product_price', array( $this, 'price_in_raq_table_total' ), 10, 3 );
        add_filter( 'yith_ywraq_product_price_html', array( $this, 'price_in_raq_table' ), 15, 3 );
        add_action( 'ywraq_quote_adjust_price', array( $this, 'adjust_price_in_raq_table' ), 10, 2 );

        add_filter( 'ywraq_request_quote_view_item_data', array( $this, 'add_booking_info_in_table' ), 10, 4 );
        add_filter( 'ywraq_quantity_max_value', array( $this, 'set_booking_max_quantity' ), 10, 2 );


        add_action( 'ywraq_from_cart_to_order_item', array( $this, 'add_order_item_meta' ), 10, 3 );
        add_filter( 'ywraq_order_cart_item_data', array( $this, 'order_cart_item_data' ), 10, 3 );


        add_action( 'ywraq_request_quote_email_view_item_after_title', array( $this, 'add_booking_data_in_raq_emails' ) );

        // integration with Multi Vendor
        add_filter( 'yith_wcbk_order_check_order_for_booking', array( $this, 'not_check_for_bookings_in_raq_orders' ), 10, 3 );
        add_action( 'wp_enqueue_scripts', array( $this, 'add_custom_scripts'), 99);

    }

    /**
     * add booking data in RAQ emails
     *
     * @param array $item
     * @use   YITH_WCBK_Cart::get_booking_data_from_request
     * @use   YITH_WCBK_Cart::woocommerce_get_item_data
     * @since 1.0.16
     */
    public function add_booking_data_in_raq_emails( $item ) {
        if ( is_array( $item ) && !empty( $item[ 'yith_booking_request' ] ) && !empty( $item[ 'product_id' ] ) ) {
            $request        = $item[ 'yith_booking_request' ];
            $booking_data   = YITH_WCBK_Cart::get_booking_data_from_request( $request );
            $new_line       = apply_filters( 'yith_wcbk_raq_booking_data_in_raq_emails_item_new_line', '<br/>' );
            $format         = apply_filters( 'yith_wcbk_raq_booking_data_in_raq_emails_item_format', '<strong>%1$s</strong>: %2$s%3$s' );
            $fake_cart_item = array(
                'product_id'        => $item[ 'product_id' ],
                'yith_booking_data' => $booking_data
            );

            $data = YITH_WCBK()->frontend->cart->woocommerce_get_item_data( array(), $fake_cart_item );

            echo !!$data ? $new_line : '';

            foreach ( $data as $data_key => $single_data ) {
                if ( isset( $single_data[ 'key' ] ) && isset( $single_data[ 'display' ] ) ) {
                    $key   = esc_html( $single_data[ 'key' ] );
                    $value = wp_kses_post( $single_data[ 'display' ] );
                    echo sprintf( $format, $key, $value, $new_line );
                }
            }
        }
    }

    /**
     * Do not create Bookings if the order is a Quote
     * fixes issue in combination with Multi Vendor
     *
     * @param       $check
     * @param       $order_id
     * @param array $posted
     * @return bool
     * @since 1.0.11
     */
    public function not_check_for_bookings_in_raq_orders( $check, $order_id, $posted = array() ) {
        $order = wc_get_order( $order_id );
        if ( $order && $order->has_status( 'ywraq-new' ) )
            $check = false;

        return $check;
    }

    /**
     * @param array $raq
     * @param array $product_raq
     * @return mixed
     */
    public function add_booking_to_quote( $raq, $product_raq ) {
        $product_id = isset( $raq[ 'product_id' ] ) ? $raq[ 'product_id' ] : false;

        if ( $product_id ) {
            $product = wc_get_product( $product_id );
            if ( $product && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
                $booking_data_keys = array_keys( YITH_WCBK_Cart::get_default_booking_data() );
                foreach ( $product_raq as $key => $value ) {
                    if ( in_array( $key, $booking_data_keys ) ) {
                        if ( in_array( $key, array( 'from', 'to' ) ) ) {
                            $value = urldecode( $value );
                        }
                        $raq[ 'yith_booking_request' ][ $key ] = $value;
                    }
                }
                $raq[ 'yith_booking_request' ][ 'add-to-cart' ] = $raq[ 'product_id' ];
            }
        }

        return $raq;
    }

    /**
     * @param string     $price
     * @param WC_Product $product
     * @param array      $raq
     * @return string
     */
    public function price_in_raq_table_total( $price, $product, $raq ) {
        if ( $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
            /**
             * @var WC_Product_Booking $product
             */
            $booking_data = $this->get_booking_data_from_raq( $product, $raq );

            $price = $product->calculate_price( $booking_data );
        }
        return $price;
    }

    /**
     * adjust price in raq table
     *
     * @param WC_Product $product
     * @param array      $raq
     */
    public function adjust_price_in_raq_table( $raq, $product ) {
        if ( $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
            /** @var WC_Product_Booking $product */
            $booking_data = $this->get_booking_data_from_raq( $product, $raq );

            $price = $product->calculate_price( $booking_data );
            $product->set_price( $price );
        }
    }

    /**
     * @param string     $price
     * @param WC_Product $product
     * @param array      $raq
     * @return string
     */
    public function price_in_raq_table( $price, $product, $raq ) {
        if ( $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
            /**
             * @var WC_Product_Booking $product
             */
            $booking_data = $this->get_booking_data_from_raq( $product, $raq );

            $price = wc_price( $product->calculate_price( $booking_data ) );
        }

        return $price;
    }

    /**
     * Get the booking data from raq array
     *
     * @param WC_Product_Booking $product
     * @param array              $raq
     * @param bool               $parse_person_types
     * @return array
     */
    public function get_booking_data_from_raq( $product, $raq, $parse_person_types = true ) {
        $booking_data = false;
        if ( isset( $raq[ 'yith_booking_request' ] ) ) {
            $booking_data = YITH_WCBK_Cart::get_booking_data_from_request( $raq[ 'yith_booking_request' ] );

            if ( $parse_person_types && $product->has_people_types_enabled() && isset( $booking_data[ 'person_types' ] ) ) {
                $booking_data[ 'person_types' ] = yith_wcbk_booking_person_types_to_list( $booking_data[ 'person_types' ] );
            }
        }

        return $booking_data;
    }

    /**
     * @param            $item_data
     * @param            $raq
     * @param WC_Product $_product
     * @param            $show_price
     * @return array
     */
    public function add_booking_info_in_table( $item_data, $raq, $_product, $show_price ) {
        if ( $_product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
            /** @var WC_Product_Booking $_product */
            $cart_item                 = array(
                'product_id'        => $_product->get_id(),
                'yith_booking_data' => $this->get_booking_data_from_raq( $_product, $raq, false )
            );
            $booking_item_data         = YITH_WCBK()->frontend->cart->woocommerce_get_item_data( $item_data, $cart_item );
            $booking_item_data_for_raq = array();
            foreach ( $booking_item_data as $booking_item_data_single ) {
                if ( isset( $booking_item_data_single[ 'key' ] ) && isset( $booking_item_data_single[ 'display' ] ) ) {
                    $singe_for_raq               = array(
                        'key'   => $booking_item_data_single[ 'key' ],
                        'value' => $booking_item_data_single[ 'display' ],
                    );
                    $booking_item_data_for_raq[] = $singe_for_raq;
                }
            }
            $item_data = array_merge( $item_data, $booking_item_data_for_raq );
        }

        return $item_data;
    }

    /**
     * @param int        $quantity
     * @param WC_Product $product
     * @return int
     */
    public function set_booking_max_quantity( $quantity, $product ) {
        if ( $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ) {
            $quantity = 1;
        }

        return $quantity;
    }


    /**
     * @param array    $cart_item_data
     * @param array    $item
     * @param WC_Order $order
     * @return mixed
     */
    public function order_cart_item_data( $cart_item_data, $item, $order ) {
        //vd($item);
        $to_copy = array(
            'yith_booking_request'
        );

        foreach ( $to_copy as $c ) {
            if ( isset( $item[ $c ] ) ) {
                $cart_item_data[ $c ] = maybe_unserialize( $item[ $c ] );
            }
        }

        return $cart_item_data;
    }

    /**
     * @param array  $values
     * @param string $cart_item_key
     * @param int    $item_id
     */
    public function add_order_item_meta( $values, $cart_item_key, $item_id ) {
        if ( isset( $values[ 'yith_booking_data' ] ) && is_array( $values[ 'yith_booking_data' ] ) ) {
            $booking_data = $values[ 'yith_booking_data' ];
            $booking_data = YITH_WCBK()->orders->parse_booking_data( $booking_data );
            $product_id   = isset( $values[ 'product_id' ] ) ? $values[ 'product_id' ] : 0;
            $details      = YITH_WCBK()->orders->get_booking_order_item_details( $booking_data, $product_id );
            foreach ( $details as $detail ) {
                wc_add_order_item_meta( $item_id, $detail[ 'key' ], $detail[ 'value' ], true );
            }
        }
        if ( isset( $values[ 'yith_booking_request' ] ) ) {
            wc_add_order_item_meta( $item_id, 'yith_booking_request', $values[ 'yith_booking_request' ] );
        }
    }

    public function add_custom_scripts(){

    	if( is_product() ){

    		global $post;
    		$product = wc_get_product( $post->ID );
		    $hide_price = get_option( 'ywraq_hide_price' ) === 'yes';
    		if( $hide_price && 'booking' == $product->get_type() ){

    			$css = '.product-type-booking .price{
    			        display:none;
    			        }';

    			wp_add_inline_style('yith-wcbk-frontend-style', $css);
		    }
	    }
    }

}
