/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function ( $ ) {

	// Site title and description.
	wp.customize( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function ( value ) {
		value.bind( function ( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Header text color.
	wp.customize( 'header_textcolor', function ( value ) {
		value.bind( function ( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-description' ).css( {
															   'clip'    : 'rect(1px, 1px, 1px, 1px)',
															   'position': 'absolute'
														   } );
			} else {
				$( '.site-title, .site-description' ).css( {
															   'clip'    : 'auto',
															   'position': 'relative'
														   } );
				$( '.site-header, .site-title a, .site-description' ).css( {
																			   'color': to
																		   } );
			}
		} );
	} );

	// Header Background Color
	wp.customize( 'yith_booking_header_background_color', function ( value ) {
		value.bind( function ( newval ) {
			$( '.site-header' ).css( 'background-color', newval );
		} );
	} );

	// Footer Background Color
	wp.customize( 'yith_booking_footer_background_color', function ( value ) {
		value.bind( function ( newval ) {
			$( '.site-footer' ).css( 'background-color', newval );
		} );
	} );

	// Footer Text Color
	wp.customize( 'yith_booking_footer_text_color', function ( value ) {
		value.bind( function ( newval ) {
			$( '.site-footer' ).css( 'color', newval );
		} );
	} );

} )( jQuery );
