<?php
/**
 * End Date Field in booking form
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/end-date.php
 * @var WC_Product_Booking $product
 * @var array              $date_info
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

    <label for="yith-wcbk-booking-end-date-<?php echo $product->get_id() ?>" class='yith-wcbk-booking-form__label'><?php echo yith_wcbk_get_label( 'end-date' ) ?></label>

    <?php
    yith_wcbk_print_field( array(
                               'type'  => YITH_WCBK()->settings->display_date_picker_inline() ? 'datepicker-inline' : 'datepicker',
                               'id'    => 'yith-wcbk-booking-end-date-' . $product->get_id(),
                               'name'  => 'to',
                               'class' => 'yith-wcbk-booking-date yith-wcbk-booking-end-date',
                               'data'  => array(
                                   'type'            => 'to',
                                   'min-date'        => $min_date,
                                   'max-date'        => $max_date,
                                   'related-from'    => '#yith-wcbk-booking-start-date-' . $product->get_id(),
                                   'allow-same-date' => !!$all_day_booking ? 'yes' : 'no',
                                   'static'          => 'yes'
                               ),
                               'value' => $default_end_date
                           ) );

    ?>

</div>