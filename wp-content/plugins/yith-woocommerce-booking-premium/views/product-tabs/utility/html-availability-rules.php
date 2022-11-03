<?php
/**
 * Template options in WC Product Panel
 *
 * @author  Yithemes
 * @package YITH Booking and Appointment for WooCommerce Premium
 * @version 1.0.0
 * @var array  $availability_rules
 * @var string $field_name
 */
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly
?>
<div class="yith-wcbk-availability-rules">
    <div class="yith-wcbk-settings-section-box__sortable-container yith-wcbk-availability-rules__list">
        <?php
        $index = 1;
        foreach ( $availability_rules as $key => $availability_rule ) {
            yith_wcbk_get_view( 'product-tabs/utility/html-availability-rule.php', compact( 'field_name', 'index', 'availability_rule' ) );
            $index++;
        } ?>
    </div>
    <div class="yith-wcbk-settings-section__content__actions">
        <span class="yith-wcbk-admin-button yith-wcbk-admin-button--icon-plus yith-wcbk-admin-button--dark yith-wcbk-availability-rules__new-rule" data-template="<?php
        $index             = '{{INDEX}}';
        $availability_rule = new YITH_WCBK_Availability_Rule();
        $add_button        = true;
        ob_start();
        yith_wcbk_get_view( 'product-tabs/utility/html-availability-rule.php', compact( 'field_name', 'index', 'availability_rule', 'add_button' ) );
        echo esc_attr( ob_get_clean() );
        ?>"><?php _e( 'Add new rule', 'yith-booking-for-woocommerce' ); ?></span>
        <div id="yith-wcbk-availability-rules__pre-new-rule"></div>
    </div>
</div>
