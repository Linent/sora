<?php
/**
 * Booking form services
 *
 * @author        Leanza Francesco <leanzafrancesco@gmail.com>
 * @var WC_Product_Booking $product
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$default_services = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'booking_services' );
$default_services = !!$default_services && is_string( $default_services ) ? explode( ',', $default_services ) : array();
$default_services = array_filter( array_map( 'absint', $default_services ) );

$services_labels = yith_wcbk_get_service_type_labels();
$services_labels = apply_filters( 'yith_wcbk_booking_form_services_labels', $services_labels, $product );
?>
<div class="yith-wcbk-form-section-services-wrapper">
    <?php
    if ( $product->has_services() ) {
        $services = $product->get_service_ids();
        if ( !!$services && is_array( $services ) ) {

            $services_to_display = yith_wcbk_split_services_by_type( $services, false );
            $services_to_display = apply_filters( 'yith_wcbk_booking_form_services_to_display', $services_to_display, $services, $product );

            $show_included_services = YITH_WCBK()->settings->show_included_services();

            foreach ( $services_to_display as $key => $current_services ) {
                $show           = 'included' !== $key || $show_included_services;
                $css_type_class = 'yith-wcbk-booking-service--type-' . sanitize_key( $key );

                if ( $show && !empty( $services_labels[ $key ] ) && !!$current_services ) {
                    echo "<label class='yith-wcbk-booking-form__label'>{$services_labels[$key]}</label>";
                }
                foreach ( $current_services as $service ) {
                    /**
                     * @var YITH_WCBK_Service $service
                     */
                    $field = array(
                        'id'             => "yith-wcbk-booking-services-{$service->id}",
                        'name'           => "booking_services[]",
                        'checkbox_value' => $service->id,
                        'value'          => in_array( $service->id, $default_services ) ? $service->id : 'no',
                        'class'          => "yith-wcbk-booking-service {$css_type_class}",
                        'data'           => array(
                            'service-id' => $service->id,
                        ),
                    );

                    if ( $service->is_optional() ) {
                        $field[ 'type' ]  = 'checkbox-alt';
                        $field[ 'label' ] = $service->get_name();
                    } else {
                        $field[ 'type' ] = 'hidden';
                        if ( $show ) {
                            $field[ 'title' ] = $service->get_name();
                        }
                    }

                    echo "<div class='yith-wcbk-form-section-service" . (!$show ? ' yith-wcbk-form-section-service--hidden' : '') . "'>";
                    yith_wcbk_print_field( $field );

                    if ( $show && $info = $service->get_info( $product ) ) {
                        $info_html = yith_wcbk_print_field( array(
                                                                'type'  => 'help-tip-alt',
                                                                'value' => $info,
                                                            ), false );

                        echo apply_filters( 'yith_wcbk_booking_form_service_info_html', $info_html, $info, $service, $product );
                    }

                    echo "<div class='yith-wcbk-form-section-service__spacer'></div>";

					if ( $service->is_quantity_enabled() ) {
						$max_qty = $service->get_max_quantity();
						$max_qty = ! ! $max_qty ? $max_qty : '';
						yith_wcbk_print_field( array(
												   'type'   => 'section',
												   'class'  => 'yith-wcbk-booking-service-quantity__container',
												   'fields' => array(
													   'id'                => "yith-wcbk-booking-service-quantity-{$service->id}",
													   'name'              => "booking_service_quantities[{$service->id}]",
													   'type'              => $show ? 'number' : 'hidden',
													   'value'             => $service->get_min_quantity(),
													   'class'             => "yith-wcbk-booking-service-quantity",
													   'custom_attributes' => "min='{$service->get_min_quantity()}' max='{$max_qty}' step='1'",
												   ),
											   ) );
					}

                    echo "</div>";
                }
            }
        }
    }

    ?>
</div>
