<?php
/**
 * Booking actions shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-booking-actions.php.
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'yith_wcbk_email_before_booking_actions', $booking, $sent_to_admin, $plain_text, $email, $actions ); ?>
<?php if ( $actions ) : ?>
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%"
           style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
        <tr>
            <td align="center" valign="top">
                <?php foreach ( $actions as $key => $action ) :
                    $background = isset( $action[ 'style' ][ 'background' ] ) ? $action[ 'style' ][ 'background' ] : '#e5e5e5 !important';
                    $color = isset( $action[ 'style' ][ 'color' ] ) ? $action[ 'style' ][ 'color' ] : '#333 !important';
                    $style = "padding:6px 28px !important;font-size: 12px !important; background: $background; color: $color; text-decoration: none!important; text-transform: uppercase!important; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif !important;font-weight: 800 !important; border-radius: 3px !important; display: inline-block !important; margin:5px !important;";
                    ?>
                    <a href="<?php echo esc_url( $action[ 'url' ] ) ?>"><span style="<?php echo $style ?>"><?php echo esc_html( $action[ 'name' ] ); ?></span></a>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>

<?php endif ?>

<?php do_action( 'yith_wcbk_email_after_booking_actions', $booking, $sent_to_admin, $plain_text, $email, $actions ); ?>
