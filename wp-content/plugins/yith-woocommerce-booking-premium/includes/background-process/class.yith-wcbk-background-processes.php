<?php
! defined( 'ABSPATH' ) && exit;

require_once 'abstract.yith-wcbk-background-process.php';
require_once 'class.yith-wcbk-background-process-google-calendar-sync.php';
require_once 'class.yith-wcbk-background-process-product-data.php';
require_once 'functions.yith-wcbk-background-process-funtions.php';

if ( ! class_exists( 'YITH_WCBK_Background_Processes' ) ) {
	/**
	 * Class YITH_WCBK_Background_Processes
	 *
	 * handle background processes
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Background_Processes {
		/** @var YITH_WCBK_Background_Processes */
		private static $_instance;

		/** @var YITH_WCBK_Background_Process_Google_Calendar_Sync */
		public $google_calendar_sync;

		/** @var YITH_WCBK_Background_Process_Product_Data */
		public $product_data;

		/** @var bool */
		private $_product_data_to_dispatch = false;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Background_Processes
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_WCBK_Background_Processes constructor.
		 */
		private function __construct() {
			$this->google_calendar_sync = new YITH_WCBK_Background_Process_Google_Calendar_Sync();
			$this->product_data         = new YITH_WCBK_Background_Process_Product_Data();

			add_action( 'yith_wcbk_background_process_product_data_update', array( $this, 'product_data_update' ), 10, 1 );

			add_action( 'shutdown', array( $this, 'shutdown' ) );
		}

		/**
		 * Schedule product data update
		 *
		 * @param int $product_id
		 * @param int $after
		 */
		public function schedule_product_data_update( $product_id, $after = 10 ) {
			$hook = 'yith_wcbk_background_process_product_data_update';
			$args = array( $product_id );

			wp_clear_scheduled_hook( $hook, $args );
			wp_schedule_single_event( time() + $after, $hook, $args );

			yith_wcbk_maybe_debug( sprintf( 'Background Process: Product Data update scheduled for product #%s', $product_id ), YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS );
		}

		/**
		 * Add the product data update to the queue of product_data background process
		 * before calling it you should delete the cache and delete external_calendars_last_sync for external update
		 *
		 * @param int $product_id
		 */
		public function product_data_update( $product_id ) {
			yith_wcbk_maybe_debug( sprintf( 'Background Process: Product Data update added to queue for product: %s', $product_id ), YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS );

			$this->product_data->push_to_queue( array(
													'callback' => 'yith_wcbk_bg_process_booking_product_regenerate_product_data',
													'params'   => array( $product_id ),
												) );
			$this->_product_data_to_dispatch = true;
		}

		/**
		 * Fires dispatch, if needed, on shutdown
		 */
		public function shutdown() {
			if ( $this->_product_data_to_dispatch ) {
				$this->product_data->save()->dispatch();
			}
		}
	}
}