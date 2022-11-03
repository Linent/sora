<?php
/**
 * Bookings
 *
 * Shows booking on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/bookings-table.php.
 *
 * @var YITH_WCBK_Booking[] $bookings
 * @var bool                $has_bookings
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

$account_bookings_columns = array(
    'booking-id'      => __( 'Booking', 'yith-booking-for-woocommerce' ),
    //'booking-order'   => __( 'Order', 'yith-booking-for-woocommerce' ),
    'booking-from'    => __( 'From', 'yith-booking-for-woocommerce' ),
    'booking-to'      => __( 'To', 'yith-booking-for-woocommerce' ),
    'booking-status'  => __( 'Status', 'yith-booking-for-woocommerce' ),
    'booking-actions' => '&nbsp;',

);
$account_bookings_columns = apply_filters( 'yith_wcbk_my_account_booking_columns', $account_bookings_columns );
?>
<?php do_action( 'yith_wcbk_before_bookings_table', $has_bookings ); ?>

<?php if ( $has_bookings ) : ?>

    <table class="shop_table shop_table_responsive my_account_bookings account-bookings-table">
        <thead>
        <tr>
            <?php foreach ( $account_bookings_columns as $column_id => $column_name ) : ?>
                <th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
            <?php endforeach; ?>
        </tr>
        </thead>

        <tbody>
        <?php foreach ( $bookings as $booking ) :
            $order = wc_get_order( $booking->order_id );
            ?>
            <tr class="booking">
                <?php foreach ( $account_bookings_columns as $column_id => $column_name ) : ?>
                    <td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
                        <?php if ( has_action( 'yith_wcbk_my_account_booking_column_' . $column_id ) ) {
                            do_action( 'yith_wcbk_my_account_booking_column_' . $column_id, $order, $booking );
                        } else {
                            switch ( $column_id ) {
                                case 'booking-id':
                                    $url   = esc_url( $booking->get_view_booking_url() );
                                    $title = $booking->get_title();
                                    echo "<a href='$url'>$title</a>";
                                    break;
                                case 'booking-order':
                                    $url   = esc_url( $order->get_view_order_url() );
                                    $title = _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number();
                                    echo "<a href='$url'>$title</a>";
                                    break;
                                case 'booking-from':
                                    echo $booking->get_formatted_date( 'from' );
                                    break;
                                case 'booking-to':
                                    echo $booking->get_formatted_date( 'to' );
                                    break;
                                case 'booking-status':
                                    echo $booking->get_status_text();
                                    break;
                                case 'booking-actions':
                                    do_action( 'yith_wcbk_show_booking_actions', $booking, true );
                                    break;
                            }
                        }
                        ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php do_action( 'yith_wcbk_before_account_bookings_pagination' ); ?>

<?php endif; ?>

<?php do_action( 'yith_wcbk_after_bookings_table', $has_bookings ); ?>

