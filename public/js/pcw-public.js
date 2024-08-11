jQuery(document).ready(function ($) {

	$('#pcw_option').on('click', function () {
		if ($('.woocommerce-product-gallery__image').hasClass('flex-active-slide')) {
			$('.woocommerce-product-gallery__image.flex-active-slide').html('<canvas id="canvas" width="400" height="400"></canvas>');
			applyColorization('https://ryanne.local/wp-content/uploads/2023/08/Camisa-polo-400x400.png', '#FF0000');
		}
	});

	function hexToRgb(hex) {
		// Remove o caractere # se estiver presente
		hex = hex.replace(/^#/, '');

		// Converte o formato de três caracteres (#f00) para seis caracteres (#ff0000)
		if (hex.length === 3) {
			hex = hex.split('').map(char => char + char).join('');
		}

		// Converte para RGB
		const bigint = parseInt(hex, 16);
		const r = (bigint >> 16) & 255;
		const g = (bigint >> 8) & 255;
		const b = bigint & 255;

		return { r, g, b };
	}

	function applyColorization(imgSrc, hexColor) {
		const img = new Image();
		img.src = imgSrc;

		img.onload = function () {
			const canvas = document.getElementById('canvas');
			const ctx = canvas.getContext('2d');

			// Desenha a imagem no canvas
			ctx.drawImage(img, 0, 0);

			// Obtém os dados de pixels da imagem
			const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
			const data = imageData.data;

			// Converte a cor hexadecimal para RGB
			const color = hexToRgb(hexColor);

			// Itera sobre cada pixel e multiplica pelo valor RGB da cor
			for (let i = 0; i < data.length; i += 4) {
				if (data[i + 3] > 0) { // Se o pixel não for transparente
					data[i] = (data[i] * color.r) / 255;     // Multiplica o componente R
					data[i + 1] = (data[i + 1] * color.g) / 255; // Multiplica o componente G
					data[i + 2] = (data[i + 2] * color.b) / 255; // Multiplica o componente B
				}
			}

			// Aplica as alterações de volta no canvas
			ctx.putImageData(imageData, 0, 0);
		};
	}
	
});