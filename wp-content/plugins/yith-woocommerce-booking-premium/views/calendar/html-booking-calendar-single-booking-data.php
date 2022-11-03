<?php
/**
 * Single booking data for Month Calendar page html
 *
 * @author  Yithemes
 * @package YITH Booking and Appointment for WooCommerce Premium
 * @version 1.0.0
 */
if ( !defined( 'YITH_WCBK' ) ) {
    exit;
} // Exit if accessed directly
/**
 * @var YITH_WCBK_Booking|YITH_WCBK_Booking_External $booking
 */
$booking_edit_link = $booking->get_edit_link();

?>
<div class="yith-wcbk-booking-calendar-single-booking-data-wrapper">

    <?php do_action( 'yith_wcbk_calendar_single_booking_data_before', $booking ); ?>

    <div class="yith-wcbk-booking-calendar-single-booking-data-actions__container">
        <?php if ( $booking_edit_link ): ?>
            <a href="<?php echo $booking_edit_link; ?>" target="_blank">
                <span class="dashicons dashicons-edit yith-wcbk-booking-calendar-single-booking-data-action yith-wcbk-booking-calendar-single-booking-data-action-edit"></span>
            </a>
        <?php endif; ?>
        <span class="dashicons dashicons-no-alt yith-wcbk-booking-calendar-single-booking-data-action yith-wcbk-booking-calendar-single-booking-data-action-close"></span>
    </div>

    <div class="yith-wcbk-booking-calendar-single-booking-data-title__container">
        <h2><?php echo apply_filters( 'yith_wcbk_calendar_single_booking_data_booking_title', $booking->get_title(), $booking ); ?></h2>
    </div>

    <div class="yith-wcbk-booking-calendar-single-booking-data-table__container">

        <table class="yith-wcbk-booking-calendar-single-booking-data-table">
            <?php do_action( 'yith_wcbk_calendar_single_booking_data_table_before', $booking ); ?>
            <tr>
                <th><?php echo __( 'Status', 'yith-booking-for-woocommerce' ); ?></th>
                <td><?php echo $booking->get_status_text() ?> </td>
            </tr>
            <tr>
                <th><?php echo __( 'Product', 'yith-booking-for-woocommerce' ); ?></th>
                <td>
                    <?php
                    if ( $product = wc_get_product( $booking->get_product_id() ) ) {
                        $product_link  = admin_url( 'post.php?post=' . $booking->get_product_id() . '&action=edit' );
                        $product_title = $product->get_title();
                        $title         = "<a href='{$product_link}'>{$product_title}</a>";
                    } else {
                        $title = sprintf( __( 'Deleted Product #%s', 'yith-booking-for-woocommerce' ), $booking->get_product_id() );
                    }
                    echo $title;
                    ?>
                </td>
            </tr>
            <?php if ( !$booking->is_external() ) : ?>
                <tr>
                    <th><?php echo __( 'Duration', 'yith-booking-for-woocommerce' ) ?></th>
                    <td><?php echo $booking->get_duration_html(); ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th><?php echo __( 'From', 'yith-booking-for-woocommerce' ); ?></th>
                <td><?php echo $booking->get_formatted_date( 'from' ) ?> </td>
            </tr>
            <tr>
                <th><?php echo __( 'To', 'yith-booking-for-woocommerce' ); ?></th>
                <td><?php echo $booking->get_formatted_date( 'to' ) ?> </td>
            </tr>
            <?php if ( !$booking->is_external() ) : ?>
                <?php
                $order_id = $booking->order_id;
                if ( $order_id > 0 ) {

                    if ( $the_order = wc_get_order( $order_id ) ) {
                        $the_order_user_id = yit_get_prop( $the_order, 'user_id', true, 'edit' );
                        $user_info         = !empty( $the_order_user_id ) ? get_userdata( $the_order_user_id ) : false;

                        if ( !!$user_info ) {

                            $username = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

                            if ( $user_info->first_name || $user_info->last_name ) {
                                $username .= esc_html( sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
                            } else {
                                $username .= esc_html( ucfirst( $user_info->display_name ) );
                            }

                            $username .= '</a>';

                        } else {
                            if ( $the_order && ( yit_get_prop( $the_order, 'billing_first_name' ) || yit_get_prop( $the_order, 'billing_last_name' ) ) ) {
                                $username = trim( sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), yit_get_prop( $the_order, 'billing_first_name' ), yit_get_prop( $the_order, 'billing_last_name' ) ) );
                            } else {
                                $username = __( 'Guest', 'woocommerce' );
                            }
                        }

                        $order_info = sprintf( _x( '%s by %s', 'Order number by Username', 'yith-booking-for-woocommerce' ), '<a href="' . admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ) . '">#' . esc_attr( $the_order->get_order_number() ) . '</a>', $username );

                        if ( $the_order && $billing_email = yit_get_prop( $the_order, 'billing_email' ) ) {
                            $order_info .= '<br /><small class="meta email"><a href="' . esc_url( 'mailto:' . $billing_email ) . '">' . esc_html( $billing_email ) . '</a></small>';
                        }
                    } else {
                        $order_info = sprintf( _x( '#%s (deleted)', '#123 (deleted)', 'yith-booking-for-woocommerce' ), $order_id );
                    }
                    ?>
                    <tr>
                        <th><?php echo __( 'Order', 'yith-booking-for-woocommerce' ); ?></th>
                        <td><?php echo $order_info ?> </td>
                    </tr>
                    <?php
                } else {
                    if ( $user_info = get_userdata( $booking->user_id ) ) {
                        $username = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

                        if ( $user_info->first_name || $user_info->last_name ) {
                            $username .= esc_html( sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
                        } else {
                            $username .= esc_html( ucfirst( $user_info->display_name ) );
                        }

                        $username .= '</a>';
                        ?>
                        <tr>
                            <th><?php echo __( 'User', 'yith-booking-for-woocommerce' ); ?></th>
                            <td><?php echo $username ?> </td>
                        </tr> <?php
                    }
                }

                ?>
                <?php if ( $booking->has_persons() ): ?>
                    <tr>
                        <th><?php echo __( 'People', 'yith-booking-for-woocommerce' ); ?></th>
                        <td><?php echo $booking->persons ?> </td>
                    </tr>
                <?php endif ?>

                <?php
                $services = $booking->get_service_names();
                if ( !!$services ) {
                    $services_html = implode( ', ', $services );
                    ?>
                    <tr>
                        <th><?php echo __( 'Services', 'yith-booking-for-woocommerce' ); ?></th>
                        <td><?php echo $services_html ?> </td>
                    </tr> <?php
                }
                ?>

            <?php else:
                $external_extra_data = array(
                    'summary'       => __( 'Summary', 'yith-booking-for-woocommerce' ),
                    'description'   => __( 'Description', 'yith-booking-for-woocommerce' ),
                    'location'      => __( 'Location', 'yith-booking-for-woocommerce' ),
                    'uid'           => __( 'UID', 'yith-booking-for-woocommerce' ),
                    'calendar_name' => __( 'Calendar Name', 'yith-booking-for-woocommerce' ),
                    'source'        => __( 'Source', 'yith-booking-for-woocommerce' ),
                );

                foreach ( $external_extra_data as $key => $label ) {
                    $getter = "get_{$key}";
                    $value  = $booking->$getter();

                    switch ( $key ) {
                        case 'description':
                            $value = nl2br( $value );
                            break;
                        case 'source':
                            $value = YITH_WCBK_Booking_External_Sources()->get_name_from_string( $value );
                            break;
                    }

                    if ( !!$value ) {
                        echo "<tr><th>$label</th><td>$value</td></tr>";
                    }
                }


                ?>

            <?php endif; ?>
            <?php do_action( 'yith_wcbk_calendar_single_booking_data_table_after', $booking ); ?>

        </table>
    </div>

    <?php do_action( 'yith_wcbk_calendar_single_booking_data_after', $booking ); ?>
</div>