<?php
/**
 * YITH Booking functions and definitions
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 * @package YITH_Booking
 */

function yith_booking_get_theme_version() {
    $version = '1.0.0';
    if ( $current_theme = wp_get_theme() ) {
        if ( $current_theme->parent() ) {
            $current_theme = $current_theme->parent();
        }
        $version = $current_theme->get( 'Version' );
    }
    return $version;
}

if ( !defined( 'YITH_BOOKING_THEME_VERSION' ) ) {
    define( 'YITH_BOOKING_THEME_VERSION', yith_booking_get_theme_version() );
}

if ( !defined( 'YITH_BOOKING_THEME_VIEWS_PATH' ) ) {
    define( 'YITH_BOOKING_THEME_VIEWS_PATH', get_template_directory() . '/views' );
}

if ( !function_exists( 'yith_booking_setup' ) ) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function yith_booking_setup() {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on YITH Booking, use a find and replace
         * to change 'yith-booking' to the name of your theme in all the template files.
         */
        load_theme_textdomain( 'yith-booking', get_template_directory() . '/languages' );

        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support( 'title-tag' );

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support( 'post-thumbnails' );

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus( array(
                                'primary' => esc_html__( 'Primary', 'yith-booking' ),
                            ) );

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ) );

        // Set up the WordPress core custom background feature.
        add_theme_support( 'custom-background', apply_filters( 'yith_booking_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        ) ) );

        // Add theme support for selective refresh for widgets.
        add_theme_support( 'customize-selective-refresh-widgets' );

        /**
         * Add support for core custom logo.
         *
         * @link https://codex.wordpress.org/Theme_Logo
         */
        add_theme_support( 'custom-logo', array(
            'height'      => 98,
            'width'       => 270,
            'flex-width'  => true,
            'flex-height' => true,
        ) );
    }
endif;
add_action( 'after_setup_theme', 'yith_booking_setup' );

function yith_booking_svg( $icon = '', $size = 24 ) {
    $icons = include( get_template_directory() . '/svg-icons.php' );
    $svg   = '';
    if ( array_key_exists( $icon, $icons ) ) {
        $repl = sprintf( '<svg class="svg-icon" width="%d" height="%d" aria-hidden="true" role="img" focusable="false" ', $size, $size );
        $svg  = preg_replace( '/^<svg /', $repl, trim( $icons[ $icon ] ) ); // Add extra attributes to SVG code.
        $svg  = preg_replace( "/([\n\t]+)/", ' ', $svg ); // Remove newlines & tabs.
        $svg  = preg_replace( '/>\s*</', '><', $svg ); // Remove white space between SVG tags.
    }
    return $svg;
}

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function yith_booking_content_width() {
    // This variable is intended to be overruled from themes.
    // Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
    $GLOBALS[ 'content_width' ] = apply_filters( 'yith_booking_content_width', 640 );
}

add_action( 'after_setup_theme', 'yith_booking_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function yith_booking_widgets_init() {
    register_sidebar( array(
                          'name'          => esc_html__( 'Sidebar', 'yith-booking' ),
                          'id'            => 'sidebar-1',
                          'description'   => esc_html__( 'Add widgets here.', 'yith-booking' ),
                          'before_widget' => '<section id="%1$s" class="widget %2$s">',
                          'after_widget'  => '</section>',
                          'before_title'  => '<h2 class="widget-title">',
                          'after_title'   => '</h2>',
                      ) );

    register_sidebar( array(
                          'name'          => esc_html__( 'Footer Left', 'yith-booking' ),
                          'id'            => 'sidebar-footer-left',
                          'description'   => esc_html__( 'Add widgets here.', 'yith-booking' ),
                          'before_widget' => '<section id="%1$s" class="widget %2$s">',
                          'after_widget'  => '</section>',
                          'before_title'  => '<h2 class="widget-title">',
                          'after_title'   => '</h2>',
                      ) );

    register_sidebar( array(
                          'name'          => esc_html__( 'Footer Center', 'yith-booking' ),
                          'id'            => 'sidebar-footer-center',
                          'description'   => esc_html__( 'Add widgets here.', 'yith-booking' ),
                          'before_widget' => '<section id="%1$s" class="widget %2$s">',
                          'after_widget'  => '</section>',
                          'before_title'  => '<h2 class="widget-title">',
                          'after_title'   => '</h2>',
                      ) );

    register_sidebar( array(
                          'name'          => esc_html__( 'Footer Right', 'yith-booking' ),
                          'id'            => 'sidebar-footer-right',
                          'description'   => esc_html__( 'Add widgets here.', 'yith-booking' ),
                          'before_widget' => '<section id="%1$s" class="widget %2$s">',
                          'after_widget'  => '</section>',
                          'before_title'  => '<h2 class="widget-title">',
                          'after_title'   => '</h2>',
                      ) );

    register_sidebar( array(
                          'name'          => esc_html__( 'Footer Row Top', 'yith-booking' ),
                          'id'            => 'sidebar-footer-row-top',
                          'description'   => esc_html__( 'Add widgets here.', 'yith-booking' ),
                          'before_widget' => '<section id="%1$s" class="widget %2$s">',
                          'after_widget'  => '</section>',
                          'before_title'  => '<h2 class="widget-title">',
                          'after_title'   => '</h2>',
                      ) );

    register_sidebar( array(
                          'name'          => esc_html__( 'Footer Row Bottom', 'yith-booking' ),
                          'id'            => 'sidebar-footer-row-bottom',
                          'description'   => esc_html__( 'Add widgets here.', 'yith-booking' ),
                          'before_widget' => '<section id="%1$s" class="widget %2$s">',
                          'after_widget'  => '</section>',
                          'before_title'  => '<h2 class="widget-title">',
                          'after_title'   => '</h2>',
                      ) );
}

add_action( 'widgets_init', 'yith_booking_widgets_init' );


function yith_booking_has_sidebar() {
    if ( is_page_template( 'page_no-sidebar.php' ) ) {
        $has_sidebar = false;
    } elseif ( function_exists( 'is_product' ) && is_product() ) {
        $has_sidebar = is_active_sidebar( 'sidebar-product' );
    } else {
        $has_sidebar = is_active_sidebar( 'sidebar-1' );
    }

    return apply_filters( 'yith_booking_has_sidebar', $has_sidebar );
}

function yith_booking_print_sidebar() {
    if ( function_exists( 'is_product' ) && is_product() ) {
        dynamic_sidebar( 'sidebar-product' );
    } else {
        dynamic_sidebar( 'sidebar-1' );
    }
}

/**
 * Enqueue scripts
 */
function yith_booking_scripts() {
    wp_enqueue_script( 'yith-booking-navigation', get_template_directory_uri() . '/js/navigation.js', array(), YITH_BOOKING_THEME_VERSION, true );
    wp_enqueue_script( 'yith-booking-utils', get_template_directory_uri() . '/js/utils.js', array(), YITH_BOOKING_THEME_VERSION, true );

    wp_enqueue_script( 'yith-booking-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), YITH_BOOKING_THEME_VERSION, true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}

/**
 * Enqueue styles.
 */
function yith_booking_styles() {
    wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.4.1' );
    wp_enqueue_style( 'montserrat', '//fonts.googleapis.com/css?family=Montserrat:300,400,500,600' );

    wp_enqueue_style( 'yith-booking-style', get_stylesheet_uri(), array(), YITH_BOOKING_THEME_VERSION );
}

add_action( 'wp_enqueue_scripts', 'yith_booking_scripts' );
add_action( 'wp_enqueue_scripts', 'yith_booking_styles' );


require get_template_directory() . '/inc/option-functions.php';
require get_template_directory() . '/inc/class.metaboxes.php';

/**
 * Gutenberg
 */
require get_template_directory() . '/inc/class.yith-booking-theme-gutenberg.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/customizer-inline-style.php';

/**
 * Implement the Shortcodes feature.
 */
require get_template_directory() . '/inc/class.shortcodes.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
    require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
    require get_template_directory() . '/inc/class.woocommerce.php';
}
