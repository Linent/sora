<?php
/**
 * Booking Search Form Field Persons
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/persons-persons.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 */

!defined( 'YITH_WCBK' ) && exit;

$persons = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( "persons" );
$persons = !!$persons ? $persons : '';
?>

<tr class="yith-wcbk-booking-search-form-row-persons">
    <td class="yith-wcbk-booking-search-form-label">
        <?php echo yith_wcbk_get_label( 'people' ); ?>
    </td>
    <td class="yith-wcbk-booking-search-form-input">
        <input type="number" class="yith-wcbk-booking-field" name="persons" min="0" step="1" value="<?php echo $persons ?>"/>
    </td>
</tr>