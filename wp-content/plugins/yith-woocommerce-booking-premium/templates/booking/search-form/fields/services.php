<?php
/**
 * Booking Search Form Field Services
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/fields/services.php.
 *
 * @var YITH_WCBK_Search_Form $search_form
 */

!defined( 'YITH_WCBK' ) && exit;
?>

<?php
$services          = YITH_WCBK()->service_helper->get_services();
$searched_services = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( "services" );
$searched_services = !!$searched_services && is_array( $searched_services ) ? $searched_services : array();

if ( $services && is_array( $services ) ) :
    foreach ( $services as $service ) :
        if ( $service->is_hidden_in_search_forms() )
            continue;
        ?>
        <tr class="yith-wcbk-booking-search-form-row-services yith-wcbk-booking-search-form-row-service-<?php echo $service->id ?>">
            <td class="yith-wcbk-booking-search-form-label">
                <?php echo $service->get_name() ?>
            </td>
            <td class="yith-wcbk-booking-search-form-input">
                <?php

                $field = array(
                    'id'             => "yith-wcbk-booking-search-form-service-{$service->id}",
                    'type'           => 'checkbox-alt',
                    'name'           => "services[]",
                    'checkbox_value' => $service->id,
                    'value'          => in_array( $service->id, $searched_services ) ? $service->id : 'no',
                    'class'          => 'yith-wcbk-booking-service',
                    'data'           => array(
                        'service-id' => $service->id,
                    ),
                );
                yith_wcbk_print_field( $field );
                ?>
            </td>
        </tr>

    <?php endforeach; ?>
<?php endif; ?>
