jQuery( function ( $ ) {
    "use strict";

    var all_bookings        = $( '.yith-wcbk-booking-calendar-single-booking' ),
        booking_hover_class = 'yith-wcbk-hover',
        reset_all_bookings  = function () {
            all_bookings.removeClass( booking_hover_class );
        },
        fast_search_input   = $( '#yith-wcbk-booking-calendar-fast-search' ),
        all_booking_datas   = $( '.yith-wcbk-booking-calendar-single-booking-data' ),
        block_params        = {
            message        : '',
            overlayCSS     : {
                background: '#fff',
                opacity   : 0.7
            },
            ignoreIfBlocked: true
        };

    $( document ).on( 'mouseenter', '.yith-wcbk-booking-calendar-single-booking', function ( e ) {
        var booking_container = $( this ),
            booking_class     = booking_container.data( 'booking-class' );

        reset_all_bookings();

        if ( booking_class ) {
            $( document ).find( '.' + booking_class ).addClass( booking_hover_class );
        }
    } )

                 .on( 'mouseleave', '.yith-wcbk-booking-calendar-single-booking', reset_all_bookings )


                 .on( 'click', '.yith-wcbk-booking-calendar-single-booking .yith-wcbk-booking-calendar-single-booking-title', function () {
                     var booking                = $( this ).closest( '.yith-wcbk-booking-calendar-single-booking' ),
                         booking_data_container = booking.find( '.yith-wcbk-booking-calendar-single-booking-data' ),
                         is_open                = booking_data_container.is( '.open' );

                     if ( is_open ) {
                         booking_data_container.trigger( 'close' );
                     } else {
                         all_booking_datas.trigger( 'hide' );
                         booking_data_container.trigger( 'open' );
                     }
                 } )

                 .on( 'click', '.yith-wcbk-booking-calendar-single-booking-data-action-close', function () {
                     var booking                = $( this ).closest( '.yith-wcbk-booking-calendar-single-booking' ),
                         booking_data_container = booking.find( '.yith-wcbk-booking-calendar-single-booking-data' );
                     booking_data_container.trigger( 'close' );
                 } )

                 .on( 'close', '.yith-wcbk-booking-calendar-single-booking-data', function () {
                     $( this ).removeClass( 'open' ).fadeOut( 300 );
                 } )

                 .on( 'hide', '.yith-wcbk-booking-calendar-single-booking-data', function () {
                     $( this ).removeClass( 'open' ).hide();
                 } )

                 .on( 'open', '.yith-wcbk-booking-calendar-single-booking-data', function () {
                     $( this ).addClass( 'open' ).fadeIn( 300 );
                 } )

                 .on( 'click', function ( event ) {
                     var booking = $( event.target ).closest( '.yith-wcbk-booking-calendar-single-booking' );
                     if ( booking.length <= 0 ) {
                         all_booking_datas.trigger( 'close' );
                     }
                 } );

    // FAST SEARCH
    fast_search_input.on( 'keyup', function () {
        var search_value = $( this ).val();

        if ( search_value.length > 2 ) {

            search_value = search_value.toLowerCase();

            all_bookings.each( function ( e ) {
                var target = $( this ),
                    text   = target.html().toLowerCase();

                if ( text.indexOf( search_value ) > -1 ) {
                    $( this ).fadeIn();
                } else {
                    $( this ).fadeOut();
                }
            } );
        } else {
            all_bookings.fadeIn();
        }
    } );


    // Expand Week
    var all_row = $( '.yith-wcbk-booking-calendar tbody tr' );
    $( document ).on( 'click', 'td.yith-wcbk-booking-calendar-expand-week', function () {
        var target      = $( this ),
            current_row = target.closest( 'tr' );

        if ( current_row.hasClass( 'expanded' ) ) {
            // Reset All Rows
            all_row.removeClass( 'expanded' ).removeClass( 'closed' );
        } else {
            all_row.removeClass( 'expanded' ).addClass( 'closed' );

            current_row.removeClass( 'closed' ).addClass( 'expanded' );
        }
    } );

    // Product ID
    $( '#product_id' ).on( 'change', function () {
        $( '#yith-wcbk-booking-calendar-wrap' ).block( block_params );
        $( this ).closest( 'form' ).submit();
    } );
} );
