<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$fields = array();

$service_taxonomy_info = YITH_WCBK_Service_Tax_Admin::get_service_taxonomy_fields();
$name_prefix           = 'yith_booking_service_data';

foreach ( $service_taxonomy_info as $key => $args ) {
    $type              = $args[ 'type' ];
    $custom_attributes = isset( $args[ 'custom_attributes' ] ) ? $args[ 'custom_attributes' ] : '';

    if ( isset( $args[ 'name' ] ) ) {
        $_name = $args[ 'name' ];
        if ( ( $_p = strpos( $_name, '[' ) ) > 0 ) {
            $_first_key = substr( $_name, 0, $_p );
            $_other_key = substr( $_name, $_p );
            $name       = sprintf( "%s[%s]%s", $name_prefix, $_first_key, $_other_key );
        }

    } else {
        $name = sprintf( "%s[%s]", $name_prefix, $key );
    }

    $extra_class    = '';
    $container_data = array();
    if ( isset( $args[ 'field_deps' ] ) && isset( $args[ 'field_deps' ][ 'id' ] ) ) {
        $extra_class                  .= 'yith-wcbk-show-conditional';
        $container_data[ 'field-id' ] = 'yith_booking_service_' . $args[ 'field_deps' ][ 'id' ];
        if ( isset( $args[ 'field_deps' ][ 'value' ] ) ) {
            $container_data[ 'value' ] = $args[ 'field_deps' ][ 'value' ];
        }
    }

    $fields[] = array(
        'type'             => 'section',
        'section_html_tag' => 'div',
        'class'            => 'form-field yith-wcbk-booking-service-form-section yith-wcbk-booking-service-form-section-' . $type . ' ' . $extra_class,
        'data'             => $container_data,
        'fields'           => array(
            array(
                'type'              => $type,
                'title'             => $args[ 'title' ],
                'value'             => isset( $args[ 'default' ] ) ? $args[ 'default' ] : '',
                'id'                => 'yith_booking_service_' . $key,
                'class'             => isset( $args[ 'class' ] ) ? $args[ 'class' ] : '',
                'name'              => $name,
                'custom_attributes' => $custom_attributes,
                'help_tip'          => $args[ 'desc' ],
                'options'           => isset( $args[ 'options' ] ) ? $args[ 'options' ] : array(),
            )
        )
    );
}

yith_wcbk_print_fields( $fields );