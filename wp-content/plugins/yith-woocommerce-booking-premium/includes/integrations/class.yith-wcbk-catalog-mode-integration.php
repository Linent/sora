<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Catalog_Mode_Integration
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.1
 */
class YITH_WCBK_Catalog_Mode_Integration extends YITH_WCBK_Integration {
    /** @var \YITH_WCBK_Catalog_Mode_Integration */
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

        if ( $this->is_active() ) {
            add_filter( 'yith_wcbk_search_form_item_add_to_cart_allowed', array( $this, 'check_add_to_cart_in_search_form_results' ), 10, 3 );
            add_filter( 'ywctm_ajax_admin_check', array( $this, 'check_admin_for_booking_ajax_call' ), 999 );
            add_filter( 'yith_wcbk_booking_product_get_calculated_price_html', array( $this, 'filter_booking_product_calculated_price_html' ), 10, 3 );
        }
    }

    /**
     * Filter calculated price html for booking product through Catalog Mode
     * to hide prices everywhere (also in AJAX call)
     *
     * @param string             $price_html
     * @param string             $price
     * @param WC_Product_Booking $product
     * @since 2.1.4
     * @return string
     */
    public function filter_booking_product_calculated_price_html( $price_html, $price, $product ) {
        return YITH_WCTM()->show_product_price( $price_html, $product );
    }

    /**
     * @param bool               $add_to_cart_allowed
     * @param WC_Product_Booking $product
     * @param array              $booking_data
     * @return bool
     */
    public function check_add_to_cart_in_search_form_results( $add_to_cart_allowed, $product, $booking_data ) {
        $hide = YITH_WCTM()->check_add_to_cart_single( true, yit_get_base_product_id( $product ) );

        return !$hide;
    }

    /**
     * return False if it's a Booking AJAX call to hide the price correctly
     *
     * @param bool $is_admin
     * @return bool
     */
    public function check_admin_for_booking_ajax_call( $is_admin ) {
        if ( defined( 'YITH_WCBK_DOING_AJAX_FRONTEND' ) && YITH_WCBK_DOING_AJAX_FRONTEND ) {
            return false;
        }

        return $is_admin;
    }
}