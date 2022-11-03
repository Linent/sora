<?php
!defined( 'YITH_WCBK' ) && exit();
?>

<form method="post">
    <div id="yith-wcbk-settings-tab-wrapper" class="integrations">

        <div class="yith-wcbk-settings-section">
            <div class="yith-wcbk-settings-section__title">
                <h3><?php _ex( 'Integrations', 'Settings tab title', 'yith-booking-for-woocommerce' ); ?></h3>
            </div>
            <div class="yith-wcbk-settings-section__content">
                <div id="yith-wcbk-integrations-tab-wrapper">
                    <?php do_action( 'yith_wcbk_integrations_tab_contents' ) ?>
                </div>
            </div>
        </div>
    </div>
</form>