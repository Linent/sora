<?php
/**
 * Booking form meta
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 *
 * @var WC_Product_Booking $product
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

$checkin  = $product->get_check_in();
$checkout = $product->get_check_out();
?>
<?php if ( !!$checkin || !!$checkout ): ?>

    <table class="yith-booking-meta">
        <tr>

            <?php if ( !!$checkin ): ?>
                <td class="yith-booking-checkin"><?php echo sprintf( __( '%s: %s', 'yith-booking-for-woocommerce' ), yith_wcbk_get_label( 'check-in' ), $checkin ) ?></td>
            <?php endif; ?>

            <?php if ( !!$checkout ): ?>
                <td class="yith-booking-checkout"><?php echo sprintf( __( '%s: %s', 'yith-booking-for-woocommerce' ), yith_wcbk_get_label( 'check-out' ), $checkout ) ?></td>
            <?php endif; ?>
        </tr>
    </table>

<?php endif; ?>
