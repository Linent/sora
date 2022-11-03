<?php
/**
 * Booking form dates
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 *
 * @var WC_Product_Booking $product
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="yith-wcbk-form-section-dates-wrapper">
    <?php

    do_action( 'yith_wcbk_booking_form_dates_duration', $product );

    do_action( 'yith_wcbk_booking_form_dates_date_fields', $product );

    ?>
</div>
