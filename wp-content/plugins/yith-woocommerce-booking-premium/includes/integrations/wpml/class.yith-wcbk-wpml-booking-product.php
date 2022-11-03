<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Wpml_Booking_Product
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.3
 */
class YITH_WCBK_Wpml_Booking_Product {
    /** @var YITH_WCBK_Wpml_Booking_Product */
    private static $_instance;

    /** @var YITH_WCBK_Wpml_Integration */
    public $wpml_integration;

    /**
     * Singleton implementation
     *
     * @param YITH_WCBK_Wpml_Integration $wpml_integration
     * @return YITH_WCBK_Wpml_Booking_Product
     */
    public static function get_instance( $wpml_integration ) {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new static( $wpml_integration );
    }

    /**
     * Constructor
     *
     * @access private
     * @param YITH_WCBK_Wpml_Integration $wpml_integration
     */
    private function __construct( $wpml_integration ) {
        $this->wpml_integration = $wpml_integration;

        // get the parent id of the booking product to associate it to the Booking object
        add_filter( 'yith_wcbk_booking_product_id_to_translate', array( 'YITH_WCBK_Wpml_Integration', 'get_parent_id' ) );

        // get the parent id of the booking product for cache data
        add_filter( 'yith_wcbk_cache_get_object_data_product_id', array( 'YITH_WCBK_Wpml_Integration', 'get_parent_id' ) );
    }
}