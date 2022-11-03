jQuery( function ( $ ) {
    "use strict";

    var block_params = {
        message        : bk.loader_svg,
        blockMsgClass  : 'yith-wcbk-block-ui-element',
        css            : {
            border    : 'none',
            background: 'transparent'
        },
        overlayCSS     : {
            background: '#fff',
            opacity   : 0.7
        },
        ignoreIfBlocked: true
    };

    $( document ).on( 'submit', '.yith-wcbk-booking-search-form.show-results-popup form', function ( event ) {
        event.preventDefault();

        var form  = $( this ),
            popup = $.fn.yith_wcbk_popup( {
                                              popup_class: 'yith-wcbk-booking-search-form-result yith-wcbk-popup woocommerce woocommerce-page',
                                              ajax       : true,
                                              ajax_data  : form.serialize(),
                                              url        : bk.ajaxurl
                                          } );

    } );

    // Select 2
    $( '.yith-wcbk-select2' ).select2( { width: '100%' } );

    // Google Maps Autocomplete
    $( '.yith-wcbk-google-maps-places-autocomplete' ).each( function () {
        new google.maps.places.Autocomplete( this );
    } );

    $( document ).on( 'click', '.yith-wcbk-search-form-result-product-thumb-actions span', function ( event ) {
        var target        = $( event.target ),
            thumb_wrapper = target.closest( '.yith-wcbk-search-form-result-product-thumb-wrapper' ),
            images        = thumb_wrapper.find( '.yith-wcbk-thumb' ),
            images_count  = images.length,
            current       = thumb_wrapper.find( '.yith-wcbk-thumb.current' );

        if ( images_count > 1 ) {
            if ( current.length < 1 ) {
                current = images.first();
                current.addClass( 'current' );
            }

            var image_to_show;

            if ( target.is( '.yith-wcbk-search-form-result-product-thumb-action-next' ) ) {
                image_to_show = current.next( '.yith-wcbk-thumb' );

                if ( image_to_show.length < 1 ) {
                    image_to_show = images.first();
                }
            } else {
                image_to_show = current.prev( '.yith-wcbk-thumb' );

                if ( image_to_show.length < 1 ) {
                    image_to_show = images.last();
                }
            }

            current.removeClass( 'current' );
            image_to_show.addClass( 'current' );
        }
    } );


    $( document ).on( 'click', '.yith-wcbk-search-form-results-show-more', function ( event ) {
        var target          = $( event.target ),
            product_ids     = target.data( 'product-ids' ),
            page            = target.data( 'page' ),
            booking_request = target.data( 'booking-request' ),
            last_page       = target.data( 'last-page' ),
            next_page       = page + 1,
            results         = target.closest( '.yith-wcbk-booking-search-form-result' ).find( '.yith-wcbk-search-form-result-products' );

        target.block( block_params );
        $.ajax( {
                    data   : {
                        product_ids    : product_ids,
                        page           : next_page,
                        booking_request: booking_request,
                        action         : 'yith_wcbk_search_booking_products_paged'
                    },
                    url    : bk.ajaxurl,
                    success: function ( data ) {
                        results.append( data );
                        if ( last_page <= next_page ) {
                            target.remove();
                        } else {
                            target.data( 'page', next_page );
                            target.unblock();
                        }

                    }
                } );
    } );
} );