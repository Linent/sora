<?php
// Exit if accessed directly
!defined( 'YITH_WCBK' ) && exit();

$tab = array(
    'integrations' => array(
        'integrations-tab' => array(
            'type' => 'custom_tab',
            'action' => 'yith_wcbk_print_integrations_tab',
            'hide_sidebar' => true,
        )
    )
);

return apply_filters( 'yith_wcbk_panel_integrations_options', $tab );