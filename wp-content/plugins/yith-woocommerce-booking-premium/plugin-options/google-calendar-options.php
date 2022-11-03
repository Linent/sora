<?php
// Exit if accessed directly
!defined( 'YITH_WCBK' ) && exit();

$tab = array(
    'google-calendar' => array(
        'google-calendar-tab' => array(
            'type' => 'custom_tab',
            'action' => 'yith_wcbk_print_google_calendar_tab',
            'hide_sidebar' => true,
        )
    )
);

return apply_filters( 'yith_wcbk_panel_google_calendar_options', $tab );