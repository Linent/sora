<?php
/**
 * Time Field in booking form
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/time.php
 *
 * @var WC_Product_Booking $product
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly
?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-dates yith-wcbk-form-section-dates-date-time">

    <label for="yith-wcbk-booking-start-date-time-<?php echo $product->get_id() ?>" class='yith-wcbk-booking-form__label'><?php echo yith_wcbk_get_label( 'time' ) ?></label>

    <?php
    yith_wcbk_print_field( array(
                               'type'    => 'select-alt',
                               'id'      => 'yith-wcbk-booking-start-date-time-' . $product->get_id(),
                               'name'    => 'from-time',
                               'class'   => 'yith-wcbk-booking-date yith-wcbk-booking-start-date-time',
                               'options' => array( '' => __( 'Select Time', 'yith-booking-for-woocommerce' ) ),
                           ) );
    ?>
</div>