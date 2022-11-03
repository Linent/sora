<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_Booking_Theme_Gutenberg' ) ) {
    /**
     * Class YITH_Booking_Theme_Gutenberg
     * handle Gutenberg integration
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_Booking_Theme_Gutenberg {
        /** @var YITH_Booking_Theme_Gutenberg */
        protected static $_instance;

        /**
         * @return YITH_Booking_Theme_Gutenberg
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_Booking_Theme_Gutenberg constructor.
         */
        private function __construct() {
            add_action( 'after_setup_theme', array( $this, 'add_theme_supports' ) );
            add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
        }

        /**
         * add theme supports
         */
        public function add_theme_supports() {
            add_theme_support( 'align-wide' );
            add_theme_support( 'wp-block-styles' );
            add_theme_support( 'responsive-embeds' );
        }

        /**
         * Enqueue block editor assets
         */
        public function enqueue_block_editor_assets() {
            wp_enqueue_style( 'montserrat', '//fonts.googleapis.com/css?family=Montserrat:300,400,500,600' );
            wp_enqueue_style( 'yith-booking-theme-editor-style', get_stylesheet_directory_uri() . "/editor.css", array(), YITH_BOOKING_THEME_VERSION );
        }
    }

    YITH_Booking_Theme_Gutenberg::get_instance();
}