<?php
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCBK_Notifier' ) ) {
	/**
	 * Class YITH_WCBK_Notifier
	 * handle notify behavior
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Notifier {

		/** @var YITH_WCBK_Notifier */
		private static $_instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Notifier
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_WCBK_Notifier constructor.
		 */
		private function __construct() {
			add_filter( 'woocommerce_email_classes', array( $this, 'add_email_classes' ) );
			add_filter( 'woocommerce_email_actions', array( $this, 'add_email_actions' ) );
		}


		/**
		 * Add email actions to WooCommerce email actions
		 *
		 * @param array $actions
		 *
		 * @return mixed
		 */
		public function add_email_actions( $actions ) {
			foreach ( array_keys( yith_wcbk_get_booking_statuses( true ) ) as $status ) {
				$actions[] = 'yith_wcbk_booking_status_' . $status;
			}

			$actions[] = 'yith_wcbk_new_booking';
			$actions[] = 'yith_wcbk_new_customer_note';

			return $actions;
		}

		/**
		 * add email classes to woocommerce
		 *
		 * @param array $emails
		 *
		 * @return array
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function add_email_classes( $emails ) {
			require_once YITH_WCBK_DIR . '/includes/emails/class.yith-wcbk-email.php';
			$emails['YITH_WCBK_Email_Booking_Status']               = include YITH_WCBK_DIR . '/includes/emails/class.yith-wcbk-email-booking-status.php';
			$emails['YITH_WCBK_Email_Admin_New_Booking']            = include YITH_WCBK_DIR . '/includes/emails/class.yith-wcbk-email-admin-new-booking.php';
			$emails['YITH_WCBK_Email_Customer_New_Booking']         = include YITH_WCBK_DIR . '/includes/emails/class.yith-wcbk-email-customer-new-booking.php';
			$emails['YITH_WCBK_Email_Customer_Confirmed_Booking']   = include YITH_WCBK_DIR . '/includes/emails/class.yith-wcbk-email-customer-confirmed-booking.php';
			$emails['YITH_WCBK_Email_Customer_Unconfirmed_Booking'] = include YITH_WCBK_DIR . '/includes/emails/class.yith-wcbk-email-customer-unconfirmed-booking.php';
			$emails['YITH_WCBK_Email_Customer_Cancelled_Booking']   = include YITH_WCBK_DIR . '/includes/emails/class.yith-wcbk-email-customer-cancelled-booking.php';
			$emails['YITH_WCBK_Email_Customer_Paid_Booking']        = include YITH_WCBK_DIR . '/includes/emails/class.yith-wcbk-email-customer-paid-booking.php';
			$emails['YITH_WCBK_Email_Customer_Completed_Booking']   = include YITH_WCBK_DIR . '/includes/emails/class.yith-wcbk-email-customer-completed-booking.php';
			$emails['YITH_WCBK_Email_Customer_Booking_Note']        = include YITH_WCBK_DIR . '/includes/emails/class.yith-wcbk-email-customer-booking-note.php';

			return $emails;
		}
	}
}