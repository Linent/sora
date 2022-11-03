<?php
/**
 * Booking Details Template
 *
 * Shows booking details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/booking-details.php.
 *
 * @var YITH_WCBK_Booking $booking
 * @var int               $booking_id
 */

!defined( 'YITH_WCBK' ) && exit;
?>

<h2><?php _e( 'Booking Details', 'yith-booking-for-woocommerce' ); ?></h2>
<table class="shop_table booking_details">
    <tr>
        <th scope="row"><?php echo __( 'Status', 'yith-booking-for-woocommerce' ) ?></th>
        <td><?php echo $booking->get_status_text(); ?></td>
    </tr>
    <?php if ( $product = wc_get_product( $booking->get_product_id() ) ) :
        $product_link = $product->get_permalink();
        $product_title = $product->get_title();
        ?>
        <tr>
            <th scope="row"><?php echo __( 'Product', 'yith-booking-for-woocommerce' ) ?></th>
            <td>
                <a href="<?php echo $product_link ?>"><?php echo $product_title ?></a>
            </td>
        </tr>
    <?php endif ?>

    <?php
    $booking_order_id = apply_filters( 'yith_wcbk_booking_details_order_id', $booking->order_id, $booking );
    ?>

    <?php if ( $booking_order_id && $order = wc_get_order( $booking_order_id ) ) :
        $order_link = $order->get_view_order_url();
        $order_title = _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number();
        ?>
        <tr>
            <th scope="row"><?php echo __( 'Order', 'yith-booking-for-woocommerce' ) ?></th>
            <td>
                <a href="<?php echo $order_link ?>"><?php echo $order_title ?></a>
            </td>
        </tr>
    <?php endif ?>

    <tr>
        <th scope="row"><?php echo yith_wcbk_get_label( 'duration' ) ?></th>
        <td><?php echo $booking->get_duration_html(); ?></td>
    </tr>
    <tr>
        <th scope="row"><?php echo yith_wcbk_get_label( 'from' ) ?></th>
        <td><?php echo $booking->get_formatted_date( 'from' ); ?></td>
    </tr>
    <tr>
        <th scope="row"><?php echo yith_wcbk_get_label( 'to' ) ?></th>
        <td><?php echo $booking->get_formatted_date( 'to' ); ?></td>
    </tr>
    <?php if ( $booking->has_persons() && !$booking->has_person_types() ) : ?>
        <tr>
            <th scope="row"><?php echo yith_wcbk_get_label( 'people' ) ?></th>
            <td><?php echo $booking->persons ?></td>
        </tr>
    <?php endif; ?>

    <?php if ( $services = $booking->get_service_names( false ) ) : ?>
        <tr>
            <th scope="row"><?php echo yith_wcbk_get_label( 'services' ) ?></th>
            <td><?php echo yith_wcbk_booking_services_html( $services ); ?></td>
        </tr>
    <?php endif; ?>
</table>

<?php if ( $booking->has_persons() && $booking->has_person_types() ) : ?>
    <h3><?php echo yith_wcbk_get_label( 'people' ) ?></h3>
    <table class="shop_table booking_person_types_details">
        <tbody>
        <?php foreach ( $booking->person_types as $person_type ) : ?>
            <?php
            if ( !$person_type[ 'number' ] )
                continue;
            $person_type_id     = absint( $person_type[ 'id' ] );
            $person_type_title  = YITH_WCBK()->person_type_helper->get_person_type_title( $person_type_id );
            $person_type_title  = !!$person_type_title ? $person_type_title : $person_type[ 'title' ];
            $person_type_number = absint( $person_type[ 'number' ] );
            ?>
            <tr>
                <th scope="row"><?php echo $person_type_title ?></th>
                <td><?php echo $person_type_number ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th scope="row"><?php echo yith_wcbk_get_label( 'total-people' ) ?></th>
            <td><?php echo $booking->persons ?></td>
        </tr>
        </tfoot>
    </table>
<?php endif ?>

<?php do_action( 'yith_wcbk_booking_details_after_booking_table', $booking ); ?>
