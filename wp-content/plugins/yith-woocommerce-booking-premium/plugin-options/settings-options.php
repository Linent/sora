<?php
// Exit if accessed directly
!defined( 'YITH_WCBK' ) && exit();


$categories = YITH_WCBK()->wp->get_terms(
    array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'fields'     => 'id=>name'
    )
);

$tab = array(
    'settings' => array(
        'general-options' => array(
            'title' => __( 'General Options', 'yith-booking-for-woocommerce' ),
            'type'  => 'title',
            'desc'  => '',
        ),

        'theme-action' => array(
            'name'             => __( 'YITH Booking Theme', 'yith-booking-for-woocommerce' ),
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => '',
        ),

        'google-maps-api-key' => array(
            'id'      => 'yith-wcbk-google-maps-api-key',
            'name'    => __( 'Google Maps API Key', 'yith-booking-for-woocommerce' ),
            'type'    => 'text',
            'desc'    => sprintf( __( 'Insert the Google Maps API Key. %1$sLearn how to use the Google Maps API Key%2$s', 'yith-booking-for-woocommerce' ),
                                  '<a href="https://docs.yithemes.com/yith-woocommerce-booking/premium-version-settings/google-map-api-key/" target="_blank">',
                                  '</a>' ),
            'default' => ''
        ),

        'google-maps-geocode-api-key' => array(
            'id'      => 'yith-wcbk-google-maps-geocode-api-key',
            'name'    => __( 'Google Maps Geocode API Key', 'yith-booking-for-woocommerce' ),
            'type'    => 'text',
            'desc'    => sprintf( __( 'Insert the Google Maps API Key for Geocode. %1$sLearn how to use the Google Maps API Key%2$s', 'yith-booking-for-woocommerce' ),
                                  '<a href="https://docs.yithemes.com/yith-woocommerce-booking/premium-version-settings/google-map-api-key/" target="_blank">',
                                  '</a>' ),
            'default' => ''
        ),

        'booking-categories' => array(
            'id'        => 'yith-wcbk-booking-categories',
            'name'      => __( 'Booking Categories', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'select-buttons',
            'multiple'  => true,
            'desc'      => implode( '<br />', array(
                __( 'Choose the categories of the booking products that will be visible in the Search Form.', 'yith-booking-for-woocommerce' ),
                __( 'Leave empty to select all categories.', 'yith-booking-for-woocommerce' )
            ) ),
            'options'   => $categories,
        ),

        'reject-pending-confirmation-booking-after' => array(
            'id'                => 'yith-wcbk-reject-pending-confirmation-bookings-after',
            'name'              => __( 'Reject bookings (days)', 'yith-booking-for-woocommerce' ),
            'type'              => 'number',
            'desc'              => implode( '<br />', array(
                __( 'When this limit is reached, the pending confirmation bookings will be automatically rejected.', 'yith-booking-for-woocommerce' ),
                __( 'Leave blank to disable.', 'yith-booking-for-woocommerce' )
            ) ),
            'default'           => '',
            'custom_attributes' => array(
                'min' => 0
            )
        ),

        'complete-paid-bookings-after' => array(
            'id'                => 'yith-wcbk-complete-paid-bookings-after',
            'name'              => __( 'Complete paid bookings (days)', 'yith-booking-for-woocommerce' ),
            'type'              => 'number',
            'desc'              => implode( '<br />', array(
                __( 'When this limit is reached, paid Bookings will be set to Completed automatically when the End Date exceeds the specified number of days.', 'yith-booking-for-woocommerce' ),
                __( 'Leave blank to disable.', 'yith-booking-for-woocommerce' ),
                __( 'Please note: it doesn\'t take into account hours/minutes since the check is executed daily.', 'yith-booking-for-woocommerce' )
            ) ),
            'default'           => '',
            'custom_attributes' => array(
                'min' => 0
            )
        ),

        'booking-style' => array(
            'id'        => 'yith-wcbk-booking-style',
            'name'      => __( 'Style', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'radio',
            'desc'      => __( 'Choose style for Booking Forms, Search Forms, Fields and so on', 'yith-booking-for-woocommerce' ),
            'default'   => 'simple',
            'options'   => array(
                'classic' => __( 'Classic', 'yith-booking-for-woocommerce' ),
                'simple'  => __( 'Simple', 'yith-booking-for-woocommerce' ),
            ),
        ),

        'debug' => array(
            'id'        => 'yith-wcbk-debug',
            'name'      => __( 'Debug', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'desc'      => __( 'If enabled, the plugin will add some bookings-related debug logs that will be available in the YITH > Booking > Logs tab.', 'yith-booking-for-woocommerce' ),
            'default'   => 'no',
        ),

        'general-options-end' => array(
            'type' => 'sectionend',
            'id'   => 'yith-wcbk-general-options'
        ),


        'booking-form-options' => array(
            'title' => __( 'Booking Form', 'yith-booking-for-woocommerce' ),
            'type'  => 'title',
            'desc'  => '',
        ),

        'booking-form-position' => array(
            'id'      => 'yith-wcbk-booking-form-position',
            'name'    => __( 'Booking Form Position', 'yith-booking-for-woocommerce' ),
            'type'    => 'select',
            'desc'    => __( 'Choose the position of the booking form in Single Product Page', 'yith-booking-for-woocommerce' ),
            'options' => array(
                'default'            => __( 'Default', 'yith-booking-for-woocommerce' ),
                'before_summary'     => __( 'Before summary', 'yith-booking-for-woocommerce' ),
                'after_title'        => __( 'After title', 'yith-booking-for-woocommerce' ),
                'before_description' => __( 'Before description', 'yith-booking-for-woocommerce' ),
                'after_description'  => __( 'After description', 'yith-booking-for-woocommerce' ),
                'after_summary'      => __( 'After summary', 'yith-booking-for-woocommerce' ),
                'widget'             => __( 'Use Widget', 'yith-booking-for-woocommerce' ),
                'none'               => __( 'None', 'yith-booking-for-woocommerce' ),
            ),
            'default' => 'after_description'
        ),

        'months-loaded-in-calendar' => array(
            'id'        => 'yith-wcbk-months-loaded-in-calendar',
            'name'      => __( 'Months loaded in calendar', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'number',
            'default'   => '12',
            'min'       => '1',
            'max'       => '12',
            'desc'      => __( 'Choose the number of months loaded in calendar. Other ones will be loaded in AJAX to improve performance (Suggested: 3)',
                               'yith-booking-for-woocommerce' )
        ),

        'date-picker-format' => array(
            'id'        => 'yith-wcbk-date-picker-format',
            'name'      => __( 'Date Picker Format', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'date-format',
            'js'        => true,
            'desc'      => __( 'Choose the date format for date-pickers', 'yith-booking-for-woocommerce' ),
            'default'   => 'yy-mm-dd',
        ),

        'enable-people-selector' => array(
            'id'        => 'yith-wcbk-people-selector-enabled',
            'name'      => __( 'Enable People Selector', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'yes',
            'desc'      => __( 'If enabled, people will be shown in a unique field (suggested).',
                               'yith-booking-for-woocommerce' )
        ),

        'person-type-columns' => array(
            'id'        => 'yith-wcbk-person-type-columns',
            'name'      => __( 'Columns for people', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'number',
            'desc'      => __( 'Choose the number of columns for people fields shown in the Booking form', 'yith-booking-for-woocommerce' ),
            'default'   => '1',
            'min'       => 1,
            'deps'      => array(
                'id'    => 'yith-wcbk-people-selector-enabled',
                'value' => 'no',
                'type'  => 'disable'
            )
        ),

        'enable-unique-calendar-range-picker' => array(
            'id'        => 'yith-wcbk-unique-calendar-range-picker-enabled',
            'name'      => __( 'Enable Unique Calendar Picker', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'yes',
            'desc'      => __( 'If enabled, dates will be shown in a unique field (suggested).', 'yith-booking-for-woocommerce' )
        ),

        'calendar-range-picker-columns' => array(
            'id'        => 'yith-wcbk-calendar-range-picker-columns',
            'name'      => __( 'Columns for calendar range picker', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'radio',
            'desc'      => __( 'Choose the number of columns for calendar range picker fields shown in the Booking form', 'yith-booking-for-woocommerce' ),
            'default'   => '1',
            'options'   => array(
                1 => __( 'One column', 'yith-booking-for-woocommerce' ),
                2 => __( 'Two columns', 'yith-booking-for-woocommerce' ),
            ),
            'deps'      => array(
                'id'    => 'yith-wcbk-unique-calendar-range-picker-enabled',
                'value' => 'no',
                'type'  => 'disable'
            )
        ),

        'display-date-picker-inline' => array(
            'id'        => 'yith-wcbk-display-date-picker-inline',
            'name'      => __( 'Display Date Picker inline', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
            'desc'      => implode( '<br />', array(
                __( 'If enabled, the date-picker will be embedded in the page instead of showing as overlay.', 'yith-booking-for-woocommerce' ),
                __( 'Please note: this will affect the single date-pickers only; the "Unique Calendar Picker" will never be displayed inline.', 'yith-booking-for-woocommerce' ),
            ) )
        ),

        'show-service-prices' => array(
            'id'            => 'yith-wcbk-show-service-prices',
            'name'          => __( 'Show', 'yith-booking-for-woocommerce' ),
            'type'          => 'checkbox',
            'desc'          => __( 'Prices for services', 'yith-booking-for-woocommerce' ),
            'default'       => 'no',
            'checkboxgroup' => 'start'
        ),

        'show-service-descriptions' => array(
            'id'            => 'yith-wcbk-show-service-descriptions',
            'desc'          => __( 'Descriptions for services', 'yith-booking-for-woocommerce' ),
            'type'          => 'checkbox',
            'default'       => 'no',
            'checkboxgroup' => ''
        ),

        'show-included-services' => array(
            'id'            => 'yith-wcbk-show-included-services',
            'type'          => 'checkbox',
            'desc'          => __( 'Included services', 'yith-booking-for-woocommerce' ),
            'default'       => 'yes',
            'checkboxgroup' => ''
        ),

        'show-totals' => array(
            'id'            => 'yith-wcbk-show-totals',
            'type'          => 'checkbox',
            'desc'          => __( 'Totals', 'yith-booking-for-woocommerce' ),
            'default'       => 'no',
            'checkboxgroup' => 'end'
        ),

        'show-booking-form-to-logged-users-only' => array(
            'id'        => 'yith-wcbk-show-booking-form-to-logged-users-only',
            'name'      => __( 'Show to logged users only', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'desc'      => __( 'If enabled, show the booking form to logged users only', 'yith-booking-for-woocommerce' ),
            'default'   => 'no',
        ),

        'check-min-max-duration-in-calendar' => array(
            'id'        => 'yith-wcbk-check-min-max-duration-in-calendar',
            'name'      => __( 'Check min/max duration', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'desc'      => __( 'If enabled, the plugin considers the minimum and maximum duration to show available dates in the calendar', 'yith-booking-for-woocommerce' ),
            'default'   => 'yes',
        ),

        'ajax-update-non-available-dates-on-load' => array(
            'id'        => 'yith-wcbk-ajax-update-non-available-dates-on-load',
            'name'      => __( ' Update non-available dates on loading (AJAX)', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'desc'      => __( 'If enabled, the plugin will update non-available dates in the calendar on page loading. You should activate it only if you use plugins to cache product pages', 'yith-booking-for-woocommerce' ),
            'default'   => 'no',
        ),

        'disable-day-if-no-time-available' => array(
            'id'        => 'yith-wcbk-disable-day-if-no-time-available',
            'name'      => __( 'Disable day if no time is available', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'desc'      => implode( '<br />', array(
                __( 'If enabled, hide days in calendar if no time is available.', 'yith-booking-for-woocommerce' ),
                __( 'Please note: enabling this option the calendar will show up to 3 months for hourly bookings and up to 1 month for per-minute bookings', 'yith-booking-for-woocommerce' )
            ) ),
            'default'   => 'no',
        ),

        'booking-form-options-end' => array(
            'type' => 'sectionend',
        ),

        'calendar-options' => array(
            'title' => __( 'Calendar', 'yith-booking-for-woocommerce' ),
            'type'  => 'title',
            'desc'  => '',
        ),

        'calendar-day-default-time-step' => array(
            'id'      => 'yith-wcbk-calendar-day-default-time-step',
            'name'    => __( 'Default Time Step', 'yith-booking-for-woocommerce' ),
            'type'    => 'select',
            'desc'    => __( 'Choose the default time step in Daily Calendar', 'yith-booking-for-woocommerce' ),
            'default' => '1h',
            'options' => YITH_WCBK_Booking_Calendar::get_time_steps()
        ),

        'calendar-day-default-start-time' => array(
            'id'      => 'yith-wcbk-calendar-day-default-start-time',
            'name'    => __( 'Default Start Time', 'yith-booking-for-woocommerce' ),
            'type'    => 'text',
            'desc'    => __( 'Choose the default start time in Daily Calendar (format: hh:mm)', 'yith-booking-for-woocommerce' ),
            'default' => '00:00',
        ),

        'calendar-options-end' => array(
            'type' => 'sectionend',
        ),

        'external-calendars-options' => array(
            'title' => __( 'External Calendars', 'yith-booking-for-woocommerce' ),
            'type'  => 'title',
            'desc'  => '',
        ),

        'external-calendars-sync-expiration' => array(
            'id'      => 'yith-wcbk-external-calendars-sync-expiration',
            'name'    => __( 'Sync Expiration', 'yith-booking-for-woocommerce' ),
            'type'    => 'select',
            'desc'    => __( 'Choose the sync expiration for external calendars', 'yith-booking-for-woocommerce' ),
            'default' => 6 * HOUR_IN_SECONDS,
            'options' => YITH_WCBK_Booking_Externals::get_sync_expiration_times()
        ),

        'external-calendars-show-externals-in-calendar' => array(
            'id'        => 'yith-wcbk-external-calendars-show-externals-in-calendar',
            'name'      => __( 'Show externals in calendar', 'yith-booking-for-woocommerce' ),
            'type'      => 'yith-field',
            'desc'      => __( 'If enabled, you\'ll see also external bookings (set in the Booking Sync tab of your products) in Booking Calendar', 'yith-booking-for-woocommerce' ),
            'yith-type' => 'onoff',
            'default'   => 'no',
        ),

        'external-calendars-options-end' => array(
            'type' => 'sectionend',
        ),
    )
);

/** YITH Booking Theme Options */
$theme_html = '';
if ( current_user_can( 'switch_themes' ) && current_user_can( 'edit_theme_options' ) ) {
    $theme_buttons   = array();
    $theme_is_active = false;
    $message         = '';
    $force_update    = isset( $_GET[ 'yith-wcbk-force-theme-update' ] );
    if ( !YITH_WCBK()->theme->has_booking_theme() ) {
        if ( current_user_can( 'install_themes' ) ) {
            $theme_buttons[ 'install' ] = array(
                'label' => __( 'Install', 'yith-booking-for-woocommerce' ),
                'url'   => YITH_WCBK()->theme->get_install_url(),

            );
        }
    } elseif ( !YITH_WCBK()->theme->is_booking_theme_allowed() ) {
        $message = __( '<strong>YITH Booking</strong> theme installed, but you cannot activate it. If you are running a Multi Site installation, please enable it in <em>Network Admin -> Themes</em>.', 'yith-booking-for-woocommerce' );
    } elseif ( !YITH_WCBK()->theme->has_booking_theme_active() ) {
        $theme_buttons[ 'activate' ] = array(
            'label' => __( 'Activate', 'yith-booking-for-woocommerce' ),
            'url'   => YITH_WCBK()->theme->get_activate_url(),

        );
    } else {
        $theme_is_active = true;
    }

    if ( current_user_can( 'update_themes' ) && YITH_WCBK()->theme->has_booking_theme() && ( YITH_WCBK()->theme->booking_theme_needs_update() || $force_update ) ) {
        $theme_buttons[ 'update' ] = array(
            'label' => !$force_update ? sprintf( __( 'Update (%s)', 'yith-booking-for-woocommerce' ), YITH_WCBK()->theme->get_package_theme_version() ) : __( 'Update', 'yith-booking-for-woocommerce' ),
            'url'   => YITH_WCBK()->theme->get_update_url(),

        );
    }

    if ( $theme_is_active ) {
        $theme_html .= "<span class='yith-wcbk-settings-theme-actions__active-status'>" . __( 'Active', 'yith-booking-for-woocommerce' ) . "</span>";
        $theme_html .= "<span class='description'>" . sprintf( __( 'You are currently using our %1$s theme. You can change theme in %2$sAppearance > Themes%3$s', 'yith-booking-for-woocommerce' ),
                                                               '<strong>YITH Booking</strong>',
                                                               '<a href="' . admin_url( 'themes.php' ) . '">',
                                                               '</a>'
            ) . "</span>";
    }

    if ( $theme_buttons ) {
        foreach ( $theme_buttons as $theme_button_key => $theme_button ) {
            $label = $theme_button[ 'label' ];
            $url   = $theme_button[ 'url' ];

            if ( $url ) {
                $theme_html .= "<a href='$url' class='yith-wcbk-admin-button yith-wcbk-settings-theme-actions__button-$theme_button_key'>$label</a>";
            } else {
                $theme_html .= "<span class='yith-wcbk-admin-button yith-wcbk-settings-theme-actions__button-$theme_button_key'>$label</span>";
            }
        }
    }

    if ( $message ) {
        $theme_html .= "<div class='yith-wcbk-settings-theme-actions__message'>$message</div>";
    }
}

if ( $theme_html ) {
    $tab[ 'settings' ][ 'theme-action' ][ 'html' ] = "<div class='yith-wcbk-settings-theme-actions__wrapper'>$theme_html</div>";
} else {
    unset( $tab[ 'settings' ][ 'theme-action' ] );
}

return apply_filters( 'yith_wcbk_panel_settings_options', $tab );