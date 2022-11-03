<?php
! defined( 'YITH_WCBK' ) && exit;

if ( ! function_exists( 'yith_wcbk_is_booking_product' ) ) {
	/**
	 * Return true if the product is Booking Product
	 *
	 * @param bool|int|WP_Post|WC_Product $product
	 *
	 * @return bool
	 */
	function yith_wcbk_is_booking_product( $product = false ) {
		return YITH_WCBK_Product_Post_Type_Admin::is_booking( $product );
	}
}

if ( ! function_exists( 'yith_wcbk_get_product_availability_per_units' ) ) {
	/**
	 * @param WC_Product_Booking $product
	 * @param int                $from
	 * @param int                $to
	 *
	 * @return array
	 * @since 2.0.3
	 */
	function yith_wcbk_get_product_availability_per_units( WC_Product_Booking $product, $from, $to ) {
		$_args     = array(
			'product_id'                => $product->get_id(),
			'from'                      => $from,
			'to'                        => $to,
			'include_externals'         => $product->has_external_calendars(),
			'unit'                      => $product->get_duration_unit(),
			'count_persons_as_bookings' => $product->has_count_people_as_separate_bookings_enabled(),
		);
		$_booked   = YITH_WCBK_Booking_Helper()->count_max_booked_bookings_per_unit_in_period( $_args );
		$_max      = $product->get_max_bookings_per_unit();
		$_bookable = $_max - $_booked;

		return array(
			'booked'   => $_booked,
			'bookable' => $_bookable,
			'max'      => $_max,
		);
	}
}

if ( ! function_exists( 'yith_wcbk_get_calendar_product_availability_per_units_html' ) ) {
	/**
	 * @param WC_Product_Booking $product
	 * @param int                $from
	 * @param int                $to
	 * @param string             $step
	 *
	 * @return string
	 * @since 2.0.3
	 */
	function yith_wcbk_get_calendar_product_availability_per_units_html( WC_Product_Booking $product, $from, $to, $step = '' ) {
		$html = '';
		if ( $product->can_show_availability( $step ) ) {
			$html  .= "<div class='yith-wcbk-booking-calendar-availability'>";
			$_args = array( 'from' => $from, 'exclude_booked' => true, 'check_non_available_in_past' => false );

			if ( $product->is_available( $_args ) ) {
				$_availability = yith_wcbk_get_product_availability_per_units( $product, $from, $to );

				$html .= "<span class='yith-wcbk-booking-calendar-availability__bookable' title='" . __( 'Bookable', 'yith-booking-for-woocommerce' ) . "'>{$_availability['bookable']}</span>";
				$html .= "<span class='yith-wcbk-booking-calendar-availability__booked' title='" . __( 'Booked', 'yith-booking-for-woocommerce' ) . "'>{$_availability['booked']}</span>";
				$html .= "<span class='yith-wcbk-booking-calendar-availability__max' title='" . __( 'Max bookings per unit', 'yith-booking-for-woocommerce' ) . "'>{$_availability['max']}</span>";
			} else {
				$html .= "<span class='yith-wcbk-booking-calendar-availability__non_bookable'>" . __( 'Non Bookable', 'yith-booking-for-woocommerce' ) . "</span>";
			}
			$html .= "</div>";
		}

		return $html;
	}
}

