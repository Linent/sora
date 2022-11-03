<?php
/**
 * Range Date Picker Field in booking form
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/range-picker.php
 * @var WC_Product_Booking $product
 * @var array              $date_info
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

$all_day_booking = $product->is_full_day();
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
$check_min_max_duration_in_calendar = YITH_WCBK()->settings->check_min_max_duration_in_calendar();
$not_available_dates                = $product->get_not_available_dates( $current_year, $current_month, $next_year, $next_month, 'day', false, false, $check_min_max_duration_in_calendar );
?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-dates yith-wcbk-form-section-dates-range-picker">
    <label class='yith-wcbk-booking-form__label'><?php echo yith_wcbk_get_label( 'dates' ) ?></label>

    <div class="yith-wcbk-date-range-picker yith-wcbk-clearfix">
        <?php
        yith_wcbk_print_field( array(
                                   'type'              => 'text',
                                   'id'                => 'yith-wcbk-booking-start-date-' . $product->get_id(),
                                   'name'              => 'start-date',
                                   'class'             => 'yith-wcbk-date-picker yith-wcbk-booking-date yith-wcbk-booking-start-date',
                                   'data'              => array(
                                       'type'                => 'from',
                                       'all-day'             => !!$all_day_booking ? 'yes' : 'no',
                                       'ajax-load-months'    => !!$ajax_load_months ? 'yes' : 'no',
                                       'min-duration'        => $product->get_minimum_duration(),
                                       'month-to-load'       => $next_month,
                                       'year-to-load'        => $next_year,
                                       'min-date'            => $min_date,
                                       'max-date'            => $max_date,
                                       'not-available-dates' => $not_available_dates ? json_encode( $not_available_dates ) : '',
                                       'product-id'          => $product->get_id(),
                                       'related-to'          => '#yith-wcbk-booking-end-date-' . $product->get_id(),
                                       'allow-same-date'     => !!$all_day_booking ? 'yes' : 'no',
                                       'allowed-days'        => json_encode( $product->get_allowed_start_days() ),
                                       'on-select-open'      => '#yith-wcbk-booking-end-date-' . $product->get_id(),
                                       'static'              => 'yes'
                                   ),
                                   'custom_attributes' => 'placeholder="' . yith_wcbk_get_label( 'start-date' ) . '" readonly',
                                   'value'             => $default_start_date
                               ) );


        yith_wcbk_print_field( array(
                                   'type'              => 'text',
                                   'id'                => 'yith-wcbk-booking-start-date-' . $product->get_id() . '--formatted',
                                   'class'             => 'yith-wcbk-date-picker--formatted yith-wcbk-booking-date yith-wcbk-booking-start-date',
                                   'custom_attributes' => 'placeholder="' . yith_wcbk_get_label( 'start-date' ) . '" readonly',
                               ) );

        yith_wcbk_print_field( array( 'id'    => 'yith-wcbk-booking-hidden-from' . $product->get_id(),
                                      'type'  => 'hidden',
                                      'name'  => 'from',
                                      'class' => 'yith-wcbk-booking-date yith-wcbk-booking-hidden-from',
                                      'value' => $default_start_date ) );

        ?>
        <div class="yith-wcbk-date-range-picker__arrow">
            <?php yith_wcbk_print_svg( 'arrow-right-alt-thin' ); ?>
        </div>
        <?php

        yith_wcbk_print_field( array(
                                   'type'              => 'text',
                                   'id'                => 'yith-wcbk-booking-end-date-' . $product->get_id(),
                                   'name'              => 'to',
                                   'class'             => 'yith-wcbk-date-picker yith-wcbk-booking-date yith-wcbk-booking-end-date',
                                   'data'              => array(
                                       'type'            => 'to',
                                       'min-date'        => $min_date,
                                       'max-date'        => $max_date,
                                       'related-from'    => '#yith-wcbk-booking-start-date-' . $product->get_id(),
                                       'allow-same-date' => !!$all_day_booking ? 'yes' : 'no',
                                   ),
                                   'custom_attributes' => 'placeholder="' . yith_wcbk_get_label( 'end-date' ) . '" readonly',
                                   'value'             => $default_end_date
                               ) );

        yith_wcbk_print_field( array(
                                   'type'              => 'text',
                                   'id'                => 'yith-wcbk-booking-end-date-' . $product->get_id() . '--formatted',
                                   'class'             => 'yith-wcbk-date-picker--formatted yith-wcbk-booking-date yith-wcbk-booking-end-date',
                                   'custom_attributes' => 'placeholder="' . yith_wcbk_get_label( 'end-date' ) . '" readonly',
                               ) );
        ?>
    </div>
</div>