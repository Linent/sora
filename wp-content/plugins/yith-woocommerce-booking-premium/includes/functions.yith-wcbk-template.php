<?php
/**
 * Template Functions
 *
 * @author  Yithemes
 * @package YITH Booking and Appointment for WooCommerce Premium
 * @version 1.0.0
 */

!defined( 'YITH_WCBK' ) && exit;

/** -------------------------
 *          HOOKS
 * --------------------------
 */

/**
 * Booking form
 */
add_action( 'yith_wcbk_booking_form_meta', 'yith_wcbk_booking_form_meta', 10, 1 );
add_action( 'yith_wcbk_booking_form_start', 'yith_wcbk_booking_form_start', 10, 1 );
add_action( 'yith_wcbk_booking_form_content', 'yith_wcbk_booking_form_dates', 10, 1 );
add_action( 'yith_wcbk_booking_form_content', 'yith_wcbk_booking_form_persons', 20, 1 );
add_action( 'yith_wcbk_booking_form_content', 'yith_wcbk_booking_form_services', 30, 1 );
add_action( 'yith_wcbk_booking_form_content', 'yith_wcbk_booking_form_totals', 40, 1 );
add_action( 'yith_wcbk_booking_form_message', 'yith_wcbk_booking_form_message', 10, 1 );
add_action( 'yith_wcbk_booking_form_price', 'yith_wcbk_booking_form_price', 10, 1 );
add_action( 'yith_wcbk_booking_form_end', 'yith_wcbk_booking_form_end', 10, 1 );
add_action( 'yith_wcbk_booking_form_dates_duration', 'yith_wcbk_booking_form_dates_duration', 10, 1 );
add_action( 'yith_wcbk_booking_form_dates_date_fields', 'yith_wcbk_booking_form_dates_date_fields', 10, 1 );

add_action( 'yith_wcbk_booking_form', 'yith_wcbk_booking_form', 10, 2 );

/**
 * PDF Booking
 */
add_action( 'yith_wcbk_booking_pdf_template_footer', 'yith_wcbk_booking_pdf_footer', 10, 2 );
add_action( 'yith_wcbk_booking_pdf_template_header', 'yith_wcbk_booking_pdf_header', 10, 2 );
add_action( 'yith_wcbk_booking_pdf_template_content', 'yith_wcbk_booking_pdf_booking_details', 10, 2 );
add_action( 'yith_wcbk_booking_pdf_template_content', 'yith_wcbk_booking_pdf_user_info', 10, 2 );

/**
 * Booking Search Form Results
 */

add_action( 'yith_wcbk_search_form_item_thumbnails', 'woocommerce_show_product_loop_sale_flash', 10 );
add_action( 'yith_wcbk_search_form_item_thumbnails', 'yith_wcbk_search_form_item_thumbnails', 10 );

add_action( 'yith_wcbk_before_search_form_item_title', 'yith_wcbk_search_form_item_link_open', 10, 1 );

add_action( 'yith_wcbk_search_form_item_title', 'yith_wcbk_search_form_item_title', 10 );

add_action( 'yith_wcbk_after_search_form_item_title', 'yith_wcbk_search_form_item_link_close', 5 );

add_action( 'yith_wcbk_search_form_item_price', 'woocommerce_template_loop_price', 10 );

add_action( 'yith_wcbk_search_form_item_add_to_cart', 'yith_wcbk_search_form_item_add_to_cart', 10, 1 );


/**
 * View Booking in frontend
 */
add_action( 'yith_wcbk_show_bookings_table', 'yith_wcbk_show_bookings_table', 10 );
add_action( 'yith_wcbk_view_booking', 'yith_wcbk_booking_details_table', 10 );
add_action( 'yith_wcbk_booking_details_after_booking_table', 'yith_wcbk_booking_actions', 10 );
add_action( 'yith_wcbk_show_booking_actions', 'yith_wcbk_booking_actions', 10, 2 );

/**
 * Emails
 */
add_action( 'yith_wcbk_email_booking_details', 'yith_wcbk_email_booking_details', 10, 4 );
add_action( 'yith_wcbk_email_booking_actions', 'yith_wcbk_email_booking_actions', 10, 5 );


/**
 * Booking Form shortcode summary
 */
add_action( 'yith_wcbk_booking_form_shortcode_before_add_to_cart_form', 'woocommerce_template_single_title', 5 );
add_action( 'yith_wcbk_booking_form_shortcode_before_add_to_cart_form', 'woocommerce_template_single_rating', 10 );
add_action( 'yith_wcbk_booking_form_shortcode_before_add_to_cart_form', 'woocommerce_template_single_price', 10 );
add_action( 'yith_wcbk_booking_form_shortcode_after_add_to_cart_form', 'woocommerce_template_single_meta', 10 );
add_action( 'yith_wcbk_booking_form_shortcode_after_add_to_cart_form', 'woocommerce_template_single_sharing', 20 );


