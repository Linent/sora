<?php
/**
 * @var int                         $index
 * @var string                      $field_name
 * @var YITH_WCBK_Availability_Rule $availability_rule
 */
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

$default_toggle_class = is_numeric( $index ) ? 'yith-wcbk-settings-section-box--closed' : '';

$_field_name      = "{$field_name}[{$index}]";
$_field_id_prefix = "{$field_name}-id--{$index}__";

$_name          = $availability_rule->get_name( 'edit' );
$_enabled       = $availability_rule->get_enabled( 'edit' );
$_type          = $availability_rule->get_type( 'edit' );
$_from          = $availability_rule->get_from( 'edit' );
$_to            = $availability_rule->get_to( 'edit' );
$_bookable      = $availability_rule->get_bookable( 'edit' );
$_days_enabled  = $availability_rule->get_days_enabled( 'edit' );
$_times_enabled = $availability_rule->get_times_enabled( 'edit' );
$_days          = $availability_rule->get_days( 'edit' );
$_day_time_from = $availability_rule->get_day_time_from( 'edit' );
$_day_time_to   = $availability_rule->get_day_time_to( 'edit' );
?>
<div class="yith-wcbk-settings-section-box yith-wcbk-availability-rule <?php echo $default_toggle_class ?>">
    <?php yith_wcbk_print_field( array( 'type' => 'hidden', 'class' => 'yith-wcbk-settings-section-box__sortable-position', 'name' => $_field_name . '[position]', 'value' => $index ) ); ?>
    <div class="yith-wcbk-settings-section-box__title yith-wcbk-settings-section-box__sortable-anchor">
        <h3><?php echo $_name ? $_name : __( 'Untitled', 'yith-booking-for-woocommerce' ) ?></h3>
        <span class="yith-wcbk-settings-section-box__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
        <span class="yith-wcbk-settings-section-box__enabled"><?php yith_wcbk_print_field( array(
                                                                                               'type'  => 'onoff',
                                                                                               'name'  => $_field_name . '[enabled]',
                                                                                               'value' => $_enabled
                                                                                           ) );
            ?></span>
    </div>
    <div class="yith-wcbk-settings-section-box__content">
        <?php
        yith_wcbk_product_metabox_form_field( array(
                                                  'title'  => __( 'Rule name', 'yith-booking-for-woocommerce' ),
                                                  'class'  => 'yith-wcbk-settings-section-box__edit-title',
                                                  'fields' => array(
                                                      array(
                                                          'type'  => 'text',
                                                          'class' => 'yith-wcbk-availability-rule__title-field',
                                                          'value' => $_name,
                                                          'name'  => $_field_name . '[name]'
                                                      )
                                                  )
                                              ) );

        yith_wcbk_product_metabox_form_field( array(
                                                  'title'  => __( 'Rule type', 'yith-booking-for-woocommerce' ),
                                                  'desc'   => '<ul style="list-style:disc"><li>' .
                                                              __( 'Rules assigned with a <strong>specific date</strong> are valid only for the year selected. Example: you want to disable bookings from 1st August to 15th August of the current year.', 'yith-booking-for-woocommerce' ) .
                                                              '</li><li>' .
                                                              __( 'Rules assigned with <strong>generic dates</strong>  are valid without any time limit, until you disable them. Example: you want to disable bookings for August each year, or all Sunday days of the year.', 'yith-booking-for-woocommerce' ) .
                                                              '</li></ul>',
                                                  'fields' => array(
                                                      'type'    => 'radios',
                                                      'class'   => 'yith-wcbk-availability-rule__type',
                                                      'value'   => $_type,
                                                      'default' => 'month',
                                                      'name'    => $_field_name . '[type]',
                                                      'options' => array(
                                                          'custom' => __( 'Specific date', 'yith-booking-for-woocommerce' ),
                                                          'month'  => __( 'Generic dates', 'yith-booking-for-woocommerce' ),
                                                      )
                                                  )
                                              ) );

        yith_wcbk_product_metabox_form_field( array(
                                                  'title'  => __( 'From', 'yith-booking-for-woocommerce' ),
                                                  'class'  => 'yith-wcbk-availability-rule__from-to-row',
                                                  'fields' => array(
                                                      array(
                                                          'type'              => 'admin-datepicker',
                                                          'name'              => $_field_name . '[from]',
                                                          'custom_attributes' => 'custom' === $_type ? '' : 'disabled="disabled"',
                                                          'value'             => 'custom' === $_type ? $_from : '',
                                                      ),
                                                      array(
                                                          'type'              => 'select',
                                                          'class'             => 'yith-wcbk-month-range-select yith-wcbk-mini-field',
                                                          'name'              => $_field_name . '[from]',
                                                          'options'           => yith_wcbk_get_months_array(),
                                                          'custom_attributes' => 'month' === $_type ? '' : 'disabled="disabled"',
                                                          'value'             => 'month' === $_type ? $_from : '',
                                                      ),
                                                      array(
                                                          'type'   => 'section',
                                                          'fields' => array(
                                                              array(
                                                                  'type'              => 'admin-datepicker',
                                                                  'title'             => __( 'To', 'yith-booking-for-woocommerce' ),
                                                                  'name'              => $_field_name . '[to]',
                                                                  'custom_attributes' => 'custom' === $_type ? '' : 'disabled="disabled"',
                                                                  'value'             => 'custom' === $_type ? $_to : '',
                                                              ),
                                                              array(
                                                                  'type'              => 'select',
                                                                  'class'             => 'yith-wcbk-month-range-select yith-wcbk-mini-field',
                                                                  'name'              => $_field_name . '[to]',
                                                                  'options'           => yith_wcbk_get_months_array(),
                                                                  'custom_attributes' => 'month' === $_type ? '' : 'disabled="disabled"',
                                                                  'value'             => 'month' === $_type ? $_to : '',
                                                              )
                                                          )
                                                      )
                                                  )
                                              ) );


        yith_wcbk_product_metabox_form_field( array(
                                                  'fields' => array(
                                                      array(
                                                          'type'  => 'checkbox',
                                                          'class' => 'yith-wcbk-availability-rule__days-enabled',
                                                          'name'  => $_field_name . '[days_enabled]',
                                                          'id'    => $_field_id_prefix . 'days_enabled',
                                                          'value' => $_days_enabled,
                                                      ),
                                                      array(
                                                          'type'  => 'label',
                                                          'value' => __( 'Add extra rules for specific days of the week', 'yith-booking-for-woocommerce' ),
                                                          'for'   => $_field_id_prefix . 'days_enabled'
                                                      )
                                                  )
                                              ) );

        yith_wcbk_product_metabox_form_field( array(
                                                  'fields' => array(
                                                      array(
                                                          'type'  => 'checkbox',
                                                          'class' => 'yith-wcbk-availability-rule__times-enabled',
                                                          'name'  => $_field_name . '[times_enabled]',
                                                          'id'    => $_field_id_prefix . 'times_enabled',
                                                          'value' => $_times_enabled,
                                                      ),
                                                      array(
                                                          'type'  => 'label',
                                                          'value' => __( 'Add extra rules for specific time ranges of the days', 'yith-booking-for-woocommerce' ),
                                                          'for'   => $_field_id_prefix . 'times_enabled'
                                                      )
                                                  )
                                              ) );

        do_action('yith_wcbk_after_availability_rule_options', $_field_name, $_field_id_prefix, $index, $availability_rule);

        yith_wcbk_product_metabox_form_field( array(
                                                  'title'  => __( 'Bookable', 'yith-booking-for-woocommerce' ),
                                                  'class'  => 'yith-wcbk-availability-rule__bookable-row',
                                                  'desc' => __( 'Set these dates as bookable/non-bookable.', 'yith-booking-for-woocommerce' ),
                                                  'fields' => array(
                                                      array(
                                                          'type'    => 'select-inline',
                                                          'class'   => 'yith-wcbk-availability-rule__bookable',
                                                          'name'    => $_field_name . '[bookable]',
                                                          'value'   => $_bookable,
                                                          'options' => array(
                                                              'yes' => __( 'Bookable', 'yith-booking-for-woocommerce' ),
                                                              'no'  => __( 'Non-bookable', 'yith-booking-for-woocommerce' ),
                                                          )
                                                      ),
                                                  )
                                              ) );

        foreach ( yith_wcbk_get_days_array( true, true ) as $day_number => $day_name ) {
            yith_wcbk_product_metabox_form_field( array(
                'class'  => 'yith-wcbk-availability-rule__day',
                'fields' => apply_filters( 'yith_wcbk_availability_rule_day_fields', array(
                    array(
                        'type'  => 'label',
                        'class' => 'yith-wcbk-availability-rule__day__label',
                        'value' => $day_name,
                        'for'   => $_field_id_prefix . "day-{$day_number}"
                    ),
                    array(
                        'type'          => 'onoff-advanced',
                        'id'            => $_field_id_prefix . "day-{$day_number}",
                        'name'          => $_field_name . '[days][' . $day_number . ']',
                        'class'         => 'yith-wcbk-availability-rule__bookable-day',
                        'options'       => array(
                            'yes'      => array(
                                'class' => 'yith-wcbk-availability-rule__bookable-day--yes',
                                'label' => __( 'Bookable', 'yith-booking-for-woocommerce' )
                            ),
                            'no'       => array(
                                'class' => 'yith-wcbk-availability-rule__bookable-day--no',
                                'label' => __( 'Non-bookable', 'yith-booking-for-woocommerce' )
                            ),
                            'disabled' => array(
                                'class' => 'yith-wcbk-availability-rule__bookable-day--disabled',
                                'label' => __( 'No change', 'yith-booking-for-woocommerce' )
                            ),
                        ),
                        'default_label' => __( 'Bookable', 'yith-booking-for-woocommerce' ),
                        'value'         => isset( $_days[ $day_number ] ) ? $_days[ $day_number ] : 'yes',
                    ),
                    array(
                        'type'   => 'section',
                        'class'  => 'yith-wcbk-availability-rule__day-time',
                        'fields' => array(
                            array(
                                'type'  => 'time-select',
                                'title' => __( 'From', 'yith-booking-for-woocommerce' ),
                                'name'  => $_field_name . '[day_time_from][' . $day_number . ']',
                                'value' => isset( $_day_time_from[ $day_number ] ) ? $_day_time_from[ $day_number ] : '00:00',
                            ),
                            array(
                                'type'  => 'time-select',
                                'title' => __( 'To', 'yith-booking-for-woocommerce' ),
                                'name'  => $_field_name . '[day_time_to][' . $day_number . ']',
                                'value' => isset( $_day_time_to[ $day_number ] ) ? $_day_time_to[ $day_number ] : '00:00',

                            )
                        )
                    )
                ), $_field_name, $_field_id_prefix, $index, $availability_rule, $day_number, $day_name)
            ) );
        }
        ?>
        <div class="yith-wcbk-settings-section-box__content__actions yith-wcbk-right">
            <?php if ( !empty( $add_button ) ): ?>
                <span class="yith-wcbk-admin-button yith-wcbk-admin-button--primary yith-wcbk-admin-button--icon-check yith-wcbk-availability-rules__add-rule"><?php _e( 'Add rule', 'yith-booking-for-woocommerce' ) ?></span>
            <?php endif; ?>
            <span class="yith-wcbk-admin-button yith-wcbk-admin-button--light-grey yith-wcbk-admin-button--icon-trash yith-wcbk-availability-rules__delete-rule"><?php _e( 'Delete rule', 'yith-booking-for-woocommerce' ) ?></span>
        </div>
    </div>
</div>