<?php
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

/**
 * Legacy product contains all deprecated methods for this class and can be removed in the future.
 */
require_once YITH_WCBK_DIR . 'includes/legacy/abstract.yith-wcbk-legacy-booking-product.php';

if ( ! class_exists( 'WC_Product_Booking' ) ) {
	/**
	 * Class WC_Product_Booking
	 * the Booking Product
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class WC_Product_Booking extends YITH_WCBK_Legacy_Booking_Product {

		protected $booking_data_defaults = array(
			'duration_type'                               => 'customer',
			'duration'                                    => 1,
			'duration_unit'                               => 'day',
			'enable_calendar_range_picker'                => false,
			'default_start_date'                          => '',
			'default_start_date_custom'                   => '',
			'default_start_time'                          => '',
			'full_day'                                    => false,
			'location'                                    => '',
			'location_latitude'                           => '',
			'location_longitude'                          => '',
			'max_bookings_per_unit'                       => 1,
			'minimum_duration'                            => 1,
			'maximum_duration'                            => 0,
			'confirmation_required'                       => false,
			'cancellation_available'                      => false,
			'cancellation_available_up_to'                => 0,
			'cancellation_available_up_to_unit'           => 'day',
			'check_in'                                    => '',
			'check_out'                                   => '',
			'allowed_start_days'                          => array(),
			'daily_start_time'                            => '00:00',
			'buffer'                                      => 0,
			'time_increment_based_on_duration'            => false,
			'time_increment_including_buffer'             => false,
			'minimum_advance_reservation'                 => 0,
			'minimum_advance_reservation_unit'            => 'day',
			'maximum_advance_reservation'                 => 1,
			'maximum_advance_reservation_unit'            => 'year',
			'availability_rules'                          => array(),
			'base_price'                                  => '',
			'multiply_base_price_by_number_of_people'     => false,
			'extra_price_per_person'                      => '',
			'extra_price_per_person_greater_than'         => 0,
			'weekly_discount'                             => 0,
			'monthly_discount'                            => 0,
			'last_minute_discount'                        => 0,
			'last_minute_discount_days_before_arrival'    => 0,
			'fixed_base_fee'                              => '',
			'multiply_fixed_base_fee_by_number_of_people' => false,
			'price_rules'                                 => array(),
			'enable_people'                               => false,
			'minimum_number_of_people'                    => 1,
			'maximum_number_of_people'                    => 0,
			'count_people_as_separate_bookings'           => false,
			'enable_people_types'                         => false,
			'people_types'                                => array(),
			'service_ids'                                 => array(),
			'external_calendars'                          => array(),
			'external_calendars_key'                      => '',
			'external_calendars_last_sync'                => 0,
			'extra_costs'                                 => array(),
		);


		/**
		 * Merges booking product data into the parent object.
		 *
		 * @param int|WC_Product|object $product Product to init.
		 */
		public function __construct( $product = 0 ) {
			$this->data = array_merge( $this->data, $this->booking_data_defaults );
			parent::__construct( $product );
		}

		/**
		 * Get internal type.
		 *
		 * @return string
		 */
		public function get_type() {
			return 'booking';
		}


		/*
        |--------------------------------------------------------------------------
        | Getters
        |--------------------------------------------------------------------------
        |
        | Methods for getting data from the product object.
        */

		/**
		 * Get product duration type.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_duration_type( $context = 'view' ) {
			return $this->get_prop( 'duration_type', $context );
		}

		/**
		 * Get product duration.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_duration( $context = 'view' ) {
			return $this->get_prop( 'duration', $context );
		}

		/**
		 * Get product duration unit.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_duration_unit( $context = 'view' ) {
			return $this->get_prop( 'duration_unit', $context );
		}

		/**
		 * Get enable calendar range picker
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function get_enable_calendar_range_picker( $context = 'view' ) {
			return $this->get_prop( 'enable_calendar_range_picker', $context );
		}

		/**
		 * Get default start date
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_default_start_date( $context = 'view' ) {
			return $this->get_prop( 'default_start_date', $context );
		}

		/**
		 * Get default start date custom
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_default_start_date_custom( $context = 'view' ) {
			return $this->get_prop( 'default_start_date_custom', $context );
		}

		/**
		 * Get default start time
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_default_start_time( $context = 'view' ) {
			return $this->get_prop( 'default_start_time', $context );
		}

		/**
		 * Get full day
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function get_full_day( $context = 'view' ) {
			return $this->get_prop( 'full_day', $context );
		}

		/**
		 * Get location
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_location( $context = 'view' ) {
			return $this->get_prop( 'location', $context );
		}

		/**
		 * Get location latitude
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_location_latitude( $context = 'view' ) {
			return $this->get_prop( 'location_latitude', $context );
		}

		/**
		 * Get location longitude
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_location_longitude( $context = 'view' ) {
			return $this->get_prop( 'location_longitude', $context );
		}

		/**
		 * Get max bookings per unit
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_max_bookings_per_unit( $context = 'view' ) {
			return $this->get_prop( 'max_bookings_per_unit', $context );
		}

		/**
		 * Get minimum duration
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_minimum_duration( $context = 'view' ) {
			return 'view' === $context && $this->is_type_fixed_blocks() ? 1 : $this->get_prop( 'minimum_duration', $context );
		}

		/**
		 * Get maximum duration
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_maximum_duration( $context = 'view' ) {
			return 'view' === $context && $this->is_type_fixed_blocks() ? 1 : $this->get_prop( 'maximum_duration', $context );
		}

		/**
		 * Get confirmation required
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function get_confirmation_required( $context = 'view' ) {
			return $this->get_prop( 'confirmation_required', $context );
		}

		/**
		 * Get cancellation available
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function get_cancellation_available( $context = 'view' ) {
			return $this->get_prop( 'cancellation_available', $context );
		}

		/**
		 * Get cancellation available up to
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_cancellation_available_up_to( $context = 'view' ) {
			return $this->get_prop( 'cancellation_available_up_to', $context );
		}

		/**
		 * Get cancellation available up to unit
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_cancellation_available_up_to_unit( $context = 'view' ) {
			return $this->get_prop( 'cancellation_available_up_to_unit', $context );
		}

		/**
		 * Get check-in
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_check_in( $context = 'view' ) {
			return $this->get_prop( 'check_in', $context );
		}

		/**
		 * Get check-out
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_check_out( $context = 'view' ) {
			return $this->get_prop( 'check_out', $context );
		}

		/**
		 * Get allowed start days
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 * @since 2.1
		 */
		public function get_allowed_start_days( $context = 'view' ) {
			return $this->get_prop( 'allowed_start_days', $context );
		}

		/**
		 * Get daily start time
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_daily_start_time( $context = 'view' ) {
			return $this->get_prop( 'daily_start_time', $context );
		}

		/**
		 * Get buffer
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_buffer( $context = 'view' ) {
			return $this->get_prop( 'buffer', $context );
		}

		/**
		 * Get time increment based on duration
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function get_time_increment_based_on_duration( $context = 'view' ) {
			return $this->get_prop( 'time_increment_based_on_duration', $context );
		}

		/**
		 * Get time increment including buffer
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function get_time_increment_including_buffer( $context = 'view' ) {
			return $this->get_prop( 'time_increment_including_buffer', $context );
		}

		/**
		 * Get minimum advance reservation
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_minimum_advance_reservation( $context = 'view' ) {
			return $this->get_prop( 'minimum_advance_reservation', $context );
		}

		/**
		 * Get minimum advance reservation unit
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_minimum_advance_reservation_unit( $context = 'view' ) {
			return $this->get_prop( 'minimum_advance_reservation_unit', $context );
		}

		/**
		 * Get maximum advance reservation
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_maximum_advance_reservation( $context = 'view' ) {
			return $this->get_prop( 'maximum_advance_reservation', $context );
		}

		/**
		 * Get maximum advance reservation unit
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_maximum_advance_reservation_unit( $context = 'view' ) {
			return $this->get_prop( 'maximum_advance_reservation_unit', $context );
		}

		/**
		 * Get availability rules
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return YITH_WCBK_Availability_Rule[]
		 * @since 2.1
		 */
		public function get_availability_rules( $context = 'view' ) {
			return $this->get_prop( 'availability_rules', $context );
		}

		/**
		 * Get base price
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_base_price( $context = 'view' ) {
			return $this->get_prop( 'base_price', $context );
		}

		/**
		 * Get multiply base price by number of people
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_multiply_base_price_by_number_of_people( $context = 'view' ) {
			return $this->get_prop( 'multiply_base_price_by_number_of_people', $context );
		}

		/**
		 * Get extra price per person
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_extra_price_per_person( $context = 'view' ) {
			return $this->get_prop( 'extra_price_per_person', $context );
		}

		/**
		 * Get extra price per person greater than
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_extra_price_per_person_greater_than( $context = 'view' ) {
			return $this->get_prop( 'extra_price_per_person_greater_than', $context );
		}

		/**
		 * Get weekly discount
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_weekly_discount( $context = 'view' ) {
			return $this->get_prop( 'weekly_discount', $context );
		}

		/**
		 * Get monthly discount
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_monthly_discount( $context = 'view' ) {
			return $this->get_prop( 'monthly_discount', $context );
		}

		/**
		 * Get last minute discount
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_last_minute_discount( $context = 'view' ) {
			return $this->get_prop( 'last_minute_discount', $context );
		}

		/**
		 * Get last minute discount - days before arrival
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_last_minute_discount_days_before_arrival( $context = 'view' ) {
			return $this->get_prop( 'last_minute_discount_days_before_arrival', $context );
		}

		/**
		 * Get multiply fixed base fee by number of people
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_multiply_fixed_base_fee_by_number_of_people( $context = 'view' ) {
			return $this->get_prop( 'multiply_fixed_base_fee_by_number_of_people', $context );
		}

		/**
		 * Get fixed base fee
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_fixed_base_fee( $context = 'view' ) {
			return $this->get_prop( 'fixed_base_fee', $context );
		}

		/**
		 * Returns the product's active price.
		 *
		 * @param string $context
		 *
		 * @return string price
		 */
		public function get_price( $context = 'view' ) {
			$price = parent::get_price( 'edit' );

			// TODO: remove this line. This was commented on 2019-12-20 to fix the integration with YITH Deposit when the deposit rate is zero.
			// $price = $price || 'edit' === $context ? $price : $this->calculate_price();

			$price = 'view' === $context ? apply_filters( 'yith_wcbk_booking_product_get_price', $price, $this ) : $price;

			return 'view' === $context ? apply_filters( 'woocommerce_product_get_price', $price, $this ) : $price;
		}

		/**
		 * Returns the product's regular price.
		 * In case of Booking Product the regular price is ''
		 *
		 * @param string $context
		 *
		 * @return string price
		 */
		public function get_regular_price( $context = 'view' ) {
			return '';
		}

		/**
		 * Get price rules
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return YITH_WCBK_Price_Rule[]
		 * @since 2.1
		 */
		public function get_price_rules( $context = 'view' ) {
			return $this->get_prop( 'price_rules', $context );
		}

		/**
		 * Get enable people
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_enable_people( $context = 'view' ) {
			return $this->get_prop( 'enable_people', $context );
		}

		/**
		 * Get minimum number of people
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_minimum_number_of_people( $context = 'view' ) {
			return $this->get_prop( 'minimum_number_of_people', $context );
		}

		/**
		 * Get maximum number of people
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_maximum_number_of_people( $context = 'view' ) {
			return $this->get_prop( 'maximum_number_of_people', $context );
		}

		/**
		 * Get count people as separate bookings
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_count_people_as_separate_bookings( $context = 'view' ) {
			return $this->get_prop( 'count_people_as_separate_bookings', $context );
		}

		/**
		 * Get enable people types
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function get_enable_people_types( $context = 'view' ) {
			return $this->get_prop( 'enable_people_types', $context );
		}

		/**
		 * Get people types
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 * @since 2.1
		 */
		public function get_people_types( $context = 'view' ) {
			return $this->get_prop( 'people_types', $context );
		}

		/**
		 * Get service ids
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int[]
		 * @since 2.1
		 */
		public function get_service_ids( $context = 'view' ) {
			return $this->get_prop( 'service_ids', $context );
		}

		/**
		 * Get external calendars
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 * @since 2.1
		 */
		public function get_external_calendars( $context = 'view' ) {
			return $this->get_prop( 'external_calendars', $context );
		}

		/**
		 * Get external calendars key
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_external_calendars_key( $context = 'view' ) {
			return $this->get_prop( 'external_calendars_key', $context );
		}

		/**
		 * Get external calendars last sync
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1
		 */
		public function get_external_calendars_last_sync( $context = 'view' ) {
			return $this->get_prop( 'external_calendars_last_sync', $context );
		}


		/**
		 * Get extra costs
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return YITH_WCBK_Product_Extra_Cost[]
		 * @since 2.1
		 */
		public function get_extra_costs( $context = 'view' ) {
			return $this->get_prop( 'extra_costs', $context );
		}


		/*
        |--------------------------------------------------------------------------
        | Setters
        |--------------------------------------------------------------------------
        |
        | Functions for setting product data. These should not update anything in the
        | database itself and should only change what is stored in the class
        | object.
        */

		/**
		 * Set product duration type.
		 *
		 * @param string $duration_type Product duration type
		 *
		 * @since 2.1
		 */
		public function set_duration_type( $duration_type ) {
			$this->set_prop( 'duration_type', $duration_type );
		}

		/**
		 * Set product duration.
		 *
		 * @param int $duration Product duration
		 *
		 * @since 2.1
		 */
		public function set_duration( $duration ) {
			$this->set_prop( 'duration', max( 1, absint( $duration ) ) );
		}

		/**
		 * Set product duration unit.
		 *
		 * @param string $duration_unit Product duration unit
		 *
		 * @since 2.1
		 */
		public function set_duration_unit( $duration_unit ) {
			$this->set_prop( 'duration_unit', $duration_unit );
		}

		/**
		 * Set enable calendar range picker
		 *
		 * @param bool|string $enabled Whether the calendar range picker is enabled or not.
		 *
		 * @since 2.1
		 */
		public function set_enable_calendar_range_picker( $enabled ) {
			$this->set_prop( 'enable_calendar_range_picker', wc_string_to_bool( $enabled ) );
		}

		/**
		 * Set default start date
		 *
		 * @param string $default_start_date Product default start date
		 *
		 * @since 2.1
		 */
		public function set_default_start_date( $default_start_date ) {
			$this->set_prop( 'default_start_date', $default_start_date );
		}

		/**
		 * Set default start date custom
		 *
		 * @param string $default_start_date_custom Product default start date custom
		 *
		 * @since 2.1
		 */
		public function set_default_start_date_custom( $default_start_date_custom ) {
			$this->set_prop( 'default_start_date_custom', $default_start_date_custom );
		}

		/**
		 * Set default start time
		 *
		 * @param string $default_start_time Product default start time
		 *
		 * @since 2.1
		 */
		public function set_default_start_time( $default_start_time ) {
			$this->set_prop( 'default_start_time', $default_start_time );
		}

		/**
		 * Set full day
		 *
		 * @param bool|string $full_day Whether the product is full day or not.
		 *
		 * @since 2.1
		 */
		public function set_full_day( $full_day ) {
			$this->set_prop( 'full_day', wc_string_to_bool( $full_day ) );
		}

		/**
		 * Set location
		 *
		 * @param string $location Product location
		 *
		 * @since 2.1
		 */
		public function set_location( $location ) {
			$this->set_prop( 'location', $location );
		}

		/**
		 * Set location latitude
		 *
		 * @param string $latitude Product location latitude
		 *
		 * @since 2.1
		 */
		public function set_location_latitude( $latitude ) {
			$this->set_prop( 'location_latitude', $latitude );
		}

		/**
		 * Set location longitude
		 *
		 * @param string $location_longitude Product location longitude
		 *
		 * @since 2.1
		 */
		public function set_location_longitude( $location_longitude ) {
			$this->set_prop( 'location_longitude', $location_longitude );
		}

		/**
		 * Set max bookings per unit
		 *
		 * @param int $max_bookings_per_unit Product max bookings per unit
		 *
		 * @since 2.1
		 */
		public function set_max_bookings_per_unit( $max_bookings_per_unit ) {
			$this->set_prop( 'max_bookings_per_unit', absint( $max_bookings_per_unit ) );
		}

		/**
		 * Set minimum duration
		 *
		 * @param int $minimum_duration Product minimum duration
		 *
		 * @since 2.1
		 */
		public function set_minimum_duration( $minimum_duration ) {
			$this->set_prop( 'minimum_duration', max( 1, absint( $minimum_duration ) ) );
		}

		/**
		 * Set maximum duration
		 *
		 * @param int $maximum_duration Product maximum duration
		 *
		 * @since 2.1
		 */
		public function set_maximum_duration( $maximum_duration ) {
			$maximum_duration = $this->is_type_fixed_blocks() ? 1 : absint( $maximum_duration );
			$this->set_prop( 'maximum_duration', absint( $maximum_duration ) );
		}

		/**
		 * Set confirmation required
		 *
		 * @param bool|string $confirmation_required Whether the product requires confirmation or not.
		 *
		 * @since 2.1
		 */
		public function set_confirmation_required( $confirmation_required ) {
			$this->set_prop( 'confirmation_required', wc_string_to_bool( $confirmation_required ) );
		}

		/**
		 * Get cancellation available
		 *
		 * @param bool|string $cancellation_available Whether the booking cancellation is available or not.
		 *
		 * @since 2.1
		 */
		public function set_cancellation_available( $cancellation_available ) {
			$this->set_prop( 'cancellation_available', wc_string_to_bool( $cancellation_available ) );
		}

		/**
		 * Set cancellation available up to
		 *
		 * @param int $cancellation_available_up_to Product cancellation available up to
		 *
		 * @since 2.1
		 */
		public function set_cancellation_available_up_to( $cancellation_available_up_to ) {
			$this->set_prop( 'cancellation_available_up_to', absint( $cancellation_available_up_to ) );
		}

		/**
		 * Get cancellation available up to unit
		 *
		 * @param string $cancellation_available_up_to_unit Product cancellation available up to unit
		 *
		 * @since 2.1
		 */
		public function set_cancellation_available_up_to_unit( $cancellation_available_up_to_unit ) {
			$this->set_prop( 'cancellation_available_up_to_unit', $cancellation_available_up_to_unit );
		}

		/**
		 * Set check-in
		 *
		 * @param string $check_in Product check-in
		 *
		 * @since 2.1
		 */
		public function set_check_in( $check_in ) {
			$this->set_prop( 'check_in', $check_in );
		}

		/**
		 * Set check-out
		 *
		 * @param string $check_out Product check-out
		 *
		 * @since 2.1
		 */
		public function set_check_out( $check_out ) {
			$this->set_prop( 'check_out', $check_out );
		}

		/**
		 * Set allowed start days
		 *
		 * @param array $allowed_start_days Product allowed start days
		 *
		 * @since 2.1
		 */
		public function set_allowed_start_days( $allowed_start_days ) {
			$this->set_prop( 'allowed_start_days', is_array( $allowed_start_days ) ? $allowed_start_days : array() );
		}

		/**
		 * Set daily start time
		 *
		 * @param string $daily_start_time Product daily start time
		 *
		 * @since 2.1
		 */
		public function set_daily_start_time( $daily_start_time ) {
			$this->set_prop( 'daily_start_time', yith_wcbk_time_slot( $daily_start_time ) );
		}

		/**
		 * Set buffer
		 *
		 * @param int $buffer Product buffer
		 *
		 * @since 2.1
		 */
		public function set_buffer( $buffer ) {
			$this->set_prop( 'buffer', absint( $buffer ) );
		}

		/**
		 * Set time increment based on duration
		 *
		 * @param bool|string $time_increment_based_on_duration Whether the time increment is based on duration or not.
		 *
		 * @since 2.1
		 */
		public function set_time_increment_based_on_duration( $time_increment_based_on_duration ) {
			$this->set_prop( 'time_increment_based_on_duration', wc_string_to_bool( $time_increment_based_on_duration ) );
		}

		/**
		 * Set time increment including buffer
		 *
		 * @param bool|string $time_increment_including_buffer Whether the time increment includes buffer or not.
		 *
		 * @since 2.1
		 */
		public function set_time_increment_including_buffer( $time_increment_including_buffer ) {
			$this->set_prop( 'time_increment_including_buffer', wc_string_to_bool( $time_increment_including_buffer ) );
		}

		/**
		 * Set minimum advance reservation
		 *
		 * @param int $minimum_advance_reservation Product minimum advance reservation
		 *
		 * @since 2.1
		 */
		public function set_minimum_advance_reservation( $minimum_advance_reservation ) {
			$this->set_prop( 'minimum_advance_reservation', absint( $minimum_advance_reservation ) );
		}

		/**
		 * Set minimum advance reservation unit
		 *
		 * @param string $minimum_advance_reservation_unit Product minimum advance reservation unit
		 *
		 * @since 2.1
		 */
		public function set_minimum_advance_reservation_unit( $minimum_advance_reservation_unit ) {
			$this->set_prop( 'minimum_advance_reservation_unit', in_array( $minimum_advance_reservation_unit, array( 'month', 'day', 'hour' ) ) ? $minimum_advance_reservation_unit : 'day' );
		}

		/**
		 * Set maximum advance reservation
		 *
		 * @param int $maximum_advance_reservation Product maximum advance reservation
		 *
		 * @since 2.1
		 */
		public function set_maximum_advance_reservation( $maximum_advance_reservation ) {
			$this->set_prop( 'maximum_advance_reservation', max( 1, absint( $maximum_advance_reservation ) ) );
		}

		/**
		 * Set maximum advance reservation unit
		 *
		 * @param string $maximum_advance_reservation_unit Product maximum advance reservation unit
		 *
		 * @since 2.1
		 */
		public function set_maximum_advance_reservation_unit( $maximum_advance_reservation_unit ) {
			$this->set_prop( 'maximum_advance_reservation_unit', in_array( $maximum_advance_reservation_unit, array( 'year', 'month', 'day' ) ) ? $maximum_advance_reservation_unit : 'year' );
		}

		/**
		 * Set availability rules
		 *
		 * @param array|YITH_WCBK_Availability_Rule[] $availability_rules Product availability rules
		 *
		 * @since 2.1
		 */
		public function set_availability_rules( $availability_rules ) {
			if ( ! ! $availability_rules && is_array( $availability_rules ) ) {
				$availability_rules = array_map( 'yith_wcbk_availability_rule', $availability_rules );
			} else {
				$availability_rules = array();
			}

			$this->set_prop( 'availability_rules', $availability_rules );
		}

		/**
		 * Set base price
		 *
		 * @param string $base_price Product base price
		 *
		 * @since 2.1
		 */
		public function set_base_price( $base_price ) {
			$this->set_prop( 'base_price', wc_format_decimal( $base_price ) );
		}

		/**
		 * Set multiply base price by number of people
		 *
		 * @param bool|string $multiply Whether the cost are multiplied by the number of people or not.
		 *
		 * @since 2.1
		 */
		public function set_multiply_base_price_by_number_of_people( $multiply ) {
			$this->set_prop( 'multiply_base_price_by_number_of_people', wc_string_to_bool( $multiply ) );
		}

		/**
		 * Set extra price per person
		 *
		 * @param string $price Product extra price per person
		 *
		 * @since 2.1
		 */
		public function set_extra_price_per_person( $price ) {
			$this->set_prop( 'extra_price_per_person', wc_format_decimal( $price ) );
		}

		/**
		 * Set extra price per person greater than
		 *
		 * @param string $price Product extra price per person
		 *
		 * @since 2.1
		 */
		public function set_extra_price_per_person_greater_than( $price ) {
			$this->set_prop( 'extra_price_per_person_greater_than', absint( $price ) );
		}

		/**
		 * Set weekly discount
		 *
		 * @param int $discount the discount to apply
		 *
		 * @since 2.1
		 */
		public function set_weekly_discount( $discount ) {
			$this->set_prop( 'weekly_discount', absint( max( 0, $discount ) ) );
		}

		/**
		 * Set monthly discount
		 *
		 * @param int $discount the discount to apply
		 *
		 * @since 2.1
		 */
		public function set_monthly_discount( $discount ) {
			$this->set_prop( 'monthly_discount', absint( max( 0, $discount ) ) );
		}

		/**
		 * Set last minute discount
		 *
		 * @param int $discount the discount to apply
		 *
		 * @since 2.1
		 */
		public function set_last_minute_discount( $discount ) {
			$this->set_prop( 'last_minute_discount', absint( max( 0, $discount ) ) );
		}

		/**
		 * Set last minute discount - days before arrival
		 *
		 * @param int $days the days before arrival
		 *
		 * @since 2.1
		 */
		public function set_last_minute_discount_days_before_arrival( $days ) {
			$this->set_prop( 'last_minute_discount_days_before_arrival', absint( $days ) );
		}

		/**
		 * Set multiply fixed base fee by number of people
		 *
		 * @param bool|string $multiply Whether the cost are multiplied by the number of people or not.
		 *
		 * @since 2.1
		 */
		public function set_multiply_fixed_base_fee_by_number_of_people( $multiply ) {
			$this->set_prop( 'multiply_fixed_base_fee_by_number_of_people', wc_string_to_bool( $multiply ) );
		}

		/**
		 * Set fixed base fee
		 *
		 * @param string $fixed_base_fee Product fixed base fee
		 *
		 * @since 2.1
		 */
		public function set_fixed_base_fee( $fixed_base_fee ) {
			$this->set_prop( 'fixed_base_fee', wc_format_decimal( $fixed_base_fee ) );
		}

		/**
		 * Set price rules
		 *
		 * @param array|YITH_WCBK_Price_Rule[] $price_rules Product price rules
		 *
		 * @since 2.1
		 */
		public function set_price_rules( $price_rules ) {
			if ( ! ! $price_rules && is_array( $price_rules ) ) {
				$price_rules = array_map( 'yith_wcbk_price_rule', $price_rules );
			} else {
				$price_rules = array();
			}
			$this->set_prop( 'price_rules', $price_rules );
		}

		/**
		 * Set enable people
		 *
		 * @param bool|string $enable_people Whether the people are enabled or not.
		 *
		 * @since 2.1
		 */
		public function set_enable_people( $enable_people ) {
			$this->set_prop( 'enable_people', wc_string_to_bool( $enable_people ) );
		}

		/**
		 * Set minimum number of people
		 *
		 * @param int $minimum_number_of_people Product minimum number of people
		 *
		 * @since 2.1
		 */
		public function set_minimum_number_of_people( $minimum_number_of_people ) {
			$this->set_prop( 'minimum_number_of_people', max( 1, absint( $minimum_number_of_people ) ) );
		}

		/**
		 * Set maximum number of people
		 *
		 * @param int $maximum_number_of_people Product maximum number of people
		 *
		 * @since 2.1
		 */
		public function set_maximum_number_of_people( $maximum_number_of_people ) {
			$this->set_prop( 'maximum_number_of_people', absint( $maximum_number_of_people ) );
		}

		/**
		 * Set count people as separate bookings
		 *
		 * @param bool|string $count_people_as_separate_bookings Whether the people are counted as separate bookings or not.
		 *
		 * @since 2.1
		 */
		public function set_count_people_as_separate_bookings( $count_people_as_separate_bookings ) {
			$this->set_prop( 'count_people_as_separate_bookings', wc_string_to_bool( $count_people_as_separate_bookings ) );
		}

		/**
		 * Set enable people types
		 *
		 * @param bool|string $enable_people_types Whether the people types are enabled or not.
		 *
		 * @since 2.1
		 */
		public function set_enable_people_types( $enable_people_types ) {
			$this->set_prop( 'enable_people_types', wc_string_to_bool( $enable_people_types ) );
		}

		/**
		 * Set people types
		 *
		 * @param array $people_types Product people types
		 *
		 * @since 2.1
		 */
		public function set_people_types( $people_types ) {
			$people_types = is_array( $people_types ) ? $people_types : array();
			foreach ( $people_types as $_key => $_value ) {
				$id = isset( $_value['id'] ) ? $_value['id'] : $_key;
				if ( $id && 'publish' === get_post_status( $id ) ) {
					if ( isset( $_value['base_cost'] ) ) {
						$people_types[ $_key ]['base_cost'] = wc_format_decimal( $_value['base_cost'] );
					}
					if ( isset( $_value['block_cost'] ) ) {
						$people_types[ $_key ]['block_cost'] = wc_format_decimal( $_value['block_cost'] );
					}
				} else {
					unset( $people_types[ $_key ] );
				}
			}
			$this->set_prop( 'people_types', $people_types );
		}

		/**
		 * Set service ids
		 *
		 * @param int[] $service_ids Product service ids
		 *
		 * @since 2.1
		 */
		public function set_service_ids( $service_ids ) {
			$this->set_prop( 'service_ids', array_filter( array_map( 'absint', $service_ids ) ) );
		}

		/**
		 * Set external calendars
		 *
		 * @param array $external_calendars Product external calendars
		 *
		 * @since 2.1
		 */
		public function set_external_calendars( $external_calendars ) {
			if ( is_array( $external_calendars ) ) {
				foreach ( $external_calendars as $key => $calendar ) {
					if ( empty( $calendar['url'] ) ) {
						unset( $external_calendars[ $key ] );
					}
				}
			}
			$this->set_prop( 'external_calendars', is_array( $external_calendars ) ? $external_calendars : array() );
		}

		/**
		 * Set external calendars key
		 *
		 * @param string $external_calendars_key Product external calendars key
		 *
		 * @since 2.1
		 */
		public function set_external_calendars_key( $external_calendars_key ) {
			if ( ! $external_calendars_key ) {
				$external_calendars_key = yith_wcbk_generate_external_calendars_key();
			}
			$this->set_prop( 'external_calendars_key', $external_calendars_key );
		}

		/**
		 * Set external calendars last sync
		 *
		 * @param int $external_calendars_last_sync Product external calendars last sync
		 *
		 * @since 2.1
		 */
		public function set_external_calendars_last_sync( $external_calendars_last_sync ) {
			$this->set_prop( 'external_calendars_last_sync', absint( $external_calendars_last_sync ) );
		}

		/**
		 * Set external calendars last sync
		 *
		 * @param array $extra_costs the product extra costs
		 *
		 * @since 2.1
		 */
		public function set_extra_costs( $extra_costs ) {
			if ( ! ! $extra_costs && is_array( $extra_costs ) ) {
				$extra_costs = array_map( 'yith_wcbk_product_extra_cost', $extra_costs );
				$extra_costs = array_reduce( $extra_costs, 'yith_wcbk_product_extra_costs_array_reduce' );
			} else {
				$extra_costs = array();
			}

			$this->set_prop( 'extra_costs', $extra_costs );
		}

		/*
        |--------------------------------------------------------------------------
        | Conditionals
        |--------------------------------------------------------------------------
        */

		/**
		 * return true if it's possible showing availability of the current product in calendar
		 *
		 * @param string $step
		 *
		 * @return bool
		 * @since 2.0.3
		 */
		public function can_show_availability( $step = '' ) {
			$show = $this->get_max_bookings_per_unit() > 1;
			if ( $show && $step ) {
				switch ( $step ) {
					case 'day':
						$show = 'day' === $this->get_duration_unit();
						break;
					case 'h':
					case 'hour':
					case 'hours':
						$show = $this->has_time();
						break;
					case 'm':
					case 'minute':
					case 'minutes':
						$show = 'minute' === $this->get_duration_unit();
						break;
				}
			}

			return $show;
		}

		/**
		 * che the product availability
		 *
		 * @param int  $from
		 * @param int  $to
		 * @param bool $exclude_time
		 *
		 * @return bool
		 */
		public function check_availability( $from, $to, $exclude_time = false ) {
			$date_helper = YITH_WCBK_Date_Helper();
			$available   = true;

			$global_availability_rules  = YITH_WCBK()->settings->get_global_availability_rules();
			$product_availability_rules = $this->get_availability_rules();
			$availability_rules         = array_merge( $global_availability_rules, $product_availability_rules );
			$availability_rules         = apply_filters( 'yith_wcbk_product_availability_rules_when_checking_for_availability', $availability_rules, $global_availability_rules, $product_availability_rules, $this );
			/** @var YITH_WCBK_Availability_Rule[] $availability_rules */

			$tmp_from = $from;
			$tmp_to   = $to - 1; // subtract one second to fix days and months availability (include the last rule day)

			foreach ( $availability_rules as $rule ) {
				if ( $rule->is_enabled() && $rule->is_valid() ) {

					$range_is_bookable = $rule->get_bookable() === 'yes';
					$days_enabled      = 'month' !== $this->get_duration_unit() && $rule->has_days_enabled();
					$intersect         = ! $range_is_bookable || $days_enabled;

					$check = $date_helper->check_date_inclusion_in_range( $rule->get_type(), $rule->get_from(), $rule->get_to(), $tmp_from, $tmp_to, $intersect );
					$check = apply_filters( 'yith_wcbk_booking_is_available_check_is_in_range', $check, $rule, $tmp_from, $tmp_to, $available, $this );

					if ( $check && $days_enabled ) {

						$range_is_bookable = true;
						$check             = false;
						$times_enabled     = $rule->has_times_enabled();

						foreach ( $rule->get_days() as $day_number => $day_bookable ) {
							$day_is_bookable = $day_bookable === 'yes';
							// set the "intersect" param to true to check all days, one by one, if the range contains the day
							$day_check = $date_helper->check_date_inclusion_in_range( 'day', $day_number, $day_number, $tmp_from, $tmp_to, true );

							if ( $day_check ) {
								$check = true;

								if ( 'disabled' === $day_bookable ) {
									$range_is_bookable = $range_is_bookable && $available;
									continue;
								}

								if ( ! $exclude_time && $times_enabled && $this->has_time() ) {
									$time_from = $rule->get_day_time_from_by_day( $day_number );
									$time_to   = $rule->get_day_time_to_by_day( $day_number );

									$intersect  = ! $day_is_bookable;
									$time_check = $date_helper->check_date_inclusion_in_range( 'time', $time_from, $time_to, $tmp_from, $tmp_to, $intersect );

									if ( $time_check ) {
										$range_is_bookable = $range_is_bookable && $day_is_bookable;
									} else {
										$range_is_bookable = $range_is_bookable && $available;
									}
								} else {
									if ( $exclude_time && $times_enabled ) {
										if ( ! $day_is_bookable ) {
											$range_is_bookable = $range_is_bookable && $available;
										}
									} else {
										$range_is_bookable = $range_is_bookable && $day_is_bookable;
									}
								}
							}
						}
					}

					if ( $check ) {
						$available = $range_is_bookable;
					}
				}
			}

			return $available;

		}

		/**
		 * Check if the duration type is "Fixed blocks"
		 *
		 * @return boolean
		 */
		public function is_type_fixed_blocks() {
			return 'fixed' === $this->get_duration_type();
		}

		/**
		 * return true if duration unit is hour or minute
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		public function has_time() {
			return in_array( $this->get_duration_unit(), array( 'hour', 'minute' ) );
		}

		/**
		 * Checks if a product has the calendar picker enabled
		 *
		 * @return bool
		 */
		public function has_calendar_picker_enabled() {
			return $this->get_enable_calendar_range_picker() && 'customer' === $this->get_duration_type() && 'day' === $this->get_duration_unit() && 1 === $this->get_duration();
		}

		/**
		 * Check if has people enabled.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function has_people() {
			return $this->get_enable_people();
		}

		/**
		 * Check if this booking has services
		 *
		 * @return bool
		 */
		public function has_services() {
			return ! ! $this->get_service_ids();
		}

		/**
		 * return true if the weekly discount is enabled
		 *
		 * @return bool
		 */
		public function is_weekly_discount_enabled() {
			return $this->get_weekly_discount() && 'customer' === $this->get_duration_type() && 'day' === $this->get_duration_unit() && 1 === $this->get_duration();
		}

		/**
		 * return true if the monthly discount is enabled
		 *
		 * @return bool
		 */
		public function is_monthly_discount_enabled() {
			return $this->get_monthly_discount() && 'customer' === $this->get_duration_type() && 'day' === $this->get_duration_unit() && 1 === $this->get_duration();
		}

		/**
		 * return true if the last minute discount is allowed from the start date
		 *
		 * @param int|string $start the start date of the booking
		 *
		 * @return bool
		 */
		public function is_last_minute_discount_allowed( $start ) {
			$start = ! is_numeric( $start ) ? strtotime( $start ) : $start;
			$now   = time();

			if ( ! $this->has_time() ) {
				$start = strtotime( 'midnight', $start );
				$now   = strtotime( 'midnight', $now );
			}

			return apply_filters( 'yith_wcbk_is_last_minute_discount_allowed', $this->get_last_minute_discount() && ( $now >= $start - $this->get_last_minute_discount_days_before_arrival() * DAY_IN_SECONDS ), $this, $now, $start );
		}

		/**
		 * return true if time increment based on duration is enabled
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		public function is_time_increment_based_on_duration() {
			return $this->get_time_increment_based_on_duration();
		}

		/**
		 * return true if time increment based on duration is enabled
		 *
		 * @return bool
		 * @since 2.0.7
		 */
		public function is_time_increment_including_buffer() {
			return $this->is_type_fixed_blocks() && $this->has_time() && $this->get_time_increment_including_buffer();
		}

		/**
		 * Checks if a product has multiply costs by persons enabled.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function has_multiply_base_price_by_number_of_people() {
			return $this->has_people() && $this->get_multiply_base_price_by_number_of_people();
		}

		/**
		 * Checks if a product has multiply costs by persons enabled.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function has_multiply_fixed_base_fee_by_number_of_people() {
			return $this->has_people() && $this->get_multiply_fixed_base_fee_by_number_of_people();
		}

		/**
		 * Checks if a product has count persons as bookings enabled.
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function has_count_people_as_separate_bookings_enabled() {
			return $this->has_people() && $this->get_count_people_as_separate_bookings();
		}

		/**
		 * Check if has people types enabled.
		 *
		 * @return boolean
		 * @since 2.1
		 */
		public function has_people_types_enabled() {
			return $this->has_people() && $this->get_enable_people_types() && ! ! $this->get_enable_people_types();
		}

		/**
		 * Check if this booking is available
		 *
		 * @param array $args {
		 *
		 * @return bool
		 * @var int     $to   [optional] timestamp to date
		 *                    }
		 * @var int     $from [optional] timestamp from date
		 */
		public function is_available( $args = array() ) {
			do_action( 'yith_wcbk_booking_before_is_available', $args, $this );
			$available                        = true;
			$date_helper                      = YITH_WCBK_Date_Helper();
			$now                              = time();
			$minimum_advance_reservation      = $this->get_minimum_advance_reservation();
			$minimum_advance_reservation_unit = $this->get_minimum_advance_reservation_unit();
			$unit                             = $this->get_duration_unit();
			$relative_maximum_duration        = $this->get_maximum_duration() * $this->get_duration();
			$relative_minimum_duration        = $this->get_minimum_duration() * $this->get_duration();

			$from                        = isset( $args['from'] ) ? $args['from'] : $now;
			$to                          = ! empty( $args['to'] ) ? $args['to'] : false;
			$exclude_booked              = isset( $args['exclude_booked'] ) ? $args['exclude_booked'] : false;
			$exclude_time                = isset( $args['exclude_time'] ) ? $args['exclude_time'] : false;
			$check_start_date            = isset( $args['check_start_date'] ) ? $args['check_start_date'] : true;
			$check_min_max_duration      = isset( $args['check_min_max_duration'] ) ? $args['check_min_max_duration'] : true;
			$check_non_available_in_past = isset( $args['check_non_available_in_past'] ) ? $args['check_non_available_in_past'] : true;
			$check_person_number         = isset( $args['check_person_number'] ) ? $args['check_person_number'] : true;
			$persons                     = isset( $args['persons'] ) ? max( 1, absint( $args['persons'] ) ) : $this->get_minimum_number_of_people();
			$exclude_order_id            = isset( $args['exclude_order_id'] ) ? $args['exclude_order_id'] : 0;

			if ( ! $this->has_people() ) {
				$persons = 0;
			}

			$return                = isset( $args['return'] ) ? $args['return'] : 'bool';
			$include_reasons       = 'array' === $return;
			$non_available_reasons = array();

			// Not available in past for Time booking
			if ( isset( $args['from'] ) && ! $exclude_time && $check_non_available_in_past && $this->has_time() ) {
				if ( $from < current_time( 'timestamp' ) ) {
					$available                                = false;
					$non_available_reasons['start-date-past'] = __( 'The selected start date has already passed', 'yith-booking-for-woocommerce' );
				}
			}

			// Not available in past (based on 'Allow after' | default 'today midnight'); take into account the current time.
			$min_date_midnight  = in_array( $minimum_advance_reservation_unit, array( 'month', 'day' ) ) ? 'midnight' : '';
			$min_date_timestamp = strtotime( "+{$minimum_advance_reservation} {$minimum_advance_reservation_unit}s {$min_date_midnight}", current_time( 'timestamp' ) );
			if ( $check_non_available_in_past && $from < $min_date_timestamp ) {
				$available                                        = false;
				$_format                                          = $this->has_time() ? ( wc_date_format() . ' ' . wc_time_format() ) : wc_date_format();
				$_min_date                                        = date_i18n( $_format, $min_date_timestamp );
				$non_available_reasons['start-non-allowed-after'] = sprintf( __( 'The selected start date is not allowed; you cannot book it before %s', 'yith-booking-for-woocommerce' ), $_min_date );
			}


			if ( ( $available || $include_reasons ) && $check_start_date && $allowed_start_days = $this->get_allowed_start_days() ) {
				$from_day = date( 'N', $from );
				if ( ! in_array( $from_day, $allowed_start_days ) ) {
					$available                                      = false;
					$non_available_reasons['start-day-non-allowed'] = __( 'The selected start day is not allowed', 'yith-booking-for-woocommerce' );
				}
			}

			if ( ! $to ) {
				$_duration = $check_min_max_duration ? $relative_minimum_duration : 1;
				$to        = $date_helper->get_time_sum( $from, $_duration, $unit );
				if ( $this->is_full_day() ) {
					$to = $date_helper->get_time_sum( $to, - 1, 'day' );
				}
			}

			if ( $this->is_full_day() ) {
				$to = strtotime( '00:00:00', $to );
				$to = $date_helper->get_time_sum( $to, 1, 'day' );
			}

			if ( $check_min_max_duration && ( $available || $include_reasons ) ) {
				$min_to = $date_helper->get_time_sum( $from, $relative_minimum_duration, $unit, true );

				if ( $to < $min_to ) {
					$available                             = false;
					$_min_duration_html                    = yith_wcbk_format_duration( $relative_minimum_duration, $unit );
					$non_available_reasons['min-duration'] = sprintf( __( 'Min duration: %s', 'yith-booking-for-woocommerce' ), $_min_duration_html );
				}

				if ( $relative_maximum_duration > 0 ) {
					$max_to = $date_helper->get_time_sum( $from, $relative_maximum_duration, $unit, true );

					if ( $this->is_full_day() ) {
						$max_to = $date_helper->get_time_sum( $max_to, 1, 'day' ) - 1;
					}

					if ( $to > $max_to ) {
						$available                             = false;
						$_max_duration_html                    = yith_wcbk_format_duration( $relative_maximum_duration, $unit );
						$non_available_reasons['max-duration'] = sprintf( __( 'Max duration: %s', 'yith-booking-for-woocommerce' ), $_max_duration_html );
					}
				}

				if ( $this->get_duration() > 1 ) {
					$_duration = $date_helper->get_time_diff( $from, $to, $unit );
					if ( $_duration % $this->get_duration() !== 0 ) {
						$available                                      = false;
						$non_available_reasons['duration-non-multiple'] = __( 'The selected duration is not allowed', 'yith-booking-for-woocommerce' );
					}
				}
			}

			if ( $check_person_number && $this->has_people() && ( $available || $include_reasons ) ) {
				if ( $persons < $this->get_minimum_number_of_people() ) {
					$available                            = false;
					$non_available_reasons['min-persons'] = sprintf( __( 'Minimum people: %s', 'yith-booking-for-woocommerce' ), $this->get_minimum_number_of_people() );
				}

				if ( $this->get_maximum_number_of_people() && $persons > $this->get_maximum_number_of_people() ) {
					$available                            = false;
					$non_available_reasons['max-persons'] = sprintf( __( 'Maximum people: %s', 'yith-booking-for-woocommerce' ), $this->get_maximum_number_of_people() );
				}
			}

			if ( ( $available || $include_reasons ) ) {
				$maximum_advance_reservation      = $this->get_maximum_advance_reservation();
				$maximum_advance_reservation_unit = $this->get_maximum_advance_reservation_unit();
				// Not available in future (based on 'Maximum advance reservation' | default '+1 year')
				$max_date_timestamp = strtotime( "+{$maximum_advance_reservation} {$maximum_advance_reservation_unit}s midnight", $now );
				if ( $to > $max_date_timestamp ) {
					$available                            = false;
					$non_available_reasons['allow-until'] = __( 'The end date is beyond available ones', 'yith-booking-for-woocommerce' );
				}
			}

			$_remained = '';

			if ( $available ) {
				$is_same_date = strtotime( 'midnight', $from ) === strtotime( 'midnight', $to - 1 );

				// Check if booking is available depending on Global availability settings and Product availability settings
				if ( ! $is_same_date && $this->has_time() && ! $exclude_time ) {
					// check availability for each single day to allow "fluid" availability
					$tmp_from = $tmp_to = $from;
					do {
						if ( ! $available ) {
							break;
						}

						$tmp_to    = min( $to, strtotime( 'tomorrow midnight', $tmp_to ) );
						$available = $this->check_availability( $tmp_from, $tmp_to, $exclude_time );
						$tmp_from  = $tmp_to;

					} while ( $tmp_to < $to );

				} else {
					$available = $this->check_availability( $from, $to, $exclude_time );
				}


				// Check if exist other booked booking (for the same product) in the same dates!
				if ( ! $exclude_booked && $available && $this->get_max_bookings_per_unit() ) {
					$get_post_args = array();
					if ( isset( $args['_booking_id'] ) ) {
						// exclude the booking if the customer is paying for a his/her confirmed booking
						$product_id_to_exclude = apply_filters( 'yith_wcbk_booking_product_id_to_translate', absint( $args['_booking_id'] ) );
						$get_post_args         = array(
							'exclude' => $product_id_to_exclude,
						);
					}

					$product_id                = apply_filters( 'yith_wcbk_booking_product_id_to_translate', $this->get_id() );
					$include_externals         = $this->has_external_calendars();
					$count_persons_as_bookings = $this->has_count_people_as_separate_bookings_enabled();
					$max_booking_per_block     = $this->get_max_bookings_per_unit();

					if ( $buffer = $this->get_buffer() ) {
						$from = $date_helper->get_time_sum( $from, - $buffer, $unit );
						$to   = $date_helper->get_time_sum( $to, $buffer, $unit );
					}

					$count_max_booked_bookings_args = compact( 'product_id', 'from', 'to', 'unit', 'include_externals', 'count_persons_as_bookings', 'get_post_args' );
					if ( $max_booking_per_block < 2 ) {
						$count_max_booked_bookings_args['return'] = 'total';
					}

					$count_max_booked_bookings_args['exclude_order_id'] = $exclude_order_id;

					$number_of_bookings = YITH_WCBK_Booking_Helper()->count_max_booked_bookings_per_unit_in_period( $count_max_booked_bookings_args );

					$booking_weight = ! ! $count_persons_as_bookings ? $persons : 1;


					if ( $number_of_bookings + $booking_weight > $max_booking_per_block ) {
						$available = false;

						if ( $_remained = $max_booking_per_block - $number_of_bookings ) {
							if ( $this->has_people() && $count_persons_as_bookings ) {
								$non_available_reasons['max-bookings-per-unit'] = sprintf( __( 'Too many people selected (%s remained)', 'yith-booking-for-woocommerce' ), $_remained );
							} else {
								$non_available_reasons['max-bookings-per-unit'] = sprintf( __( '(%s remained)', 'yith-booking-for-woocommerce' ), $_remained );
							}
						}
					}
				}
			}

			$available             = apply_filters( 'yith_wcbk_booking_is_available', $available, $args, $this );
			$non_available_reasons = apply_filters( 'yith_wcbk_booking_is_available_non_available_reasons', $non_available_reasons, $args, $this, $_remained );
			$non_available_reasons = ! $available ? $non_available_reasons : array();

			if ( 'array' === $return ) {
				$available = compact( 'available', 'non_available_reasons' );
			}

			return $available;
		}

		/**
		 * Check if the confirmation is required
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function is_confirmation_required() {
			return $this->get_confirmation_required();
		}

		/**
		 * check if the product is full day
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function is_full_day() {
			return $this->get_full_day() && 'day' === $this->get_duration_unit();
		}

		/**
		 * check if the cancellation is available
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function is_cancellation_available() {
			return $this->get_cancellation_available();
		}


		/**
		 * Returns false if the product cannot be bought.
		 *
		 * @return bool
		 */
		public function is_purchasable() {
			return apply_filters( 'woocommerce_is_purchasable', $this->exists() && ( 'publish' === $this->get_status() || current_user_can( 'edit_post', $this->get_id() ) ), $this );
		}

		/**
		 * The booking product is sold individually
		 *
		 * @return boolean
		 */
		public function is_sold_individually() {
			return true;
		}

		/**
		 * Checks if a product is virtual (has no shipping).
		 *
		 * @return bool
		 */
		public function is_virtual() {
			return apply_filters( 'yith_wcbk_booking_product_is_virtual', parent::is_virtual(), $this );
		}

		/**
		 * @param string|int $date
		 * @param bool       $exclude_booked
		 *
		 * @return bool
		 * @since 2.0.8
		 */
		public function has_at_least_one_time_slot_available_on( $date, $exclude_booked = false ) {
			if ( ! is_numeric( $date ) ) {
				$date = strtotime( $date );
			}
			$current_day = strtotime( 'midnight', $date );
			$next_day    = strtotime( 'tomorrow', $current_day );
			$available   = true;

			if ( $this->has_time() ) {
				$check = true;
				if ( apply_filters( 'yith_wcbk_product_has_at_least_one_time_slot_available_on_check_only_if_bookings_exist', false ) ) {
					$_count_args = array(
						'product_id'        => $this->get_id(),
						'from'              => $current_day,
						'to'                => $next_day,
						'include_externals' => true,
					);
					$check       = $exclude_booked ? 1 : YITH_WCBK_Booking_Helper()->count_booked_bookings_in_period( $_count_args );
				}

				if ( $check ) {
					$available = ! ! $this->create_availability_time_array( $current_day );
				}
			} else {
				$available = $this->is_available( array( 'from' => $current_day, 'to' => $next_day ) );
			}

			return $available;
		}

		/**
		 * return true if has external calendars
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		public function has_external_calendars() {
			return ! ! $this->get_external_calendars();
		}

		/**
		 * return true if externals has already loaded (and not expired) for this product
		 *
		 * @return bool
		 * @since 2.0
		 */
		public function has_externals_synchronized() {
			$expiring_time = get_option( 'yith-wcbk-external-calendars-sync-expiration', 6 * HOUR_IN_SECONDS );
			$now           = time();
			$last_loaded   = $this->get_external_calendars_last_sync();

			return ! ! $last_loaded && ( $now - $last_loaded < $expiring_time );
		}

		/**
		 * is this a valid external calendars key?
		 *
		 * @param string $key
		 *
		 * @return bool
		 * @since 2.1
		 */
		public function is_valid_external_calendars_key( $key ) {
			return $key === $this->get_external_calendars_key();
		}


		/*
        |--------------------------------------------------------------------------
        | Non-CRUD Getters
        |--------------------------------------------------------------------------
        */

		/**
		 * Get the add to cart button text
		 *
		 * @access public
		 * @return string
		 */
		public function add_to_cart_text() {
			return apply_filters( 'woocommerce_product_add_to_cart_text', yith_wcbk_get_label( 'read-more' ), $this );
		}

		/**
		 * Get the add to cart button text for the single page.
		 *
		 * @return string
		 */
		public function single_add_to_cart_text() {
			$text = ! $this->is_confirmation_required() ? yith_wcbk_get_label( 'add-to-cart' ) : yith_wcbk_get_label( 'request-confirmation' );

			return apply_filters( 'woocommerce_product_single_add_to_cart_text', $text, $this );
		}

		/**
		 * Calculate costs (block or base) for a single timestamp
		 *
		 * @param        $timestamp
		 * @param string $type
		 * @param array  $args {
		 *
		 * @return float
		 * @var array person_type the type of person ['id' => id, ...]
		 *                     }
		 */
		public function calculate_cost( $timestamp, $type = 'base_price', $args = array() ) {
			do_action( 'yith_wcbk_booking_before_calculate_cost', $timestamp, $type, $this );

			// backward compatibility
			if ( 'block' === $type ) {
				$type = 'base_price';
			} elseif ( 'base' === $type ) {
				$type = 'fixed_base_fee';
			}

			$allowed_types = array( 'base_price', 'fixed_base_fee' );
			$type          = in_array( $type, $allowed_types ) ? $type : 'base_price';
			$cost          = 'base_price' === $type ? (float) $this->get_base_price() : (float) $this->get_fixed_base_fee();

			$price_rules        = $this->get_price_rules();
			$global_price_rules = YITH_WCBK()->settings->get_global_price_rules();
			$price_rules        = array_merge( $price_rules, $global_price_rules );

			$person_type_id = false;
			$person_number  = 0;

			if ( isset( $args['person_type'] ) ) {
				$current_person_type = $args['person_type'];
				$person_types        = $this->get_enabled_people_types();
				if ( $current_person_type['id'] !== 0 && isset( $person_types[ $current_person_type['id'] ] ) ) {
					$person_type_id = $current_person_type['id'];
					$person_number  = absint( $current_person_type['number'] );

					$product_person_type   = $person_types[ $current_person_type['id'] ];
					$person_type_cost_type = 'base_price' === $type ? 'block_cost' : 'base_cost';
					if ( $product_person_type[ $person_type_cost_type ] !== '' ) {
						$cost = (float) $product_person_type[ $person_type_cost_type ];
					}
				}
			}

			$date_helper = YITH_WCBK_Date_Helper();

			$persons  = isset( $args['persons'] ) ? absint( $args['persons'] ) : $this->get_minimum_number_of_people();
			$duration = isset( $args['duration'] ) ? absint( $args['duration'] ) : 1;

			$variables = array(
				'persons'   => $persons,
				'duration'  => $duration,
				'qty'       => 1,
				'extra_qty' => 1,
			);

			foreach ( $price_rules as $price_rule ) {
				/** @var YITH_WCBK_Price_Rule $price_rule */
				if ( $price_rule->is_enabled() ) {
					$date_from         = $timestamp;
					$date_to           = $timestamp;
					$conditions        = $price_rule->get_conditions();
					$current_variables = $variables;
					$check             = ! ! $conditions;

					foreach ( $conditions as $condition ) {
						$condition_type = $condition['type'];
						$condition_from = $condition['from'];
						$condition_to   = $condition['to'];
						$intersect      = false;

						$is_date_range = ! in_array( $condition_type, array( 'person', 'block' ) ) && 0 !== strpos( $condition_type, 'person-type-' );

						if ( $is_date_range ) {
							$condition_check = $date_helper->check_date_inclusion_in_range( $condition_type, $condition_from, $condition_to, $date_from, $date_to, $intersect );
						} else {
							$condition_check = false;
							$condition_from  = absint( $condition_from );
							$condition_to    = absint( $condition_to );

							if ( $condition_type === 'person' && $this->has_people() ) {
								$current_variables['qty']       = $persons;
								$current_variables['extra_qty'] = $persons - $condition_from + 1;
								if ( ( ! $condition_to || $persons <= $condition_to ) && $persons >= $condition_from ) {
									$condition_check = true;
								}
							} elseif ( $condition_type === 'block' ) {
								$current_variables['qty']       = $duration;
								$current_variables['extra_qty'] = $duration - $condition_from + 1;
								if ( ( ! $condition_to || $duration <= $condition_to ) && $duration >= $condition_from ) {
									$condition_check = true;
								}
							} elseif ( strpos( $condition_type, 'person-type-' ) === 0 && $this->has_people() && $this->has_people_types_enabled() ) {
								$range_person_type_id         = absint( str_replace( 'person-type-', '', $condition_type ) );
								$multiply_by_number_of_people = 'base_price' === $type ? $this->has_multiply_base_price_by_number_of_people() : $this->has_multiply_fixed_base_fee_by_number_of_people();
								$apply_to_all_people          = ! $multiply_by_number_of_people;
								$apply_to_all_people          = apply_filters( 'yith_wcbk_booking_calculate_cost_apply_person_type_rule_to_all_people', $apply_to_all_people, $timestamp, $type, $args, $this );

								if ( ! empty( $args['person_types'] ) && $apply_to_all_people ) {
									foreach ( $args['person_types'] as $_current_person_type ) {
										if ( $_current_person_type['id'] == $range_person_type_id ) {
											$_person_number                 = absint( $_current_person_type['number'] );
											$current_variables['qty']       = $_person_number;
											$current_variables['extra_qty'] = $_person_number - $condition_from + 1;
											if ( ( ! $condition_to || $_person_number <= $condition_to ) && $_person_number >= $condition_from ) {
												$condition_check = true;
											}
											break;
										}
									}
								} else {
									if ( $person_type_id && $person_type_id == $range_person_type_id ) {
										$current_variables['qty']       = $person_number;
										$current_variables['extra_qty'] = $person_number - $condition_from + 1;
										if ( ( ! $condition_to || $person_number <= $condition_to ) && $person_number >= $condition_from ) {
											$condition_check = true;
										}
									}
								}
							}
						}

						$check = $check && $condition_check;
					}

					if ( ! empty( $args['person_types'] ) ) {
						foreach ( $args['person_types'] as $_current_person_type ) {
							$_person_number                                               = absint( $_current_person_type['number'] );
							$current_variables[ 'person_' . $_current_person_type['id'] ] = $_person_number;

						}
					}

					$variable_alias = array(
						'extra_qty' => array( 'qty_diff' ),
					);

					foreach ( $variable_alias as $key => $alias_array ) {
						foreach ( $alias_array as $alias ) {
							$current_variables[ $alias ] = $current_variables[ $key ];
						}
					}

					$check = apply_filters( 'yith_wcbk_booking_calculate_cost_check_is_in_range', $check, $price_rule, $timestamp, $type, $this );

					if ( $check ) {
						$this_cost     = 'base_price' === $type ? $price_rule->get_base_price() : $price_rule->get_base_fee();
						$this_operator = 'base_price' === $type ? $price_rule->get_base_price_operator() : $price_rule->get_base_fee_operator();

						if ( strpos( $this_cost, '*' ) ) {
							list( $this_cost, $variable ) = explode( '*', $this_cost, 2 );
							// the $current_variables[ $variable ] is an INTEGER: for this reason it should be > 1
							if ( isset( $current_variables[ $variable ] ) && $current_variables[ $variable ] > 1 ) {
								$this_cost *= $current_variables[ $variable ];
							} elseif ( 'person_' === substr( $variable, 0, 7 ) && empty( $current_variables[ $variable ] ) ) {
								$this_cost = 0;
							}
						} elseif ( strpos( $this_cost, '/' ) ) {
							list( $this_cost, $variable ) = explode( '/', $this_cost, 2 );
							// the $current_variables[ $variable ] is an INTEGER: for this reason it should be > 1
							if ( ! empty( $current_variables[ $variable ] ) && $current_variables[ $variable ] > 1 ) {
								$this_cost /= $current_variables[ $variable ];
							} elseif ( 'person_' === substr( $variable, 0, 7 ) && empty( $current_variables[ $variable ] ) ) {
								$this_cost = 0;
							}
						}

						$this_cost = (float) $this_cost;

						switch ( $this_operator ) {
							case 'add':
								$cost = $cost + $this_cost;
								break;
							case 'sub':
								$cost = $cost - $this_cost;
								break;
							case 'mul':
								$cost = $cost * $this_cost;
								break;
							case 'div':
								if ( $this_cost != 0 ) {
									$cost = $cost / $this_cost;
								}
								break;
							case 'set-to':
								$cost = $this_cost;
								break;
							case 'add-percentage':
								$cost = $cost * ( 1 + $this_cost / 100 );
								break;
							case 'sub-percentage':
								$cost = $cost * ( 1 - $this_cost / 100 );
								break;
						}
					}
				}
			}

			$cost = apply_filters( 'yith_wcbk_booking_calculate_cost', $cost, $timestamp, $type, $this );

			return (float) $cost;

		}

		/**
		 * calculate the extra price per person
		 *
		 * @param array $args
		 *
		 * @return float|int
		 * @since 2.1
		 */
		public function calculate_extra_price_per_person( $args = array() ) {
			$extra_price_per_person = 0;
			if ( $this->has_people() && ! $this->has_multiply_base_price_by_number_of_people() && $this->get_extra_price_per_person() && '' !== $this->get_extra_price_per_person_greater_than() ) {
				$people_number = isset( $args['persons'] ) ? absint( $args['persons'] ) : $this->get_minimum_number_of_people();
				$people_types  = $this->get_enabled_people_types();
				if ( isset( $args['person_types'] ) ) {
					foreach ( $args['person_types'] as $people_type ) {
						$person_type_id     = absint( $people_type['id'] );
						$person_type_number = absint( $people_type['number'] );

						if ( isset( $people_types[ $person_type_id ] ) && isset( $people_types[ $person_type_id ]['block_cost'] ) && "0" === $people_types[ $person_type_id ]['block_cost'] ) {
							$people_number -= $person_type_number;
						}
					}
				}

				$extra_people = $people_number - $this->get_extra_price_per_person_greater_than();
				if ( $extra_people > 0 ) {
					$extra_price_per_person = $extra_people * $this->get_extra_price_per_person();
				}
			}

			return $extra_price_per_person;
		}


		/**
		 * parse args before calculating price
		 *
		 * @param array $args
		 *
		 * @return array
		 * @since 2.1
		 */
		public function parse_price_args( $args = array() ) {
			$args['from'] = isset( $args['from'] ) ? $args['from'] : time();
			$args['from'] = ! is_numeric( $args['from'] ) ? strtotime( $args['from'] ) : $args['from'];

			$args['persons'] = isset( $args['persons'] ) ? $args['persons'] : $this->get_minimum_number_of_people();
			if ( ! empty( $args['person_types'] ) && is_array( $args['person_types'] ) && $this->has_people() ) {
				$args['persons'] = 0;
				foreach ( $args['person_types'] as $people_type ) {
					$args['persons'] += absint( $people_type['number'] );
				}
			}

			if ( isset( $args['to'] ) ) {
				$args['to'] = ! is_numeric( $args['to'] ) ? strtotime( $args['to'] ) : $args['to'];

				if ( $this->is_full_day() ) {
					$args['to'] = YITH_WCBK_Date_Helper()->get_time_sum( $args['to'], 1, 'day' );
				}

				$args['duration'] = YITH_WCBK_Date_Helper()->get_time_diff( $args['from'], $args['to'], $this->get_duration_unit() ) / $this->get_duration();
				$args['duration'] = max( $args['duration'], $this->get_minimum_duration() );
			} else if ( ! isset( $args['duration'] ) ) {
				$args['duration'] = 1;
			}

			return $args;
		}

		/**
		 * retrieve an array with Totals
		 *
		 * @param array $args
		 * @param bool  $formatted if true format each price in the 'display' parameter
		 *
		 * @return array
		 * @since 2.1
		 */
		public function calculate_totals( $args = array(), $formatted = false ) {
			$args          = $this->parse_price_args( $args );
			$from          = $args['from'];
			$duration      = $args['duration'];
			$people_number = $args['persons'];


			$totals = apply_filters( 'yith_wcbk_totals', array(
				'fixed_base_fee'                        => array( 'label' => __( 'Fixed base fee', 'yith-booking-for-woocommerce' ), 'value' => 0 ),
				'base_price_and_extra_price_per_person' => array( 'value' => 0 ),
				'base_price'                            => array( 'label' => __( 'Base Price', 'yith-booking-for-woocommerce' ), 'value' => 0 ),
				'extra_price_per_person'                => array( 'label' => __( 'Extra price for additional people', 'yith-booking-for-woocommerce' ), 'value' => 0 ),
				'weekly_discount'                       => array( 'label' => __( 'Weekly discount', 'yith-booking-for-woocommerce' ), 'value' => 0 ),
				'monthly_discount'                      => array( 'label' => __( 'Monthly discount', 'yith-booking-for-woocommerce' ), 'value' => 0 ),
				'last_minute_discount'                  => array( 'label' => __( 'Last minute discount', 'yith-booking-for-woocommerce' ), 'value' => 0 ),
				'services'                              => array( 'label' => __( 'Services', 'yith-booking-for-woocommerce' ), 'value' => 0 ),
			) );

			$default_people_types = array( array( 'id' => 0, 'number' => $people_number ) );

			// Fixed Base Fee --------------------------------------
			$people_types = ! empty( $args['person_types'] ) && $this->has_multiply_fixed_base_fee_by_number_of_people() ? $args['person_types'] : $default_people_types;

			foreach ( $people_types as $people_type ) {
				// the fixed base fee depends only from starting date
				$calculate_cost_args                = $args;
				$calculate_cost_args['person_type'] = $people_type;
				$person_type_number                 = absint( $people_type['number'] );
				$fixed_base_fee                     = $this->calculate_cost( $from, 'fixed_base_fee', $calculate_cost_args );

				if ( $this->has_multiply_fixed_base_fee_by_number_of_people() ) {
					$fixed_base_fee = $fixed_base_fee * $person_type_number;
				}

				$totals['fixed_base_fee']['value'] += $fixed_base_fee;
			}

			// Base Price --------------------------------------
			$people_types = ! empty( $args['person_types'] ) && $this->has_multiply_base_price_by_number_of_people() ? $args['person_types'] : $default_people_types;

			foreach ( $people_types as $people_type ) {
				$calculate_cost_args                = $args;
				$calculate_cost_args['person_type'] = $people_type;
				$person_type_number                 = absint( $people_type['number'] );
				$unit                               = $this->get_duration_unit();
				$single_block_duration              = $this->get_duration();
				$unit_cost                          = 0;
				$weekly_discount                    = 0;
				$monthly_discount                   = 0;
				$actual_week_cost                   = 0;
				$actual_month_cost                  = 0;

				// increase the block cost for every block in base of settings
				for ( $i = 0; $i < $duration; $i ++ ) {
					$referring_date      = YITH_WCBK_Date_Helper()->get_time_sum( $from, $single_block_duration * $i, $unit, true );
					$_current_block_cost = $this->calculate_cost( $referring_date, 'base_price', $calculate_cost_args );
					$unit_cost           += $_current_block_cost;
					$actual_week_cost    += $_current_block_cost;
					$actual_month_cost   += $_current_block_cost;

					$check_for_weekly_discount  = ( $i + 1 ) % 7 === 0;
					$check_for_monthly_discount = ( $i + 1 ) % 30 === 0;

					if ( apply_filters( 'yith_wcbk_apply_weekly_discount', true, $duration ) && $check_for_weekly_discount && $this->is_weekly_discount_enabled() ) {
						$_current_discount = $this->get_weekly_discount() / 100 * $actual_week_cost;
						$weekly_discount   += $_current_discount;
						$actual_month_cost -= $_current_discount;
						$actual_week_cost  = 0;
					}

					if ( $check_for_monthly_discount && $this->is_monthly_discount_enabled() ) {
						$monthly_discount  += $this->get_monthly_discount() / 100 * $actual_month_cost;
						$actual_month_cost = 0;
					}
				}

				if ( $this->has_multiply_base_price_by_number_of_people() ) {
					$unit_cost        = $unit_cost * $person_type_number;
					$weekly_discount  = $weekly_discount * $person_type_number;
					$monthly_discount = $monthly_discount * $person_type_number;
				}

				$totals['base_price']['value']       += $unit_cost;
				$totals['weekly_discount']['value']  -= $weekly_discount;
				$totals['monthly_discount']['value'] -= $monthly_discount;
			}

			// Extra Price Per Person --------------------------------------
			$single_extra_price_per_person             = $this->calculate_extra_price_per_person( $args );
			$totals['extra_price_per_person']['value'] = $single_extra_price_per_person * $duration;
			$extra_price_per_person_weekly_discount    = 0;
			$extra_price_per_person_monthly_discount   = 0;
			if ( $this->is_weekly_discount_enabled() && $duration >= 7 ) {
				$extra_price_per_person_weekly_discount = $single_extra_price_per_person * ( absint( $duration / 7 ) ) * $this->get_weekly_discount() / 100;
			}

			if ( $this->is_monthly_discount_enabled() && $duration >= 30 ) {
				$extra_price_per_person_monthly_discount = ( $single_extra_price_per_person - $extra_price_per_person_weekly_discount ) * ( absint( $duration / 30 ) ) * $this->get_monthly_discount() / 100;
			}

			$totals['weekly_discount']['value']  -= $extra_price_per_person_weekly_discount;
			$totals['monthly_discount']['value'] -= $extra_price_per_person_monthly_discount;

			if ( $this->is_weekly_discount_enabled() ) {
				$totals['weekly_discount']['label'] = sprintf( __( '%s%% weekly discount', 'yith-booking-for-woocommerce' ), $this->get_weekly_discount() );
			}

			if ( $this->is_monthly_discount_enabled() ) {
				$totals['monthly_discount']['label'] = sprintf( __( '%s%% monthly discount', 'yith-booking-for-woocommerce' ), $this->get_monthly_discount() );
			}

			// Extra Costs --------------------------------------
			foreach ( $this->get_extra_costs() as $extra_cost ) {
				if ( $extra_cost->is_valid() ) {
					$totals["extra_cost_{$extra_cost->get_identifier()}"] = array(
						'label' => $extra_cost->get_name(),
						'value' => $extra_cost->calculate_cost( $duration, $people_number ),
					);
				}
			}

			// Service Costs --------------------------------------
			$service_args                = array(
				'persons'                    => $people_number,
				'person_types'               => ! empty( $args['person_types'] ) ? $args['person_types'] : $people_types,
				'duration'                   => $duration,
				'booking_services'           => isset( $args['booking_services'] ) ? $args['booking_services'] : array(),
				'booking_service_quantities' => isset( $args['booking_service_quantities'] ) ? $args['booking_service_quantities'] : array(),
			);
			$totals['services']['value'] = $this->calculate_service_costs( $service_args );


			// Last Minute Discount --------------------------------------
			if ( $this->is_last_minute_discount_allowed( $from ) ) {
				$totals_for_last_minute_discount = apply_filters( 'yith_wcbk_booking_product_last_minute_discount_applied_on', array( 'fixed_base_fee', 'base_price', 'extra_price_per_person', 'weekly_discount', 'monthly_discount' ), $args, $this );
				$total_to_discount               = 0;
				foreach ( $totals_for_last_minute_discount as $_key ) {
					if ( isset( $totals[ $_key ] ) && isset( $totals[ $_key ]['value'] ) ) {
						$total_to_discount += $totals[ $_key ]['value'];
					}
				}

				$totals['last_minute_discount']['value'] = - ( $total_to_discount * $this->get_last_minute_discount() / 100 );
			}

			$totals = apply_filters( 'yith_wcbk_booking_product_calculated_price_totals', $totals, $args, $formatted, $this );

			if ( $formatted ) {
				// merge base price and extra price per person
				if ( apply_filters( 'yith_wcbk_booking_product_merge_base_price_and_extra_price_per_person_in_totals', true, $this ) ) {
					$base_price_and_extra_price_per_person = (float) $totals['base_price']['value'] + $totals['extra_price_per_person']['value'];
					$price_per_unit_average                = $base_price_and_extra_price_per_person / $duration;
					if ( 1 === $this->get_duration() ) {
						$_label = sprintf( "%s x %s", yith_wcbk_get_formatted_price_to_display( $this, $price_per_unit_average ), yith_wcbk_format_duration( $duration, $this->get_duration_unit() ) );
					} else {
						$_label = $totals['base_price']['label'];
					}
					$totals['base_price_and_extra_price_per_person'] = array(
						'label' => $_label,
						'value' => $base_price_and_extra_price_per_person,
					);
					unset( $totals['base_price'] );
					unset( $totals['extra_price_per_person'] );
				} else {
					if ( 1 === $this->get_duration() ) {
						$price_per_unit_average        = (float) $totals['base_price']['value'] / $duration;
						$totals['base_price']['label'] = sprintf( "%s x %s", wc_price( $price_per_unit_average ), yith_wcbk_format_duration( $duration, $this->get_duration_unit() ) );
					}
				}

				foreach ( $totals as $total_key => $total ) {
					if ( ! empty( $total['value'] ) ) {
						$totals[ $total_key ]['display'] = yith_wcbk_get_formatted_price_to_display( $this, $total['value'] );
					}
				}

				$totals = apply_filters( 'yith_wcbk_booking_product_calculated_price_totals_formatted', $totals, $args, $this );
			}

			$totals = array_filter( $totals, function ( $total ) {
				return ! empty( $total['value'] );
			} );

			return $totals;
		}

		/**
		 * calculate the total price from totals array
		 *
		 * @param array $totals
		 *
		 * @return float|int
		 * @since 2.1
		 */
		public function calculate_price_from_totals( $totals = array() ) {
			return array_sum( wp_list_pluck( $totals, 'value' ) );
		}

		/**
		 * Calculate price for Booking product
		 *
		 * @param array $args
		 *
		 * @return float price
		 */
		public function calculate_price( $args = array() ) {
			$totals = $this->calculate_totals( $args, false );
			$price  = $this->calculate_price_from_totals( $totals );

			return apply_filters( 'yith_wcbk_booking_product_calculated_price', $price, $args, $this );
		}

		/**
		 * Calculate the total service costs
		 *
		 * @param array $args
		 *
		 * @return float
		 */
		public function calculate_service_costs( $args = array() ) {
			extract( $args );
			/**
			 * @var int   $persons
			 * @var array $person_types
			 * @var int   $duration
			 * @var array $booking_services
			 * @var array $booking_service_quantities
			 */
			$service_cost = 0;

			if ( $persons > 0 && $this->has_services() ) {
				$services = $this->get_service_ids();
				foreach ( $services as $service_id ) {
					$service = yith_get_booking_service( $service_id );

					if ( ! $service->is_valid() ) {
						continue;
					}

					if ( $service->is_optional() && ! in_array( $service_id, $booking_services ) ) {
						continue;
					}

					$service_cost_total = 0;

					if ( $service->is_multiply_per_persons() ) {
						foreach ( $person_types as $person_type ) {
							$pt_id                = absint( $person_type['id'] );
							$pt_number            = absint( $person_type['number'] );
							$current_service_cost = $service->get_price( $pt_id );

							if ( $service->is_multiply_per_blocks() ) {
								$current_service_cost *= $duration;
							}
							if ( $service->is_multiply_per_persons() ) {
								$current_service_cost *= $pt_number;
							}

							$service_cost_total += floatval( $current_service_cost );
						}
					} else {
						$service_cost_total = $service->get_price();
						if ( $service->is_multiply_per_blocks() ) {
							$service_cost_total *= $duration;
						}
					}

					if ( $service->is_quantity_enabled() ) {
						$quantity = isset( $booking_service_quantities[ $service->id ] ) ? $booking_service_quantities[ $service->id ] : 0;
						$quantity = $service->validate_quantity( $quantity );

						$service_cost_total = $service_cost_total * $quantity;
					}

					$service_cost_total = apply_filters( 'yith_wcbk_booking_product_single_service_cost_total', $service_cost_total, $service, $args, $this );

					$service_cost += floatval( $service_cost_total );
				}
			}

			$service_cost = apply_filters( 'yith_wcbk_booking_product_calculate_service_costs', $service_cost, $args, $this );

			return (float) $service_cost;
		}

		/**
		 * create availability calendar
		 *
		 * @param        $from_year
		 * @param        $from_month
		 * @param        $to_year
		 * @param        $to_month
		 * @param string $return
		 * @param string $range
		 * @param bool   $exclude_booked
		 * @param bool   $check_start_date
		 * @param bool   $check_min_max_duration
		 *
		 * @return array
		 */
		public function create_availability_calendar( $from_year, $from_month, $to_year, $to_month, $return = 'all', $range = 'day', $exclude_booked = false, $check_start_date = true, $check_min_max_duration = true ) {
			$calendar = array();

			for ( $year = $from_year; $year <= $to_year; $year ++ ) {
				$first_month        = $year == $from_year ? $from_month : 1;
				$last_month         = $year == $to_year ? ( $to_month - 1 ) : 12; // last month is not included
				$this_year_calendar = $this->create_availability_year_calendar( $year, $first_month, $last_month, $return, $range, $exclude_booked, $check_start_date, $check_min_max_duration );
				if ( ! empty( $this_year_calendar ) ) {
					$calendar[ $year ] = $this_year_calendar;
				}

			}

			return $calendar;
		}

		/**
		 * @param int    $year
		 * @param int    $month
		 * @param string $return
		 * @param string $range day or month
		 * @param bool   $exclude_booked
		 * @param bool   $check_start_date
		 * @param bool   $check_min_max_duration
		 *
		 * @return array
		 */
		public function create_availability_month_calendar( $year = 0, $month = 0, $return = 'all', $range = 'day', $exclude_booked = false, $check_start_date = true, $check_min_max_duration = true ) {
			$disable_day_if_no_time = YITH_WCBK()->settings->get( 'disable-day-if-no-time-available', 'no' ) === 'yes';

			// default for year and month
			$year  = $year == 0 ? date( 'Y', time() ) : $year;
			$month = $month == 0 ? date( 'm', time() ) : $month;

			$month_calendar = array();

			$first_day_of_month = strtotime( $year . '-' . $month . '-01' );
			if ( $this->has_time() ) {
				$first_day_of_month = strtotime( $year . '-' . $month . '-01', current_time( 'timestamp' ) );
			}
			$first_day_of_next_month = strtotime( ' + 1 month', $first_day_of_month );

			$current_day = $first_day_of_month;
			while ( $current_day < $first_day_of_next_month ) {
				$number_of_day = date( 'j', $current_day );
				switch ( $range ) {
					case 'month':
						$next_day = $first_day_of_next_month;
						break;
					case 'day':
					default:
						$next_day = strtotime( ' + 1 day', $current_day );
				}

				$is_available = $this->is_available( array(
														 'from'                   => $current_day,
														 'exclude_booked'         => ( $exclude_booked || $this->has_time() ), // force excluding booked for time-bookings
														 'exclude_time'           => true,
														 'check_start_date'       => $check_start_date,
														 'check_min_max_duration' => $check_min_max_duration,
														 'check_non_available_in_past' => ! $this->has_time(), // exclude checking in past for time-bookings
													 ) );

				if ( $disable_day_if_no_time && $this->has_time() ) {
					$check = true;

					if ( apply_filters( 'yith_wcbk_disable_day_if_no_time_available_check_only_if_bookings_exist', false ) ) {
						$_count_args = array(
							'product_id'        => $this->get_id(),
							'from'              => $current_day,
							'to'                => $next_day,
							'include_externals' => true,
						);
						$check       = $exclude_booked ? 1 : YITH_WCBK_Booking_Helper()->count_booked_bookings_in_period( $_count_args );
					}

					if ( $check ) {
						$is_available = $is_available && $this->create_availability_time_array( $current_day );
					}
				}

				switch ( $return ) {
					case 'bookable':
						if ( $is_available ) {
							$month_calendar[ $number_of_day ] = $is_available;
						}
						break;
					case 'not_bookable':
						if ( ! $is_available ) {
							$month_calendar[ $number_of_day ] = $is_available;
						}
						break;
					default:
						$month_calendar[ $number_of_day ] = $is_available;

				}
				$current_day = $next_day;
			}

			return $month_calendar;
		}

		/**
		 * create availability year calendar
		 *
		 * @param int    $year
		 * @param int    $from_month
		 * @param int    $to_month
		 * @param string $return
		 * @param string $range
		 * @param bool   $exclude_booked
		 * @param bool   $check_start_date
		 * @param bool   $check_min_max_duration
		 *
		 * @return array
		 */
		public function create_availability_year_calendar( $year = 0, $from_month = 1, $to_month = 12, $return = 'all', $range = 'day', $exclude_booked = false, $check_start_date = true, $check_min_max_duration = true ) {
			$year_calendar = array();
			for ( $i = $from_month; $i <= $to_month; $i ++ ) {
				$this_month_calendar = $this->create_availability_month_calendar( $year, $i, $return, $range, $exclude_booked, $check_start_date, $check_min_max_duration );
				if ( ! empty( $this_month_calendar ) ) {
					$year_calendar[ $i ] = $this_month_calendar;
				}
			}

			return $year_calendar;
		}

		/**
		 * create an array of available times
		 *
		 * @param string $from
		 * @param int    $duration
		 *
		 * @return array
		 * @since 2.0.0
		 */
		public function create_availability_time_array( $from = '', $duration = 0 ) {
			$function     = __FUNCTION__;
			$cached_key   = compact( 'function', 'from', 'duration' );
			$cached_value = YITH_WCBK_Cache()->get_product_data( $this->get_id(), $cached_key );

			$is_today = false;
			if ( is_numeric( $from ) ) {
				$is_today = strtotime( 'midnight', $from ) === strtotime( 'midnight', time() );
			} elseif ( ! ! $from ) {
				$is_today = strtotime( 'midnight', strtotime( $from ) ) === strtotime( 'midnight', time() );
			}

			if ( ! $is_today && ! is_null( $cached_value ) ) {
				$times = $cached_value;
			} else {
				$times = array();
				$unit  = $this->get_duration_unit();
				if ( in_array( $unit, array( 'hour', 'minute' ) ) ) {
					$date_helper      = YITH_WCBK_Date_Helper();
					$booking_duration = $this->get_duration();
					$duration         = ! ! $duration ? $duration : $this->get_minimum_duration();
					$daily_start_time = $this->get_daily_start_time();

					if ( ! $from ) {
						$from = strtotime( $daily_start_time );
					} else {
						if ( ! is_numeric( $from ) ) {
							$from = strtotime( $from );
						}

						$daily_start_timestamp = strtotime( $daily_start_time, $from );
						$from                  = max( $from, $daily_start_timestamp );
					}

					$tomorrow     = $date_helper->get_time_sum( $from, 1, 'day', true );
					$current_time = $from;

					if ( $this->is_time_increment_based_on_duration() ) {
						$unit_increment = $booking_duration * $this->get_minimum_duration();
					} else {
						$unit_increment = 'hour' === $unit ? 1 : yith_wcbk_get_minimum_minute_increment();
					}

					if ( $this->is_time_increment_including_buffer() && $this->get_buffer() ) {
						$unit_increment += $this->get_buffer();
					}

					$unit_increment = apply_filters( 'yith_wcbk_booking_product_create_availability_time_array_unit_increment', $unit_increment, $this, $from, $duration );

					/**
					 * TODO: add custom time slots in Booking product settings
					 */
					$custom_time_slots = apply_filters( 'yith_wcbk_booking_product_create_availability_time_array_custom_time_slots', array(), $this, $from, $duration );
					if ( $custom_time_slots ) {
						foreach ( $custom_time_slots as $time_slot ) {
							$current_time = strtotime( date( 'Y-m-d', $from ) . ' ' . $time_slot );
							$_duration    = absint( $duration ) * $booking_duration;
							$_to          = $date_helper->get_time_sum( $current_time, $_duration, $unit );
							$is_available = $this->is_available( array( 'from' => $current_time, 'to' => $_to ) );
							if ( $is_available ) {
								$time_to_add = date( 'H:i', $current_time );
								$times[]     = $time_to_add;
							}
						}
					} else {
						while ( $current_time < $tomorrow ) {
							$_duration    = absint( $duration ) * $booking_duration;
							$_to          = $date_helper->get_time_sum( $current_time, $_duration, $unit );
							$is_available = $this->is_available( array( 'from' => $current_time, 'to' => $_to ) );
							if ( $is_available ) {
								$time_to_add = date( 'H:i', $current_time );
								$times[]     = $time_to_add;
							}
							$current_time = $date_helper->get_time_sum( $current_time, $unit_increment, $unit );
						}
					}

				}
				if ( ! $is_today ) {
					YITH_WCBK_Cache()->set_product_data( $this->get_id(), $cached_key, $times );
				}
			}

			return apply_filters( 'yith_wcbk_booking_product_create_availability_time_array', $times, $from, $duration, $this );
		}

		/**
		 * get the block duration html
		 */
		public function get_block_duration_html() {
			return yith_wcbk_format_duration( $this->get_duration(), $this->get_duration_unit() );
		}

		/**
		 * Retrieve an array containing the booking data
		 *
		 * @return array
		 */
		public function get_booking_data() {
			$booking_data_props = apply_filters( 'yith_wcbk_get_booking_data_props', array(
				'minimum_number_of_people',
				'maximum_number_of_people',
				'duration_unit',
				'minimum_duration',
				'maximum_duration',
				'full_day',
			), $this );

			$booking_data = array();
			try {
				/** @var YITH_WCBK_Product_Booking_Data_Store_CPT $data_store */
				$data_store = WC_Data_Store::load( 'product-booking' );

				foreach ( $booking_data_props as $prop ) {
					$getter = "get_{$prop}";
					if ( is_callable( array( $this, $getter ) ) ) {
						$booking_data[ $prop ] = $data_store->is_boolean_prop( $prop ) ? wc_bool_to_string( $this->$getter() ) : $this->$getter();
					}
				}
			} catch ( Exception $e ) {

			}

			$old_booking_data = array(
				'min_persons' => $this->get_minimum_number_of_people(),
				'max_persons' => $this->get_maximum_number_of_people(),
				'all_day'     => $this->is_full_day() ? 'yes' : 'no',
			);

			$booking_data = $booking_data + $old_booking_data;


			return apply_filters( 'yith_wcbk_get_booking_data', $booking_data, $this );
		}


		/**
		 * Get the calculated price html
		 *
		 * @param string $price
		 *
		 * @return string
		 */
		public function get_calculated_price_html( $price = '' ) {
			if ( ! $price || is_array( $price ) ) {
				// backward compatibility, since the 1st params was an array before 2.1
				$args  = is_array( $price ) ? $price : array();
				$price = $this->calculate_price( $args );
			}
			$_price = wc_get_price_to_display( $this, array( 'price' => $price ) );
			$_price = apply_filters( 'yith_wcbk_get_calculated_price_html_price', $_price, $price, $this );

			if ( ! $_price ) {
				$price_html = apply_filters( 'yith_wcbk_booking_product_free_price_html', __( 'Free!', 'woocommerce' ), $this );
			} else {
				$price_html = wc_price( $_price ) . $this->get_price_suffix();
			}

			return apply_filters( 'yith_wcbk_booking_product_get_calculated_price_html', $price_html, $price, $this );
		}

		/**
		 * return the calculated default start date
		 *
		 * @return string
		 * @since 2.1
		 */
		public function get_calculated_default_start_date() {
			$date               = '';
			$default_start_date = $this->get_default_start_date();
			$offset             = (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
			$today_utc          = strtotime( 'now midnight' );

			if ( in_array( $default_start_date, array( 'today', 'tomorrow' ) ) ) {
				$minimum_advance_reservation = $this->get_minimum_advance_reservation();
				$timestamp                   = strtotime( $default_start_date ) + $offset;

				if ( $minimum_advance_reservation ) {
					$minimum_advance_reservation_unit = $this->get_minimum_advance_reservation_unit();
					$first_available_timestamp        = YITH_WCBK_Date_Helper()->get_time_sum( $today_utc, $minimum_advance_reservation, $minimum_advance_reservation_unit, true ) + $offset;
					if ( $timestamp < $first_available_timestamp ) {
						$timestamp = $first_available_timestamp;
					}
				}

				$date = date( 'Y-m-d', $timestamp );

			} elseif ( 'first-available' === $default_start_date ) {
				$current_day                 = $today_utc + $offset;
				$minimum_advance_reservation = $this->get_minimum_advance_reservation();
				if ( $minimum_advance_reservation ) {
					$date_helper                      = YITH_WCBK_Date_Helper();
					$minimum_advance_reservation_unit = $this->get_minimum_advance_reservation_unit();
					$current_day                      = $date_helper->get_time_sum( $current_day, $minimum_advance_reservation, $minimum_advance_reservation_unit, true ) + $offset;
				}
				$date_info           = yith_wcbk_get_booking_form_date_info( $this, array( 'include_default_start_date' => false, 'include_default_end_date' => false ) );
				$last_date           = strtotime( $date_info['next_year'] . '-' . $date_info['next_month'] . '-1 +1 month' ) + $offset;
				$not_available_dates = $this->get_not_available_dates( $date_info['current_year'], $date_info['current_month'], $date_info['next_year'], $date_info['next_month'], 'day' );

				$allowed_start_days = $this->get_allowed_start_days();

				do {
					$current_date = date( 'Y-m-d', $current_day );
					if ( ! in_array( $current_date, $not_available_dates ) && ( ! $allowed_start_days || in_array( date( 'N', $current_day ), $allowed_start_days ) ) ) {
						$date = $current_date;
						break;
					} else {
						$current_day = strtotime( '+1 day', $current_day );
					}

				} while ( $current_day < $last_date );

			} elseif ( 'custom' === $default_start_date ) {
				$date = $this->get_default_start_date_custom();
			}

			return apply_filters( 'yith_wcbk_booking_product_get_default_start_date', $date, $this );

		}

		/**
		 * get the admin calendar Url
		 *
		 * @return string
		 * @since 2.0.3
		 */
		public function get_admin_calendar_url() {
			$args = array(
				'post_type'  => YITH_WCBK_Post_Types::$booking,
				'page'       => 'yith-wcbk-booking-calendar',
				'product_id' => $this->get_id(),
			);
			$url  = add_query_arg( $args, admin_url( 'edit.php' ) );

			return apply_filters( 'yith_wcbk_product_get_admin_calendar_url', $url, $this );
		}

		/**
		 * Get the enabled people types
		 *
		 * @return array
		 */
		public function get_enabled_people_types() {
			return array_filter( $this->get_people_types(), function ( $people_type ) {
				return isset( $people_type['enabled'] ) && $people_type['enabled'] === 'yes';
			} );
		}

		/**
		 * return an array of bookings loaded from external calendars
		 *
		 * @param bool $force_loading
		 *
		 * @return YITH_WCBK_Booking_External[]
		 * @since 2.0
		 */
		public function get_externals( $force_loading = false ) {
			$calendars = $this->get_external_calendars();
			$externals = array();
			if ( $calendars ) {
				$load = $force_loading || ! $this->has_externals_synchronized();

				if ( $load ) {
					YITH_WCBK_Booking_Externals()->delete_externals_from_product_id( $this->get_id() );
					$externals = array();

					foreach ( $calendars as $calendar ) {
						$name = htmlspecialchars( $calendar['name'] );
						$url  = $calendar['url'];

						$timeout  = apply_filters( 'yith_wcbk_booking_product_get_externals_timeout', 15 );
						$response = wp_remote_get( $url, array( 'timeout' => $timeout ) );

						if ( ! is_wp_error( $response ) && 200 == $response['response']['code'] && 'OK' == $response['response']['message'] ) {
							$body = $response['body'];
							try {
								$ics_parser = new YITH_WCBK_ICS_Parser( $body, array(
									'product_id'    => $this->get_id(),
									'calendar_name' => $name,
								) );

								$externals = array_merge( $externals, $ics_parser->get_events() );

							} catch ( Exception $e ) {
								$message = sprintf( "Error while parsing ICS externals for product #%s - %s - %s",
													$this->get_id(), $e->getMessage(),
													print_r( compact( 'name', 'url', 'body' ), true )
								);

								yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GENERAL );
							}
						} else {
							$message = sprintf( "Error while retrieving externals for product #%s - %s",
												$this->get_id(),
												print_r( compact( 'name', 'url', 'response' ), true )
							);

							yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GENERAL );
						}
					}

					$externals = apply_filters( 'yith_wcbk_product_retrieved_externals', $externals, $this );

					// remove completed externals
					$externals = array_filter( $externals, function ( $external ) {
						/** @var YITH_WCBK_Booking_External $external */
						return ! $external->is_completed();
					} );

					YITH_WCBK_Booking_Externals()->add_externals( $externals, false );
					yith_wcbk_product_update_external_calendars_last_sync( $this );

				} else {
					$externals = YITH_WCBK_Booking_Externals()->get_externals_from_product_id( $this->get_id() );
				}
			}

			return $externals;
		}


		/**
		 * Get the location coordinates
		 *
		 * @return array|bool
		 */
		public function get_location_coordinates() {
			$coordinates = false;

			if ( $location = $this->get_location() ) {
				$latitude  = $this->get_location_latitude();
				$longitude = $this->get_location_longitude();

				if ( '' !== $latitude && '' !== $longitude ) {
					$coordinates = array( 'lat' => $latitude, 'lng' => $longitude, );
				} else {
					$this->update_location_coordinates();

					$latitude  = $this->get_location_latitude();
					$longitude = $this->get_location_longitude();

					if ( '' !== $latitude && '' !== $longitude ) {
						$coordinates = array( 'lat' => $latitude, 'lng' => $longitude, );
					}
				}
			}

			return $coordinates;
		}

		/**
		 * get non available dates
		 *
		 * @param        $from_year
		 * @param        $from_month
		 * @param        $to_year
		 * @param        $to_month
		 * @param string $range
		 * @param bool   $exclude_booked
		 * @param bool   $check_start_date
		 * @param bool   $check_min_max_duration
		 *
		 * @return array
		 */
		public function get_not_available_dates( $from_year, $from_month, $to_year, $to_month, $range = 'day', $exclude_booked = false, $check_start_date = false, $check_min_max_duration = true ) {
			$args     = compact( 'from_year', 'from_month', 'to_year', 'to_month', 'range', 'exclude_booked', 'check_start_date', 'check_min_max_duration' );
			$dates    = apply_filters( 'yith_wcbk_product_get_not_available_dates_before', null, $args, $this );
			$no_cache = apply_filters( 'yith_wcbk_product_get_not_available_dates_force_no_cache', false );
			if ( ! is_null( $dates ) ) {
				return $dates;
			}
			$cached_key = array_merge( array( 'function' => __FUNCTION__ ), $args );

			if ( ( $this->has_external_calendars() && ! $this->has_externals_synchronized() ) || $no_cache ) {
				$cached_value = null; // not use cache to consider new data for external calendars
			} else {
				$cached_value = YITH_WCBK_Cache()->get_product_data( $this->get_id(), $cached_key );
			}

			if ( ! is_null( $cached_value ) ) {
				$dates = $cached_value;
			} else {
				$calendar = $this->create_availability_calendar( $from_year, $from_month, $to_year, $to_month, 'not_bookable', $range, $exclude_booked, $check_start_date, $check_min_max_duration );
				$dates    = array();
				foreach ( $calendar as $year => $months ) {
					foreach ( $months as $month => $days ) {
						if ( $month < 10 ) {
							$month = '0' . $month;
						}
						foreach ( $days as $day => $bookable ) {
							if ( $day < 10 ) {
								$day = '0' . $day;
							}
							$dates[] = $year . '-' . $month . '-' . $day;
						}
					}
				}

				// set data if cache is enabled
				$no_cache || YITH_WCBK_Cache()->set_product_data( $this->get_id(), $cached_key, $dates );
			}

			return apply_filters( 'yith_wcbk_product_get_not_available_dates', $dates, $args, $this );
		}

		/**
		 * get non available months
		 *
		 * @param        $from_year
		 * @param        $from_month
		 * @param        $to_year
		 * @param        $to_month
		 *
		 * @return array
		 */
		public function get_not_available_months( $from_year, $from_month, $to_year, $to_month ) {
			$dates           = $this->get_not_available_dates( $from_year, $from_month, $to_year, $to_month, 'month', false, false );
			$number_of_dates = count( $dates );
			if ( $number_of_dates < 1 ) {
				return array();
			}

			$zero_array  = array_fill( 0, $number_of_dates, 0 );
			$seven_array = array_fill( 0, $number_of_dates, 7 );
			$dates       = array_map( 'substr', $dates, $zero_array, $seven_array );

			return $dates;
		}

		/**
		 * get the permalink by adding query args based on passed array
		 *
		 * @param array $booking_data
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function get_permalink_with_data( $booking_data = array() ) {
			$booking_data_array = array();
			foreach ( $booking_data as $id => $value ) {
				switch ( $id ) {
					case 'booking_services':
						if ( is_array( $value ) && ! ! $value ) {
							$booking_data_array[ $id ] = implode( ',', $value );
						} else {
							$booking_data_array[ $id ] = $value;
						}
						break;
					case 'person_types':
						if ( is_array( $value ) && ! ! $value ) {
							foreach ( $value as $child_id => $child_value ) {
								$current_id                        = 'person_type_' . absint( $child_id );
								$booking_data_array[ $current_id ] = $child_value;
							}
						}
						break;
					default:
						if ( is_scalar( $value ) ) {
							$booking_data_array[ $id ] = $value;
						}
						break;
				}
			}

			return add_query_arg( $booking_data_array, $this->get_permalink() );
		}

		/*
        |--------------------------------------------------------------------------
        | Other Methods
        |--------------------------------------------------------------------------
        */

		/**
		 * Load external calendars if not already loaded
		 *
		 * @since 2.0.0
		 */
		public function maybe_load_externals() {
			if ( $this->has_external_calendars() && ! $this->has_externals_synchronized() ) {
				$this->get_externals();
			}
		}

		/**
		 * regenerate product data
		 *
		 * @param array $data
		 */
		public function regenerate_data( $data = array() ) {
			$time_debug_key = __FUNCTION__ . '_' . $this->get_id();
			yith_wcbk_time_debug_start( $time_debug_key );
			if ( ! $data ) {
				$data = array( 'externals', 'not-available-dates' );
			}

			$data_debug = PHP_EOL . 'Data regenerated for ' . implode( ', ', $data );

			if ( in_array( 'externals', $data ) ) {
				$this->maybe_load_externals();
			}

			if ( in_array( 'not-available-dates', $data ) ) {
				$date_info           = yith_wcbk_get_booking_form_date_info( $this );
				$non_available_dates = $this->get_not_available_dates( $date_info['current_year'], $date_info['current_month'], $date_info['next_year'], $date_info['next_month'], 'day' );
				$data_debug          .= PHP_EOL . 'Non-available dates: ' . print_r( $non_available_dates, true );
			}

			$seconds = yith_wcbk_time_debug_end( $time_debug_key );
			yith_wcbk_maybe_debug( sprintf( 'Product Data regenerated for product #%s (%s seconds taken) %s', $this->get_id(), $seconds, $data_debug ) );

			do_action( 'yith_wcbk_booking_product_after_regenerating_data', $data, $this );
		}

		/*
        |--------------------------------------------------------------------------
        | Updaters and Deleters
        |--------------------------------------------------------------------------
        */

		/**
		 * update location coordinates based on product location
		 */
		public function update_location_coordinates() {
			$location = $this->get_location();
			$latitude = $longitude = '';
			if ( $location ) {
				$coordinates = YITH_WCBK()->maps->get_location_by_address( $location );
				if ( isset( $coordinates['lat'] ) && isset( $coordinates['lng'] ) ) {
					$latitude  = $coordinates['lat'];
					$longitude = $coordinates['lng'];
				}
			}

			// save changes only if needed
			if ( $this->get_location_latitude( 'edit' ) !== $latitude || $this->get_location_longitude( 'edit' ) !== $longitude ) {
				$this->set_location_latitude( $latitude );
				$this->set_location_longitude( $longitude );

				/**
				 * store changes directly to DB
				 *
				 * @var WC_Product_Booking $clone_product
				 */
				$clone_product = wc_get_product( $this );
				if ( $clone_product ) {
					$clone_product->set_location_latitude( $latitude );
					$clone_product->set_location_longitude( $longitude );
					$clone_product->save();
				}
			}
		}
	}
}
