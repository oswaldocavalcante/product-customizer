jQuery(document).ready(function ($)
{
	var productWrapperFront = $('.woocommerce-product-gallery__wrapper').children().eq(0);
	var productWrapperBack = $('.woocommerce-product-gallery__wrapper').children().eq(1);
	var defaultColor = $('#pcw_color_container').children().first().addClass('active');

	if(productWrapperFront.length > 0) {
		setCanvas(productWrapperFront, 'front');
	}
	if(productWrapperBack.length > 0) {
		setTimeout(() => setCanvas(productWrapperBack, 'back'), 100); // Timeout due to WooCommerce script processing for second image
	}

	function setCanvas(productWrapper, view)
	{
		const originalImageURL = productWrapper.find('a').attr('href');

		productWrapper.addClass(`pcw_${view}`);
		productWrapper.html
		(`
			<div id="canvas_container_${view}" class="canvas_container">
				<canvas id="canvas_${view}" width="${productWrapper.width()}" height="${productWrapper.height()}" data-image-url="${productWrapper.find('a').attr('href')}"></canvas>
				<div id="pcw_logo_container_${view}" class="pcw_logo_container" style="display: none;">
					<canvas id="pcw_logo_canvas_${view}" class="pcw_logo_canvas"></canvas>
					<div class="control-icons" style="display: none;">
						<div class="pcw_icon move-icon"></div>
						<div class="pcw_icon resize-icon top-left"></div>
						<div class="pcw_icon resize-icon bottom-right"></div>
						<div class="pcw_icon resize-icon bottom-left"></div>
						<div class="pcw_icon delete-icon"></div>
					</div>
				</div>
			</div>
		`);

		if (pcw_ajax_object.is_admin) {
			$(productWrapper).prepend('<a class="pcw_button_save_image"></a>');
		}

		const canvas = document.getElementById(`canvas_${view}`);
		const img = new Image();
		img.src = originalImageURL;
		img.onload = function() 
		{
			canvas.setAttribute('data-original-width', this.width);
			canvas.setAttribute('data-original-height', this.height);
		};

		render(document.getElementById(`canvas_${view}`), rgbToHex(defaultColor.css('background-color')));
	}

	if (pcw_ajax_object.is_admin) 
	{
		var default_custom_color = '#b4e415';
		$('#pcw_color_container').append
		(`
			<div id="pcw_custom_color_container" class="dashicons-before dashicons-color-picker">
				<input id="pcw_custom_color" class="pcw_color" style="background-color: ${default_custom_color}; color: ${default_custom_color}" value="${default_custom_color}" data-default-color="${default_custom_color}" readonly />
			</div>
		`);

		var custom_color = $('#pcw_custom_color');
		custom_color.iris
		({
			defaultColor: true,
			hide: true,
			palettes: false,
			clear: function () {},
			change: function (event, ui)
			{
				custom_color.css('background-color', event.target.value);
				custom_color.css('color', event.target.value);
				custom_color.trigger('click');
				
				$('.pcw_color').removeClass('active');
				custom_color.addClass('active');
			}
		});

		$(document).on('click', function (event)
		{
			var $target = $(event.target);

			if (!$target.closest('#pcw_custom_color').length && custom_color.hasClass('active')) {
				$('#pcw_custom_color_container .iris-picker').fadeOut();
			}

			if (!$target.closest('.pcw_custom_option_color').length) {
				$('.pcw_custom_option_color_container .iris-picker').fadeOut();
			}
		});

		$(document).on('click', '#pcw_custom_color', function()
		{
			$('#pcw_custom_color_container .iris-picker').fadeIn();
		});

		$('.pcw_option_colors').append
		(`
			<div class="pcw_custom_option_color_container dashicons-before dashicons-color-picker">
				<input class="pcw_custom_option_color pcw_option_color" style="background-color: ${default_custom_color}; color: ${default_custom_color}" value="${default_custom_color}" data-default-color="${default_custom_color}" readonly />
			</div>
		`);

		var custom_option_color = $('.pcw_custom_option_color');
		custom_option_color.iris
		({
			defaultColor: true,
			hide: true,
			palettes: false,
			clear: function () { },
			change: function (event, ui)
			{
				var pcw_option_color = $(event.target);
				pcw_option_color.css('background-color', event.target.value);
				pcw_option_color.css('color', event.target.value);
				pcw_option_color.toggleClass('active');	
				pcw_option_color.trigger('click');
			}
		});

		$(document).on('click', '.pcw_custom_option_color', function ()
		{
			$(this).siblings('.iris-picker').fadeIn();
		});
	}

	$(document).on('click', '.pcw_color', function ()
	{
		$('.pcw_color').removeClass('active');
		$(this).addClass('active');

		var hexColor = rgbToHex($(this).css('background-color'));
		render(document.getElementById('canvas_front'), hexColor);
		render(document.getElementById('canvas_back'), hexColor);
	});

	$(document).on('change', '.pcw-printing-method', function () 
	{
		updatePrice();
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
			$option.removeClass('active');
			$(this).removeClass('active');
			optionCanvasFront.hide();
			optionCanvasBack.hide();
		} else {
			if ($option.hasClass('active')){
				$option.find('.pcw_option_color').removeClass('active');
			}
			$(this).addClass('active');
			optionCanvasFront.show();
			optionCanvasBack.show();
		}

		updatePrice();
	});

	var $dropArea = $('.pcw_upload_drop_area');

	$(document).on('click', '.pcw_upload_drop_area', function() {
		// Reset the file input value to allow selecting the same file again
		$(this).find('.pcw_upload_input').val('');
	});

	$(document).on('change', '.pcw_upload_drop_area', function (event) 
	{
		var view = get_upload_view($(this))
		var file = event.target.files[0];
		if (file) {
			uploadLogo(file, view);
		}
	});

	function get_upload_view(dropArea) 
	{
		if(dropArea.hasClass('front')) {
			return 'front';
		} else if(dropArea.hasClass('back')) {
			return 'back';
		}
	}

	$dropArea.on('dragenter dragover', function (e) 
	{
		e.preventDefault();
		e.stopPropagation();
		$(this).addClass('highlight');
	});

	$dropArea.on('dragleave', function (e) 
	{
		e.preventDefault();
		e.stopPropagation();
		$(this).removeClass('highlight');
	});

	$dropArea.on('drop', function (e) 
	{
		e.preventDefault();
		e.stopPropagation();
		$(this).removeClass('highlight');

		var view = get_upload_view($(this));
		var file = e.originalEvent.dataTransfer.files[0];
		if (file) {
			uploadLogo(file, view);
		}
	});

	var tempLogos = {};
	function uploadLogo(file, view)
	{
		$('.pcw-printing-method').slideDown();

		var reader = new FileReader();
		reader.onload = function (e) 
		{
			var img = new Image();
			img.src = e.target.result;

			img.onload = function() 
			{
				tempLogos[view] = {
					file: file,
				};
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

				$logoWrapper.find('.control-icons').show();

				// Deslizar para o container da logo
				var $gallery = $('.woocommerce-product-gallery');
				var viewIndex = $(`.pcw_${view}`).index();
				$gallery.find('.flex-control-nav li').eq(viewIndex).find('img').trigger('click');
			};
		};
		reader.readAsDataURL(file);
	}

	// Check if the click was outside the logo container
	$(document).on('click', function (event)
	{
		var $target = $(event.target);

		if (!$target.closest('.pcw_logo_container').length && $('.pcw_logo_container').hasClass('active')) {
			$('.pcw_logo_container .control-icons').fadeOut();
			$('.pcw_logo_container').removeClass('active');
		}
	});

	$(document).on('click', '.pcw_logo_container', function ()
	{
		$(this).find('.control-icons').show();
		$(this).addClass('active');
	});

	$(document).on('click', '.delete-icon', function ()
	{
		var $logoWrapper = $(this).closest('.pcw_logo_container');
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
	});

	function resizeLogoWrapper(event, $wrapper, $canvas, direction)
	{
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

	function setupResizeInteraction(selector, direction) 
	{
		interact(selector).draggable
		({
			onstart: function (event)
			{
				var $wrapper = $(event.target).closest('.pcw_logo_container');
				var $canvas = $wrapper.find('.pcw_logo_canvas');
				var canvas = $canvas[0];

				var img = new Image();
				img.src = $canvas.data('image-url');
				$canvas.data('original-img', img);
				$canvas.data('original-width', canvas.width);
				$canvas.data('original-height', canvas.height);
			},
			onmove: function (event)
			{
				var $wrapper = $(event.target).closest('.pcw_logo_container');
				var $canvas = $wrapper.find('.pcw_logo_canvas');
				resizeLogoWrapper(event, $wrapper, $canvas, direction);
			}
		});
	}

	setupResizeInteraction('.resize-icon.bottom-right', 'bottom-right');
	setupResizeInteraction('.resize-icon.top-left', 'top-left');
	setupResizeInteraction('.resize-icon.bottom-left', 'bottom-left');

	interact('.pcw_logo_container').draggable
	({
		onmove: function (event)
		{
			var wrapper = $(event.target).closest('.pcw_logo_container');
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
			var wrapper = $(event.target).closest('.pcw_logo_container');
			var x = (parseFloat(wrapper.attr('data-x')) || 0) + event.dx;
			var y = (parseFloat(wrapper.attr('data-y')) || 0) + event.dy;

			wrapper.css({
				transform: 'translate(' + x + 'px, ' + y + 'px)'
			});

			wrapper.attr('data-x', x);
			wrapper.attr('data-y', y);
		}
	});

	// Obter o preço base e converter corretamente
	var basePriceText = $('.price .amount').first().text().trim();
	var basePrice = parseFloat(basePriceText.replace(/[^\d,]/g, '').replace(',', '.'));
	var additionalCost = 0;

	function updatePrice() 
	{
		additionalCost = 0;

		// Calcular custo adicional das opções de camadas ativas
		$('.pcw_option_color.active').each(function () {
			additionalCost += parseFloat($(this).closest('.pcw_option').data('option-cost') || 0);
		});

		// Calcular custo adicional dos métodos de impressão selecionados
		$('.pcw-printing-method').each(function () {
			if ($(this).val()) {
				additionalCost += parseFloat($(this).find('option:selected').data('cost') || 0);
			}
		});

		var newPrice = basePrice + additionalCost;
		var formattedPrice = newPrice.toFixed(2).replace('.', ',');

		// Atualizar o preço exibido
		$('.price .amount').text(pcw_ajax_object.currency_symbol + ' ' + formattedPrice);

		// Disparar um evento customizado com o novo preço
		$(document).trigger('pcw_price_updated', [newPrice, additionalCost]);
	}

	$(document).on('click', '.pcw_button_save_image', async function() 
	{
		const canvas_container = $(this).siblings('.canvas_container').get(0);

		await html2canvas(canvas_container)
		.then(function(canvas)
		{
			var image = canvas.toDataURL("image/png");
			var link = document.createElement('a');
			link.download = 'ry-product.png';
			link.href = image;
			link.click();
		})
		.catch(function(error) {
			console.error('Erro ao capturar imagem:', error);
		});
	});

	// Used by external hooks
	$(document).on('pcw_save_customizations', function() 
	{
		saveCustomizations();
	});

	async function saveCustomizations() 
	{
		$('.control-icons').hide();

		var customizations = {
			cost: additionalCost,
			color: {
				name: $('.pcw_color.active').attr('title'),
				value: $('.pcw_color.active').css('background-color')
			},
			layers: {},
			images: {},
			printing_logos: {},
			printing_methods: {
				front: $('#pcw_printing_method_front').val(),
				back: $('#pcw_printing_method_back').val()
			},
			notes: $('#pcw_notes').val()
		};

		$('.pcw_layer').each(function () {
			var layerId = $(this).data('layer-id');
			customizations.layers[layerId] = {
				options: {}
			};

			$(this).find('.pcw_option').each(function () {
				var optionId = $(this).data('option-id');
				var activeColor = $(this).find('.pcw_option_color.active');

				if (activeColor.length > 0) {
					var optionColorId = activeColor.data('option-color-id');
					customizations.layers[layerId].options[optionId] = {
						color: optionColorId
					};
				}
			});
		});

		var viewsToCapture = [];
		$('.woocommerce-product-gallery__image').each(function () 
		{
			if($(this).hasClass('pcw_front')) {
				viewsToCapture.push('front');
			}
			if($(this).hasClass('pcw_back')) {
				viewsToCapture.push('back');
			}
		});

		await Promise.all(viewsToCapture.map(async function (view) 
		{
			var container = document.querySelector(`#canvas_container_${view}`);
			container.offsetHeight; // Forçar um reflow para garantir que todas as mudanças foram aplicadas

			await html2canvas(container, 
			{
				useCORS: true,
				allowTaint: true,
				imageTimeout: 0,
			})
			.then(function(canvas) {
				customizations.images[view] = canvas.toDataURL('image/png');
			})
			.catch(function(error) {
				console.error('Erro ao capturar imagem:', error);
			});
		}));

		sendCustomizationsToServer(customizations);
	}

	function sendCustomizationsToServer(customizations) 
	{
		var product_id = $('input[name="variation_id"]').val() || $('input[name="product_id"]').val();

		var formData = new FormData();
		formData.append('action', 			'pcw_save_customizations');
		formData.append('nonce', 			pcw_ajax_object.nonce);
		formData.append('product_id', 		product_id);
		formData.append('customizations', 	JSON.stringify(customizations));

		if (tempLogos.front && tempLogos.front.file) {
			formData.append('logo_front', tempLogos.front.file);
		}
		if (tempLogos.back && tempLogos.back.file) {
			formData.append('logo_back', tempLogos.back.file);
		}

		$.ajax
		({
			url: pcw_ajax_object.url,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) 
			{
				if (response.success) {
					console.log(response.data);
				} else {
					console.error('Erro ao salvar customizações');
				}
			},
			error: function (error) {
				console.error('Erro Ajax:', error);
			}
		});

		$(document).trigger('pcw_customizations_updated', [customizations]);
	}
});