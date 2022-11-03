<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

$actions = array( 'pay', 'view' );

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( __( '%s has been confirmed!', 'yith-booking-for-woocommerce', $booking->get_name() ), $booking->id ); ?></p>

<?php do_action( 'yith_wcbk_email_booking_details', $booking, $sent_to_admin, $plain_text, $email ); ?>

<?php do_action( 'yith_wcbk_email_booking_actions', $booking, $sent_to_admin, $plain_text, $email, $actions ); ?>

<?php
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
?>
