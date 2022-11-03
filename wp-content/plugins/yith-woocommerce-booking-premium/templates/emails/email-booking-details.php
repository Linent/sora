<?php
/**
 * Booking details table shown in emails.
 *
 * @var YITH_WCBK_Booking $booking
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-booking-details.php.
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'yith_wcbk_email_before_booking_table', $booking, $sent_to_admin, $plain_text, $email ); ?>

<?php if ( !$sent_to_admin ) : ?>
    <h2><a class="link"
           href="<?php echo esc_url( $booking->get_view_booking_url() ); ?>"><?php echo $booking->get_name() ?></a></h2>
<?php else : ?>
    <h2><a class="link"
           href="<?php echo esc_url( admin_url( 'post.php?post=' . $booking->id . '&action=edit' ) ); ?>"><?php echo $booking->get_name() ?></a>
        (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $booking->post->post_date ) ), date_i18n( wc_date_format(), strtotime( $booking->post->post_date ) ) ); ?>
        )</h2>
<?php endif; ?>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
    <tr>
        <th class="td" scope="row" colspan="2" style="text-align:left;"><?php echo __( 'Status', 'yith-booking-for-woocommerce' ) ?></th>
        <td class="td" style="text-align:left;"><?php echo $booking->get_status_text(); ?></td>
    </tr>
    <?php if ( $product = wc_get_product( $booking->product_id ) ) :
        $product_link = get_permalink( $booking->product_id );
        $product_title = $product->get_title();
        ?>
        <tr>
            <th class="td" scope="row" colspan="2" style="text-align:left;"><?php echo __( 'Product', 'yith-booking-for-woocommerce' ) ?></th>
            <td class="td" style="text-align:left;">
                <a href="<?php echo $product_link ?>"><?php echo $product_title ?></a>
            </td>
        </tr>
    <?php endif ?>

    <?php
    $booking_order_id = apply_filters( 'yith_wcbk_email_booking_details_order_id', $booking->order_id, $booking, $sent_to_admin, $plain_text, $email );
    ?>

    <?php if ( $booking_order_id && $order = wc_get_order( $booking_order_id ) ) :
        if ( !$sent_to_admin ) {
            $order_link = $order->get_view_order_url();
        } else {
            $order_link = esc_url( admin_url( 'post.php?post=' . $booking_order_id . '&action=edit' ) );
        }
        $order_title = _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number();
        ?>
        <tr>
            <th class="td" scope="row" colspan="2" style="text-align:left;"><?php echo __( 'Order', 'yith-booking-for-woocommerce' ) ?></th>
            <td class="td" style="text-align:left;">
                <a href="<?php echo $order_link ?>"><?php echo $order_title ?></a>
            </td>
        </tr>
    <?php endif ?>

    <tr>
        <th class="td" scope="row" colspan="2" style="text-align:left;"><?php echo yith_wcbk_get_label( 'duration' ) ?></th>
        <td class="td" style="text-align:left;"><?php echo $booking->get_duration_html(); ?></td>
    </tr>
    <tr>
        <th class="td" scope="row" colspan="2" style="text-align:left;"><?php echo yith_wcbk_get_label( 'from' ) ?></th>
        <td class="td" style="text-align:left;"><?php echo $booking->get_formatted_date( 'from' ); ?></td>
    </tr>
    <tr>
        <th class="td" scope="row" colspan="2" style="text-align:left;"><?php echo yith_wcbk_get_label( 'to' ) ?></th>
        <td class="td" style="text-align:left;"><?php echo $booking->get_formatted_date( 'to' ); ?></td>
    </tr>
    <?php if ( $booking->has_persons() && !$booking->has_person_types() ) : ?>
        <tr>
            <th class="td" scope="row" colspan="2" style="text-align:left;"><?php echo yith_wcbk_get_label( 'people' ) ?></th>
            <td class="td" style="text-align:left;"><?php echo $booking->persons ?></td>
        </tr>
    <?php endif; ?>

    <?php if ( $services = $booking->get_service_names( $sent_to_admin, 'additional' ) ) : ?>
        <tr>
            <th class="td" scope="row" colspan="2" style="text-align:left;"><?php echo yith_wcbk_get_label( 'additional-services' ) ?></th>
            <td class="td" style="text-align:left;">
                <?php echo yith_wcbk_booking_services_html( $services ); ?>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ( $services = $booking->get_service_names( $sent_to_admin, 'included' ) ) : ?>
        <tr>
            <th class="td" scope="row" colspan="2" style="text-align:left;"><?php echo yith_wcbk_get_label( 'included-services' ) ?></th>
            <td class="td" style="text-align:left;">
                <?php echo yith_wcbk_booking_services_html( $services ); ?>
            </td>
        </tr>
    <?php endif; ?>
</table>

<?php if ( $booking->has_persons() && $booking->has_person_types() ) : ?>
    <h3><?php echo yith_wcbk_get_label( 'people' ) ?></h3>
    <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
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
                <th class="td" scope="row" colspan="2" style="text-align:left;"><?php echo $person_type_title ?></th>
                <td class="td" style="text-align:left;"><?php echo $person_type_number ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th class="td" scope="row" colspan="2" style="text-align:left;"><?php echo yith_wcbk_get_label( 'total-people' ) ?></th>
            <td class="td" style="text-align:left;"><?php echo $booking->persons ?></td>
        </tr>
        </tfoot>
    </table>
<?php endif ?>

<?php do_action( 'yith_wcbk_email_after_booking_table', $booking, $sent_to_admin, $plain_text, $email ); ?>
