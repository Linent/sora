<?php
/**
 * Booking form persons - persons
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 *
 * @var WC_Product_Booking $product
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

$default_persons   = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'persons' );
$min               = $product->get_minimum_number_of_people();
$max               = $product->get_maximum_number_of_people();
$custom_attributes = "step='1' min='{$min}'";
$custom_attributes .= $max > 0 ? " max='{$max}'" : '';

?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-persons">
    <label class='yith-wcbk-booking-form__label'><?php echo yith_wcbk_get_label( 'people' ) ?></label>
    <?php
    yith_wcbk_print_field( array(
                               'type'              => 'number',
                               'id'                => 'yith-wcbk-booking-persons',
                               'name'              => 'persons',
                               'custom_attributes' => $custom_attributes,
                               'value'             => max( $min, $default_persons ),
                               'class'             => 'yith-wcbk-booking-persons yith-wcbk-number-minifield',
                           ) );
    ?>
</div>