if ( ! function_exists( 'yith_wcbk_delete_data_for_booking_products' ) ) {
	/**
	 * delete data for all booking products
	 *
	 * @since 2.0.8
	 */
	function yith_wcbk_delete_data_for_booking_products() {
		$products = wc_get_products( array( 'type' => YITH_WCBK_Product_Post_Type_Admin::$prod_type, 'limit' => - 1, 'return' => 'ids' ) );
		foreach ( $products as $product_id ) {
			YITH_WCBK_Cache()->delete_product_data( $product_id );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_generate_external_calendars_key' ) ) {
	function yith_wcbk_generate_external_calendars_key() {
		return wp_hash( wp_generate_password() );
	}
}

if ( ! function_exists( 'yith_wcbk_get_booking_form_date_info' ) ) {
	/**
	 * @param WC_Product_Booking $product
	 * @param array              $args
	 *
	 * @return array
	 */
	function yith_wcbk_get_booking_form_date_info( $product, $args = array() ) {
		$default_args                     = array(
			'include_default_start_date' => true,
			'include_default_end_date'   => true,
			'start'                      => 'now',
		);
		$args                             = wp_parse_args( $args, $default_args );
		$minimum_advance_reservation      = $product->get_minimum_advance_reservation();
		$minimum_advance_reservation_unit = $product->get_minimum_advance_reservation_unit();
		$allow_until                      = $product->get_maximum_advance_reservation();
		$allow_until_unit                 = $product->get_maximum_advance_reservation_unit();
		$default_start_date               = '';
		$default_end_date                 = '';
		$start                            = strtotime( $args['start'] );

		if ( $args['include_default_start_date'] ) {
			$default_start_date = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'from' );
			$default_start_date = ! ! $default_start_date ? $default_start_date : $product->get_calculated_default_start_date();
		}

		if ( $args['include_default_end_date'] ) {
			$default_end_date = YITH_WCBK_Search_Form_Helper::get_searched_value_for_field( 'to' );
			$default_end_date = ! ! $default_end_date ? $default_end_date : '';
		}

		$min_date           = '';
		$min_date_timestamp = $start;
		if ( in_array( $minimum_advance_reservation_unit, array( 'month', 'day', 'hour' ) ) ) {
			if ( in_array( $minimum_advance_reservation_unit, array( 'month', 'day' ) ) ) {
				$min_date = '+' . $minimum_advance_reservation . strtoupper( substr( $minimum_advance_reservation_unit, 0, 1 ) );
			} else {
				$min_date = $product->has_time() ? '+0D' : '+1D';
			}
			$min_date_midnight  = in_array( $minimum_advance_reservation_unit, array( 'month', 'day' ) ) ? 'midnight' : '';
			$min_date_timestamp = strtotime( "+{$minimum_advance_reservation} {$minimum_advance_reservation_unit}s {$min_date_midnight}", current_time( 'timestamp' ) );
		}

		$current_year  = date( 'Y', $start );
		$current_month = absint( date( 'm', $start ) );

		/**
		 * force $months_to_load to 3 months MAX (if hourly booking) or 1 month MAX (if minutely booking)
		 * to prevent loading issue if disable-day-if-no-time-available is active
		 * set default months to 12 if duration unit is month
		 */
		$months_to_load = YITH_WCBK()->settings->get_months_loaded_in_calendar();
		if ( $product->has_time() && 'yes' === YITH_WCBK()->settings->get( 'disable-day-if-no-time-available', 'no' ) ) {
			$max_months_to_load = yith_wcbk_get_max_months_to_load( $product->get_duration_unit() );
			$months_to_load     = min( $months_to_load, $max_months_to_load );
		} elseif ( 'month' === $product->get_duration_unit() ) {
			$months_to_load = 12;
		}

		$max_date           = '';
		$max_date_timestamp = strtotime( 'first day of this month', strtotime( "+ $months_to_load months", $start ) );
		$ajax_load_months   = true;
		if ( in_array( $allow_until_unit, array( 'year', 'month', 'day', 'hour' ) ) ) {
			$max_date         = '+' . $allow_until . strtoupper( substr( $allow_until_unit, 0, 1 ) );
			$ajax_load_months = $max_date_timestamp < strtotime( '+' . $allow_until . ' ' . $allow_until_unit . 's' );

			if ( 'month' === $product->get_duration_unit() ) {
				$max_date_timestamp = strtotime( '+' . $allow_until . ' ' . $allow_until_unit . 's' );
			}
		}

		$next_year  = date( 'Y', $max_date_timestamp );
		$next_month = absint( date( 'm', $max_date_timestamp ) );

		if ( 'month' === $product->get_duration_unit() ) {
			$next_year  += 1;
			$next_month = 1;
		}

		return compact( 'current_year', 'current_month', 'next_year', 'next_month', 'min_date', 'min_date_timestamp', 'max_date', 'max_date_timestamp', 'default_start_date', 'default_end_date', 'months_to_load', 'ajax_load_months' );
	}
}

if ( ! function_exists( 'yith_wcbk_sync_booking_product_prices' ) ) {
	/**
	 * sync prices for booking products
	 *
	 * @since 2.0.8
	 */
	function yith_wcbk_sync_booking_product_prices() {
		$products = wc_get_products( array( 'type' => YITH_WCBK_Product_Post_Type_Admin::$prod_type, 'limit' => - 1 ) );
		if ( $products ) {
			foreach ( $products as $product ) {
				/** @var WC_Product_Booking $product */
				yith_wcbk_product_price_sync( $product );
			}

			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'yith_wcbk_product_price_sync' ) ) {
	/**
	 * sync booking product price
	 *
	 * @param int|WC_Product_Booking $product
	 *
	 * @return bool
	 * @since 2.1.0
	 */
	function yith_wcbk_product_price_sync( $product ) {
		$product = wc_get_product( $product );

		if ( $product ) {
			try {
				/** @var YITH_WCBK_Product_Booking_Data_Store_CPT $data_store */
				$data_store = WC_Data_Store::load( 'product-booking' );

				return $data_store->sync_booking_price( $product );
			} catch ( Exception $e ) {
				$message = sprintf( "Error when trying to sync booking product #%s price. Exception: %s", $product->get_id(), $e->getMessage() );
				yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GENERAL );
			}
		}

		return false;
	}
}

if ( ! function_exists( 'yith_wcbk_product_update_external_calendars_last_sync' ) ) {
	/**
	 * update the last sync for external calendars
	 *
	 * @param WC_Product_Booking $product
	 * @param null|int           $last_sync
	 *
	 * @return bool
	 */
	function yith_wcbk_product_update_external_calendars_last_sync( $product, $last_sync = null ) {
		$product = wc_get_product( $product );

		if ( $product ) {
			try {
				/** @var YITH_WCBK_Product_Booking_Data_Store_CPT $data_store */
				$data_store = WC_Data_Store::load( 'product-booking' );

				return $data_store->update_external_calendars_last_sync( $product, $last_sync );
			} catch ( Exception $e ) {
				$message = sprintf( "Error when trying to update last sync for external calendars on product #%s. Exception: %s", $product->get_id(), $e->getMessage() );
				yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GENERAL );
			}
		}

		return false;
	}
}

