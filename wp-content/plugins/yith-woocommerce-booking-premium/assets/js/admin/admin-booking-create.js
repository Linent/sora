/* global wcbk_admin */
jQuery( function ( $ ) {
    "use strict";

    var booking_product_select = $( '#product_id' ),
        booking_form_wrapper   = $( '#yith-wcbk-create-booking-form-wrap' ),
        block_params             = {
            message        : wcbk_admin.loader_svg,
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

    booking_product_select.on( 'change', function () {
        booking_form_wrapper.block( block_params );
        var product_id = $( this ).val(),
            post_data  = {
                product_id      : product_id,
                action          : 'yith_wcbk_get_product_booking_form',
                is_create_page  : 'yes'
            };

        $.ajax( {
            type    : "POST",
            data    : post_data,
            url     : ajaxurl,
            success : function ( response ) {
                booking_form_wrapper.html( response );
                $( document.body ).trigger( 'yith-wcbk-init-booking-form' );
            },
            complete: function () {
                booking_form_wrapper.unblock();
            }
        } );
    } );
} );