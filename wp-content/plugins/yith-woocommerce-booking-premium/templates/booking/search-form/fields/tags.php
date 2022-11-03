<?php
/**
 * Booking Search Form Field Categories
 *
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/categories.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 */

!defined( 'YITH_WCBK' ) && exit;

$booking_tag_args = array(
    'taxonomy'   => 'product_tag',
    'hide_empty' => true,
    'fields'     => 'id=>name'
);

$tags = YITH_WCBK()->wp->get_terms( $booking_tag_args );

$searched_tags = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( "tags" );
$searched_tags = !!$searched_tags && is_array( $searched_tags ) ? $searched_tags : array();

if ( !!$tags ):
    ?>
    <tr class="yith-wcbk-booking-search-form-row-tags">
        <td class="yith-wcbk-booking-search-form-label">
            <?php echo apply_filters( 'yith_wcbk_search_form_label_tags', __( 'Tags', 'yith-booking-for-woocommerce' ) ); ?>
        </td>
        <td class="yith-wcbk-booking-search-form-input">
            <select name="tags[]" class="yith-wcbk-booking-tags yith-wcbk-select2" multiple>
                <?php foreach ( $tags as $id => $name ): ?>
                    <option value="<?php echo $id ?>" <?php selected( in_array( $id, $searched_tags ) ) ?>><?php echo $name ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>

<?php endif; ?>