<?php
/**
 * Date Fields in booking form
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/dates.php
 *
 * @var WC_Product_Booking $product
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

$unit            = $product->get_duration_unit();
$all_day_booking = $product->is_full_day();
$date_info       = yith_wcbk_get_booking_form_date_info( $product );
/**
 * @var $current_year
 * @var $current_month
 * @var $next_year
 * @var $next_month
 * @var $min_date
 * @var $min_date_timestamp
 * @var $max_date
 * @var $max_date_timestamp
 * @var $default_start_date
 * @var $default_end_date
 * @var $months_to_load
 * @var $ajax_load_months
 */
extract( $date_info );

if ( 'month' !== $unit ) {
    if ( YITH_WCBK()->settings->is_unique_calendar_range_picker_enabled() && $product->has_calendar_picker_enabled() ) {
        wc_get_template( '/single-product/add-to-cart/booking-form/dates/range-picker.php', compact( 'product', 'date_info' ), '', YITH_WCBK_TEMPLATE_PATH );
    } else {
        $check_min_max_duration_in_calendar = YITH_WCBK()->settings->check_min_max_duration_in_calendar();
        $not_available_dates                = $product->get_not_available_dates( $current_year, $current_month, $next_year, $next_month, 'day', false, false, $check_min_max_duration_in_calendar );
        $calendar_day_range_picker_class    = $product->has_calendar_picker_enabled() ? ' calendar-day-range-picker' : '';

        wc_get_template( '/single-product/add-to-cart/booking-form/dates/start-date.php', compact( 'product', 'date_info', 'not_available_dates', 'calendar_day_range_picker_class' ), '', YITH_WCBK_TEMPLATE_PATH );

        if ( $product->has_calendar_picker_enabled() ) {
            wc_get_template( '/single-product/add-to-cart/booking-form/dates/end-date.php', compact( 'product', 'date_info', 'calendar_day_range_picker_class' ), '', YITH_WCBK_TEMPLATE_PATH );
        }

        if ( in_array( $unit, array( 'hour', 'minute' ) ) ) {

            wc_get_template( '/single-product/add-to-cart/booking-form/dates/time.php', compact( 'product' ), '', YITH_WCBK_TEMPLATE_PATH );
        }
    }
} else {
    $not_available_months = $product->get_not_available_months( $current_year, $current_month, $next_year, $next_month );
    wc_get_template( '/single-product/add-to-cart/booking-form/dates/month.php', compact( 'product', 'date_info', 'not_available_months' ), '', YITH_WCBK_TEMPLATE_PATH );
}