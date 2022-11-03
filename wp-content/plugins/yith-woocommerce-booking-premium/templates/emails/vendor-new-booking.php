<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

$actions = $booking->has_status( 'pending-confirm' ) ? array( 'confirm', 'unconfirm' ) : array();

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( __( '%s has been created. It is now <strong>%s<strong>', 'yith-booking-for-woocommerce' ), $booking->get_name(), $booking->get_status_text() ); ?></p>

<?php do_action( 'yith_wcbk_email_booking_details', $booking, $sent_to_admin, $plain_text, $email ); ?>

<?php do_action( 'yith_wcbk_email_booking_actions', $booking, $sent_to_admin, $plain_text, $email, $actions ); ?>

<?php
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
?>
