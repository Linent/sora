<?php
/**
 * Template options in WC Product Panel
 *
 * @author  Yithemes
 * @package YITH Booking and Appointment for WooCommerce Premium
 * @version 1.0.0
 * @var int   $people_type_id
 * @var array $people_type
 */

!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

$default_args = array(
    'enabled'    => 'no',
    'id'         => $people_type_id,
    'min'        => 0,
    'max'        => 0,
    'base_cost'  => '',
    'block_cost' => '',
    'title'      => ''
);

$args = wp_parse_args( $people_type, $default_args );

/**
 * @var string $enabled
 * @var int    $id
 * @var int    $min
 * @var int    $max
 * @var int    $base_cost
 * @var int    $block_cost
 */
extract( $args );

$default_toggle_class = is_numeric( $id ) ? 'yith-wcbk-settings-section-box--closed' : '';
?>
<div class="yith-wcbk-settings-section-box <?php echo $default_toggle_class ?>">
    <?php yith_wcbk_print_field( array( 'type' => 'hidden', 'name' => "_yith_booking_person_types[$id][id]", 'value' => $id, ) ); ?>

    <div class="yith-wcbk-settings-section-box__title yith-wcbk-settings-section-box__sortable-anchor">
        <h3><?php echo !!$title ? $title : get_the_title( $id ) ?></h3>
        <span class="yith-wcbk-settings-section-box__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
        <span class="yith-wcbk-settings-section-box__enabled"><?php yith_wcbk_print_field( array(
                                                                                               'type'  => 'onoff',
                                                                                               'id'    => 'yith_booking_person_type-enabled-' . $id,
                                                                                               'name'  => "_yith_booking_person_types[$id][enabled]",
                                                                                               'value' => $enabled === 'yes' ? 'yes' : 'no'
                                                                                           ) );
            ?></span>
    </div>
    <div class="yith-wcbk-settings-section-box__content">
        <div class="yith-wcbk-settings-section-box__content__row">
            <label><?php _e( 'Minimum', 'yith-booking-for-woocommerce' ) ?></label>
            <?php yith_wcbk_print_field( array(
                                             'type'              => 'number',
                                             'class'             => 'yith-wcbk-mini-field',
                                             'custom_attributes' => 'step="1" min="0"',
                                             'name'              => "_yith_booking_person_types[$id][min]",
                                             'value'             => $min,
                                         ) );
            ?>

            <label><?php _e( 'Maximum', 'yith-booking-for-woocommerce' ) ?></label>
            <?php yith_wcbk_print_field( array(
                                             'type'              => 'number',
                                             'class'             => 'yith-wcbk-mini-field',
                                             'custom_attributes' => 'step="1" min="0"',
                                             'name'              => "_yith_booking_person_types[$id][max]",
                                             'value'             => $max,
                                         ) );
            ?>
        </div>
        <div class="yith-wcbk-settings-section-box__description">
            <?php _e( 'Enter a minimum number required and a maximum number available of this type. Example: you can set min 1 adult or leave it empty.', 'yith-booking-for-woocommerce' ); ?>
        </div>

        <div class="yith-wcbk-settings-section-box__content__row">
            <label><?php _e( 'Base Price', 'yith-booking-for-woocommerce' ) ?></label>
            <?php yith_wcbk_print_field( array(
                                             'type'  => 'text',
                                             'name'  => "_yith_booking_person_types[$id][block_cost]",
                                             'class' => 'wc_input_price yith-wcbk-mini-field',
                                             'value' => wc_format_localized_price( $block_cost ),
                                         ) );
            ?>

            <label><?php _e( 'Base Fee', 'yith-booking-for-woocommerce' ) ?></label>
            <?php yith_wcbk_print_field( array(
                                             'type'  => 'text',
                                             'name'  => "_yith_booking_person_types[$id][base_cost]",
                                             'class' => 'wc_input_price yith-wcbk-mini-field',
                                             'value' => wc_format_localized_price( $base_cost ),
                                         ) );
            ?>
        </div>
        <div class="yith-wcbk-settings-section-box__description">
            <?php _e( 'Enter a customized base price and fixed base fee for this type. These prices will override prices set in Booking Prices.', 'yith-booking-for-woocommerce' ); ?>
        </div>
    </div>
</div>