<?php
/**
 * Start Date Field in booking form
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/start-date.php
 * @var WC_Product_Booking $product
 * @var array              $date_info
 * @var array              $not_available_dates
 * @var string             $calendar_day_range_picker_class
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
?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-dates <?php echo $calendar_day_range_picker_class ?>">

    <label for="yith-wcbk-booking-start-date-<?php echo $product->get_id() ?>" class='yith-wcbk-booking-form__label'><?php echo yith_wcbk_get_label( 'start-date' ) ?></label>

    <?php
    yith_wcbk_print_field( array(
                               'type'  => YITH_WCBK()->settings->display_date_picker_inline() ? 'datepicker-inline' : 'datepicker',
                               'id'    => 'yith-wcbk-booking-start-date-' . $product->get_id(),
                               'name'  => 'start-date',
                               'class' => 'yith-wcbk-booking-date yith-wcbk-booking-start-date',
                               'data'  => array(
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
                               'value' => $default_start_date
                           ) );


    yith_wcbk_print_field( array( 'id'    => 'yith-wcbk-booking-hidden-from' . $product->get_id(),
                                  'type'  => 'hidden',
                                  'name'  => 'from',
                                  'class' => 'yith-wcbk-booking-date yith-wcbk-booking-hidden-from',
                                  'value' => $default_start_date ) );
    ?>
</div>