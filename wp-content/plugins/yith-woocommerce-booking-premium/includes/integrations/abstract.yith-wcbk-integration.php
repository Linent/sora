<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Integration
 *
 * @abstract
 * @author Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.1
 */
abstract class YITH_WCBK_Integration {

    /** @var YITH_WCBK_Integration */
    protected static $_instance;

    /** @var bool true if has the plugin active */
    protected $_plugin_active = false;

    /** @var bool true if has the integration is active */
    protected $_integration_active = false;

    /**
     * Singleton implementation
     *
     * @param $plugin_active
     * @param $integration_active
     *
     * @return YITH_WCBK_Integration
     */
    public static function get_instance( $plugin_active, $integration_active ) {
        if ( is_null( static::$_instance ) ) {
            static::$_instance = new static( $plugin_active, $integration_active );
        }

        return static::$_instance;
    }

    /**
     * Constructor
     *
     * @param bool $plugin_active
     * @param bool $integration_active
     *
     * @access protected
     */
    protected function __construct( $plugin_active, $integration_active ) {
        $this->_plugin_active      = !!$plugin_active;
        $this->_integration_active = !!$integration_active;
    }

    /**
     * return true if the plugin is active
     *
     * @return bool
     */
    public function has_plugin_active() {
        return !!$this->_plugin_active;
    }

    /**
     * return true if the integration is active
     *
     * @return bool
     */
    public function is_active() {
        return !!$this->_integration_active;
    }
}