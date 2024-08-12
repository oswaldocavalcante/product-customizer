jQuery(document).ready(function ($) {
	var productImages = $('.woocommerce-product-gallery__wrapper').children();

	$(document).on('click', '.pcw_color', function () {
		var backgroundColor = $(this).css('background-color');
		var hexColor = rgbToHex(backgroundColor);

		var productFront = productImages.eq(0);
		if (productFront.length) {
			render(productFront, hexColor, 'front');
		}

		var productBack = productImages.eq(1);
		if (productBack.length) {
			render(productBack, hexColor, 'back');
		}
	});

	function render(productImage, hexColor, view) {
		var width = productImage.css('width');
		var height = productImage.css('height');
		var imageURL = productImage.find('a').attr('href');

		// Verifica se já existe um canvas
		var existingCanvas = productImage.find(`#canvas_${view}`);

		// Cria um novo canvas se não existir
		if (existingCanvas.length === 0) {
			productImage.html(`<canvas id="canvas_${view}" width="${width}" height="${height}"><a href="${imageURL}"></a></canvas>`);
			existingCanvas = productImage.find(`#canvas_${view}`);
		}

		const canvas = existingCanvas[0];
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

	function rgbToHex(rgb) {
		let result = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		return result
			? "#" +
			("0" + parseInt(result[1], 10).toString(16)).slice(-2) +
			("0" + parseInt(result[2], 10).toString(16)).slice(-2) +
			("0" + parseInt(result[3], 10).toString(16)).slice(-2)
			: rgb;
	}

	function hexToRgb(hex) {
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
});