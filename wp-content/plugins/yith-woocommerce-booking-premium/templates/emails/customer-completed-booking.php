<?php
/**
 * @var string $custom_message
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php echo $custom_message ?></p>

<?php do_action( 'yith_wcbk_email_booking_details', $booking, $sent_to_admin, $plain_text, $email ); ?>

<?php
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
?>
