<?php
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCBK_Booking_Calendar' ) ) {

	/**
	 * Class YITH_WCBK_Booking_Calendar
	 *
	 * handle the booking calendar in admin
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Booking_Calendar {
		/** @var YITH_WCBK_Booking_Calendar */
		protected static $_instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Booking_Calendar
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_WCBK_Booking_Calendar constructor.
		 */
		protected function __construct() {
			add_action( 'admin_menu', array( $this, 'add_submenu' ) );
		}

		/**
		 * add Calendar Submenu to Booking Admin Menu
		 */
		public function add_submenu() {
			add_submenu_page( 'edit.php?post_type=' . YITH_WCBK_Post_Types::$booking,   //parent_slug
							  __( 'Calendar', 'yith-booking-for-woocommerce' ),         //page_title
							  __( 'Calendar', 'yith-booking-for-woocommerce' ),         //menu_title
							  'edit_' . YITH_WCBK_Post_Types::$booking . 's',           // capability
							  'yith-wcbk-booking-calendar',                             // menu_slug
							  array( $this, 'render_calendar_page' )                    // callback function
			);
		}

		/**
		 * Render Calendar page in base of requests
		 */
		public function render_calendar_page() {
			echo '<div class="wrap">';
			$view      = isset( $_REQUEST['view'] ) ? $_REQUEST['view'] : 'month';
			$view_file = YITH_WCBK_VIEWS_PATH . 'calendar/html-booking-calendar-' . $view . '.php';
			$args      = array();

			switch ( $view ) {
				case 'day':
					$default_time_step  = YITH_WCBK()->settings->get( 'calendar-day-default-time-step', '1h' );
					$default_start_time = YITH_WCBK()->settings->get( 'calendar-day-default-start-time', '00:00' );

					$default_start_time_check = explode( ':', $default_start_time );
					if ( ! ( 2 === count( $default_start_time_check ) && $default_start_time_check[0] < 24 && $default_start_time_check[1] < 60 ) ) {
						$default_start_time = '';
					}

					$date       = isset( $_REQUEST['date'] ) ? $_REQUEST['date'] : date( 'Y-m-d' );
					$time_step  = isset( $_REQUEST['time_step'] ) ? $_REQUEST['time_step'] : $default_time_step;
					$start_time = isset( $_REQUEST['start_time'] ) ? $_REQUEST['start_time'] : $default_start_time;

					$args = array(
						'view'       => $view,
						'date'       => $date,
						'time_step'  => $time_step,
						'start_time' => $start_time,
					);

					break;

				default:
					// month
					$view = 'month';

					$default_month = isset( $_REQUEST['date'] ) ? date( 'n', strtotime( $_REQUEST['date'] ) ) : date( 'n' );
					$default_year  = isset( $_REQUEST['date'] ) ? date( 'Y', strtotime( $_REQUEST['date'] ) ) : date( 'Y' );

					$month = isset( $_REQUEST['month'] ) ? absint( $_REQUEST['month'] ) : $default_month;
					$year  = isset( $_REQUEST['year'] ) ? absint( $_REQUEST['year'] ) : $default_year;

					$start_of_week              = absint( get_option( 'start_of_week', 1 ) );
					$first_day_of_current_month = date( 'N', strtotime( "$year-$month-01" ) );

					$diff = $start_of_week - $first_day_of_current_month;

					$start_timestamp = strtotime( $diff . ' days midnight', strtotime( "$year-$month-01" ) );
					$end_timestamp   = strtotime( '+34 days midnight', $start_timestamp );

					$last_day_of_month = strtotime( '+1 month -1 day', strtotime( "$year-$month-01" ) );
					if ( $end_timestamp < $last_day_of_month ) {
						$end_timestamp = strtotime( '+7 days', $end_timestamp );
					}

					$args = array(
						'view'            => $view,
						'month'           => $month,
						'year'            => $year,
						'start_timestamp' => $start_timestamp,
						'end_timestamp'   => $end_timestamp,
					);


					break;
			}

			extract( $args );
			if ( file_exists( $view_file ) ) {
				include $view_file;
			}

			echo "</div>";
		}

		/**
		 * print the action bar
		 */
		public function print_action_bar( $args ) {
			extract( $args );
			$view_file = YITH_WCBK_VIEWS_PATH . 'calendar/html-booking-calendar-action-bar.php';

			if ( file_exists( $view_file ) ) {
				include $view_file;
			}
		}

		/**
		 * return an array of time steps
		 *
		 * @return array
		 */
		public static function get_time_steps() {
			$time_steps = array(
				'1h'  => __( '1 h', 'yith-booking-for-woocommerce' ),
				'30m' => __( '30 min', 'yith-booking-for-woocommerce' ),
				'15m' => __( '15 min', 'yith-booking-for-woocommerce' ),
			);

			return apply_filters( 'yith_wcbk_calendar_day_time_steps', $time_steps );
		}
	}
}

return YITH_WCBK_Booking_Calendar::get_instance();