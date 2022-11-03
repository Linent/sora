<?php
/**
 * YITH Booking Theme Customizer
 *
 * @package YITH_Booking
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function yith_booking_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'yith_booking_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'yith_booking_customize_partial_blogdescription',
		) );
	}

	/**
	 * Add Header management
	 */
	$wp_customize->add_section(
		'yith_booking_header_management',
		array(
			'title'    => __( 'Header', 'yith-booking' ),
			'priority' => 20,
		)
	);

	// Header sticky
	$wp_customize->add_setting(
		'yith_booking_header_sticky',
		array(
			'default'    => 'yes',
			'capability' => 'manage_options',
			'type'       => 'theme_mod',
		)
	);

	$wp_customize->add_control(
		'yith_booking_header_sticky',
		array(
			'type'        => 'radio',
			'label'       => __( 'Enable sticky header', 'yith-booking' ),
			'section'     => 'yith_booking_header_management',
			'description' => __( 'Choose whether to make the header stick to the page when scrolling down', 'yith-booking' ),
			'choices'     => array(
				'yes' => __( 'Yes', 'yith-booking' ),
				'no'  => __( 'No', 'yith-booking' ),
			),
		)
	);

	/**
	 * Colors
	 */
	$wp_customize->add_setting(
		'yith_booking_header_background_color',
		array(
			'default'              => '#ffffff',
			'sanitize_callback'    => 'sanitize_hex_color',
			'sanitize_js_callback' => 'maybe_hash_hex_color',
			'transport'            => 'postMessage',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'yith_booking_header_background_color',
			array(
				'label'    => __( 'Header Background Color', 'yith-booking' ),
				'section'  => 'colors',
				'priority' => 5,
			)
		)
	);

	$wp_customize->add_setting(
		'yith_booking_footer_background_color',
		array(
			'default'              => '#f7f7f7',
			'sanitize_callback'    => 'sanitize_hex_color',
			'sanitize_js_callback' => 'maybe_hash_hex_color',
			'transport'            => 'postMessage',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'yith_booking_footer_background_color',
			array(
				'label'   => __( 'Footer Background Color', 'yith-booking' ),
				'section' => 'colors',
			)
		)
	);

	$wp_customize->add_setting(
		'yith_booking_footer_text_color',
		array(
			'default'              => '#555555',
			'sanitize_callback'    => 'sanitize_hex_color',
			'sanitize_js_callback' => 'maybe_hash_hex_color',
			'transport'            => 'postMessage',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'yith_booking_footer_text_color',
			array(
				'label'   => __( 'Footer Text Color', 'yith-booking' ),
				'section' => 'colors',
			)
		)
	);

	/**
	 * Single Product
	 */
	$wp_customize->add_section(
		'yith_booking_wc_single_product',
		array(
			'title'    => __( 'Single Product', 'yith-booking' ),
			'priority' => 10,
			'panel'    => 'woocommerce',
		)
	);

	$wp_customize->add_setting(
		'yith_booking_wc_gallery_in_header',
		array(
			'default'    => true,
			'type'       => 'theme_mod',
			'capability' => 'manage_woocommerce',
			'transport'  => 'refresh',
		)
	);

	$wp_customize->add_control(
		'yith_booking_wc_gallery_in_header',
		array(
			'type'     => 'checkbox',
			'label'    => __( 'Product Gallery in header', 'yith-booking' ),
			'section'  => 'yith_booking_wc_single_product',
			'priority' => 10,
		)
	);

	$wp_customize->add_setting(
		'yith_booking_wc_reverse_description_position_with_short_description',
		array(
			'default'    => false,
			'type'       => 'theme_mod',
			'capability' => 'manage_woocommerce',
			'transport'  => 'refresh',
		)
	);

	$wp_customize->add_control(
		'yith_booking_wc_reverse_description_position_with_short_description',
		array(
			'type'     => 'checkbox',
			'label'    => __( 'Reverse description position with short description', 'yith-booking' ),
			'section'  => 'yith_booking_wc_single_product',
			'priority' => 20,
		)
	);
}

add_action( 'customize_register', 'yith_booking_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function yith_booking_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function yith_booking_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function yith_booking_customize_preview_js() {
	wp_enqueue_script( 'yith-booking-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), YITH_BOOKING_THEME_VERSION, true );
}

add_action( 'customize_preview_init', 'yith_booking_customize_preview_js' );