/**
 * Widget Booking Form shortcode summary
 */
add_action( 'yith_wcbk_widget_booking_form_head', 'woocommerce_template_single_price', 10 );
add_action( 'yith_wcbk_widget_booking_form_head', 'woocommerce_template_single_rating', 20 );


/** -------------------------
 *          FUNCTIONS
 * --------------------------
 */

if ( !function_exists( 'yith_wcbk_search_form_item_thumbnails' ) ) {
    function yith_wcbk_search_form_item_thumbnails() {
        wc_get_template( 'booking/search-form/results/single/thumbnails.php', array(), '', YITH_WCBK_TEMPLATE_PATH );
    }
}

if ( !function_exists( 'yith_wcbk_search_form_item_add_to_cart' ) ) {
    function yith_wcbk_search_form_item_add_to_cart( $booking_data ) {
        wc_get_template( 'booking/search-form/results/single/add-to-cart.php', compact( 'booking_data' ), '', YITH_WCBK_TEMPLATE_PATH );
    }
}

if ( !function_exists( 'yith_wcbk_search_form_item_title' ) ) {
    function yith_wcbk_search_form_item_title() {
        wc_get_template( 'booking/search-form/results/single/title.php', array(), '', YITH_WCBK_TEMPLATE_PATH );
    }
}

if ( !function_exists( 'yith_wcbk_search_form_item_link_open' ) ) {
    function yith_wcbk_search_form_item_link_open( $booking_data = array() ) {
        global $product;

        if ( isset( $booking_data[ 'person_types' ] ) ) {
            if ( $product->has_people_types_enabled() ) {
                $booking_data[ 'person_types' ] = yith_wcbk_booking_person_types_to_id_number_array( $booking_data[ 'person_types' ] );
            } else {
                unset( $booking_data[ 'person_types' ] );
            }
        }

        $link = $product->get_permalink_with_data( $booking_data );

        echo '<a href="' . esc_url( $link ) . '">';
    }
}

if ( !function_exists( 'yith_wcbk_search_form_item_link_close' ) ) {
    function yith_wcbk_search_form_item_link_close() {
        echo '</a>';
    }
}


