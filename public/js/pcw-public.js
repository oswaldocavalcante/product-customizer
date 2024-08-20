jQuery(document).ready(function ($)
{
	var productWrapperFront = $('.woocommerce-product-gallery__wrapper').children().eq(0);
	var productWrapperBack = $('.woocommerce-product-gallery__wrapper').children().eq(1);

	setCanvas(productWrapperFront, 'front');
	setTimeout(() => setCanvas(productWrapperBack, 'back'), 100); // Timeout due to WooCommerce script processing for second image

	function setCanvas(productWrapper, view)
	{
		productWrapper.html(`
			<div id="pcw_logo_container_${view}" class="logo-wrapper" style="display: none">
				<canvas id="pcw_logo_canvas_${view}" class="pcw_logo_canvas"></canvas>
				<div class="control-icons">
					<div class="pcw_icon move-icon"></div>
					<div class="pcw_icon resize-icon"></div>
					<div class="pcw_icon delete-icon"></div>
				</div>
			</div>
			<div id="canvas_container_${view}">
				<canvas id="canvas_${view}" width="${productWrapper.width()}" height="${productWrapper.height()}" data-image-url="${productWrapper.find('a').attr('href')}"></canvas>
			</div>
		`);
		render(document.getElementById(`canvas_${view}`));
		productWrapper.find('a').hide(); // hide original product image
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

	// Quando uma logo é selecionada
	$('#pcw_button_logo_upload').on('change', function (event) {
		var file = event.target.files[0];
		if (file) {
			var reader = new FileReader();
			reader.onload = function (e) {
				var img = new Image();
				img.src = e.target.result;

				var $canvas = $('#pcw_logo_canvas_front');
				$canvas.data('image-url', img.src);
				$canvas.data('original-width', img.width);
				$canvas.data('original-height', img.height);
				
				$canvas.closest('.logo-wrapper').fadeIn();

				// Desenhar a imagem no canvas
				renderLogo($canvas, img);
			};
			reader.readAsDataURL(file);
		}
	});

	function renderLogo($canvas, img) {
		var canvas = $canvas[0];
		var ctx = canvas.getContext('2d');

		// Aguarde a imagem ser carregada para definir as dimensões do canvas
		img.onload = function () {
			// Defina as dimensões do canvas com base na imagem
			canvas.width = img.width;
			canvas.height = img.height;

			// Desenhe a imagem no canvas com as dimensões corretas
			ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
		};
	}

	$(document).on('click', function (event) {
		var $target = $(event.target);

		// Verifica se o clique foi fora do container da logo
		if (!$target.closest('.logo-wrapper').length) {
			// Esconde os ícones de controle
			$('.logo-wrapper .control-icons').fadeOut();
		}
	});

	$('.logo-wrapper').on('click', function () {
		$(this).find('.control-icons').show();
	});

	$(document).on('click', '.delete-icon', function () {
		// var $canvas = $(this).closest('.pcw_logo_canvas');
		var $logoWrapper = $(this).closest('.logo-wrapper');
		var canvas = $logoWrapper.find('canvas')[0];
		console.log(canvas);
		var ctx = canvas.getContext('2d');

		ctx.clearRect(0, 0, canvas.width, canvas.height);
	});

	interact('.logo-wrapper').draggable({
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

	interact('.move-icon').draggable({
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

	interact('.resize-icon').draggable({
		onmove: function (event) {
			var $wrapper = $(event.target).closest('.logo-wrapper');
			var $canvas = $wrapper.find('.pcw_logo_canvas');
			var canvas = $canvas[0];
			var ctx = canvas.getContext('2d');

			// Dimensões atuais do canvas
			var currentWidth = canvas.width;
			var currentHeight = canvas.height;

			// Dimensões originais da imagem
			var originalWidth = $canvas.data('original-width');
			var originalHeight = $canvas.data('original-height');

			// Calcula a nova largura e altura mantendo a proporção
			var newWidth = currentWidth + event.dx;
			var newHeight = (newWidth * originalHeight) / originalWidth;

			if (newWidth > 0 && newHeight > 0) {
				// Atualiza o tamanho do canvas
				canvas.width = newWidth;
				canvas.height = newHeight;

				// Redesenha a imagem no novo tamanho
				var img = new Image();
				img.src = $canvas.data('image-url');

				img.onload = function () {
					ctx.clearRect(0, 0, canvas.width, canvas.height);
					ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
				};
			}
		}
	});
});