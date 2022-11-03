<?php
/**
 * Booking form start
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 *
 * @var WC_Product_Booking $product
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="yith-wcbk-booking-form" data-product-id="<?php echo $product->get_id() ?>" data-booking_data="<?php echo htmlspecialchars( json_encode( $product->get_booking_data() ) ) ?>">