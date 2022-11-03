<?php
/**
 * @var string $client_id
 * @var string $client_secret
 * @var string $redirect_uri
 * @var string $nonce
 */
!defined( 'YITH_WCBK' ) && exit();
?>
<form method='POST' enctype='multipart/form-data'>
    <ul class="yith-wcbk-google-calendar-access-steps">
        <li class="active"><?php _e( 'Credentials', 'yith-booking-for-woocommerce' ) ?></li>
        <li><?php _e( 'Access', 'yith-booking-for-woocommerce' ) ?></li>
    </ul>
    <input type='hidden' name='yith-wcbk-google-calendar-action' value='save-credentials'/>
    <div style="margin: 20px 0 30px">
        <input type='text' name='client-id' value='<?php echo $client_id ?>' placeholder="<?php _e( 'Client ID', 'yith-booking-for-woocommerce' ) ?>"/>
        <input type='text' name='client-secret' value='<?php echo $client_secret ?>' placeholder="<?php _e( 'Client Secret', 'yith-booking-for-woocommerce' ) ?>"/>
    </div>
    <?php echo $nonce ?>

    <div class="yith-wcbk-google-calendar-how-to">
        <div style="text-align: left">
            <?php _e( 'To use this integration you should:', 'yith-booking-for-woocommerce' ) ?>
            <ol>
                <li><?php echo sprintf( __( 'create a project in %s<strong>Google Developers Console</strong>%s', 'yith-booking-for-woocommerce' ), "<a href='https://console.developers.google.com' target='_blank'>", "</a>" ) ?></li>
                <li><?php _e( 'enable the <strong>Google Calendar API</strong> in <strong>Your Project > Library</strong>', 'yith-booking-for-woocommerce' ) ?></li>
                <li><?php _e( 'create an <strong>OAuth Client ID</strong> for a <strong>Web application</strong> in <strong>Your Project > Credentials > Create Credentials</strong>', 'yith-booking-for-woocommerce' ) ?></li>
                <li><?php _e( 'add the following URI to the <strong>Allowed Redirect URI</strong> in your ID client OAuth 2.0', 'yith-booking-for-woocommerce' ) ?></li>
            </ol>
        </div>
        <input type="text" id="yith-wcbk-google-calendar-redirect-uri" value="<?php echo $redirect_uri ?>" disabled/>
        <span class="dashicons dashicons-admin-page yith-wcbk-copy-to-clipboard tips" data-selector-to-copy="#yith-wcbk-google-calendar-redirect-uri" data-tip='<?php _e( 'Copy to clipboard', 'yith-booking-for-woocommerce' ) ?>'></span>

    </div>

    <div class='yith-wcbk-google-calendar-actions__container'>
        <div class='yith-wcbk-google-calendar-actions'>
            <input type='submit' value='<?php _e( 'Save', 'yith-booking-for-woocommerce' ) ?>' class='yith-wcbk-admin-button'>
        </div>
    </div>
</form>