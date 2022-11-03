<?php
/**
 * View booking
 *
 * Shows booking on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-booking.php.
 *
 * @var YITH_WCBK_Booking $booking
 * @var int               $booking_id
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

?>

<p><?php
    printf( __( '%1$s was placed on %2$s and is currently %3$s.', 'yith-booking-for-woocommerce' ),
            '<mark class="booking-id">' . $booking->get_name() . '</mark>',
            '<mark class="booking-date">' . date_i18n( get_option( 'date_format' ), strtotime( $booking->post->post_date ) ) . '</mark>',
            '<mark class="booking-status">' . $booking->get_status_text() . '</mark>'
    );
    ?></p>

<?php do_action( 'yith_wcbk_view_booking', $booking_id ); ?>
