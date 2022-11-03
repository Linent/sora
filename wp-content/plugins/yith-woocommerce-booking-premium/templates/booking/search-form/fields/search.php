<?php
/**
 * Booking Search Form Field Search
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/search.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 */

!defined( 'YITH_WCBK' ) && exit;

$label  = !empty( $field_data[ 'label' ] ) ? __( $field_data[ 'label' ], 'yith-booking-for-woocommerce' ) : __( 'Search', 'yith-booking-for-woocommerce' );
$search = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( "s" );
?>

<tr class="yith-wcbk-booking-search-form-row-search">
    <td class="yith-wcbk-booking-search-form-label">
        <?php echo $label ?>
    </td>
    <td class="yith-wcbk-booking-search-form-input">
        <input type="text" class="yith-wcbk-booking-field" name="s" value="<?php echo $search ?>"/>
    </td>
</tr>