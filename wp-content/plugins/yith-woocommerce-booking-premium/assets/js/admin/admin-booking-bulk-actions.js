/* global wcbk_bulk_actions */
jQuery( function ( $ ) {
    "use strict";
    var top_select    = $( '#bulk-action-selector-top' ),
        bottom_select = $( '#bulk-action-selector-bottom' ),
        actions       = wcbk_bulk_actions.actions;

    for ( var action_id in actions ) {
        if ( actions.hasOwnProperty( action_id ) ) {
            var action_value  = actions[ action_id ],
                action_option = $( '<option>' ).val( action_id ).text( action_value );

            action_option.appendTo( top_select ).clone().appendTo( bottom_select );
        }
    }
} );