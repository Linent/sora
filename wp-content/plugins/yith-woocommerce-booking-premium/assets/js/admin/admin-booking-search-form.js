jQuery( function ( $ ) {
    "use strict";

    var field_container   = $( '.yith-wcbk-admin-search-form-fields' ),
        enabled_class     = field_container.data( 'enabled-class' ),
        not_enabled_class = field_container.data( 'not-enabled-class' );

    field_container.sortable( {
            items               : '.yith-wcbk-admin-search-form-field',
            cursor              : 'move',
            handle              : '.yith-wcbk-admin-search-form-field-title',
            scrollSensitivity   : 40,
            forcePlaceholderSize: true,
            revert              : 200,
            axis                : 'y'
        }
    );

    $( document ).on( 'click', '.yith-wcbk-admin-search-form-field-enable', function ( event ) {
        event.stopPropagation();
        var target        = $( this ),
            parent        = target.closest( '.yith-wcbk-admin-search-form-field' ),
            enabled_field = parent.find( 'input.yith-wcbk-admin-search-form-field-enabled' );

        if ( target.is( '.' + enabled_class ) ) {
            target.removeClass( enabled_class );
            target.addClass( not_enabled_class );
            enabled_field.val( 'no' );
        } else {
            target.addClass( enabled_class );
            target.removeClass( not_enabled_class );
            enabled_field.val( 'yes' );
        }

        field_container.trigger( 'change' );
    } );
    
    $( document ).on( 'click', '.yith-wcbk-admin-search-form-field-toggle', function ( event ) {
        event.stopPropagation();
        var target  = $( this ),
            parent  = target.closest( '.yith-wcbk-admin-search-form-field' ),
            content = parent.find( '.yith-wcbk-admin-search-form-field-content' );

        if ( target.is( '.dashicons-arrow-down' ) ) {
            target.removeClass( 'dashicons-arrow-down' );
            target.addClass( 'dashicons-arrow-up' );
            content.slideDown( 200 );
        } else {
            target.addClass( 'dashicons-arrow-down' );
            target.removeClass( 'dashicons-arrow-up' );
            content.slideUp( 200 );
        }
    } );
} );