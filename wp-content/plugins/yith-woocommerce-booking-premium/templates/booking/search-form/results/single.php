<?php
/**
 * Booking Search Form Single Result Template
 *
 * shows the single result product
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/results/single.php.
 *
 * @var WC_Product_Booking $product
 * @var array              $booking_data
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if ( empty( $product ) || !$product->is_visible() ) {
    return;
}
?>
<li <?php post_class() ?> >
    <?php

    /**
     * yith_wcbk_before_search_form_item hook.
     *
     */
    do_action( 'yith_wcbk_before_search_form_item', $booking_data ); ?>

    <?php ob_start(); ?>
    <div class="yith-wcbk-search-form-result-product-thumb-wrapper">

        <?php
        /**
         * yith_wcbk_search_form_item_thumbnails hook.
         *
         * @hooked woocommerce_show_product_loop_sale_flash - 10
         * @hooked yith_wcbk_search_form_item_thumbnails - 10
         */

        do_action( 'yith_wcbk_search_form_item_thumbnails', $booking_data ); ?>

    </div>
    <?php echo apply_filters( 'yith_wcbk_search_form_result_product_thumb_wrapper', ob_get_clean(), $product->get_id() ); ?>

    <?php
    /**
     * yith_wcbk_before_search_form_item_title hook.
     *
     * @hooked yith_wcbk_search_form_item_link_open - 10
     */
    do_action( 'yith_wcbk_before_search_form_item_title', $booking_data ); ?>

    <div class="yith-wcbk-search-form-result-product-meta-wrapper">
        <?php
        /**
         * yith_wcbk_search_form_item_title hook.
         *
         * @hooked yith_wcbk_search_form_item_title - 10
         */
        do_action( 'yith_wcbk_search_form_item_title', $booking_data ); ?>
    </div>

    <?php
    /**
     * yith_wcbk_after_search_form_item_title hook.
     *
     * @hooked woocommerce_template_loop_product_link_close - 5
     */
    do_action( 'yith_wcbk_after_search_form_item_title', $booking_data ); ?>

    <div class="yith-wcbk-search-form-result-product-price">
        <?php
        /**
         * yith_wcbk_search_form_item_price hook.
         *
         * @hooked woocommerce_template_loop_price - 10
         */
        do_action( 'yith_wcbk_search_form_item_price', $booking_data ); ?>
    </div>

    <div class="yith-wcbk-search-form-result-product-add-to-cart">

        <?php
        /**
         * yith_wcbk_search_form_item_add_to_cart hook.
         *
         * @hooked yith_wcbk_search_form_item_add_to_cart - 10
         */
        do_action( 'yith_wcbk_search_form_item_add_to_cart', $booking_data ); ?>

    </div>

    <?php do_action( 'yith_wcbk_after_search_form_item', $booking_data ); ?>
</li>
