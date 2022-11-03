<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Wpml_Search_Forms
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.10
 */
class YITH_WCBK_Wpml_Search_Forms {
    /** @var YITH_WCBK_Wpml_Search_Forms */
    private static $_instance;

    /** @var YITH_WCBK_Wpml_Integration */
    public $wpml_integration;

    /**
     * Singleton Implementation
     *
     * @param YITH_WCBK_Wpml_Integration $wpml_integration
     *
     * @return YITH_WCBK_Wpml_Search_Forms
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

        add_action( 'yith_wcbk_booking_search_form_after_print_fields', array( $this, 'add_language_hidden_input' ) );
        add_action( 'yith_wcbk_search_booking_products_search_args', array( $this, 'fix_search_args' ) );
    }

    /**
     * Add language hidden input in search forms
     */
    public function add_language_hidden_input() {
        $lang = $this->wpml_integration->current_language;
        echo "<input type='hidden' name='lang' value='{$lang}'>";
    }

    /**
     * Fix search args by setting parent terms
     *
     * @param $search_args
     *
     * @return mixed
     */
    public function fix_search_args( $search_args ) {
        if ( isset( $search_args[ 'tax_query' ] ) ) {
            foreach ( $search_args[ 'tax_query' ] as $key => $value ) {
                if ( isset( $value[ 'taxonomy' ] ) && !empty( $value[ 'field' ] ) && 'term_id' === $value[ 'field' ] && !empty( $value[ 'terms' ] ) && is_array( $value[ 'terms' ] ) ) {
                    $original_terms = array();
                    foreach ( $value[ 'terms' ] as $id ) {
                        $original_id      = $this->wpml_integration->get_original_term_id( $id, $value[ 'taxonomy' ] );
                        $original_terms[] = !!$original_id ? $original_id : $id;
                    }

                    $search_args[ 'tax_query' ][ $key ][ 'terms' ] = $original_terms;

                }
            }

        }
        return $search_args;
    }

}