<?php
/**
 * Services Functions
 *
 * @author  Yithemes
 * @package YITH Booking and Appointment for WooCommerce Premium
 * @version 1.0.0
 */

!defined( 'YITH_WCBK' ) && exit;

if ( !function_exists( 'yith_wcbk_get_service_type_labels' ) ) {
    function yith_wcbk_get_service_type_labels() {
        $services_labels = array(
            'additional' => yith_wcbk_get_label( 'additional-services' ),
            'included'   => yith_wcbk_get_label( 'included-services' ),
        );
        return apply_filters( 'yith_wcbk_get_service_type_labels', $services_labels );
    }
}

if ( !function_exists( 'yith_wcbk_split_services_by_type' ) ) {
    function yith_wcbk_split_services_by_type( $services, $include_hidden = true ) {
        $splitted_services = array(
            'additional' => array(),
            'included'   => array(),
        );
        if ( !!$services && is_array( $services ) ) {
            foreach ( $services as $service_id ) {
                $service = yith_get_booking_service( $service_id );

                if ( !$service->is_valid() || ( !$include_hidden && $service->is_hidden() ) )
                    continue;

                if ( $service->is_optional() ) {
                    $splitted_services[ 'additional' ][] = $service;
                } else {
                    $splitted_services[ 'included' ][] = $service;
                }
            }
        }

        return apply_filters( 'yith_wcbk_split_services_by_type', $splitted_services );
    }
}

if ( !function_exists( 'yith_wcbk_booking_services_html' ) ) {
    /**
     * @param array $services
     *
     * @return string
     */
    function yith_wcbk_booking_services_html( $services ) {
        $separator = apply_filters( 'yith_wcbk_booking_services_separator', ', ' );
        return apply_filters( 'yith_wcbk_booking_services_html', implode( $separator, $services ) );
    }
}