if ( ! function_exists( 'yith_wcbk_product_delete_external_calendars_last_sync' ) ) {
	/**
	 * delete the last sync for external calendars
	 *
	 * @param WC_Product_Booking $product
	 *
	 * @return bool
	 */
	function yith_wcbk_product_delete_external_calendars_last_sync( $product ) {
		return yith_wcbk_product_update_external_calendars_last_sync( $product, 0 );
	}
}

if ( ! function_exists( 'yith_wcbk_get_price_to_display' ) ) {
	/**
	 * get price to display based on tax settings
	 *
	 * @param WC_Product $product
	 * @param string     $price
	 * @param bool       $allow_negative
	 *
	 * @return float|int
	 * @since 2.1
	 */
	function yith_wcbk_get_price_to_display( $product, $price, $allow_negative = true ) {
		$price_to_display = $price;
		$is_negative      = $price < 0;

		$allow_negative && $is_negative && ( $price_to_display *= - 1 );
		$old_price = $product->get_price( 'edit' );
		$product->set_price( 1 ); // prevent memory issues when getting prices (avoid calculate_price was fired)

		$price_to_display = wc_get_price_to_display( $product, array( 'price' => $price_to_display ) );

		$product->set_price( $old_price );
		$allow_negative && $is_negative && ( $price_to_display *= - 1 );

		return apply_filters( 'yith_wcbk_get_price_to_display', $price_to_display, $product, $price, $allow_negative );
	}
}

