<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Wpml_Person_Types
 *
 * @author Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.3
 */
class YITH_WCBK_Wpml_Person_Types {
    /** @var YITH_WCBK_Wpml_Person_Types */
    private static $_instance;

    /** @var YITH_WCBK_Wpml_Integration */
    public $wpml_integration;

    /**
     * Singleton Implementation
     *
     * @param YITH_WCBK_Wpml_Integration $wpml_integration
     *
     * @return YITH_WCBK_Wpml_Person_Types
     */
    public static function get_instance( $wpml_integration ) {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new static( $wpml_integration );
    }

    /**
     * Constructor
     *
     * @access private
     *
     * @param YITH_WCBK_Wpml_Integration $wpml_integration
     */
    private function __construct( $wpml_integration ) {
        $this->wpml_integration = $wpml_integration;

        // translate the title of the person type
        add_filter( 'yith_wcbk_get_person_type_title', array( $this, 'translate_person_type_title' ), 10, 2 );

        // retrieve only the person types in Default Language
        add_action( 'yith_wcbk_before_get_person_types', array( $this->wpml_integration, 'set_current_language_to_default' ) );
        add_action( 'yith_wcbk_after_get_person_types', array( $this->wpml_integration, 'restore_current_language' ) );
    }

    /**
     * Translate the person type title in current language
     *
     * @param $title
     * @param $person_type_id
     *
     * @return string
     */
    public function translate_person_type_title( $title, $person_type_id ) {
        $title = get_the_title( $this->wpml_integration->get_current_language_id( $person_type_id ) );

        return $title;
    }

}