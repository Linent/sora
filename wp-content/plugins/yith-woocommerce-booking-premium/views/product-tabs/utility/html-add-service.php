<?php
/**
 */
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

$name_prefix           = 'yith_booking_service_data';


yith_wcbk_product_metabox_form_field( array(
                                          'title'  => __( 'Service name', 'yith-booking-for-woocommerce' ),
                                          'fields' => array(
                                              array(
                                                  'type'  => 'text',
                                                  'value' => '',
                                                  'class' => 'yith-wcbk-fake-form-field',
                                                  'data'  => array(
                                                      'name'     => 'title',
                                                      'required' => 'yes',
                                                  ),
                                              )
                                          )
                                      ) );

yith_wcbk_product_metabox_form_field( array(
                                          'title'  => __( 'Description', 'yith-booking-for-woocommerce' ),
                                          'fields' => array(
                                              array(
                                                  'type'  => 'textarea',
                                                  'value' => '',
                                                  'class' => 'yith-wcbk-fake-form-field',
                                                  'data'  => array(
                                                      'name' => 'description',
                                                  ),
                                              )
                                          )
                                      ) );


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

    yith_wcbk_product_metabox_form_field( array(
                                              'title'  => $args[ 'title' ],
                                              'class'  => $extra_class,
                                              'data'   => $container_data,
                                              'desc'   => $args[ 'desc' ],
                                              'fields' => array(
                                                  array(
                                                      'type'              => $type,
                                                      'id'                => 'yith_booking_service_' . $key,
                                                      'value'             => isset( $args[ 'default' ] ) ? $args[ 'default' ] : '',
                                                      'class'             => 'yith-wcbk-fake-form-field',
                                                      'data'              => array(
                                                          'name' => $name,
                                                      ),
                                                      'custom_attributes' => $custom_attributes,
                                                      'options'           => isset( $args[ 'options' ] ) ? $args[ 'options' ] : array(),

                                                  )
                                              )
                                          ) );
}

?>
<input type="hidden" class="yith-wcbk-fake-form-field" data-name="security" value="<?php echo wp_create_nonce( 'create-service' ) ?>">