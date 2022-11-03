<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$service_taxonomy_info = YITH_WCBK_Service_Tax_Admin::get_service_taxonomy_fields();
$name_prefix           = 'yith_booking_service_data';

/**
 * @var YITH_WCBK_Service $service the booking service
 */


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
    $args[ 'name' ] = $name;

    $container_class     = 'form-field ';
    $container_data_html = '';

    if ( isset( $args[ 'field_deps' ] ) && isset( $args[ 'field_deps' ][ 'id' ] ) ) {
        $container_class     .= 'yith-wcbk-show-conditional';
        $container_data_html .= " data-field-id='yith_booking_service_{$args[ 'field_deps' ][ 'id' ]}'";
        if ( isset( $args[ 'field_deps' ][ 'value' ] ) ) {
            $container_data[ 'value' ] = $args[ 'field_deps' ][ 'value' ];
            $container_data_html       .= " data-value='{$args[ 'field_deps' ][ 'value' ]}'";
        }
    }


    $args[ 'id' ] = "yith_booking_service_{$key}";

    if ( isset( $args[ 'person_type_id' ] ) ) {
        $args[ 'value' ] = $service->get_price_for_person_type( $args[ 'person_type_id' ] );
    } else {
        $args[ 'value' ] = $service->$key;
    }
    ?>

    <tr class="<?php echo $container_class ?>" <?php echo $container_data_html ?>>
        <th scope="row" valign="top">
            <label for="yith_booking_service_<?php echo $key; ?>"><?php echo $args[ 'title' ] ?></label>
        </th>
        <td>
            <?php
            if ( isset( $args[ 'title' ] ) ) unset( $args[ 'title' ] );
            yith_wcbk_print_field( $args );
            ?>
        </td>
    </tr>

    <?php

}
?>