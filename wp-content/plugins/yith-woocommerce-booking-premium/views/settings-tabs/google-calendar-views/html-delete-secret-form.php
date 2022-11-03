<?php
/**
 * @var string $nonce
 */
!defined( 'YITH_WCBK' ) && exit();
?>
<form method='POST' class='yith-wcbk-google-calendar-action'>
    <input type='hidden' name='yith-wcbk-google-calendar-action' value='delete-client-secret'/>
    <input type='submit' value='<?php _e( 'Delete Client Secret', 'yith-booking-for-woocommerce' ) ?>' class='yith-wcbk-admin-button yith-wcbk-admin-button--red'>
    <?php echo $nonce ?>
</form>