<?php
/**
 * Booking form price
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 *
 * @var WC_Product_Booking $product
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<p class="price"><?php $product->get_price_html() ?></p>
