<?php
/**
 * Start Date Field in booking form
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/booking-form/dates/start-date.php
 *
 * @var WC_Product_Booking $product
 * @var array              $date_info
 * @var array              $not_available_months
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
 */
extract( $date_info );
?>
<div class="yith-wcbk-form-section yith-wcbk-form-section-dates">

    <label for="yith-wcbk-booking-start-month-<?php echo $product->get_id() ?>" class='yith-wcbk-booking-form__label'><?php _e( 'Start month', 'yith-booking-for-woocommerce' ) ?></label>

    <?php
    yith_wcbk_print_field( array(
                               'type'        => 'month-picker',
                               'id'          => 'yith-wcbk-booking-start-month-' . $product->get_id(),
                               'name'        => 'from',
                               'class'       => 'yith-wcbk-month-picker',
                               'value_class' => 'yith-wcbk-booking-date yith-wcbk-booking-start-date',
                               'options'     => array(
                                   'not_available_months' => $not_available_months,
                                   'min_date'             => $min_date_timestamp,
                                   'max_date'             => $max_date_timestamp,
                               ) ) );
    ?>
</div>