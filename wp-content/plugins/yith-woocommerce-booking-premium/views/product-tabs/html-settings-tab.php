<?php
/**
 * Template options in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 */
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly
?>

<div class="yith-wcbk-product-metabox-options-panel options_group show_if_<?php echo $prod_type; ?>">
    <div class="yith-wcbk-settings-section">
        <div class="yith-wcbk-settings-section__title">
            <h3><?php _e( 'Booking Settings', 'yith-booking-for-woocommerce' ) ?></h3>
        </div>
        <div class="yith-wcbk-settings-section__content">

            <?php
            yith_wcbk_product_metabox_form_field( array(
                                                      'class'     => '_yith_booking_duration_field yith_booking_multi_fields',
                                                      'title'     => __( 'Booking Duration', 'yith-booking-for-woocommerce' ),
                                                      'label_for' => '_yith_booking_duration',
                                                      'desc'      => __( 'Set if your customers can book for minutes, hours, days or months', 'yith-booking-for-woocommerce' ),
                                                      'fields'    => array(
                                                          array(
                                                              'type'    => 'select',
                                                              'value'   => $booking_product ? $booking_product->get_duration_type( 'edit' ) : 'customer',
                                                              'id'      => '_yith_booking_duration_type',
                                                              'class'   => 'select short',
                                                              'options' => array(
                                                                  'customer' => __( 'Customer can book units of', 'yith-booking-for-woocommerce' ),
                                                                  'fixed'    => __( 'Fixed units of', 'yith-booking-for-woocommerce' ),
                                                              )
                                                          ),
                                                          array(
                                                              'type'              => 'number',
                                                              'value'             => $booking_product ? $booking_product->get_duration( 'edit' ) : 1,
                                                              'id'                => '_yith_booking_duration',
                                                              'class'             => 'mini',
                                                              'custom_attributes' => 'step="1" min="1"'
                                                          ),
                                                          array(
                                                              'type'    => 'select',
                                                              'value'   => $booking_product ? $booking_product->get_duration( 'edit' ) : '',
                                                              'id'      => '_yith_booking_duration_minute_select',
                                                              'name'    => false,
                                                              'class'   => 'mini',
                                                              'options' => apply_filters( 'yith_wcbk_duration_minute_select_options', array( '15' => '15', '30' => '30', '45' => '45', '60' => '60', '90' => '90' ) )
                                                          ),
                                                          array(
                                                              'type'    => 'select',
                                                              'value'   => $booking_product ? $booking_product->get_duration_unit( 'edit' ) : 'day',
                                                              'id'      => '_yith_booking_duration_unit',
                                                              'class'   => 'select',
                                                              'options' => array(
                                                                  'month'  => __( 'Month(s)', 'yith-booking-for-woocommerce' ),
                                                                  'day'    => __( 'Day(s)', 'yith-booking-for-woocommerce' ),
                                                                  'hour'   => __( 'Hour(s)', 'yith-booking-for-woocommerce' ),
                                                                  'minute' => __( 'Minute(s)', 'yith-booking-for-woocommerce' )
                                                              ),
                                                          )
                                                      )
                                                  ) );


            yith_wcbk_product_metabox_form_field( array(
                                                      'class'  => '_yith_booking_enable_calendar_range_picker bk_enable_if_customer_one_day',
                                                      'title'  => __( 'Enable calendar range picker', 'yith-booking-for-woocommerce' ),
                                                      'desc'   => __( 'Enable or disable the calendar range picker in product page.', 'yith-booking-for-woocommerce' ),
                                                      'fields' => array(
                                                          'type'  => 'onoff',
                                                          'value' => wc_bool_to_string( $booking_product ? $booking_product->get_enable_calendar_range_picker( 'edit' ) : false ),
                                                          'id'    => '_yith_booking_enable_calendar_range_picker',
                                                      ) ) );


            yith_wcbk_product_metabox_form_field( array(
                                                      'class'  => '_yith_booking_default_start_date',
                                                      'title'  => __( 'Default start date in booking form', 'yith-booking-for-woocommerce' ),
                                                      'desc'   => __( 'Insert the day to show as default in Start Date field of booking form.', 'yith-booking-for-woocommerce' ),
                                                      'fields' => array(
                                                          'type'    => 'select',
                                                          'class'   => 'select short',
                                                          'value'   => $booking_product ? $booking_product->get_default_start_date( 'edit' ) : '',
                                                          'id'      => '_yith_booking_default_start_date',
                                                          'options' => array(
                                                              ''                => __( 'None', 'yith-booking-for-woocommerce' ),
                                                              'today'           => __( 'Today', 'yith-booking-for-woocommerce' ),
                                                              'tomorrow'        => __( 'Tomorrow', 'yith-booking-for-woocommerce' ),
                                                              'first-available' => __( 'First available', 'yith-booking-for-woocommerce' ),
                                                              'custom'          => __( 'Custom Date', 'yith-booking-for-woocommerce' ),
                                                          ),
                                                      ) ) );

            yith_wcbk_product_metabox_form_field( array(
                                                      'class'  => '_yith_booking_default_start_date_custom yith-wcbk-show-conditional',
                                                      'data'   => array(
                                                          'field-id' => '_yith_booking_default_start_date',
                                                          'value'    => 'custom',
                                                      ),
                                                      'title'  => __( 'Custom default start date', 'yith-booking-for-woocommerce' ),
                                                      'desc'   => __( 'Insert the custom date to show as default in Start Date field of booking form.', 'yith-booking-for-woocommerce' ),
                                                      'fields' => array(
                                                          'type'  => 'text',
                                                          'class' => 'yith-wcbk-date-input-field yith-wcbk-admin-date-picker',
                                                          'name'  => '_yith_booking_default_start_date_custom',
                                                          'value' => $booking_product ? $booking_product->get_default_start_date_custom( 'edit' ) : '',
                                                      ) ) );

            yith_wcbk_product_metabox_form_field( array(
                                                      'class'  => '_yith_booking_default_start_time bk_enable_if_time',
                                                      'title'  => __( 'Default start time in booking form', 'yith-booking-for-woocommerce' ),
                                                      'desc'   => __( 'Insert the time to show as default in Time field of booking form.', 'yith-booking-for-woocommerce' ),
                                                      'fields' => array(
                                                          'type'    => 'select',
                                                          'class'   => 'select short',
                                                          'value'   => $booking_product ? $booking_product->get_default_start_time( 'edit' ) : '',
                                                          'id'      => '_yith_booking_default_start_time',
                                                          'options' => array(
                                                              ''                => __( 'None', 'yith-booking-for-woocommerce' ),
                                                              'first-available' => __( 'First available', 'yith-booking-for-woocommerce' ),
                                                          ),
                                                      ) ) );

            yith_wcbk_product_metabox_form_field( array(
                                                      'class'  => '_yith_booking_all_day bk_enable_if_day',
                                                      'title'  => __( 'Full day booking', 'yith-booking-for-woocommerce' ),
                                                      'desc'   => __( 'Choose whether the booking will be active or not for the full day (Example: for a booking from day 1 to day 2, day 2 will be fully booked only if this option is active)', 'yith-booking-for-woocommerce' ),
                                                      'fields' => array(
                                                          'type'  => 'onoff',
                                                          'value' => wc_bool_to_string( $booking_product ? $booking_product->get_full_day( 'edit' ) : false ),
                                                          'id'    => '_yith_booking_all_day',
                                                      ) ) );


            $booking_settings_url  = admin_url( 'admin.php?page=yith_wcbk_panel' );
            $booking_settings_link = "<a href='{$booking_settings_url}'>YITH > Booking > " . __( 'Settings', 'yith-booking-for-woocommerce' ) . "</a>";
            yith_wcbk_product_metabox_form_field( array(
                                                      'class'  => '_yith_booking_location_field',
                                                      'title'  => __( 'Location', 'yith-booking-for-woocommerce' ),
                                                      'desc'   => sprintf( __( 'Set your Google Maps API Keys in %s and then enter your address in this field.', 'yith-booking-for-woocommerce' ), $booking_settings_link ) .
                                                                  '<br />' .
                                                                  sprintf( __( 'You can put a map in product page by using the %s shortcode.', 'yith-booking-for-woocommerce' ), '<strong>[booking_map]</strong>' ),
                                                      'fields' => array(
                                                          'type'  => 'text',
                                                          'value' => $booking_product ? $booking_product->get_location( 'edit' ) : '',
                                                          'id'    => '_yith_booking_location',
                                                          'class' => 'yith-wcbk-google-maps-places-autocomplete',
                                                      ) ) );

            ?>
        </div>
    </div>
</div>
