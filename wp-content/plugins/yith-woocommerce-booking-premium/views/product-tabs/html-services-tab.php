<?php
/**
 * Template options in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 */
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly


$services         = YITH_WCBK()->service_helper->get_services( array( 'fields' => 'id=>name' ) );
$product_services = $booking_product ? $booking_product->get_service_ids( 'edit' ) : array();
?>

<div class="yith-wcbk-product-metabox-options-panel options_group show_if_<?php echo $prod_type; ?>">
    <div class="yith-wcbk-settings-section">
        <div class="yith-wcbk-settings-section__title">
            <h3><?php _e( 'Services', 'yith-booking-for-woocommerce' ) ?></h3>
        </div>
        <div class="yith-wcbk-settings-section__content yith-wcbk-services__list">
            <?php
            $service_field_html = "<select id='_yith_wcbk_booking_services' name='_yith_booking_services[]' class='multiselect attribute_values wc-enhanced-select' multiple='multiple'
                        placeholder='" . __( 'select one or more services...', 'yith-booking-for-woocommerce' ) . "' style='width:100%;'>";
            foreach ( $services as $service_id => $service_name ) {
                /** @var string $service_name */
                $service_field_html .= "<option value='{$service_id}' " . selected( in_array( $service_id, $product_services ), true, false ) . ">{$service_name}</option>";
            }
            $service_field_html .= "</select>";
            $service_field_html .= "<div class='yith-wcbk-center'>";
            $service_field_html .= "<span class='yith-wcbk-admin-button yith-wcbk-admin-button--dark yith-wcbk-select2-select-all' data-select-id='_yith_wcbk_booking_services'>" . __( 'Select all', 'yith-booking-for-woocommerce' ) . "</span>";
            $service_field_html .= "<span class='yith-wcbk-admin-button yith-wcbk-admin-button--light-grey yith-wcbk-select2-deselect-all' data-select-id='_yith_wcbk_booking_services'>" . __( 'Deselect all', 'yith-booking-for-woocommerce' ) . "</span>";
            $service_field_html .= "</div>";

            yith_wcbk_product_metabox_form_field( array(
                                                      'class'  => 'yith_booking_multi_fields',
                                                      'title'  => __( 'Insert services available for this product', 'yith-booking-for-woocommerce' ),
                                                      'desc'   => __( "Click on the field to add a service available for this product or click on 'Select all' to add all services", 'yith-booking-for-woocommerce' ),
                                                      'fields' => array(
                                                          'type'  => 'html',
                                                          'value' => $service_field_html
                                                      ) ) );
            ?>

            <div class="yith-wcbk-settings-section__content__actions">
                <?php if ( current_user_can( 'manage_' . YITH_WCBK_Post_Types::$service_tax . 's' ) ): ?>
                    <span id="yith-wcbk-services__create-btn" class="yith-wcbk-admin-button yith-wcbk-admin-button--icon-plus yith-wcbk-admin-button--dark"><?php _e( 'Create new service', 'yith-booking-for-woocommerce' ) ?></span>


                    <div id="yith-wcbk-services__create" class="yith-wcbk-settings-section-box yith-wcbk-settings-section-box--no-toggle">
                        <div class="yith-wcbk-settings-section-box__title">
                            <h3><?php _e( 'Create new service', 'yith-booking-for-woocommerce' ) ?></h3>
                        </div>
                        <div class="yith-wcbk-settings-section-box__content">

                            <div id="yith-wcbk-services__create__fields">
                                <?php yith_wcbk_get_view( 'product-tabs/utility/html-add-service.php' ); ?>
                            </div>

                            <div class="yith-wcbk-settings-section-box__content__actions yith-wcbk-right">
                                <span class="yith-wcbk-admin-button yith-wcbk-admin-button--icon-check yith-wcbk-services__create-submit"><?php _e( 'Create', 'yith-booking-for-woocommerce' ) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>