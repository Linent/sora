<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package YITH_Booking
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function yith_booking_body_classes( $classes ) {
    // Adds a class of no-sidebar to sites without active sidebar.
    if ( !yith_booking_has_sidebar() ) {
        $classes[] = 'no-sidebar';
    }

    // Adds a class of hfeed to non-singular pages.
    if ( !is_singular() ) {
        $classes[] = 'hfeed';
    }

    return $classes;
}

add_filter( 'body_class', 'yith_booking_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function yith_booking_pingback_header() {
    if ( is_singular() && pings_open() ) {
        echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
    }
}

add_action( 'wp_head', 'yith_booking_pingback_header' );


/**
 * Adds custom classes to the array of post classes.
 */
function yith_booking_post_classes( $classes, $class, $post_id ) {
    if ( 'page' === get_post_type( $post_id ) && yith_booking_get_page_option( get_the_ID(), 'hide_title' ) === 'yes' ) {
        $classes[] = 'has-hidden-title';
    }
    return $classes;
}

add_filter( 'post_class', 'yith_booking_post_classes', 10, 3 );
