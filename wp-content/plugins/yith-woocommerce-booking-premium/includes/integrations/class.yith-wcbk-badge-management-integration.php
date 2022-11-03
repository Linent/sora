<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * Class YITH_WCBK_Badge_Management_Integration
 *
 * @author Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.1
 */
class YITH_WCBK_Badge_Management_Integration extends YITH_WCBK_Integration {
    /** @var YITH_WCBK_Badge_Management_Integration */
    protected static $_instance;

    /**
     * Constructor
     *
     * @param bool $plugin_active
     * @param bool $integration_active
     *
     * @access protected
     */
    protected function __construct( $plugin_active, $integration_active ) {
        parent::__construct( $plugin_active, $integration_active );

        if ( $this->is_active() ) {
            add_filter( 'yith_wcbk_search_form_result_product_thumb_wrapper', array( $this, 'add_badges_in_search_form_results' ), 10, 2 );
        }
    }

    /**
     * adds badges in Search Form results
     *
     * @param string $html
     * @param int    $product_id
     *
     * @return string
     */
    public function add_badges_in_search_form_results( $html, $product_id ) {
        return apply_filters( 'yith_wcbm_product_thumbnail_container', $html, $product_id );
    }
}