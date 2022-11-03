<?php
/**
 * Booking Totals
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 * @var array              $totals     array of totals
 * @var string             $price_html the total price of the booking product
 * @var WC_Product_Booking $product    the booking product
 */
!defined( 'ABSPATH' ) && exit;
?>
<div class="yith-wcbk-booking-form-totals__list">
    <?php foreach ( $totals as $key => $total ):
        $label = $total[ 'label' ];
        $value = $total[ 'value' ];
        $is_discount = $value < 0;
        $price = isset( $total[ 'display' ] ) ? $total[ 'display' ] : ( yith_wcbk_get_formatted_price_to_display( $product, $total[ 'value' ] ) );
        $extra_classes = "yith-wcbk-booking-form-total__" . esc_attr( $key );
        $extra_classes .= $is_discount ? " yith-wcbk-booking-form-total--discount" : '';
        ?>
        <div class="yith-wcbk-booking-form-total <?php echo $extra_classes ?>">
            <div class="yith-wcbk-booking-form-total__label"><?php echo $label ?></div>
            <div class="yith-wcbk-booking-form-total__value"><?php echo $price ?></div>
        </div>
    <?php endforeach; ?>

    <div class="yith-wcbk-booking-form-total  yith-wcbk-booking-form-total--total-price">
        <div class="yith-wcbk-booking-form-total__label"><?php _e( 'Total', 'yith-booking-for-woocommerce' ) ?></div>
        <div class="yith-wcbk-booking-form-total__value"><?php echo $price_html ?></div>
    </div>
</div>