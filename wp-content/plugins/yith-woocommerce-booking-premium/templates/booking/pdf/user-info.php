<?php
/**
 * @var YITH_WCBK_Booking $booking
 * @var bool              $is_admin
 */

if ( !$booking->user_id || apply_filters('yith_wcbk_show_user_info_in_pdf_only_for_admin',!$is_admin ) ) {
    return;
}

$user_id = absint( $booking->user_id );
$user    = get_user_by( 'id', $user_id );
if ( !$user )
    return;

$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
$user_link   = get_edit_user_link( $user_id );
?>
<h3><?php _e( 'User info', 'yith-booking-for-woocommerce' ) ?></h3>
<table class="booking-table booking-user-info">
    <tr>
        <th scope="row"><?php echo __( 'User', 'yith-booking-for-woocommerce' ) ?></th>
        <td><a href="<?php echo $user_link; ?>"><?php echo $user->nickname ?></a></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __( 'First Name', 'yith-booking-for-woocommerce' ) ?></th>
        <td><?php echo esc_html( $user->user_firstname ) ?></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __( 'Last Name', 'yith-booking-for-woocommerce' ) ?></th>
        <td><?php echo esc_html( $user->user_lastname ) ?></td>
    </tr>
    <tr>
        <th scope="row"><?php echo __( 'Email', 'yith-booking-for-woocommerce' ) ?></th>
        <td><a href="mailto:<?php echo esc_html( $user->user_email ) ?>"><?php echo esc_html( $user->user_email ) ?></a></td>
    </tr>

</table>
