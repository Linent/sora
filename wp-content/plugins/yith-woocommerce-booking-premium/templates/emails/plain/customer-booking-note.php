<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

echo "= " . $email_heading . " =\n\n";


echo sprintf( __( 'Hello, a note has just been added to your %s:', 'yith-booking-for-woocommerce' ), $booking->get_name() )  . "\n\n";

echo "----------\n\n";

echo wptexturize( $note ) . "\n\n";

echo "----------\n\n";

echo __( "For your reference, your booking details are shown below.", 'yith-booking-for-woocommerce' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

do_action( 'yith_wcbk_email_booking_details', $booking, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );