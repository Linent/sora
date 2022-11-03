/* global wcbk_admin */
jQuery( function ( $ ) {
    "use strict";

    /**
     * Onoff
     */
    var yith_wcbk_onoff = {
        init  : function () {
            $( document ).on( 'click', '.yith-wcbk-printer-field__on-off', yith_wcbk_onoff.update );
        },
        update: function ( event ) {
            var onoff        = $( event.target ).closest( '.yith-wcbk-printer-field__on-off' ),
                hidden_input = onoff.find( '.yith-wcbk-printer-field__on-off__value' ).first(),
                value        = hidden_input ? hidden_input.val() : 'no';

            if ( value === 'yes' ) {
                hidden_input.val( 'no' );
                onoff.removeClass( 'yith-wcbk-printer-field__on-off--enabled' );
            } else {
                hidden_input.val( 'yes' );
                onoff.addClass( 'yith-wcbk-printer-field__on-off--enabled' );
            }
            hidden_input.trigger( 'change' );
        }
    };

    yith_wcbk_onoff.init();

    /**
     * Onoff Advanced
     */
    var yith_wcbk_onoff_advanced = {
        init       : function () {
            $( document ).on( 'click', '.yith-wcbk-on-off-advanced', yith_wcbk_onoff_advanced.next );
        },
        next       : function ( event ) {
            var onoff     = $( event.target ).closest( '.yith-wcbk-on-off-advanced' ),
                input     = onoff.find( '.yith-wcbk-on-off-advanced__value' ).first(),
                label     = onoff.find( '.yith-wcbk-on-off-advanced__label' ).first(),
                options   = onoff.data( 'options' ),
                value     = input.val(),
                nextValue = yith_wcbk_onoff_advanced.findNextKey( options, value ),
                classes   = [], i;

            // populate the classes array
            for ( i in options ) {
                classes.push( options[ i ].class );
            }

            onoff.removeClass( classes.join( ' ' ) );
            onoff.addClass( options[ nextValue ].class );
            input.val( nextValue );
            label.html( options[ nextValue ].label );
        },
        findNextKey: function ( db, key ) {
            var keys = Object.keys( db ),
                i    = keys.indexOf( key );
            if ( i !== -1 && keys[ i + 1 ] ) {
                return keys[ i + 1 ];
            } else {
                return keys[ 0 ];
            }
        }
    };

    yith_wcbk_onoff_advanced.init();

    /**
     * Select Inline
     */
    var yith_wcbk_fields_select_inline = {
        optionHandler   : '.yith-wcbk-printer-field__select-inline__option',
        containerHandler: '.yith-wcbk-printer-field__select-inline',
        inputHandler    : 'input',
        selectedClass   : 'yith-wcbk-printer-field__select-inline__option--selected',
        init            : function () {
            var self = yith_wcbk_fields_select_inline;

            $( document ).on( 'click', self.optionHandler, function ( event ) {
                var option    = $( event.target ),
                    container = option.closest( self.containerHandler ),
                    input     = container.find( self.inputHandler ),
                    options   = container.find( self.optionHandler ),
                    value     = option.data( 'value' );

                input.val( value );
                options.removeClass( self.selectedClass );
                option.addClass( self.selectedClass );
            } );
        }
    };
    yith_wcbk_fields_select_inline.init();


    /**
     * Time Select
     */
    var yith_wcbk_timeselect = {
        container: '.yith-wcbk-time-select__container',
        hour     : '.yith-wcbk-time-select-hour',
        minute   : '.yith-wcbk-time-select-minute',
        separator: ':',
        init     : function () {
            var self = yith_wcbk_timeselect;

            $( document ).on( 'change', self.hour + ', ' + self.minute, self.update );
        },
        update   : function ( event ) {
            var self      = yith_wcbk_timeselect,
                container = $( event.target ).closest( self.container ),
                hour      = container.find( self.hour ).first(),
                minute    = container.find( self.minute ).first(),
                input     = container.find( 'input' ).first();

            input.val( hour.val() + self.separator + minute.val() ).trigger( 'change' );
        }
    };

    yith_wcbk_timeselect.init();


    /**
     * Select2 - Select All | Deselect All
     */
    var select_all_btn   = $( '.yith-wcbk-select2-select-all' ),
        deselect_all_btn = $( '.yith-wcbk-select2-deselect-all' );

    select_all_btn.on( 'click', function () {
        var select_id     = $( this ).data( 'select-id' ),
            target_select = $( '#' + select_id );

        target_select.find( 'option' ).prop( 'selected', true );
        target_select.trigger( 'change' );
    } );

    deselect_all_btn.on( 'click', function () {
        var select_id     = $( this ).data( 'select-id' ),
            target_select = $( '#' + select_id );

        target_select.find( 'option' ).prop( 'selected', false );
        target_select.trigger( 'change' );
    } );


    /**
     * Delete Logs Confirmation
     */
    $( '#yith-wcbk-logs' ).on( 'click', 'h2 a.page-title-action', function ( event ) {
        event.stopImmediatePropagation();
        return window.confirm( wcbk_admin.i18n_delete_log_confirmation );
    } );


    /**
     * Tip Tip
     */
    $( document ).on( 'yith-wcbk-init-tiptip', function () {
        // Remove any lingering tooltips
        $( '#tiptip_holder' ).removeAttr( 'style' );
        $( '#tiptip_arrow' ).removeAttr( 'style' );
        $( '.tips' ).tipTip( {
                                 'attribute': 'data-tip',
                                 'fadeIn'   : 50,
                                 'fadeOut'  : 50,
                                 'delay'    : 200
                             } );
    } ).trigger( 'yith-wcbk-init-tiptip' );


    /**
     * Date Picker
     */
    $( document ).on( 'yith-wcbk-init-datepickers', function () {
        $( '.yith-wcbk-admin-date-picker' ).yith_wcbk_datepicker();
    } ).trigger( 'yith-wcbk-init-datepickers' );


    /**
     *  Copy on Clipboard
     */
    var copy_to_clipboard_tip = false;
    $( document ).on( 'click', '.yith-wcbk-copy-to-clipboard', function ( event ) {
        var target           = $( this ),
            selector_to_copy = target.data( 'selector-to-copy' ),
            obj_to_copy      = $( selector_to_copy );

        if ( obj_to_copy.length > 0 ) {
            copy_to_clipboard_tip && copy_to_clipboard_tip.remove() && ( copy_to_clipboard_tip = false );

            if ( !copy_to_clipboard_tip ) {
                copy_to_clipboard_tip = $( '<div id="yith-wcbk-copy-to-clipboard__copied">' + wcbk_admin.i18n_copied + '</div>' );
                $( 'body' ).append( copy_to_clipboard_tip );
            }

            copy_to_clipboard_tip.hide();


            var temp  = $( "<input>" ),
                value = obj_to_copy.is( 'input' ) ? obj_to_copy.val() : obj_to_copy.html();
            $( 'body' ).append( temp );

            temp.val( value ).select();
            document.execCommand( "copy" );

            temp.remove();

            copy_to_clipboard_tip.css( {
                                           left: target.offset().left + target.outerWidth() / 2 - copy_to_clipboard_tip.outerWidth() / 2,
                                           top : target.offset().top - copy_to_clipboard_tip.outerHeight() - 7
                                       } )
                .fadeIn().delay( 1000 ).fadeOut();
        }
    } );


    /**
     *  Show conditional: show/hide element based on other element value
     */
    $( '.yith-wcbk-show-conditional' ).hide().each( function () {
        var $show_conditional = $( this ),
            field_id          = $show_conditional.data( 'field-id' ),
            $field            = $( '#' + field_id ),
            value             = $show_conditional.data( 'value' ),
            _to_compare, _is_checkbox, _is_onoff;

        if ( $field.length ) {
            _is_checkbox = $field.is( 'input[type=checkbox]' );
            _is_checkbox && ( value = value !== 'no' );

            _is_onoff = $field.is( '.yith-wcbk-printer-field__on-off' );
            _is_onoff && ( $field = $field.find( 'input' ) );

            $field.on( 'change keyup', function () {
                _to_compare = !_is_checkbox ? $field.val() : $field.is( ':checked' );
                if ( _to_compare === value ) {
                    $show_conditional.show();
                } else {
                    $show_conditional.hide();
                }
            } ).trigger( 'change' );
        }
    } );


    /**
     *  Move
     */
    $( '.yith-wcbk-move' ).each( function () {
        var $to_move = $( this ),
            after    = $to_move.data( 'after' );

        if ( after.length > 0 ) {
            $to_move.insertAfter( after ).show();
        }
    } );


    /**
     *  Date Time Fields
     */
    $( '.yith-wcbk-date-time-field' ).each( function () {
        var $dateTime  = $( this ),
            dateAnchor = $( this ).data( 'date' ),
            timeAnchor = $( this ).data( 'time' ),
            $date      = $( dateAnchor ).first(),
            $time      = $( timeAnchor ).first(),
            update     = function () {
                $dateTime.val( $date.val() + ' ' + $time.val() );
            };

        $date.on( 'change', update );
        $time.on( 'change', update );
    } );

    /**
     *  Logs
     */
    $( document ).on( 'click', '#yith-wcbk-logs-tab-table td.description-column .expand:not(.disabled)', function ( e ) {
        var open               = $( e.target ),
            description_column = open.closest( 'td.description-column' );
        description_column.toggleClass( 'expanded' );
    } );
} );