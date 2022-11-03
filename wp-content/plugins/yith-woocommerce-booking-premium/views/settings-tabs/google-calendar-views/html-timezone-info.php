<?php
/**
 * @var bool   $is_calendar_sync_enabled
 * @var string $google_calendar_timezone
 * @var string $current_timezone
 */
!defined( 'YITH_WCBK' ) && exit();
?>

<?php if ( $is_calendar_sync_enabled ) : ?>
    <div class='yith-wcbk-google-calendar-timezone-info'>
        <?php
        if ( $google_calendar_timezone != $current_timezone ) {
            echo sprintf( __( 'Please note: your WordPress Timezone <code>%1$s</code> is different by your Google Calendar Timezone <code>%2$s</code>', 'yith-booking-for-woocommerce' ), $current_timezone, $google_calendar_timezone );
        } else {
            echo sprintf( __( 'Timezone <code>%s</code>', 'yith-booking-for-woocommerce' ), $current_timezone );
        }
        ?>
    </div>
<?php endif ?>