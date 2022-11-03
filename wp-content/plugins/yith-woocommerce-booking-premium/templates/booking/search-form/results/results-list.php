<?php
/**
 * Booking Search Form Results List Template
 * Shows list of booking search form results
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/results/results-list.php.
 *
 * @var WP_Query $products
 * @var array    $booking_request
 * @var int      $current_page
 * @var array    $product_ids
 */

!defined( 'YITH_WCBK' ) && exit;
?>

<?php if ( $products->have_posts() ) : ?>

    <?php while ( $products->have_posts() ) : $products->the_post(); ?>
        <?php
        /**
         * @var WC_Product_Booking $product
         */
        global $product;

        $booking_data = array();

        if ( !empty( $booking_request[ 'from' ] ) && !empty( $booking_request[ 'to' ] ) && !$product->has_time() ) {
            $booking_request[ 'add-to-cart' ]                                         = $product->get_id();
            $booking_data                                                             = YITH_WCBK_Cart::get_booking_data_from_request( $booking_request );
            $booking_data[ YITH_WCBK_Search_Form_Helper::RESULT_KEY_IN_BOOKING_DATA ] = true;
            $booking_data_for_price                                                   = $booking_data;

            if ( isset( $booking_data[ 'person_types' ] ) ) {
                if ( $product->has_people_types_enabled() ) {
                    $booking_data[ 'person_types' ] = yith_wcbk_booking_person_types_to_list( $booking_data[ 'person_types' ] );
                } else {
                    unset( $booking_data[ 'person_types' ] );
                }
            }
            
            $the_price = $product->calculate_price( $booking_data_for_price );
            yit_set_prop( $product, 'price', $the_price );
        }


        wc_get_template( 'booking/search-form/results/single.php', compact( 'product', 'booking_data', 'the_price' ), '', YITH_WCBK_TEMPLATE_PATH );
        ?>

    <?php endwhile; // end of the loop. ?>

    <?php wp_reset_query(); ?>

<?php endif; ?>

