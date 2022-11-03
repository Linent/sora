/**
 * utils
 *
 */
( function ( $ ) {

    /**
     * Read More shortcode
     */

    $( '.yith-booking-shortcode__read-more__label' ).on( 'click', function () {
        var container  = $( this ).closest( '.yith-booking-shortcode__read-more__container' ),
            content    = container.find( '.yith-booking-shortcode__read-more__content' ),
            label_div  = container.find( '.yith-booking-shortcode__read-more__label' ),
            label_text = container.find( '.yith-booking-shortcode__read-more__label_text' ),
            label      = label_div.data( 'label' ),
            hide_label = label_div.data( 'hide-label' ),
            opened     = container.hasClass( 'opened' );

        if ( opened ) {
            container.removeClass( 'opened' );
            content.slideUp(300);
            label_text.html( label );
        } else {
            container.addClass( 'opened' );
            content.slideDown(300);
            label_text.html( hide_label );
        }
    } );

} )( jQuery );
