jQuery(document).ready(function ($) {

	$('#ryu_option').on('click', function () {
		if ($('.woocommerce-product-gallery__image').hasClass('flex-active-slide')) {
			$('.woocommerce-product-gallery__image.flex-active-slide').css('background-color', '#000');
		}
	});

});