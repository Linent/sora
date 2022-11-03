<?php
/**
 * Booking Search Form Template
 * Shows booking search form
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/booking-search-form.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 */

!defined( 'YITH_WCBK' ) && exit;

wp_enqueue_script( 'yith-wcbk-booking-search-form' );

$styles             = $search_form->get_styles();
$style              = $styles[ 'style' ];
$options            = $search_form->get_options();
$show_results_class = 'show-results-' . $options[ 'show-results' ];
$form_method        = 'shop' === $options[ 'show-results' ] ? 'get' : 'post';

$shop_url = !get_option( 'permalink_structure' ) ? get_post_type_archive_link( 'product' ) : get_permalink( wc_get_page_id( 'shop' ) );
?>

<?php do_action( 'yith_wcbk_booking_before_search_form', $search_form ); ?>

<div class="yith-wcbk-booking-search-form yith-wcbk-booking-search-form-<?php echo $search_form->id ?> <?php echo $style ?> <?php echo $show_results_class ?>"
     data-search-form-id="<?php echo $search_form->id ?>"
     data-search-form-result="#yith-wcbk-booking-search-form-result-<?php echo $search_form->id ?>">
    <form method="<?php echo $form_method ?>" enctype='multipart/form-data' action="<?php echo $shop_url; ?>" autocomplete="off">
        <input type="hidden" name="yith-wcbk-booking-search" value="search-bookings"/>
        <input type="hidden" name="action" value="yith_wcbk_search_booking_products"/>
        <input type="hidden" name="context" value="frontend"/>

        <table class="yith-wcbk-booking-search-form-table">
            <?php
            foreach ( $search_form->get_fields() as $field_key => $field_data ) {
                if ( 'yes' === $field_data[ 'enabled' ] ) {
                    do_action( 'yith_wcbk_booking_search_form_before_print_field', $field_key, $field_data, $search_form );
                    do_action( 'yith_wcbk_booking_search_form_print_field', $field_key, $field_data, $search_form );
                    do_action( 'yith_wcbk_booking_search_form_after_print_field', $field_key, $field_data, $search_form );
                }
            }
            ?>

            <?php do_action( 'yith_wcbk_booking_search_form_after_print_fields', $search_form ); ?>

            <?php wp_nonce_field( 'search-booking-products', 'security', false ); ?>

            <tr>
                <td colspan="2">
                    <button type="submit" class="button alt yith-wcbk-booking-search-form-submit"><?php _e( 'Search', 'yith-booking-for-woocommerce' ); ?></button>
                </td>
            </tr>
        </table>

        <?php if ( !empty( $cat ) ) : ?>
            <?php
            if ( 'current' === $cat ) {
                $cat            = '';
                $current_object = get_queried_object();
                if ( $current_object instanceof WP_Term && 'product_cat' === $current_object->taxonomy ) {
                    $cat = $current_object->term_id;
                }
            }
            ?>
            <input type="hidden" name="categories" value="<?php echo $cat ?>">
        <?php endif; ?>
    </form>
</div>

<?php do_action( 'yith_wcbk_booking_after_search_form', $search_form ); ?>
