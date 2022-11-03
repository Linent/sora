<?php
/**
 * YITH Booking Background Process Functions
 *
 * functions for background processes
 *
 * @author Leanza Francesco <leanzafrancesco@gmail.com>
 */

!defined( 'ABSPATH' ) && exit;

if ( !function_exists( 'yith_wcbk_bg_process_booking_product_regenerate_product_data' ) ) {
    /**
     * regenerate product data
     *
     * @param int   $product_id
     * @param array $data
     */
    function yith_wcbk_bg_process_booking_product_regenerate_product_data( $product_id, $data = array() ) {
        yith_wcbk_maybe_debug( sprintf( 'Regenerate product data for product #%s', $product_id ), YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS );
        $product = wc_get_product( $product_id );
        if ( $product && $product->is_type( 'booking' ) ) {
            /** @var WC_Product_Booking $product */
            $product->regenerate_data( $data );
        }
    }
}