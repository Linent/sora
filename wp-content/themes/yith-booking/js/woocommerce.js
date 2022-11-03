( function( $ ) {
	var wc_images = $('.yith-booking-woocommerce-images');

	$(document).on('click', '.yith-booking-woocommerce-images .open-gallery', function(){
		console.log('Open Gallery');
		console.log(wc_images.find('.woocommerce-product-gallery__image').first());
		wc_images.find('.woocommerce-product-gallery__image img').first().trigger('click');
	});
} )( jQuery );
