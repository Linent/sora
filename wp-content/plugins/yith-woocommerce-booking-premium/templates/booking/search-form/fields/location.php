<?php
/**
 * Booking Search Form Field Location
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/location.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 */

!defined( 'YITH_WCBK' ) && exit;

$default_location_range = apply_filters( 'yith_wcbk_booking_search_form_default_location_range', 30 );
$location               = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'location' );
$location_range         = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'location_range' );
$location_range         = !!$location_range ? $location_range : $default_location_range;
?>

<tr class="yith-wcbk-booking-search-form-row-location">
    <td class="yith-wcbk-booking-search-form-label">
        <?php echo apply_filters( 'yith_wcbk_search_form_label_location', __( 'Location', 'yith-booking-for-woocommerce' ) ); ?>
    </td>
    <td class="yith-wcbk-booking-search-form-input">
        <input type="text" name="location" class="yith-wcbk-booking-location yith-wcbk-booking-field yith-wcbk-google-maps-places-autocomplete" value="<?php echo $location ?>"/>
    </td>
</tr>
<tr class="yith-wcbk-booking-search-form-row-location-range">
    <td class="yith-wcbk-booking-search-form-label">
        <?php _e( 'Distance (Km)', 'yith-booking-for-woocommerce' ); ?>
    </td>
    <td class="yith-wcbk-booking-search-form-input">
        <input type="number" name="location_range" class="yith-wcbk-booking-location-range yith-wcbk-booking-field" min="0" value="<?php echo $location_range ?>"/>
    </td>
</tr>