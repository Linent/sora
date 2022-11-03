<?php
// Exit if accessed directly
!defined( 'YITH_WCBK' ) && exit();


$price_sync_url = wp_nonce_url( add_query_arg( array( 'yith_wcbk_tools_action'   => 'sync_booking_product_prices',
                                                      'yith_wcbk_tools_redirect' => urlencode( admin_url( 'admin.php?page=yith_wcbk_panel&tab=tools' ) )
                                               ), admin_url() ), 'yith-wcbk-sync-booking-prices' );

$tab = array(
    'tools' => array(
        'tools-options' => array(
            'title' => __( 'Tools', 'yith-booking-for-woocommerce' ),
            'type'  => 'title',
            'desc'  => '',
        ),

        'sync-booking-product-prices' => array(
            'name'             => __( 'Sync Booking Prices', 'yith-booking-for-woocommerce' ),
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => "<a href='$price_sync_url' class='yith-wcbk-admin-button yith-wcbk-admin-button--green yith-wcbk-admin-button--icon-update'>" . __( 'Sync Booking Product Prices', 'yith-booking-for-woocommerce' ) . "</a>",
        ),

        'booking-cache' => array(
            'name'             => __( 'Booking Cache', 'yith-booking-for-woocommerce' ),
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'yith-display-row' => true,
            'html'             => yith_plugin_fw_get_field( array(
                                                                'type'  => 'onoff',
                                                                'id'    => 'yith-wcbk-cache-enabled',
                                                                'name'  => 'yith-wcbk-cache-enabled',
                                                                'value' => YITH_WCBK()->settings->is_cache_enabled() ? 'yes' : 'no',
                                                            ) )
                .
                "<input type='hidden' name='yith-wcbk-cache-check-for-transient-creation' value='yes'/>",
            'desc'             => __( 'If enabled, booking data are stored in cache to speed up the site.', 'yith-booking-for-woocommerce' ) . "<br />" .
                __( 'Important: we suggest to <strong>keep it enabled</strong>; disable it only for testing purpose.', 'yith-booking-for-woocommerce' ) . "<br />" .
                __( 'Please note: you can disable this option only for 24 hours; so it will be automatically activated 24 hours after disabling it.', 'yith-booking-for-woocommerce' )
        ),

        'general-options-end' => array(
            'type' => 'sectionend',
            'id'   => 'yith-wcbk-general-options'
        ),

    )
);


if ( has_filter( 'yith_wcbk_is_cache_enabled' ) ) {
    $tab[ 'tools' ][ 'booking-cache' ][ 'desc' ] .= "<br />";
    $tab[ 'tools' ][ 'booking-cache' ][ 'desc' ] .= "<strong style='color:#e47400'>" . sprintf( 'Warning: value overridden through %s filter', '<code>yith_wcbk_is_cache_enabled</code>' ) . "</strong>";
}

return apply_filters( 'yith_wcbk_panel_tools_options', $tab );