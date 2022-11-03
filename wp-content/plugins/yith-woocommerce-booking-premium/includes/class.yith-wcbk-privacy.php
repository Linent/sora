<?php
!defined( 'YITH_WCBK' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCBK_Privacy' ) ) {
    /**
     * Class YITH_WCBK_Privacy
     * Privacy Class
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBK_Privacy extends YITH_Privacy_Plugin_Abstract {

        /**
         * YITH_WCBK_Privacy constructor.
         */
        public function __construct() {
            parent::__construct( YITH_WCBK_PLUGIN_NAME );
        }

        public function get_privacy_message( $section ) {
            $privacy_content_path = YITH_WCBK_VIEWS_PATH . '/privacy/html-policy-content-' . $section . '.php';
            if ( file_exists( $privacy_content_path ) ) {
                ob_start();
                include $privacy_content_path;
                return ob_get_clean();
            }
            return '';
        }
    }
}

new YITH_WCBK_Privacy();