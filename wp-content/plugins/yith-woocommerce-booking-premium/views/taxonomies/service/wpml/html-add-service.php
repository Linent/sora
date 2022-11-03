<?php
/**
 * @var array $languages
 */

$fields = array();

foreach ( $languages as $language_code => $language ) {
    $language_name     = isset( $language[ 'display_name' ] ) ? $language[ 'display_name' ] : $language_code;
    $fields[] = array(
        'type'             => 'section',
        'section_html_tag' => 'div',
        'class'            => 'form-field',
        'fields'           => array(
            array(
                'type'  => 'text',
                'title' => sprintf( __( 'Name (%s)', 'yith-booking-for-woocommerce' ), $language_name ),
                'value' => '',
                'id'    => 'yith_booking_service_wpml_translated_name_' . $language_code,
                'name'  => "yith_booking_service_data[wpml_translated_name][{$language_code}]",
            )
        )
    );
}

echo '<h3>' . __( 'WPML translations', 'yith-booking-for-woocommerce' ) . '</h3>';

YITH_WCBK_Printer()->print_fields( $fields );