<?php
/**
 * Booking Actions Template
 *
 * Shows booking actions
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/booking-actions.php.
 *
 * @var YITH_WCBK_Booking $booking
 * @var bool              $show_view_action
 */

!defined( 'YITH_WCBK' ) && exit;
$show_view_action = isset( $show_view_action ) ? !!$show_view_action : false;
?>

<div class="yith-wcbk-booking-actions">
    <?php
    $actions = array(
        'pay'    => array(
            'url'  => $booking->get_confirmed_booking_payment_url(),
            'name' => __( 'Pay', 'yith-booking-for-woocommerce' )
        ),
        'view'   => array(
            'url'  => $booking->get_view_booking_url(),
            'name' => __( 'View', 'yith-booking-for-woocommerce' )
        ),
        'cancel' => array(
            'url'   => $booking->get_cancel_booking_url(),
            'name'  => __( 'Cancel', 'yith-booking-for-woocommerce' ),
            'class' => 'yith-wcbk-confirm-button',
            'data'  => array(
                'confirm-text'  => __( 'Confirm', 'yith-booking-for-woocommerce' ),
                'confirm-class' => 'yith-wcbk-confirm-cancel-button'
            )
        )
    );

    if ( !$show_view_action ) {
        unset( $actions[ 'view' ] );
    }

    if ( !$booking->has_status( 'confirmed' ) ) {
        unset( $actions[ 'pay' ] );
    }

    if ( !$booking->can_be( 'cancelled_by_user' ) ) {
        unset( $actions[ 'cancel' ] );
    }

    if ( $actions = apply_filters( 'yith_wcbk_bookings_actions', $actions, $booking ) ) {
        foreach ( $actions as $key => $action ) {
            $class = isset( $action[ 'class' ] ) ? sanitize_html_class( $action[ 'class' ] ) : '';
            $data  = '';
            if ( isset( $action[ 'data' ] ) && is_array( $action[ 'data' ] ) ) {
                foreach ( $action[ 'data' ] as $data_id => $data_value ) {
                    $data .= 'data-' . $data_id . '="' . $data_value . '" ';
                }
            }
            echo '<a href="' . esc_url( $action[ 'url' ] ) . '" class="button ' . sanitize_html_class( $key ) . ' '. $class .'" ' . $data . '>' . esc_html( $action[ 'name' ] ) . '</a>';
        }
    }
    ?>
</div>
