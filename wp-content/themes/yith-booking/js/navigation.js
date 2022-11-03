/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function ( $ ) {
	var body, masthead, menuToggle, siteNavigation, siteHeaderMenu, resizeTimer, sidebar, page, stickyMenu;

	function initMainNavigation( container ) {

		// Add dropdown toggle that displays child menu items.
		var dropdownToggle = $( '<button />', {
			'class'        : 'dropdown-toggle',
			'aria-expanded': false
		} );

		container.find( '.menu-item-has-children > a' ).after( dropdownToggle );

		// Toggle buttons and submenu items with active children menu items.
		container.find( '.current-menu-ancestor > button' ).addClass( 'toggled-on' );
		container.find( '.current-menu-ancestor > .sub-menu' ).addClass( 'toggled-on' );

		// Add menu items with submenus to aria-haspopup="true".
		container.find( '.menu-item-has-children' ).attr( 'aria-haspopup', 'true' );

		container.find( '.dropdown-toggle' ).click( function ( e ) {
			var _this = $( this );

			e.preventDefault();
			_this.toggleClass( 'toggled-on' );
			_this.next( '.children, .sub-menu' ).toggleClass( 'toggled-on' );

			_this.attr( 'aria-expanded', _this.attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
		} );
	}

	initMainNavigation( $( '.main-navigation' ) );

	masthead       = $( '#masthead' );
	menuToggle     = masthead.find( '#menu-toggle' );
	siteHeaderMenu = masthead.find( '#site-header-menu' );
	siteNavigation = masthead.find( '#site-navigation' );
	sidebar        = $( '#secondary' );
	page           = $( '#page' );
	stickyMenu     = $( '.sticky-menu' );

	// Enable menuToggle.
	( function () {

		// Return early if menuToggle is missing.
		if ( !menuToggle.length ) {
			return;
		}

		// Add an initial values for the attribute.
		menuToggle.add( siteNavigation ).attr( 'aria-expanded', 'false' );

		menuToggle.on( 'click', function () {
			$( this ).add( siteHeaderMenu ).toggleClass( 'toggled-on' );

			// jscs:disable
			$( this ).add( siteNavigation ).attr( 'aria-expanded', $( this ).add( siteNavigation ).attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
			// jscs:enable
		} );
	} )();

	// Fix sub-menus for touch devices and better focus for hidden submenu items for accessibility.
	( function () {
		if ( !siteNavigation.length || !siteNavigation.children().length ) {
			return;
		}

		// Toggle `focus` class to allow submenu access on tablets.
		function toggleFocusClassTouchScreen() {
			if ( window.innerWidth >= 910 ) {
				$( document.body ).on( 'touchstart', function ( e ) {
					if ( !$( e.target ).closest( '.main-navigation li' ).length ) {
						$( '.main-navigation li' ).removeClass( 'focus' );
					}
				} );
				siteNavigation.find( '.menu-item-has-children > a' ).on( 'touchstart', function ( e ) {
					var el = $( this ).parent( 'li' );

					if ( !el.hasClass( 'focus' ) ) {
						e.preventDefault();
						el.toggleClass( 'focus' );
						el.siblings( '.focus' ).removeClass( 'focus' );
					}
				} );
			} else {
				siteNavigation.find( '.menu-item-has-children > a' ).unbind( 'touchstart' );
			}
		}

		if ( 'ontouchstart' in window ) {
			$( window ).on( 'resize', toggleFocusClassTouchScreen );
			toggleFocusClassTouchScreen();
		}

		siteNavigation.find( 'a' ).on( 'focus blur', function () {
			$( this ).parents( '.menu-item' ).toggleClass( 'focus' );
		} );
	} )();

	// Add the default ARIA attributes for the menu toggle and the navigations.
	function onResizeARIA() {
		if ( window.innerWidth < 910 ) {
			if ( menuToggle.hasClass( 'toggled-on' ) ) {
				menuToggle.attr( 'aria-expanded', 'true' );
			} else {
				menuToggle.attr( 'aria-expanded', 'false' );
			}

			if ( siteHeaderMenu.hasClass( 'toggled-on' ) ) {
				siteNavigation.attr( 'aria-expanded', 'true' );
			} else {
				siteNavigation.attr( 'aria-expanded', 'false' );
			}

			menuToggle.attr( 'aria-controls', 'site-navigation social-navigation' );
		} else {
			menuToggle.removeAttr( 'aria-expanded' );
			siteNavigation.removeAttr( 'aria-expanded' );
			menuToggle.removeAttr( 'aria-controls' );
		}
	}

	// Add 'below-entry-meta' class to elements.
	function belowEntryMetaClass( param ) {
		if ( body.hasClass( 'page' ) || body.hasClass( 'search' ) || body.hasClass( 'single-attachment' ) || body.hasClass( 'error404' ) ) {
			return;
		}

		$( '.entry-content' ).find( param ).each( function () {
			var element              = $( this ),
				elementPos           = element.offset(),
				elementPosTop        = typeof elementPos !== 'undefined' ? elementPos.top : 0,
				entryFooter          = element.closest( 'article' ).find( '.entry-footer' ),
				entryFooterPos       = entryFooter.offset(),
				entryFooterPosBottom = typeof entryFooterPos !== 'undefined' ? entryFooterPos.top + ( entryFooter.height() + 28 ) : entryFooter.height() + 28,
				caption              = element.closest( 'figure' ),
				newImg;

			// Add 'below-entry-meta' to elements below the entry meta.
			if ( elementPosTop > entryFooterPosBottom ) {

				// Check if full-size images and captions are larger than or equal to 840px.
				if ( 'img.size-full' === param ) {

					// Create an image to find native image width of resized images (i.e. max-width: 100%).
					newImg     = new Image();
					newImg.src = element.attr( 'src' );

					$( newImg ).on( 'load', function () {
						if ( newImg.width >= 840 ) {
							element.addClass( 'below-entry-meta' );

							if ( caption.hasClass( 'wp-caption' ) ) {
								caption.addClass( 'below-entry-meta' );
								caption.removeAttr( 'style' );
							}
						}
					} );
				} else {
					element.addClass( 'below-entry-meta' );
				}
			} else {
				element.removeClass( 'below-entry-meta' );
				caption.removeClass( 'below-entry-meta' );
			}
		} );
	}

	function isMobile() {
		return window.innerWidth < 910;
	}

	// sticky the sidebar if it's fully visible
	function stickySidebar() {
		if ( sidebar.length && !sidebar.hasClass( '.no-sticky' ) ) {
			var sidebarTop = 20;

			if ( !masthead.hasClass( 'no-sticky' ) ) {
				sidebarTop += masthead.outerHeight() + parseInt( masthead.css( 'margin-top' ) );
			} else {
				sidebarTop += masthead.offset().top;
			}

			if ( !isMobile() && window.innerHeight >= ( sidebar.outerHeight() + sidebarTop ) ) {
				sidebar.css( { position: 'sticky', top: sidebarTop + 'px' } );
				sidebar.addClass( 'sticky-sidebar' );
			} else {
				sidebar.css( { position: 'static' } );
				sidebar.removeClass( 'sticky-sidebar' );
			}
		}
	}

	// sticky the sidebar if it's fully visible
	function stickyHeader() {
		if ( masthead.length && !masthead.hasClass( 'no-sticky' ) ) {
			if ( !isMobile() && $( window ).scrollTop() ) {
				masthead.addClass( 'sticky-header' );
				page.css( 'padding-top', masthead.outerHeight() );
			} else {
				masthead.removeClass( 'sticky-header' );
				page.css( 'padding-top', '0' );
			}
		}
	}

	// sticky the sidebar if it's fully visible
	function stickyMenus() {
		if ( stickyMenu.length ) {
			var sidebarTop = parseInt( stickyMenu.css( 'margin-top' ) );

			if ( !masthead.hasClass( 'no-sticky' ) ) {
				sidebarTop += masthead.outerHeight() + parseInt( masthead.css( 'margin-top' ) );
			} else {
				sidebarTop += masthead.offset().top;
			}


			stickyMenu.css( { top: sidebarTop + 'px' } );
			if ( $( window ).scrollTop() >= sidebarTop ) {
				stickyMenu.addClass( 'is-sticky' );
			} else {
				stickyMenu.removeClass( 'is-sticky' );
			}
		}
	}

	var initial_values = {
		masterheadOffsetTop  : masthead.offset().top,
		masterheadOuterHeight: masthead.outerHeight()
	};

	function initValues() {
		initial_values.masterheadOffsetTop   = masthead.offset().top;
		initial_values.masterheadOuterHeight = masthead.outerHeight();
	}

	var initial_masterhead_offset_top = masthead.offset().top;

	function enableAnchors() {
		$( document ).on( 'click', 'a.anchor', function ( e ) {
			e.preventDefault();
			var target = $( $( e.target ).attr( 'href' ) );
			if ( target.length ) {
				var offsetTop = masthead.outerHeight() + Math.max( parseInt( masthead.css( 'margin-top' ) ), initial_masterhead_offset_top ),
					scrollto  = target.offset().top - target.outerHeight() - offsetTop;

				$( 'html, body' ).animate( { scrollTop: scrollto }, 400 );
			}
		} );
	}

	$( document ).ready( function () {
		body = $( document.body );

		$( window )
			.on( 'load', onResizeARIA )
			.on( 'resize', function () {
				clearTimeout( resizeTimer );
				resizeTimer = setTimeout( function () {
					belowEntryMetaClass( 'img.size-full' );
					belowEntryMetaClass( 'blockquote.alignleft, blockquote.alignright' );
				}, 300 );
				onResizeARIA();
				initValues();
			} )
			.on( 'scroll', function () {
				stickySidebar();
				stickyHeader();
				stickyMenus();
			} );

		belowEntryMetaClass( 'img.size-full' );
		belowEntryMetaClass( 'blockquote.alignleft, blockquote.alignright' );
		stickySidebar();
		stickyHeader();

		stickyMenus();
		enableAnchors();
	} );

	$( window ).load( function () {
		stickySidebar();
	} );


} )( jQuery );
