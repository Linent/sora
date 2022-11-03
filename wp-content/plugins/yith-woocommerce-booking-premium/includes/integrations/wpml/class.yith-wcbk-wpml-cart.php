<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Wpml_Cart
 *
 * @author Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.11
 */
class YITH_WCBK_Wpml_Cart {
    /** @var YITH_WCBK_Wpml_Cart */
    private static $_instance;

    /** @var YITH_WCBK_Wpml_Integration */
    public $wpml_integration;

    /**
     * Singleton Implementation
     *
     * @param YITH_WCBK_Wpml_Integration $wpml_integration
     *
     * @return YITH_WCBK_Wpml_Cart
     */
    public static function get_instance( $wpml_integration ) {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new static( $wpml_integration );
    }

    /**
     * Constructor
     *
     * @access private
     *
     * @param YITH_WCBK_Wpml_Integration $wpml_integration
     */
    private function __construct( $wpml_integration ) {
        $this->wpml_integration = $wpml_integration;

        add_filter( 'wcml_add_to_cart_sold_individually', array( $this, 'prevent_sold_individually_error_in_cart' ), 10, 4 );
    }

    /**
     * @param bool  $value
     * @param array $cart_item_data
     * @param int   $product_id
     * @param int   $quantity
     *
     * @return bool
     */
    public function prevent_sold_individually_error_in_cart( $value, $cart_item_data = array(), $product_id, $quantity = 1 ) {
        if ( yith_wcbk_is_booking_product( $product_id ) )
            return false;

        return $value;
    }

}