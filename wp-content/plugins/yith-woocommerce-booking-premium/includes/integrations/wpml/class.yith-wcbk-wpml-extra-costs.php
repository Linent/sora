<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Wpml_Extra_Costs
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   2.1
 */
class YITH_WCBK_Wpml_Extra_Costs {
    /** @var YITH_WCBK_Wpml_Extra_Costs */
    private static $_instance;

    /** @var YITH_WCBK_Wpml_Integration */
    public $wpml_integration;

    /**
     * Singleton Implementation
     *
     * @param YITH_WCBK_Wpml_Integration $wpml_integration
     * @return YITH_WCBK_Wpml_Extra_Costs
     */
    public static function get_instance( $wpml_integration ) {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new static( $wpml_integration );
    }

    /**
     * Constructor
     *
     * @access private
     * @param YITH_WCBK_Wpml_Integration $wpml_integration
     */
    private function __construct( $wpml_integration ) {
        $this->wpml_integration = $wpml_integration;

        // translate the title of the person type
        add_filter( 'yith_wcbk_product_extra_cost_get_name', array( $this, 'translate_extra_cost_name' ), 10, 2 );

        // retrieve only the extra costs in Default Language
        add_action( 'yith_wcbk_before_get_extra_costs', array( $this->wpml_integration, 'set_current_language_to_default' ) );
        add_action( 'yith_wcbk_after_get_extra_costs', array( $this->wpml_integration, 'restore_current_language' ) );
    }

    /**
     * Translate the person type title in current language
     *
     * @param $title
     * @param $id
     * @return string
     */
    public function translate_extra_cost_name( $title, $id ) {
        return get_the_title( $this->wpml_integration->get_current_language_id( $id ) );
    }

}