if ( ! function_exists( 'yith_wcbk_get_formatted_price_to_display' ) ) {
	/**
	 *  get formatted price to display based on tax settings
	 *
	 * @param WC_Product $product
	 * @param string     $price
	 * @param bool       $allow_negative
	 *
	 * @return string
	 * @since 2.1
	 */
	function yith_wcbk_get_formatted_price_to_display( $product, $price, $allow_negative = true ) {
		return wc_price( yith_wcbk_get_price_to_display( $product, $price, $allow_negative ) );
	}
}

if ( ! function_exists( 'yith_wcbk_product_metabox_form_field' ) ) {
	/**
	 * print a form field for product metabox
	 *
	 * @since 2.1.0
	 */
	function yith_wcbk_product_metabox_form_field( $field ) {
		$defaults = array(
			'class'     => '',
			'title'     => '',
			'label_for' => '',
			'desc'      => '',
			'data'      => array(),
			'fields'    => array(),
		);
		$field    = wp_parse_args( $field, $defaults );
		/**
		 * @var string $class
		 * @var string $title
		 * @var string $label_for
		 * @var string $desc
		 * @var array  $data
		 * @var array  $fields
		 */
		extract( $field );

		if ( isset( $fields['type'] ) ) {
			$fields = array( $fields );
		}

		if ( ! $label_for && $fields ) {
			$first_field = current( $fields );
			if ( isset( $first_field['id'] ) ) {
				$label_for = $first_field['id'];
			}
		}

		$data_html = '';
		foreach ( $data as $key => $value ) {
			$data_html .= "data-{$key}='{$value}' ";
		}

		$html = '';
		$html .= "<div class='yith-wcbk-form-field {$class}' {$data_html}>";
		$html .= "<label class='yith-wcbk-form-field__label' for='{$label_for}'>{$title}</label>";

		$html .= "<div class='yith-wcbk-form-field__container'>";
		ob_start();
		yith_wcbk_print_fields( $fields );
		$html .= ob_get_clean();
		$html .= "</div><!-- yith-wcbk-form-field__container -->";

		if ( $desc ) {
			$html .= "<div class='yith-wcbk-form-field__description'>{$desc}</div>";
		}

		$html .= "</div><!-- yith-wcbk-form-field -->";

		echo apply_filters( 'yith_wcbk_product_metabox_form_field_html', $html, $field );
	}
}

if ( ! function_exists( 'yith_wcbk_product_metabox_dynamic_duration' ) ) {
	/**
	 * print an element that updates his text based on product duration
	 *
	 * @return string
	 * @since 2.1.0
	 */
	function yith_wcbk_product_metabox_dynamic_duration() {
		return "<span class='yith-wcbk-product-metabox-dynamic-duration'></span>";
	}
}

if ( ! function_exists( 'yith_wcbk_product_metabox_dynamic_duration_qty' ) ) {
	/**
	 * print an element that updates his text based on product duration
	 *
	 * @return string
	 * @since 2.1.0
	 */
	function yith_wcbk_product_metabox_dynamic_duration_qty() {
		return "<span class='yith-wcbk-product-metabox-dynamic-duration-qty'></span>";
	}
}

if ( ! function_exists( 'yith_wcbk_product_metabox_dynamic_duration_unit' ) ) {
	/**
	 * print an element that updates his text based on product duration
	 *
	 * @since 2.1.0
	 */
	function yith_wcbk_product_metabox_dynamic_duration_unit() {
		return "<span class='yith-wcbk-product-metabox-dynamic-duration-unit'></span>";
	}
}