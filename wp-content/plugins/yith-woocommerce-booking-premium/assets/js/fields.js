jQuery( function ( $ ) {
    "use strict";
    /**
     * Select Alt
     */
    var selectAltParams = {
        container      : $( '.yith-wcbk-select-alt__container' ),
        openedClass    : 'yith-wcbk-select-alt__container--opened',
        unselectedClass: 'yith-wcbk-select-alt__container--unselected',
        open           : function ( event ) {
            $( this ).closest( '.yith-wcbk-select-alt__container' ).addClass( selectAltParams.openedClass );
        },
        close          : function ( event ) {
            $( this ).closest( '.yith-wcbk-select-alt__container' ).removeClass( selectAltParams.openedClass );
        },
        blur           : function ( event ) {
            $( this ).trigger( 'blur' );
        }
    };

    selectAltParams.container
        .on( 'focusin', 'select', selectAltParams.open )
        .on( 'focusout change', 'select', selectAltParams.close )
        .on( 'change', 'select', selectAltParams.blur );

    /**
     * Tip tip
     */
    $( '.yith-wcbk-help-tip' ).tipTip( {
                                           'attribute': 'data-tip',
                                           'fadeIn'   : 50,
                                           'fadeOut'  : 50,
                                           'delay'    : 200
                                       } );
} );

