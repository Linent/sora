<?php
! defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Integrations
 *
 * handle plugin integrations
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.1
 */
class YITH_WCBK_Integrations {

	/** @var YITH_WCBK_Integrations */
	private static $_instance;

	protected $_plugins = array();

	/**
	 * Singleton implementation
	 *
	 * @return YITH_WCBK_Integrations
	 */
	public static function get_instance() {
		return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
	}

	/**
	 * YITH_WCBK_Integrations constructor.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_plugins' ), 15 );

		add_action( 'wp_loaded', array( $this, 'manage_actions' ) );
	}

	/**
	 * Load plugins
	 */
	public function load_plugins() {
		$this->_plugins = require_once 'plugins-list.php';
		$this->_load();

		add_action( 'yith_wcbk_integrations_tab_contents', array( $this, 'print_integrations' ) );
	}

	/**
	 * Load Integration classes
	 */
	private function _load() {
		require_once YITH_WCBK_INCLUDES_PATH . '/integrations/abstract.yith-wcbk-integration.php';

		foreach ( $this->_plugins as $slug => $plugin_info ) {
			$filename  = YITH_WCBK_INCLUDES_PATH . '/integrations/class.yith-wcbk-' . $slug . '-integration.php';
			$classname = $this->get_class_name_from_slug( $slug );

			$var = str_replace( '-', '_', $slug );
			if ( file_exists( $filename ) && ! class_exists( $classname ) ) {
				require_once $filename;
			}

			if ( method_exists( $classname, 'get_instance' ) ) {
				$has_plugin         = $this->has_plugin( $slug );
				$is_optional        = isset( $plugin_info['optional'] ) && $plugin_info['optional'] === true;
				$integration_active = $has_plugin && ( ! $is_optional || get_option( 'yith-wcbk-' . $slug . '-add-on-active', 'no' ) === 'yes' );

				$this->$var = $classname::get_instance( $has_plugin, $integration_active );
			}
		}
	}

	/**
	 * Manage Integration actions
	 * Activate Deactivate
	 */
	public function manage_actions() {
		$allowed_actions = array( 'activate', 'deactivate' );
		if ( current_user_can( 'manage_options' )
			 &&
			 isset( $_REQUEST['yith-wcbk-integration-action'] ) && in_array( $_REQUEST['yith-wcbk-integration-action'], $allowed_actions )
			 &&
			 isset( $_REQUEST['integration'] ) && in_array( $_REQUEST['integration'], array_keys( $this->_plugins ) )
		) {
			$slug   = $_REQUEST['integration'];
			$status = 'activate' === $_REQUEST['yith-wcbk-integration-action'] ? 'yes' : 'no';

			update_option( 'yith-wcbk-' . $slug . '-add-on-active', $status );

			do_action( 'yith_wcbk_' . $slug . '_add_on_active_status_change', $status );
		}
	}

	/**
	 * print integration list
	 */
	public function print_integrations() {
		foreach ( $this->_plugins as $slug => $plugin ) {
			if ( isset( $plugin['show'] ) && ! $plugin['show'] ) {
				continue;
			}

			$has_plugin = $this->has_plugin( $slug );
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-single-integration.php';
		}
	}

	/**
	 * get the class name from slug
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	public function get_class_name_from_slug( $slug ) {
		$class_slug = str_replace( '-', ' ', $slug );
		$class_slug = ucwords( $class_slug );
		$class_slug = str_replace( ' ', '_', $class_slug );

		return 'YITH_WCBK_' . $class_slug . '_Integration';
	}

	/**
	 * Check if user has a plugin
	 *
	 * @param string $slug
	 *
	 * @return bool
	 */
	public function has_plugin( $slug ) {
		if ( ! empty( $this->_plugins[ $slug ] ) ) {
			$plugin = $this->_plugins[ $slug ];

			if ( isset( $plugin['premium'] ) && defined( $plugin['premium'] ) && constant( $plugin['premium'] ) ) {
				if ( ! isset ( $plugin['installed_version'] ) || ! isset( $plugin['min_version'] ) ) {
					return true;
				}

				$compare = isset( $plugin['compare'] ) ? $plugin['compare'] : '>=';

				if ( defined( $plugin['installed_version'] ) && constant( $plugin['installed_version'] ) &&
					 version_compare( constant( $plugin['installed_version'] ), $plugin['min_version'], $compare )
				) {
					return true;
				}

			}
		}

		return false;
	}
}