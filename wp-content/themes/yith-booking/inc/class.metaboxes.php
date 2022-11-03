<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_Booking_Theme_Metaboxes' ) ) {
    /**
     * Class YITH_Booking_Theme_Metaboxes
     * register and manage metaboxes
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_Booking_Theme_Metaboxes {
        /** @var YITH_Booking_Theme_Metaboxes */
        protected static $_instance;


        /**
         * @return YITH_Booking_Theme_Metaboxes
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_Booking_Theme_Metaboxes constructor.
         */
        private function __construct() {
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'save' ), 10, 1 );
        }

        public function add_meta_boxes() {
            foreach ( $this->_get_metaboxes() as $metabox ) {
                add_meta_box( $metabox[ 'id' ], $metabox[ 'title' ], array(
                    $this,
                    'meta_box_print',
                ), $metabox[ 'post_type' ], $metabox[ 'context' ], $metabox[ 'priority' ] );
            }
        }

        /**
         * get the metaboxes
         *
         * @access private
         * @return array
         */
        private function _get_metaboxes() {
            $metaboxes = array(
                10 => array(
                    'id'            => 'yith-booking-theme-page-options',
                    'title'         => __( 'Page options', 'yith-booking' ),
                    'context'       => 'side',
                    'priority'      => 'default',
                    'post_type'     => array( 'page' ),
                    'save_callback' => array( $this, 'save_page_options' )
                ),
            );

            return $metaboxes;
        }

        /**
         * Print metaboxes content
         *
         * @param WP_Post $post Post object.
         * @param array   $metabox
         */
        public function meta_box_print( $post, $metabox ) {

            if ( !isset( $metabox[ 'id' ] ) ) {
                return;
            }

            switch ( $metabox[ 'id' ] ) {
                case 'yith-booking-theme-page-options':
                    include( YITH_BOOKING_THEME_VIEWS_PATH . '/metaboxes/html-page-options.php' );
                    break;
            }
        }

        /**
         * Save meta on save post
         *
         * @param int $post_id
         */
        public function save( $post_id ) {
            foreach ( $this->_get_metaboxes() as $metabox ) {
                if ( in_array( get_post_type( $post_id ), (array) $metabox[ 'post_type' ] ) ) {
                    $cb = isset( $metabox[ 'save_callback' ] ) ? $metabox[ 'save_callback' ] : false;
                    if ( $cb && is_callable( $cb ) ) {
                        call_user_func( $cb, $post_id );
                    }
                }
            }
        }

        public function save_page_options( $post_id ) {
            $defaults = yith_booking_get_page_option_defaults();
            foreach ( array_keys( $defaults ) as $option ) {
                $key = yith_booking_page_option_prefix() . $option;
                if ( isset( $_POST[ $key ] ) ) {
                    update_post_meta( $post_id, $key, $_POST[ $key ] );
                } else {
                    delete_post_meta( $post_id, $key );
                }
            }
        }
    }

    YITH_Booking_Theme_Metaboxes::get_instance();
}