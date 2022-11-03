<?php
/**
 * @package yith-booking
 */

/**
 * Add a custom style based on customizer options
 *
 * @author Leanza Francesco <leanzafrancesco@gmail.com
 */
function yith_booking_inline_style() {
	wp_register_style( 'yith-booking-custom-style', false );
	wp_enqueue_style( 'yith-booking-custom-style' );

	$header_bg_color   = get_theme_mod( 'yith_booking_header_background_color', '#ffffff' );
	$footer_bg_color   = get_theme_mod( 'yith_booking_footer_background_color', '#f7f7f7' );
	$footer_text_color = get_theme_mod( 'yith_booking_footer_text_color', '#555555' );

	$custom_css = "
	.site-header{
		background: {$header_bg_color};
	}
	
	.site-footer{
		background: {$footer_bg_color};
		color: {$footer_text_color};
	}
	";


	wp_add_inline_style( 'yith-booking-custom-style', $custom_css );
}

add_action( 'wp_enqueue_scripts', 'yith_booking_inline_style', 10 );