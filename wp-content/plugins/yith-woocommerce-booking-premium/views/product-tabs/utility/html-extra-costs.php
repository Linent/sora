<?php
/**
 * Template options in WC Product Panel
 *
 * @var array $extra_costs The product extra costs
 */
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

$global_extra_cost_ids = YITH_WCBK()->extra_cost_helper->get_extra_costs();
?>

<div id="yith-wcbk-extra-costs__list">
    <?php

    foreach ( $global_extra_cost_ids as $extra_cost_id ) {
        $extra_cost_args = isset( $extra_costs[ $extra_cost_id ] ) ? $extra_costs[ $extra_cost_id ] : array( 'id' => $extra_cost_id );
        $extra_cost      = yith_wcbk_product_extra_cost( $extra_cost_args );

        yith_wcbk_get_view( 'product-tabs/utility/html-extra-cost.php', compact( 'extra_cost' ) );
    }

    // Custom Extra Costs
    $idx = 1;
    foreach ( $extra_costs as $extra_cost_identifier => $extra_cost ) {
        $extra_cost = yith_wcbk_product_extra_cost( $extra_cost );
        if ( $extra_cost->is_custom() ) {
            $index = '_' . $idx;
            yith_wcbk_get_view( 'product-tabs/utility/html-extra-cost-custom.php', compact( 'extra_cost', 'index' ) );
            $idx++;
        }
    }
    ?>
</div>

<div class="yith-wcbk-settings-section__content__actions">
        <span id="yith-wcbk-extra-costs__new-extra-cost" class="yith-wcbk-admin-button yith-wcbk-admin-button--icon-plus yith-wcbk-admin-button--dark" data-template="<?php
        $index      = '_{{INDEX}}';
        $extra_cost = yith_wcbk_product_extra_cost( array( 'id' => 0 ) );
        ob_start();
        yith_wcbk_get_view( 'product-tabs/utility/html-extra-cost-custom.php', compact( 'index', 'extra_cost' ) );
        echo esc_attr( ob_get_clean() );
        ?>"><?php _e( 'Add a new cost', 'yith-booking-for-woocommerce' ) ?></span>
</div>