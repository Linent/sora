jQuery( function ( $ ) {
    "use strict";

    var widget       = $( '.yith_wcbk_booking_product_form_widget.yith_wcbk_booking_product_form_widget--mobile-fixed' ),
        mouseTrap    = $( '.yith_wcbk_widget_booking_form_mouse_trap' ),
        closeBtn     = $( '.yith_wcbk_widget_booking_form_close' ),
        open         = function () {
            show_overlay();
            widget.addClass( 'yith_wcbk_booking_product_form_widget__opened' );
        },
        close        = function () {
            widget.removeClass( 'yith_wcbk_booking_product_form_widget__opened' );
            hide_overlay();
        },
        show_overlay = function () {
            var overlay = $( '.yith_wcbk_widget_booking_form_overlay' );
            if ( overlay.length < 1 ) {
                overlay = $( '<div class="yith_wcbk_widget_booking_form_overlay"></div>' );
                $( 'body' ).append( overlay );
            }

            overlay.show();
        },
        hide_overlay = function () {
            $( '.yith_wcbk_widget_booking_form_overlay' ).hide();
        };

    if ( widget.length > 0 ) {
        $( document ).on( 'click', '.yith_wcbk_widget_booking_form_overlay', close );

        mouseTrap.on( 'click', open );

        closeBtn.on( 'click', close );
    }

} );
