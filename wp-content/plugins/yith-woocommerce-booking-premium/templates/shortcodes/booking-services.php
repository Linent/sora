<?php
/**
 * @var WC_Product_Booking $product
 * @var string             $type
 * @var string             $show_title
 * @var string             $show_prices
 * @var string             $show_descriptions
 */

$services_labels = array(
    'additional' => yith_wcbk_get_label( 'additional-services' ),
    'included'   => yith_wcbk_get_label( 'included-services' ),
);
$services_labels = apply_filters( 'yith_wcbk_shortcode_services_labels', $services_labels, $product );

?>
<div class="yith-wcbk-shortcode-services-wrapper">
    <?php
    if ( $product->has_services() ) {
        $services = $product->get_service_ids();
        if ( !!$services && is_array( $services ) ) {

            $services_to_display = array(
                'additional' => array(),
                'included'   => array(),
            );

            foreach ( $services as $service_id ) {
                $service = yith_get_booking_service( $service_id );

                if ( !$service->is_valid() || $service->is_hidden() )
                    continue;

                if ( $service->is_optional() ) {
                    $services_to_display[ 'additional' ][] = $service;
                } else {
                    $services_to_display[ 'included' ][] = $service;
                }
            }

            if ( 'all' !== $type ) {
                if ( isset( $services_to_display[ $type ] ) ) {
                    $services_to_display = array(
                        $type => $services_to_display[ $type ]
                    );
                } else {
                    $services_to_display = array();
                }
            }

            foreach ( $services_to_display as $key => $current_services ) {
                if ( !!$current_services ) {
                    $_key = sanitize_key( $key );
                    echo "<div class='yith-wcbk-shortcode-services yith-wcbk-shortcode-services-{$_key}'>";

                    if ( 'yes' === $show_title && !empty( $services_labels[ $key ] ) ) {
                        echo "<h3 class='yith-wcbk-shortcode-services__title'>{$services_labels[$key]}</h3>";
                    }

                    foreach ( $current_services as $service ) {
                        /** @var YITH_WCBK_Service $service */
                        $title    = $service->get_name();
                        $help_tip = '';
                        $info     = '';

                        if ( 'yes' === $show_descriptions ) {
                            if ( $description = $service->get_description() ) {
                                $info .= "<div class='yith-wcbk-booking-service__description'>{$description}</div>";
                            }
                        }

                        if ( 'yes' === $show_prices ) {
                            $pricing = $service->get_pricing_html( $product );
                            $info    .= "<div class='yith-wcbk-booking-service__pricing'>{$pricing}</div>";
                        }

                        if ( $info ) {
                            $help_tip = yith_wcbk_print_field( array(
                                                                   'type'  => 'help-tip-alt',
                                                                   'value' => $info,
                                                               ), false );
                        }

                        echo "<div class='yith-wcbk-shortcode-service yith-wcbk-shortcode-service--{$service->slug}'><span class='yith-wcbk-shortcode-service__title'>$title</span>$help_tip</div>";
                    }

                    echo '</div>';
                }
            }
        }
    }

    ?>
</div>