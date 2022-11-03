<?php
/**
 * Booking Search Form Field Categories
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/categories.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 */

!defined( 'YITH_WCBK' ) && exit;

$booking_cat_args   = array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'fields'     => 'id=>name'
);
$booking_categories = get_option( 'yith-wcbk-booking-categories', array() );

if ( !!$booking_categories ) {
    $booking_cat_args[ 'include' ] = $booking_categories;
}

$categories = YITH_WCBK()->wp->get_terms( $booking_cat_args );

$searched_categories = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( "categories" );
$searched_categories = apply_filters( 'yith_wcbk_searched_categories', !!$searched_categories && is_array( $searched_categories ) ? $searched_categories : array() );

if ( !!$categories ):
    ?>
    <tr class="yith-wcbk-booking-search-form-row-categories">
        <td class="yith-wcbk-booking-search-form-label">
            <?php echo apply_filters( 'yith_wcbk_search_form_label_categories', __( 'Categories', 'yith-booking-for-woocommerce' ) ); ?>
        </td>
        <td class="yith-wcbk-booking-search-form-input">
            <select name="categories[]" class="yith-wcbk-booking-categories yith-wcbk-select2" multiple>
                <?php foreach ( $categories as $cat_id => $cat_name ): ?>
                    <option value="<?php echo $cat_id ?>" <?php selected( in_array( $cat_id, $searched_categories ) ) ?>><?php echo $cat_name ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>

<?php endif; ?>