if ( !function_exists( 'yith_wcbk_booking_pdf_footer' ) ) {
    function yith_wcbk_booking_pdf_footer( $booking, $is_admin ) {
        if ( !$booking )
            return;

        $args = array(
            'footer'   => '',
            'booking'  => $booking,
            'is_admin' => $is_admin,
        );
        wc_get_template( 'booking/pdf/footer.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
    }
}

if ( !function_exists( 'yith_wcbk_booking_pdf_header' ) ) {
    function yith_wcbk_booking_pdf_header( $booking, $is_admin ) {
        if ( !$booking )
            return;

        $args = array(
            'booking'  => $booking,
            'is_admin' => $is_admin,

        );
        wc_get_template( 'booking/pdf/header.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
    }
}

if ( !function_exists( 'yith_wcbk_booking_pdf_booking_details' ) ) {
    function yith_wcbk_booking_pdf_booking_details( $booking, $is_admin ) {
        if ( !$booking )
            return;

        $args = array(
            'booking'  => $booking,
            'is_admin' => $is_admin,

        );
        wc_get_template( 'booking/pdf/booking-details.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
    }
}

if ( !function_exists( 'yith_wcbk_booking_pdf_user_info' ) ) {
    function yith_wcbk_booking_pdf_user_info( $booking, $is_admin ) {
        if ( !$booking )
            return;

        $args = array(
            'booking'  => $booking,
            'is_admin' => $is_admin,

        );
        wc_get_template( 'booking/pdf/user-info.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
    }
}


if ( !function_exists( 'yith_wcbk_show_bookings_table' ) ) {

    /**
     * Displays bookings in a table.
     *
     * @param array $bookings
     */
    function yith_wcbk_show_bookings_table( $bookings ) {
        if ( !$bookings )
            return;
        if ( !is_array( $bookings ) )
            $bookings = array( $bookings );

        $args = array(
            'bookings'     => $bookings,
            'has_bookings' => !!$bookings,
        );
        wc_get_template( 'myaccount/bookings-table.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
    }
}


if ( !function_exists( 'yith_wcbk_booking_details_table' ) ) {

    /**
     * Displays booking details in a table.
     *
     * @param mixed $booking_id
     */
    function yith_wcbk_booking_details_table( $booking_id ) {
        if ( !$booking_id )
            return;

        $booking = yith_get_booking( $booking_id );

        if ( !$booking || !$booking->is_valid() )
            return;

        wc_get_template( 'booking/booking-details.php', array(
            'booking_id' => $booking_id,
            'booking'    => $booking,
        ), '', YITH_WCBK_TEMPLATE_PATH );
    }
}

if ( !function_exists( 'yith_wcbk_booking_actions' ) ) {

    /**
     * Displays booking actions
     *
     * @param YITH_WCBK_Booking $booking
     * @param bool              $show_view_action
     */
    function yith_wcbk_booking_actions( $booking, $show_view_action = false ) {
        if ( !$booking || !$booking->is_valid() )
            return;

        wc_get_template( 'booking/booking-actions.php', array(
            'booking'          => $booking,
            'show_view_action' => $show_view_action,
        ), '', YITH_WCBK_TEMPLATE_PATH );
    }
}

if ( !function_exists( 'yith_wcbk_email_booking_details' ) ) {
    function yith_wcbk_email_booking_details( $booking, $sent_to_admin = false, $plain_text = false, $email = null ) {
        if ( $plain_text ) {
            wc_get_template( 'emails/plain/email-booking-details.php', array(
                'booking'       => $booking,
                'sent_to_admin' => $sent_to_admin,
                'plain_text'    => $plain_text,
                'email'         => $email,
            ), '', YITH_WCBK_TEMPLATE_PATH );
        } else {
            wc_get_template( 'emails/email-booking-details.php', array(
                'booking'       => $booking,
                'sent_to_admin' => $sent_to_admin,
                'plain_text'    => $plain_text,
                'email'         => $email,
            ), '', YITH_WCBK_TEMPLATE_PATH );
        }
    }
}

if ( !function_exists( 'yith_wcbk_email_booking_actions' ) ) {
    /**
     * @param YITH_WCBK_Booking $booking
     * @param bool              $sent_to_admin
     * @param bool              $plain_text
     * @param null              $email
     * @param array             $actions_to_show
     */
    function yith_wcbk_email_booking_actions( $booking, $sent_to_admin = false, $plain_text = false, $email = null, $actions_to_show = array() ) {
        if ( !$actions_to_show )
            return;

        $booking_edit_uri = admin_url( 'post.php?post=' . $booking->get_id() . '&action=edit' );
        $booking_actions  = apply_filters( 'yith_wcbk_booking_actions_for_emails',
                                           array(
                                               'pay'       => array(
                                                   'url'  => $booking->get_confirmed_booking_payment_url(),
                                                   'name' => __( 'Pay booking', 'yith-booking-for-woocommerce' ),
                                               ),
                                               'view'      => array(
                                                   'url'  => $booking->get_view_booking_url(),
                                                   'name' => __( 'View booking', 'yith-booking-for-woocommerce' ),
                                               ),
                                               'cancel'    => array(
                                                   'url'  => $booking->get_cancel_booking_url(),
                                                   'name' => __( 'Cancel', 'yith-booking-for-woocommerce' ),
                                               ),
                                               'confirm'   => array(
                                                   'url'  => $booking->get_mark_action_url( 'confirmed', array( '_wp_http_referer' => urlencode( $booking_edit_uri ) ) ),
                                                   'name' => __( 'Confirm booking', 'yith-booking-for-woocommerce' ),
                                               ),
                                               'unconfirm' => array(
                                                   'url'  => $booking->get_mark_action_url( 'unconfirmed', array( '_wp_http_referer' => urlencode( $booking_edit_uri ) ) ),
                                                   'name' => __( 'Reject booking', 'yith-booking-for-woocommerce' ),
                                               ),
                                           ),
                                           $booking, $sent_to_admin, $plain_text, $email, $actions_to_show );

        $actions = array();

        foreach ( $booking_actions as $key => $value ) {
            if ( in_array( $key, $actions_to_show ) ) {
                $actions[ $key ] = $value;
            }
        }

        if ( isset( $actions[ 'pay' ] ) && !$booking->has_status( 'confirmed' ) ) {
            unset( $actions[ 'pay' ] );
        }

        if ( isset( $actions[ 'cancel' ] ) && !$booking->can_be( 'cancelled_by_user' ) ) {
            unset( $actions[ 'cancel' ] );
        }

        if ( $plain_text ) {
            wc_get_template( 'emails/plain/email-booking-actions.php', array(
                'booking'       => $booking,
                'sent_to_admin' => $sent_to_admin,
                'plain_text'    => $plain_text,
                'email'         => $email,
                'actions'       => $actions,
            ), '', YITH_WCBK_TEMPLATE_PATH );
        } else {
            wc_get_template( 'emails/email-booking-actions.php', array(
                'booking'       => $booking,
                'sent_to_admin' => $sent_to_admin,
                'plain_text'    => $plain_text,
                'email'         => $email,
                'actions'       => $actions,
            ), '', YITH_WCBK_TEMPLATE_PATH );
        }
    }
}

/**
 * @param WC_Product $product
 * @param array      $args
 */
function yith_wcbk_booking_form( $product, $args = array() ) {
    if ( !$product || !$product instanceof WC_Product_Booking )
        return;

    $defaults = array(
        'show_price'      => false,
        'additional_data' => array()
    );
    $args     = wp_parse_args( $args, $defaults );

    /**
     * yith_wcbk_booking_form_start hook.
     *
     * @hooked yith_wcbk_booking_form_start - 10
     */
    do_action( 'yith_wcbk_booking_form_start', $product );

    foreach ( $args[ 'additional_data' ] as $_key => $_value ) {
        $_key   = sanitize_key( $_key );
        $_value = sanitize_text_field( $_value );
        echo "<input type='hidden' class='yith-wcbk-booking-form-additional-data' name='{$_key}' value='{$_value}' />";
    }

    /**
     * yith_wcbk_booking_form_fields hook.
     *
     * @hooked yith_wcbk_booking_form_dates - 10
     * @hooked yith_wcbk_booking_form_persons - 20
     * @hooked yith_wcbk_booking_form_services - 30
     */
    do_action( 'yith_wcbk_booking_form_content', $product );

    if ( $args[ 'show_price' ] ) {
        /**
         * yith_wcbk_booking_form_price hook.
         *
         * @hooked yith_wcbk_booking_form_price - 10
         */
        do_action( 'yith_wcbk_booking_form_price', $product );
    }

    /**
     * yith_wcbk_booking_form_message hook.
     *
     * @hooked yith_wcbk_booking_form_message - 10
     */
    do_action( 'yith_wcbk_booking_form_message', $product );

    /**
     * yith_wcbk_booking_form_end hook.
     *
     * @hooked yith_wcbk_booking_form_end - 10
     */
    do_action( 'yith_wcbk_booking_form_end', $product );
}

/**
 * @param WC_Product $product
 */
function yith_wcbk_booking_form_meta( $product ) {
    if ( !$product || !$product instanceof WC_Product_Booking )
        return;
    wc_get_template( 'single-product/add-to-cart/booking-form/meta.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * @param WC_Product $product
 */
function yith_wcbk_booking_form_start( $product ) {
    if ( !$product || !$product instanceof WC_Product_Booking )
        return;
    wc_get_template( 'single-product/add-to-cart/booking-form/start.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * @param WC_Product $product
 */
function yith_wcbk_booking_form_persons( $product ) {
    if ( !$product || !$product instanceof WC_Product_Booking )
        return;
    wc_get_template( 'single-product/add-to-cart/booking-form/persons.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * @param WC_Product $product
 */
function yith_wcbk_booking_form_dates( $product ) {
    if ( !$product || !$product instanceof WC_Product_Booking )
        return;
    wc_get_template( 'single-product/add-to-cart/booking-form/dates.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * @param WC_Product $product
 */
function yith_wcbk_booking_form_dates_duration( $product ) {
    if ( !$product || !$product instanceof WC_Product_Booking )
        return;

    $unit                = $product->get_duration_unit();
    $show_duration_field = 'month' === $unit || !$product->has_calendar_picker_enabled();

    if ( $show_duration_field )
        wc_get_template( 'single-product/add-to-cart/booking-form/dates/duration.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * @param WC_Product $product
 */
function yith_wcbk_booking_form_dates_date_fields( $product ) {
    if ( !$product || !$product instanceof WC_Product_Booking )
        return;

    wc_get_template( 'single-product/add-to-cart/booking-form/dates/dates.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * @param WC_Product $product
 */
function yith_wcbk_booking_form_services( $product ) {
    if ( !$product || !$product instanceof WC_Product_Booking )
        return;
    wc_get_template( 'single-product/add-to-cart/booking-form/services.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * @param WC_Product $product
 */
function yith_wcbk_booking_form_totals( $product ) {
    if ( !$product || !$product instanceof WC_Product_Booking )
        return;
    wc_get_template( 'single-product/add-to-cart/booking-form/totals.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * @param WC_Product $product
 */
function yith_wcbk_booking_form_message( $product ) {
    if ( !$product || !$product instanceof WC_Product_Booking )
        return;
    wc_get_template( 'single-product/add-to-cart/booking-form/message.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * @param WC_Product $product
 */
function yith_wcbk_booking_form_price( $product ) {
    if ( !$product || !$product instanceof WC_Product_Booking )
        return;
    wc_get_template( 'single-product/add-to-cart/booking-form/price.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}

/**
 * @param WC_Product $product
 */
function yith_wcbk_booking_form_end( $product ) {
    if ( !$product || !$product instanceof WC_Product_Booking )
        return;
    wc_get_template( 'single-product/add-to-cart/booking-form/end.php', array( 'product' => $product ), '', YITH_WCBK_TEMPLATE_PATH );
}


