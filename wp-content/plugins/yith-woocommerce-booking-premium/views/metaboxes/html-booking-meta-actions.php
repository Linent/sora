<?php
/**
 * Booking Actions Metabox
 *
 * @author  Yithemes
 * @package YITH Booking and Appointment for WooCommerce Premium
 * @version 1.0.0
 */
if ( !defined( 'YITH_WCBK' ) ) {
    exit;
} // Exit if accessed directly

/**
 * @var YITH_WCBK_Booking $booking
 */


$booking_type_object = get_post_type_object( $post->post_type );

$booking_pdf_customer_link = add_query_arg( array(
                                                'action'     => 'get-booking-pdf-customer',
                                                'booking-id' => $booking->id,
                                            ), admin_url() );

$booking_pdf_admin_link = add_query_arg( array(
                                             'action'     => 'get-booking-pdf-admin',
                                             'booking-id' => $booking->id,
                                         ), admin_url() );

?>
<div class="yith-wcbk-booking-actions-metabox-content">
    <p style="text-align: center">
        <a href="<?php echo $booking_pdf_customer_link ?>" class="yith-wcbk-admin-button yith-wcbk-admin-button--small yith-wcbk-admin-button--outline" target="_blank"><?php _e( 'Customer PDF', 'yith-booking-for-woocommerce' ) ?></a>
        <a href="<?php echo $booking_pdf_admin_link ?>" class="yith-wcbk-admin-button yith-wcbk-admin-button--small yith-wcbk-admin-button--outline" target="_blank"><?php _e( 'Admin PDF', 'yith-booking-for-woocommerce' ) ?></a>
    </p>

    <?php if ( YITH_WCBK()->google_calendar_sync->is_enabled() ) : ?>
        <div class="yith-wcbk-booking-actions-metabox-google-calendar">
            <?php
            $sync_status = 'not-sync';
            $date        = '';
            $label       = __( 'not synchronized', 'yith-booking-for-woocommerce' );
            if ( $booking->google_calendar_last_update ) {
                $sync_status = 'sync';
                $date        = date_i18n( wc_date_format() . ' ' . wc_time_format(), $booking->google_calendar_last_update + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
                $label       = __( 'synchronized', 'yith-booking-for-woocommerce' );
            }

            $tip = $label;
            $tip .= $date ? '<br />' . $date : '';

            $icon_url = YITH_WCBK_ASSETS_URL . '/images/google-calendar.png';

            $force_sync_url = YITH_WCBK()->google_calendar_sync->get_action_url( 'sync-booking', array( 'booking_id' => $booking->get_id() ) );
            echo "<div class='yith-wcbk-google-calendar-sync-icon__container'>";
            echo "<img class='yith-wcbk-google-calendar-sync-icon' src='$icon_url' />";
            echo "<span class='yith-wcbk-google-calendar-sync-status $sync_status dashicons dashicons-update tips' data-tip='$tip'></span>";
            echo "</div>";
            echo "<div class='yith-wcbk-google-calendar-sync-force__container'>";
            echo "<a class='yith-wcbk-google-calendar-sync-force' href='$force_sync_url'>" . __( 'force sync', 'yith-booking-for-woocommerce' ) . "</a>";
            echo "</div>";
            ?>
        </div>
    <?php endif; ?>
</div>

<div class="yith-wcbk-booking-actions-metabox-footer">
    <input type="submit" class="yith-wcbk-admin-button tips" name="save"
           value="<?php printf( __( 'Save %s', 'yith-booking-for-woocommerce' ), $booking_type_object->labels->singular_name ); ?>"
           data-tip="<?php printf( __( 'Save/update the %s', 'woocommerce' ), $booking_type_object->labels->singular_name ); ?>"/>
</div>