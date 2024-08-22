jQuery(document).ready(function ($)
{
	var productWrapperFront = $('.woocommerce-product-gallery__wrapper').children().eq(0);
	var productWrapperBack = $('.woocommerce-product-gallery__wrapper').children().eq(1);

	setCanvas(productWrapperFront, 'front');
	setTimeout(() => setCanvas(productWrapperBack, 'back'), 100); // Timeout due to WooCommerce script processing for second image

	function setCanvas(productWrapper, view)
	{
		productWrapper.html(`
			<div id="pcw_logo_container_${view}" class="logo-wrapper logo-active" style="display: none">
				<canvas id="pcw_logo_canvas_${view}" class="pcw_logo_canvas"></canvas>
				<div class="control-icons">
					<div class="pcw_icon move-icon"></div>
					<div class="pcw_icon resize-icon top-left"></div>
					<div class="pcw_icon resize-icon bottom-right"></div>
					<div class="pcw_icon resize-icon bottom-left"></div>
					<div class="pcw_icon delete-icon"></div>
				</div>
			</div>
			<div id="canvas_container_${view}">
				<canvas id="canvas_${view}" width="${productWrapper.width()}" height="${productWrapper.height()}" data-image-url="${productWrapper.find('a').attr('href')}"></canvas>
			</div>
		`);
		render(document.getElementById(`canvas_${view}`));
		productWrapper.find('a').hide(); // hide the original product image
	}

	$(document).on('click', '.pcw_color', function ()
	{
		var hexColor = rgbToHex($(this).css('background-color'));
		
		render(document.getElementById('canvas_front'), hexColor);
		render(document.getElementById('canvas_back'), hexColor);
	});

	function render(canvas, hexColor = '#FFFFFF')
	{
		imageURL = $(canvas).attr('data-image-url');
		const ctx = canvas.getContext('2d');

		const img = new Image();
		img.src = imageURL;

		img.onload = function () {
			const canvasWidth = canvas.width;
			const canvasHeight = canvas.height;

			const imgWidth = img.width;
			const imgHeight = img.height;

			const imgRatio = imgWidth / imgHeight;
			const canvasRatio = canvasWidth / canvasHeight;

			let newWidth, newHeight;

			if (imgRatio > canvasRatio) {
				newWidth = canvasWidth;
				newHeight = canvasWidth / imgRatio;
			} else {
				newHeight = canvasHeight;
				newWidth = canvasHeight * imgRatio;
			}

			ctx.clearRect(0, 0, canvasWidth, canvasHeight);
			ctx.drawImage(img, (canvasWidth - newWidth) / 2, (canvasHeight - newHeight) / 2, newWidth, newHeight);

			const imageData = ctx.getImageData(0, 0, canvasWidth, canvasHeight);
			const data = imageData.data;

			const color = hexToRgb(hexColor);

			for (let i = 0; i < data.length; i += 4) {
				if (data[i + 3] > 0) { // Se o pixel não for transparente
					data[i] = (data[i] * color.r) / 255;     // Multiplicar o componente R
					data[i + 1] = (data[i + 1] * color.g) / 255; // Multiplicar o componente G
					data[i + 2] = (data[i + 2] * color.b) / 255; // Multiplicar o componente B
				}
			}

			ctx.putImageData(imageData, 0, 0);
		};
	}

	function rgbToHex(rgb)
	{
		let result = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		return result
			? "#" +
			("0" + parseInt(result[1], 10).toString(16)).slice(-2) +
			("0" + parseInt(result[2], 10).toString(16)).slice(-2) +
			("0" + parseInt(result[3], 10).toString(16)).slice(-2)
			: rgb;
	}

	function hexToRgb(hex) 
	{
		hex = hex.replace(/^#/, '');
		if (hex.length === 3) {
			hex = hex.split('').map(char => char + char).join('');
		}
		const bigint = parseInt(hex, 16);
		const r = (bigint >> 16) & 255;
		const g = (bigint >> 8) & 255;
		const b = bigint & 255;
		return { r, g, b };
	}
	
	var $firstMenuItem = $('.pcw_layer_menu_item').first();
	$firstMenuItem.addClass('active');
	
	$('.pcw_layer').hide();
	var firstLayerId = $firstMenuItem.data('layer-id');
	$('.pcw_layer[data-layer-id="' + firstLayerId + '"]').show();

	$(document).on('click', '.pcw_layer_menu_item', function()
	{
		$('.pcw_layer_menu_item').removeClass('active');
		$(this).addClass('active');
		$('.pcw_layer').hide();

		var layerId = $(this).data('layer-id');
		$('.pcw_layer[data-layer-id="' + layerId + '"]').fadeToggle();
	});

	$(document).on('click', '.pcw_option_color', function()
	{
		$('.pcw_option').removeClass('active');
		var $option = $(this).closest('.pcw_option');
		$option.addClass('active');

		var optionId = $option.data('option-id');
		var colorHex = rgbToHex($(this).css('background-color'));

		var optionCanvasFront = $('.pcw_image_front[image-front-id="' + optionId + '"]');
		var optionCanvasBack = $('.pcw_image_back[image-back-id="' + optionId + '"]');

		optionCanvasFront.attr('width', productWrapperFront.width());
		optionCanvasFront.attr('height', productWrapperFront.height());

		optionCanvasBack.attr('width', productWrapperBack.width());
		optionCanvasBack.attr('height', productWrapperBack.height());

		$('#canvas_container_front').prepend(optionCanvasFront);
		$('#canvas_container_back').prepend(optionCanvasBack);

		render(optionCanvasFront[0], colorHex);
		render(optionCanvasBack[0], colorHex);

		if($(this).hasClass('active')) {
			$(this).removeClass('active');
			optionCanvasFront.fadeOut();
			optionCanvasBack.fadeOut();
		} else {
			if ($option.hasClass('active')){
				$option.find('.pcw_option_color').removeClass('active');
			}
			$(this).addClass('active');
			optionCanvasFront.fadeIn();
			optionCanvasBack.fadeIn();
		}
	});

	$('.pcw_upload_drop_area').on('click', function() {
		// Reset the file input value to allow selecting the same file again
		$(this).val('');
	});

	$('.pcw_upload_drop_area').on('change', function (event) {
		var view = '';
		if($(this).hasClass('front')) {
			view = 'front';
		} else if($(this).hasClass('back')) {
			view = 'back';
		}
		var file = event.target.files[0];
		if (file) {
			uploadLogo(file, view);
		}
	});

	var $dropArea = $('.pcw_upload_drop_area');
	var $fileInput = $('.pcw_upload_input');

	function get_upload_view(dropArea) {
		if(dropArea.hasClass('front')) {
			return 'front';
		} else if(dropArea.hasClass('back')) {
			return 'back';
		}
	}

	$dropArea.on('dragenter dragover', function (e) {
		e.preventDefault();
		e.stopPropagation();
		$(this).addClass('highlight');
	});

	$dropArea.on('dragleave', function (e) {
		e.preventDefault();
		e.stopPropagation();
		$(this).removeClass('highlight');
	});

	$dropArea.on('drop', function (e) {
		e.preventDefault();
		e.stopPropagation();
		$(this).removeClass('highlight');

		var view = get_upload_view($(this));

		var file = e.originalEvent.dataTransfer.files[0];
		if (file) {
			uploadLogo(file, view);
		}
	});

	$fileInput.on('change', function (event) {
		var view = get_upload_view($(this));
		var file = event.target.files[0];
		if (file) {
			uploadLogo(file, view);
		}
	});

	function uploadLogo(file, view) {
		var reader = new FileReader();
		reader.onload = function (e) {
			var img = new Image();
			img.src = e.target.result;

			img.onload = function() {
				var $logoWrapper = $(`#pcw_logo_container_${view}`);
				var $canvas = $(`#pcw_logo_canvas_${view}`);
				var canvas = $canvas[0];
				var ctx = canvas.getContext('2d');

				// Set a maximum size for the logo
				var maxWidth = 200;
				var maxHeight = 200;
				var width = img.width;
				var height = img.height;

				if (width > height) {
					if (width > maxWidth) {
						height *= maxWidth / width;
						width = maxWidth;
					}
				} else {
					if (height > maxHeight) {
						width *= maxHeight / height;
						height = maxHeight;
					}
				}

				// Adjust the canvas size
				canvas.width = width;
				canvas.height = height;

				// Clear the canvas and draw the new image
				ctx.clearRect(0, 0, canvas.width, canvas.height);
				ctx.drawImage(img, 0, 0, width, height);

				// Update the canvas data
				$canvas.data('image-url', img.src);
				$canvas.data('original-width', width);
				$canvas.data('original-height', height);
				
				// Show the logo wrapper
				$logoWrapper.show();

				// Reset the wrapper transformation
				$logoWrapper.css('transform', 'translate(0px, 0px)');
				$logoWrapper.attr('data-x', 0);
				$logoWrapper.attr('data-y', 0);

				$(`#pcw_logo_container_${view}`).find('.control-icons').show();
				$(`#pcw_logo_container_${view}`).addClass('logo-active');

				console.log('Logo carregada e renderizada');
			};
		};
		reader.readAsDataURL(file);
	}

	$(document).on('click', function (event) {
		var $target = $(event.target);

		// Check if the click was outside the logo container
		if (!$target.closest('.logo-wrapper').length) {
			$('.logo-wrapper .control-icons').fadeOut();
			$('.logo-wrapper').removeClass('logo-active');
		}
	});

	$(document).on('click', '.logo-wrapper', function () {
		console.log('click');
		$(this).find('.control-icons').show();
		$(this).addClass('logo-active');
	});

	$(document).on('click', '.delete-icon', function () {
		var $logoWrapper = $(this).closest('.logo-wrapper');
		var $canvas = $logoWrapper.find('.pcw_logo_canvas');
		var canvas = $canvas[0];
		var ctx = canvas.getContext('2d');

		// Clear the canvas
		ctx.clearRect(0, 0, canvas.width, canvas.height);

		// Hide the logo wrapper
		$logoWrapper.hide();

		// Reset the canvas data
		$canvas.removeData('image-url original-width original-height');

		// Reset the wrapper transformation
		$logoWrapper.css('transform', 'translate(0px, 0px)');
		$logoWrapper.attr('data-x', 0);
		$logoWrapper.attr('data-y', 0);

		console.log('Logo deletada');
	});

	function resizeLogoWrapper(event, $wrapper, $canvas, direction) {
		var canvas = $canvas[0];
		var ctx = canvas.getContext('2d');

		var originalImg = $canvas.data('original-img');
		var originalWidth = $canvas.data('original-width');
		var originalHeight = $canvas.data('original-height');

		var deltaX = event.dx;
		var deltaY = event.dy;

		var newWidth, newHeight;

		switch (direction) {
			case 'bottom-right':
				newWidth = originalWidth + deltaX;
				newHeight = (newWidth * originalHeight) / originalWidth;
				break;
			case 'top-left':
				newWidth = originalWidth - deltaX;
				newHeight = (newWidth * originalHeight) / originalWidth;
				$wrapper.css({
					transform: 'translate(' + (parseFloat($wrapper.attr('data-x') || 0) + deltaX) + 'px, ' + 
							   (parseFloat($wrapper.attr('data-y') || 0) + deltaY) + 'px)'
				});
				$wrapper.attr('data-x', parseFloat($wrapper.attr('data-x') || 0) + deltaX);
				$wrapper.attr('data-y', parseFloat($wrapper.attr('data-y') || 0) + deltaY);
				break;
			case 'bottom-left':
				newWidth = originalWidth - deltaX;
				newHeight = (newWidth * originalHeight) / originalWidth;
				$wrapper.css({
					transform: 'translate(' + (parseFloat($wrapper.attr('data-x') || 0) + deltaX) + 'px, ' + 
							   (parseFloat($wrapper.attr('data-y') || 0)) + 'px)'
				});
				$wrapper.attr('data-x', parseFloat($wrapper.attr('data-x') || 0) + deltaX);
				break;
		}

		if (newWidth > 10 && newHeight > 10) {
			canvas.width = newWidth;
			canvas.height = newHeight;

			ctx.clearRect(0, 0, canvas.width, canvas.height);
			ctx.drawImage(originalImg, 0, 0, canvas.width, canvas.height);

			$canvas.data('original-width', newWidth);
			$canvas.data('original-height', newHeight);
		}
	}

	function setupResizeInteraction(selector, direction) {
		interact(selector).draggable({
			onstart: function (event) {
				var $wrapper = $(event.target).closest('.logo-wrapper');
				var $canvas = $wrapper.find('.pcw_logo_canvas');
				var canvas = $canvas[0];

				var img = new Image();
				img.src = $canvas.data('image-url');
				$canvas.data('original-img', img);
				$canvas.data('original-width', canvas.width);
				$canvas.data('original-height', canvas.height);
			},
			onmove: function (event) {
				var $wrapper = $(event.target).closest('.logo-wrapper');
				var $canvas = $wrapper.find('.pcw_logo_canvas');
				resizeLogoWrapper(event, $wrapper, $canvas, direction);
			}
		});
	}

	setupResizeInteraction('.resize-icon.bottom-right', 'bottom-right');
	setupResizeInteraction('.resize-icon.top-left', 'top-left');
	setupResizeInteraction('.resize-icon.bottom-left', 'bottom-left');

	interact('.logo-wrapper').draggable
	({
		onmove: function (event) {
			var wrapper = $(event.target).closest('.logo-wrapper');
			var x = (parseFloat(wrapper.attr('data-x')) || 0) + event.dx;
			var y = (parseFloat(wrapper.attr('data-y')) || 0) + event.dy;

			wrapper.css({
				transform: 'translate(' + x + 'px, ' + y + 'px)'
			});

			wrapper.attr('data-x', x);
			wrapper.attr('data-y', y);
		}
	});

	interact('.move-icon').draggable
	({
		onmove: function (event) {
			var wrapper = $(event.target).closest('.logo-wrapper');
			var x = (parseFloat(wrapper.attr('data-x')) || 0) + event.dx;
			var y = (parseFloat(wrapper.attr('data-y')) || 0) + event.dy;

			wrapper.css({
				transform: 'translate(' + x + 'px, ' + y + 'px)'
			});

			wrapper.attr('data-x', x);
			wrapper.attr('data-y', y);
		}
	});
});