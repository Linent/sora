<?php
// Exit if accessed directly
!defined( 'YITH_WCBK' ) && exit();

$default_labels = YITH_WCBK()->language->get_default_labels();

$label_options[ 'label-options' ] = array(
    'title' => __( 'Labels', 'yith-booking-for-woocommerce' ),
    'type'  => 'title',
    'desc'  => '',
    'id'    => 'yith-wcbk-label-options'
);


foreach ( $default_labels as $key => $label ) {
    $label_options[ $key ] = array(
        'id'          => 'yith-wcbk-label-' . $key,
        'name'        => yith_wcbk_get_default_label( $key ),
        'type'        => 'text',
        'desc'        => implode( '<br />', array(
            sprintf( __( 'Choose the text of "%s" label for booking products.', 'yith-booking-for-woocommerce' ), yith_wcbk_get_default_label( $key ) ),
            __( 'Leave empty to get the default label.', 'yith-booking-for-woocommerce' )
        ) ),
        'default'     => '',
        'placeholder' => yith_wcbk_get_default_label( $key ),
    );
}

$label_options[ 'label-options-end' ] = array(
    'type' => 'sectionend',
    'id'   => 'yith-wcbk-label-options'
);


$tab = array(
    'labels' => $label_options
);

return apply_filters( 'yith_wcbk_panel_label_options', $tab );