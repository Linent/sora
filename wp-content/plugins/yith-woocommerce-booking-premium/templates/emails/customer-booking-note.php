<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( __( 'Hello, a note has just been added to your %s:', 'yith-booking-for-woocommerce' ), $booking->get_name() ); ?></p>

<blockquote><?php echo wpautop( wptexturize( $note ) ) ?></blockquote>

<p><?php _e( "For your reference, your booking details are shown below.", 'yith-booking-for-woocommerce' ); ?></p>

<?php do_action( 'yith_wcbk_email_booking_details', $booking, $sent_to_admin, $plain_text, $email ); ?>

<?php
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
?>
