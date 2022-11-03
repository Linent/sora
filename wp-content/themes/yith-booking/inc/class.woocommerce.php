<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_Booking_WooCommerce' ) ) {
    /**
     * Class YITH_Booking_WooCommerce
     * handle WooCommerce
     *
     * @link    https://woocommerce.com/
     * @package YITH_Booking
     * @author  Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_Booking_WooCommerce {
        /** @var YITH_Booking_WooCommerce */
        protected static $_instance;

        /**
         * @return YITH_Booking_WooCommerce
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_Booking_WooCommerce constructor.
         */
        private function __construct() {
            add_action( 'after_setup_theme', array( $this, 'setup' ) );
            add_action( 'widgets_init', array( $this, 'widgets_init' ) );

            add_action( 'wp_loaded', array( $this, 'gallery_setup' ) );
            add_action( 'wp_loaded', array( $this,'reverse_description_position_with_short_description') );

            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        }

        /**
         * WooCommerce setup function.
         *
         * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
         * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)-in-3.0.0
         */
        public function setup() {
            add_theme_support( 'woocommerce' );
            add_theme_support( 'wc-product-gallery-lightbox' );
            add_theme_support( 'wc-product-gallery-zoom' );
            add_theme_support( 'wc-product-gallery-slider' );
        }

        /**
         * handle gallery setup
         */
        public function gallery_setup() {
            if ( get_theme_mod( 'yith_booking_wc_gallery_in_header', true ) ) {

                remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
                remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

                add_action( 'yith_booking_content_header', array( $this, 'image_gallery_in_header_content' ) );
                add_filter( 'body_class', array( $this, 'add_body_class_for_wc_image_gallery' ), 10, 1 );
                add_filter( 'woocommerce_gallery_image_size', array( $this, 'gallery_image_size_full' ), 999 );

                remove_theme_support( 'wc-product-gallery-zoom' );
                remove_theme_support( 'wc-product-gallery-slider' );
            }
        }

        /**
         * print image gallery in header content
         */
        public function image_gallery_in_header_content() {
            if ( function_exists( 'is_product' ) && is_product() ) {
                global $product;
                $product = wc_get_product();
                woocommerce_show_product_sale_flash();
                echo "<div class='yith-booking-woocommerce-images'>";
                woocommerce_show_product_images();
                $attachment_ids = $product->get_gallery_image_ids();
                if ( $attachment_ids ) {
                    echo "<span class='open-gallery'>" . __( 'Show Images', 'yith-booking' ) . "</span>";
                }
                echo '</div>';
            }
        }

        /**
         * add body class for wc image gallery
         *
         * @param $classes
         *
         * @return array
         */
        public function add_body_class_for_wc_image_gallery( $classes ) {
            $classes[] = 'yith-booking-has-woocommerce-gallery-in-header';

            return $classes;
        }

        /**
         * return full
         *
         * @return string
         */
        public function gallery_image_size_full() {
            return 'full';
        }

        /**
         * Register widget area.
         *
         * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
         */
        public function widgets_init() {
            register_sidebar( array(
                                  'name'          => esc_html__( 'Product Sidebar', 'yith-booking' ),
                                  'id'            => 'sidebar-product',
                                  'description'   => esc_html__( 'Add widgets here.', 'yith-booking' ),
                                  'before_widget' => '<section id="%1$s" class="widget %2$s">',
                                  'after_widget'  => '</section>',
                                  'before_title'  => '<h2 class="widget-title">',
                                  'after_title'   => '</h2>',
                              ) );
        }

        /**
         * reverse description with short description
         */
        public function reverse_description_position_with_short_description(){
            if ( get_theme_mod( 'yith_booking_wc_reverse_description_position_with_short_description', false ) ) {
                add_action( 'woocommerce_product_description_heading', '__return_empty_string' );
                remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
                add_action( 'woocommerce_single_product_summary', 'woocommerce_product_description_tab', 20 );

                add_filter( 'woocommerce_product_tabs', array($this, 'short_description_in_tabs'), 20 );
            }
        }

        /**
         * short description in tabs instead of description
         *
         * @param $tabs
         *
         * @return mixed
         */
        public function short_description_in_tabs( $tabs ) {
            global $post;
            $priority = 10;
            $title    = __( 'Description', 'woocommerce' );
            if ( isset( $tabs[ 'description' ] ) ) {
                if ( isset( $tabs[ 'description' ][ 'priority' ] ) ) {
                    $priority = $tabs[ 'description' ][ 'priority' ];
                }
                if ( isset( $tabs[ 'description' ][ 'title' ] ) ) {
                    $title = $tabs[ 'description' ][ 'title' ];
                }

                unset( $tabs[ 'description' ] );
            }
            $short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );

            if ( $short_description ) {
                $tabs[ 'description' ] = array(
                    'title'    => $title,
                    'priority' => $priority,
                    'callback' => 'woocommerce_template_single_excerpt',
                );
            }

            return $tabs;
        }


        /**
         * WooCommerce specific scripts & stylesheets.
         *
         * @return void
         */
        public function enqueue_scripts() {
            wp_enqueue_style( 'yith-booking-woocommerce-style', get_template_directory_uri() . '/woocommerce.css', array(), YITH_BOOKING_THEME_VERSION );
            wp_enqueue_script( 'yith-booking-woocommerce-script', get_template_directory_uri() . '/js/woocommerce.js', array( 'jquery' ), YITH_BOOKING_THEME_VERSION, true );

            $font_path   = WC()->plugin_url() . '/assets/fonts/';
            $inline_font = '@font-face {
                font-family: "star";
                src: url("' . $font_path . 'star.eot");
                src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
                    url("' . $font_path . 'star.woff") format("woff"),
                    url("' . $font_path . 'star.ttf") format("truetype"),
                    url("' . $font_path . 'star.svg#star") format("svg");
                font-weight: normal;
                font-style: normal;
            }';

            wp_add_inline_style( 'yith-booking-woocommerce-style', $inline_font );
        }

    }

    YITH_Booking_WooCommerce::get_instance();
}
