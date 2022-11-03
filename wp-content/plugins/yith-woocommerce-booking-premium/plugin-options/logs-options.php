<?php
// Exit if accessed directly
!defined( 'YITH_WCBK' ) && exit();

$tab = array(
    'logs' => array(
        'logs-tab' => array(
            'type' => 'custom_tab',
            'action' => 'yith_wcbk_print_logs_tab'
        )
    )
);

return apply_filters( 'yith_wcbk_panel_logs_options', $tab );