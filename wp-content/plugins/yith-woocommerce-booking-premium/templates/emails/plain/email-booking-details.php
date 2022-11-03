<?php
/**
 * @var YITH_WCBK_Booking $booking
 */
!defined( 'ABSPATH' ) && exit;

do_action( 'yith_wcbk_email_before_booking_table', $booking, $sent_to_admin, $plain_text, $email );

echo $booking->get_name() . "\n\n";

echo __( 'Status', 'yith-booking-for-woocommerce' ) . ': ' . $booking->get_status_text() . "\n";

if ( $product = wc_get_product( $booking->product_id ) ) {
    echo __( 'Product', 'yith-booking-for-woocommerce' ) . ': ' . $product->get_title() . "\n";
}

$booking_order_id = apply_filters( 'yith_wcbk_email_booking_details_order_id', $booking->order_id, $booking, $sent_to_admin, $plain_text, $email );
if ( $booking_order_id && $order = wc_get_order( $booking_order_id ) ) {
    echo __( 'Order', 'yith-booking-for-woocommerce' ) . ': ' . _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() . "\n";
}

echo yith_wcbk_get_label( 'duration' ) . ': ' . $booking->get_duration_html() . "\n";
echo yith_wcbk_get_label( 'from' ) . ': ' . $booking->get_formatted_date( 'from' ) . "\n";
echo yith_wcbk_get_label( 'to' ) . ': ' . $booking->get_formatted_date( 'to' ) . "\n";

if ( !$booking->has_person_types() ) {
    echo yith_wcbk_get_label( 'people' ) . ': ' . $booking->persons . "\n";
}

if ( $services = $booking->get_service_names( $sent_to_admin ) ) {
    echo yith_wcbk_get_label( 'services' ) . ': ' . implode( ', ', $services ) . "\n";

}

if ( $booking->has_person_types() ) {
    echo "\n";
    echo yith_wcbk_get_label( 'people' ) . "\n";
    foreach ( $booking->person_types as $person_type ) {
        if ( !$person_type[ 'number' ] )
            continue;
        $person_type_id     = absint( $person_type[ 'id' ] );
        $person_type_title  = YITH_WCBK()->person_type_helper->get_person_type_title( $person_type_id );
        $person_type_title  = !!$person_type_title ? $person_type_title : $person_type[ 'title' ];
        $person_type_number = absint( $person_type[ 'number' ] );

        echo ' - ' . $person_type_title . ': ' . $person_type_number . "\n";
    }
}

do_action( 'yith_wcbk_email_after_booking_table', $booking, $sent_to_admin, $plain_text, $email );