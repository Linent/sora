<?php
// Exit if accessed directly
!defined( 'YITH_WCBK' ) && exit();

$tab = array(
    'price-rules' => array(
        'price-rules-tab' => array(
            'type' => 'custom_tab',
            'action' => 'yith_wcbk_print_global_price_rules_tab'
        )
    )
);

return apply_filters( 'yith_wcbk_panel_costs_options', $tab );