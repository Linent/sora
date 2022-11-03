<?php
! defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCBK_Admin' ) ) {
	/**
	 * Class YITH_WCBK_Admin
	 * handle all admin behaviors
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Admin {

		/** @var YITH_WCBK_Admin */
		private static $_instance;

		/** @var YIT_Plugin_Panel_WooCommerce $_panel Panel object */
		private $_panel;

		/** @var string Panel page */
		private $_panel_page = 'yith_wcbk_panel';

		/** @var string Doc Url */
		public $doc_url = 'https://yithemes.com/docs-plugins/yith-woocommerce-booking/';

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Admin
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_WCBK_Admin constructor.
		 */
		private function __construct() {
			add_filter( 'admin_body_class', array( $this, 'add_classes_to_body' ) );

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			// Settings Custom Tab
			add_action( 'yith_wcbk_print_global_availability_rules_tab', array( $this, 'print_global_availability_rules_tab' ) );
			add_action( 'yith_wcbk_print_global_price_rules_tab', array( $this, 'print_global_price_rules_tab' ) );
			add_action( 'yith_wcbk_print_integrations_tab', array( $this, 'print_integrations_tab' ) );
			add_action( 'yith_wcbk_print_google_calendar_tab', array( $this, 'print_google_calendar_tab' ) );
			add_action( 'yith_wcbk_print_logs_tab', array( $this, 'print_logs_tab' ) );

			// Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCBK_DIR . '/' . basename( YITH_WCBK_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );

			YITH_WCBK_Product_Post_Type_Admin::get_instance();
			YITH_WCBK_Service_Tax_Admin::get_instance();
			YITH_WCBK_Search_Form_Post_Type_Admin::get_instance();
			YITH_WCBK_Booking_Admin::get_instance();
			YITH_WCBK_Admin_Assets::get_instance();
			YITH_WCBK_Tools::get_instance();
		}

		/**
		 * Add classes in body
		 *
		 * @param string $classes
		 *
		 * @return string
		 */
		public function add_classes_to_body( $classes ) {
			$style   = get_option( 'yith-wcbk-booking-style', 'simple' );
			$classes .= ' yith-booking-admin';
			$classes .= " yith-booking--{$style}-style ";

			return $classes;
		}

		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return  array
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			return yith_add_action_links( $links, $this->_panel_page, defined( 'YITH_WCBK_PREMIUM' ), YITH_WCBK_SLUG );
		}

		/**
		 * Adds action links to plugin admin page
		 *
		 * @param array  $row_meta_args
		 * @param array  $plugin_meta
		 * @param string $plugin_file
		 *
		 * @return array
		 */
		public function plugin_row_meta( $row_meta_args, $plugin_meta, $plugin_file ) {
			if ( YITH_WCBK_INIT === $plugin_file ) {
				$row_meta_args['slug']       = YITH_WCBK_SLUG;
				$row_meta_args['is_premium'] = defined( 'YITH_WCBK_PREMIUM' );
			}

			return $row_meta_args;
		}

		/**
		 * Print the Global availability rules tab
		 */
		public function print_global_availability_rules_tab() {
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-global-availability-rules.php';
		}

		/**
		 * Print the Global price rules tab
		 */
		public function print_global_price_rules_tab() {
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-global-price-rules.php';
		}

		/**
		 * Print the Integrations tab
		 */
		public function print_integrations_tab() {
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-integrations.php';
		}

		/**
		 * Print the Google Calendar tab
		 */
		public function print_google_calendar_tab() {
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-google-calendar.php';
		}

		/**
		 * Print the Google Calendar tab
		 */
		public function print_logs_tab() {
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-logs.php';
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @use      YIT_Plugin_Panel_WooCommerce class
		 * @see      plugin-fw/lib/yit-plugin-panel-woocommerce.php
		 */
		public function register_panel() {
			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'settings'           => _x( 'Settings', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				'availability-rules' => _x( 'Availability rules', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				'price-rules'        => _x( 'Price rules', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				'labels'             => _x( 'Labels', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				'integrations'       => _x( 'Integrations', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				'google-calendar'    => _x( 'Google Calendar', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				'tools'              => _x( 'Tools', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				'logs'               => _x( 'Logs', 'Settings tab name', 'yith-booking-for-woocommerce' ),
			);

			$admin_tabs = apply_filters( 'yith_wcbk_settings_admin_tabs', $admin_tabs );

			$args = apply_filters( 'yith_wcbk_plugin_panel_args', array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'Booking and Appointment for WooCommerce',
				'menu_title'       => 'Booking',
				'class'            => function_exists( 'yith_set_wrapper_class' ) ? yith_set_wrapper_class() : '',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_WCBK_DIR . '/plugin-options',
			) );

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once '../plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}
	}
}

/**
 * Unique access to instance of YITH_WCBK_Admin class
 *
 * @return YITH_WCBK_Admin
 */
function YITH_WCBK_Admin() {
	return YITH_WCBK_Admin::get_instance();
}