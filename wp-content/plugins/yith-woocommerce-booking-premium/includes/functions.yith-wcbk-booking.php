<?php
!defined( 'YITH_WCBK' ) && exit;

if ( !function_exists( 'yith_wcbk_get_booking_statuses' ) ) {

    /**
     * Return the list of status available
     *
     * @return array
     * @since 1.0.0
     */

    function yith_wcbk_get_booking_statuses( $include_accessory_statuses = false ) {
        $statuses = array(
            'unpaid'          => _x( 'Unpaid', 'Booking Status', 'yith-booking-for-woocommerce' ),
            'paid'            => _x( 'Paid', 'Booking Status', 'yith-booking-for-woocommerce' ),
            'completed'       => _x( 'Completed', 'Booking Status', 'yith-booking-for-woocommerce' ),
            'cancelled'       => _x( 'Cancelled', 'Booking Status', 'yith-booking-for-woocommerce' ),
            'pending-confirm' => _x( 'Pending Confirmation', 'Booking Status', 'yith-booking-for-woocommerce' ),
            'confirmed'       => _x( 'Confirmed', 'Booking Status', 'yith-booking-for-woocommerce' ),
            'unconfirmed'     => _x( 'Rejected', 'Booking Status', 'yith-booking-for-woocommerce' ),

        );

        if ( $include_accessory_statuses ) {
            $statuses[ 'cancelled_by_user' ] = _x( 'Cancelled by customer', 'Booking Status', 'yith-booking-for-woocommerce' );
        }

        return apply_filters( 'yith_wcbk_booking_statuses', $statuses );
    }
}

if ( !function_exists( 'yith_wcbk_is_a_booking_status' ) ) {

    /**
     * check if booking status is valid
     *
     * @param string $status
     * @return array
     * @since 1.0.0
     */

    function yith_wcbk_is_a_booking_status( $status ) {
        $booking_statuses = yith_wcbk_get_booking_statuses();

        return isset( $booking_statuses[ $status ] );
    }
}

if ( !function_exists( 'yith_wcbk_get_booking_status_name' ) ) {

    /**
     * Get the booking status name
     *
     * @param string $status
     * @return string
     * @since 1.0.0
     */

    function yith_wcbk_get_booking_status_name( $status ) {
        return strtr( $status, yith_wcbk_get_booking_statuses() );
    }
}

if ( !function_exists( 'yith_get_booking' ) ) {

    /**
     * Get the booking object.
     *
     * @param  mixed $the_booking
     * @uses   WP_Post
     * @uses   YITH_WCBK_Booking
     * @return YITH_WCBK_Booking|bool false on failure
     */
    function yith_get_booking( $the_booking ) {
        $_booking = false;
        if ( false === $the_booking ) {
            $the_booking = isset( $GLOBALS[ 'post' ] ) ? $GLOBALS[ 'post' ] : false;
            if ( $the_booking && isset( $the_booking->ID ) ) {
                $_booking = new YITH_WCBK_Booking( $the_booking->ID );
            }
        } elseif ( is_numeric( $the_booking ) ) {
            $_booking = new YITH_WCBK_Booking( $the_booking );
        } elseif ( $the_booking instanceof YITH_WCBK_Booking ) {
            $_booking = $the_booking;
        } elseif ( !( $the_booking instanceof WP_Post ) ) {
            $_booking = false;
        }

        if ( $_booking instanceof YITH_WCBK_Booking ) {
            if ( !$_booking->is_valid() ) {
                $_booking = false;
            }
        } else {
            $_booking = false;
        }

        return apply_filters( 'yith_wcbk_booking_object', $_booking );
    }
}

if ( !function_exists( 'yith_wcbk_get_booked_statuses' ) ) {
    /**
     * return an array of statuses, in which the booking is booked
     */
    function yith_wcbk_get_booked_statuses() {
        $statuses = array(
            'bk-unpaid',
            'bk-paid',
            'bk-completed',
            'bk-confirmed',
        );

        return apply_filters( 'yith_wcbk_get_booked_statuses', $statuses );
    }
}