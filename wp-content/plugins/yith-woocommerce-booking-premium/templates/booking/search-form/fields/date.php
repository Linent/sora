<?php
/**
 * Booking Search Form Field Date daily
 *
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/date.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 */

!defined( 'YITH_WCBK' ) && exit;
$current_id = $search_form->get_unique_id();

?>
<tr class="yith-wcbk-booking-search-form-row-start-date">
    <td class="yith-wcbk-booking-search-form-label">
        <?php echo yith_wcbk_get_label( 'start-date' ); ?>
    </td>
    <td class="yith-wcbk-booking-search-form-input">
        <?php
        yith_wcbk_print_field( array(
                                   'type'  => 'datepicker',
                                   'id'    => 'yith-wcbk-booking-search-form-date-day-start-date-' . $current_id,
                                   'name'  => 'from',
                                   'class' => 'yith-wcbk-booking-field yith-wcbk-booking-date yith-wcbk-booking-start-date',
                                   'data'  => apply_filters( 'yith_wcbk_search_form_start_date_input_data', array(
                                       'min-date'   => '+0D',
                                       'related-to' => '#yith-wcbk-booking-search-form-date-day-end-date-' . $current_id,
                                   ), $search_form ),
                                   'value' => YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'from' )
                               ) );
        ?>
    </td>
</tr>

<tr class="yith-wcbk-booking-search-form-row-end-date">
    <td class="yith-wcbk-booking-search-form-label">
        <?php echo yith_wcbk_get_label( 'end-date' ); ?>
    </td>
    <td class="yith-wcbk-booking-search-form-input">
        <?php
        yith_wcbk_print_field( array(
                                   'type'  => 'datepicker',
                                   'id'    => 'yith-wcbk-booking-search-form-date-day-end-date-' . $current_id,
                                   'name'  => 'to',
                                   'class' => 'yith-wcbk-booking-field yith-wcbk-booking-date yith-wcbk-booking-end-date',
                                   'data'  => array(
                                       'min-date'     => '+0D',
                                       'related-from' => '#yith-wcbk-booking-search-form-date-day-start-date-' . $current_id,
                                   ),
                                   'value' => YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'to' )
                               ) );
        ?>
    </td>
</tr>