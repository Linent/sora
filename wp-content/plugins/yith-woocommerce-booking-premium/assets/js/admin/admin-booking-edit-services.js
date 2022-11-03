jQuery( function ( $ ) {
    "use strict";

    var form = $( '#addtag' ),
        submit_btn = form.find('#submit');

    $( document ).on( 'yith_wcbk_service_taxonomy_form_reset', function () {
        var numbers  = form.find( '.form-field input[type=number]' ),
            checkbox = form.find( '.form-field input[type=checkbox]' );

        numbers.val( '' );
        checkbox.prop( 'checked', false );
    } );

    submit_btn.on( 'click', function () {
        $( document ).trigger( 'yith_wcbk_service_taxonomy_form_reset' );
    } );
} );
