<?php
// Exit if accessed directly
!defined( 'YITH_WCBK' ) && exit();

$tab = array(
    'availability-rules' => array(
        'availability-rules-tab' => array(
            'type' => 'custom_tab',
            'action' => 'yith_wcbk_print_global_availability_rules_tab'
        )
    )
);

return apply_filters( 'yith_wcbk_panel_availability_rules_options', $tab );