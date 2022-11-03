<?php
/**
 * Template options in WC Product Panel
 *
 * @var WC_Product_Booking|false $booking_product The booking product or false (if it's not a booking product)
 * @var string                   $prod_type       The booking product type
 * @var int                      $post_id         The post ID
 */
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly
?>

<div class="yith-wcbk-product-metabox-options-panel options_group show_if_<?php echo $prod_type; ?>">
	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php _e( 'Booking terms', 'yith-booking-for-woocommerce' ) ?></h3>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php
			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_available_max_per_block_field',
													  'title'  => __( 'Max bookings per unit', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( 'Select the maximum number of bookings allowed for each unit. Set 0 (zero) for unlimited.', 'yith-booking-for-woocommerce' ),
													  'fields' =>
														  array(
															  'type'              => 'number',
															  'value'             => $booking_product ? $booking_product->get_max_bookings_per_unit( 'edit' ) : 1,
															  'id'                => '_yith_booking_max_per_block',
															  'class'             => 'yith-wcbk-mini-field',
															  'custom_attributes' => 'step="1" min="0"',
														  ),
												  ) );

			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_minimum_duration_field bk_enable_if_customer_chooses_blocks yith_booking_multi_fields',
													  'title'  => __( 'Minimum booking duration', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( 'Set the minimum booking duration that customers can select.', 'yith-booking-for-woocommerce' ),
													  'fields' =>
														  array(
															  array(
																  'type'              => 'number',
																  'value'             => $booking_product ? $booking_product->get_minimum_duration( 'edit' ) : 1,
																  'id'                => '_yith_booking_minimum_duration',
																  'class'             => 'yith-wcbk-mini-field',
																  'custom_attributes' => 'step="1" min="1"',
															  ),
															  array(
																  'type'  => 'html',
																  'value' => yith_wcbk_product_metabox_dynamic_duration_qty(),
															  ),
														  ),
												  ) );

			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_maximum_duration_field bk_enable_if_customer_chooses_blocks yith_booking_multi_fields',
													  'title'  => __( 'Maximum booking duration', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( 'Set the maximum booking duration that customers can select.', 'yith-booking-for-woocommerce' ),
													  'fields' =>
														  array(
															  array(
																  'type'              => 'number',
																  'value'             => $booking_product ? $booking_product->get_maximum_duration( 'edit' ) : 0,
																  'id'                => '_yith_booking_maximum_duration',
																  'class'             => 'yith-wcbk-mini-field',
																  'custom_attributes' => 'step="1" min="0"',
															  ),
															  array(
																  'type'  => 'html',
																  'value' => yith_wcbk_product_metabox_dynamic_duration_qty(),
															  ),
														  ),
												  ) );


			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_request_confirmation_field',
													  'title'  => __( 'Confirmation required', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( 'Select if the admin has to confirm a booking before accepting it.', 'yith-booking-for-woocommerce' ),
													  'fields' =>
														  array(
															  'type'  => 'onoff',
															  'value' => wc_bool_to_string( $booking_product ? $booking_product->get_confirmation_required( 'edit' ) : false ),
															  'id'    => '_yith_booking_request_confirmation',
														  ),
												  ) );

			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_can_be_cancelled_field',
													  'title'  => __( 'Cancellation available', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( 'Select if the customer can cancel the booking.', 'yith-booking-for-woocommerce' ),
													  'fields' =>
														  array(
															  'type'  => 'onoff',
															  'value' => wc_bool_to_string( $booking_product ? $booking_product->get_cancellation_available( 'edit' ) : false ),
															  'id'    => '_yith_booking_can_be_cancelled',
														  ),
												  ) );

			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_cancelled_time_field bk_enable_if_can_be_cancelled yith_booking_multi_fields',
													  'title'  => __( 'The booking can be cancelled up to', 'yith-booking-for-woocommerce' ),
													  'fields' =>
														  array(
															  array(
																  'type'              => 'number',
																  'value'             => $booking_product ? $booking_product->get_cancellation_available_up_to( 'edit' ) : '0',
																  'id'                => '_yith_booking_cancelled_duration',
																  'class'             => 'yith-wcbk-mini-field',
																  'custom_attributes' => 'step="1" min="0"',
															  ),
															  array(
																  'type'    => 'select',
																  'value'   => $booking_product ? $booking_product->get_cancellation_available_up_to_unit( 'edit' ) : 'day',
																  'id'      => '_yith_booking_cancelled_unit',
																  'class'   => 'select',
																  'options' => array(
																	  'day'   => __( 'Day(s)', 'yith-booking-for-woocommerce' ),
																	  'month' => __( 'Month(s)', 'yith-booking-for-woocommerce' ),
																  ),
															  ),
															  array(
																  'type'  => 'html',
																  'value' => __( 'before the booking start date', 'yith-booking-for-woocommerce' ),
															  ),
														  ),
												  ) );


			?>
		</div>
	</div>

	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php _e( 'Booking preferences', 'yith-booking-for-woocommerce' ) ?></h3>
			<span class="yith-wcbk-settings-section__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php

			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_available_checkin_field',
													  'title'  => __( 'Check-in time', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( 'Insert check-in time for your customers', 'yith-booking-for-woocommerce' ),
													  'fields' => array(
														  'type'  => 'text',
														  'value' => $booking_product ? $booking_product->get_check_in( 'edit' ) : '',
														  'id'    => '_yith_booking_checkin',
														  'class' => 'yith-wcbk-mini-field',
													  ),
												  ) );

			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_available_checkout_field',
													  'title'  => __( 'Check-out time', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( 'Insert check-out time for your customers', 'yith-booking-for-woocommerce' ),
													  'fields' => array(
														  'type'  => 'text',
														  'value' => $booking_product ? $booking_product->get_check_out( 'edit' ) : '',
														  'id'    => '_yith_booking_checkout',
														  'class' => 'yith-wcbk-mini-field',
													  ),
												  ) );

			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_allowed_start_days_field',
													  'title'  => __( 'Allowed Start Days', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( 'Select on which days the booking can start. Leave empty if it can start without any limit on any day of the week.', 'yith-booking-for-woocommerce' ),
													  'fields' => array(
														  'type'              => 'select',
														  'class'             => 'wc-enhanced-select select short',
														  'multiple'          => true,
														  'name'              => '_yith_booking_allowed_start_days[]',
														  'options'           => yith_wcbk_get_days_array(),
														  'value'             => $booking_product ? $booking_product->get_allowed_start_days( 'edit' ) : array(),
														  'custom_attributes' => ' style="width:50%" ',
													  ),
												  ) );

			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_daily_start_time bk_enable_if_time',
													  'title'  => __( 'Daily Start Time', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( 'Insert the start time for every day of the week.', 'yith-booking-for-woocommerce' ),
													  'fields' => array(
														  'type'  => 'time-select',
														  'name'  => '_yith_booking_daily_start_time',
														  'value' => $booking_product ? $booking_product->get_daily_start_time( 'edit' ) : '00:00',
													  ),
												  ) );


			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_buffer_field yith_booking_multi_fields',
													  'title'  => __( 'Buffer time', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( 'Time for preparation or cleanup between two bookings.', 'yith-booking-for-woocommerce' ),
													  'fields' => array(
														  array(
															  'type'              => 'number',
															  'value'             => $booking_product ? $booking_product->get_buffer( 'edit' ) : 0,
															  'id'                => '_yith_booking_buffer',
															  'custom_attributes' => apply_filters( 'yith_wcbk_buffer_field_custom_attributes', 'step="1" min="0"' ),
															  'class'             => 'yith-wcbk-mini-field',
														  ),
														  array(
															  'type'  => 'html',
															  'value' => yith_wcbk_product_metabox_dynamic_duration_unit(),
														  ),
													  ),
												  ) );


			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_time_increment_based_on_duration bk_enable_if_time',
													  'title'  => __( 'Time increment based on duration', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( "Select if the time increment of your booking is based on booking duration. By default the time increment is 1 hour for hourly bookings and 15 minutes for per-minute bookings. Example: if enabled and your booking duration is 3 hours, the time increment will be 3 hours, so you'll see the following time slots: 8:00 - 11:00 - 14:00 - 17:00", 'yith-booking-for-woocommerce' ),
													  'fields' => array(
														  'type'  => 'checkbox',
														  'value' => wc_bool_to_string( $booking_product ? $booking_product->get_time_increment_based_on_duration( 'edit' ) : false ),
														  'id'    => '_yith_booking_time_increment_based_on_duration',
													  ),
												  ) );

			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_time_increment_including_buffer bk_enable_if_fixed_and_time',
													  'title'  => __( 'Time increment including buffer', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( "Select if you want to include buffer time to the time increment. Example: if enabled and the booking duration is 1 hour and you set a buffer of 1 hour, the time increment will be 1 hour + 1 hour, so you'll see the following time slots: 8:00 - 10:00 - 12:00 - 14:00", 'yith-booking-for-woocommerce' ),
													  'fields' => array(
														  'type'  => 'checkbox',
														  'value' => wc_bool_to_string( $booking_product ? $booking_product->get_time_increment_including_buffer( 'edit' ) : false ),
														  'id'    => '_yith_booking_time_increment_including_buffer',
													  ),
												  ) );
			?>
		</div>
	</div>

	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php _e( 'Booking window', 'yith-booking-for-woocommerce' ) ?></h3>
			<span class="yith-wcbk-settings-section__toggle"><span class="dashicons dashicons-arrow-up-alt2"></span></span>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<?php
			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_available_allow_after_field yith_booking_multi_fields',
													  'title'  => __( 'Minimum advance reservation', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( 'Insert the minimum advance reservation for the booking. For example: if you set it to 10 days, customers can book now a booking that will start in 10 days', 'yith-booking-for-woocommerce' ),
													  'fields' => array(
														  array(
															  'type'              => 'number',
															  'value'             => $booking_product ? $booking_product->get_minimum_advance_reservation( 'edit' ) : 0,
															  'id'                => '_yith_booking_allow_after',
															  'class'             => 'yith-wcbk-mini-field',
															  'custom_attributes' => 'step="1" min="0"',
														  ),
														  array(
															  'type'    => 'select',
															  'value'   => $booking_product ? $booking_product->get_minimum_advance_reservation_unit( 'edit' ) : 'day',
															  'id'      => '_yith_booking_allow_after_unit',
															  'class'   => 'select',
															  'options' => array(
																  'month' => __( 'Month(s)', 'yith-booking-for-woocommerce' ),
																  'day'   => __( 'Day(s)', 'yith-booking-for-woocommerce' ),
																  'hour'  => __( 'Hour(s)', 'yith-booking-for-woocommerce' ),
															  ),
														  ),
													  ),
												  ) );

			yith_wcbk_product_metabox_form_field( array(
													  'class'  => '_yith_booking_available_allow_until_field yith_booking_multi_fields',
													  'title'  => __( 'Maximum advance reservation', 'yith-booking-for-woocommerce' ),
													  'desc'   => __( 'Insert the maximum advance reservation for the booking. For example: if you set it to 6 months, customers can only book within 6 months.', 'yith-booking-for-woocommerce' ),
													  'fields' => array(
														  array(
															  'type'              => 'number',
															  'value'             => $booking_product ? $booking_product->get_maximum_advance_reservation( 'edit' ) : 1,
															  'id'                => '_yith_booking_allow_until',
															  'class'             => 'yith-wcbk-mini-field',
															  'custom_attributes' => 'step="1" min="1"',
														  ),
														  array(
															  'type'    => 'select',
															  'value'   => $booking_product ? $booking_product->get_maximum_advance_reservation_unit( 'edit' ) : 'year',
															  'id'      => '_yith_booking_allow_until_unit',
															  'class'   => 'select',
															  'options' => array(
																  'year'  => __( 'Year(s)', 'yith-booking-for-woocommerce' ),
																  'month' => __( 'Month(s)', 'yith-booking-for-woocommerce' ),
																  'day'   => __( 'Day(s)', 'yith-booking-for-woocommerce' ),
															  ),
														  ),
													  ),
												  ) );

			?>

		</div>
	</div>

	<div class="yith-wcbk-settings-section">
		<div class="yith-wcbk-settings-section__title">
			<h3><?php _e( 'Additional availability rules', 'yith-booking-for-woocommerce' ) ?></h3>
			<div class="yith-wcbk-availability-rules__expand-collapse">
				<span class="yith-wcbk-availability-rules__expand"><?php _e( 'Expand all', 'yith-booking-for-woocommerce' ) ?></span>
				<span class="yith-wcbk-availability-rules__collapse"><?php _e( 'Collapse all', 'yith-booking-for-woocommerce' ) ?></span>
			</div>
		</div>
		<div class="yith-wcbk-settings-section__content">
			<div class="yith-wcbk-settings-section__description"><?php _e( "You can create advanced rules to enable/disable booking availability for specific dates or months", 'yith-booking-for-woocommerce' ) ?></div>
			<?php
			$availability_rules = $booking_product ? $booking_product->get_availability_rules() : array();
			$field_name         = '_yith_booking_availability_range';
			yith_wcbk_get_view( 'product-tabs/utility/html-availability-rules.php', compact( 'availability_rules', 'field_name' ) );
			?>
		</div>
	</div>
</div>