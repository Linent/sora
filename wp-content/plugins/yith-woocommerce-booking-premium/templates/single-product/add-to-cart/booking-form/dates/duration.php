<?php
/**
 * Duration Field in booking form
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/duration.php
 *
 * @var WC_Product_Booking $product
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

$default_duration = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'duration' );


$duration            = $product->get_duration();
$duration_unit       = $product->get_duration_unit();
$is_fixed_blocks     = $product->is_type_fixed_blocks();
$min                 = $product->get_minimum_duration();
$max                 = $product->get_maximum_duration();
$show_duration_field = !$is_fixed_blocks && $min !== $max;
$duration_number     = $show_duration_field ? $duration : ( $duration * $min );
$duration_label      = yith_wcbk_get_product_duration_label( $duration_number, $duration_unit, !$show_duration_field );
$duration_label      = apply_filters( 'yith_wcbk_booking_form_dates_duration_label_html', $duration_label, $product );

$custom_attributes = "step='1' min='{$min}'";
$custom_attributes .= $max > 0 ? " max='{$max}'" : '';
$custom_attributes .= ' pattern="[0-9]*" inputmode="numeric"';

$id   = 'yith-wcbk-booking-duration-' . $product->get_id();
$type = $show_duration_field ? 'number' : 'hidden';

$duration_type_class = 'yith-wcbk-form-section-duration--type-' . sanitize_key( $product->get_duration_type() );

$duration_value = max( $min, $default_duration );
$duration_value = $max > 0 ? min($max, $duration_value) : $duration_value;
?>

<div class="yith-wcbk-form-section yith-wcbk-form-section-duration <?php echo $duration_type_class ?>">
    <label for="<?php echo $id ?>" class='yith-wcbk-booking-form__label'><?php echo yith_wcbk_get_label( 'duration' ) ?></label>
    <?php
    yith_wcbk_print_field( array(
                               'type'              => $type,
                               'id'                => 'yith-wcbk-booking-duration',
                               'name'              => 'duration',
                               'custom_attributes' => $custom_attributes,
                               'value'             => $duration_value,
                               'class'             => 'yith-wcbk-booking-duration yith-wcbk-number-minifield',
                           ) );
    ?>
    <?php echo $duration_label ?>
    <?php do_action('yith_wcbk_booking_form_after_label_duration',$duration,$duration_unit,$duration_number,$duration_label); ?>
</div>