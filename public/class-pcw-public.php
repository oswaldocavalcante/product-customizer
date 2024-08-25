<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Pcw
 * @subpackage Pcw/public
 * @author     Oswaldo Cavalcante <contato@oswaldocavalcante.com>
 * @link       https://oswaldocavalcante.com
 * @since      1.0.0
 */

class Pcw_Public
{
	public function add_script()
	{
		$wc_product = wc_get_product(get_the_ID());
		if ($wc_product->is_on_backorder())
		{
			wp_enqueue_script('interactjs', 'https://cdn.jsdelivr.net/npm/interactjs@1.10.11/dist/interact.min.js', array(), null, true);
			wp_enqueue_script('html2canvas', 'https://html2canvas.hertzen.com/dist/html2canvas.min.js', array(), '1.4.1', true);
			
			wp_enqueue_style('pcw', plugin_dir_url(__FILE__) . 'css/pcw-public.css', array(), PCW_VERSION, 'all');
			wp_enqueue_script('pcw', plugin_dir_url(__FILE__) . 'js/pcw-public.js', array('jquery', 'woocommerce', 'html2canvas'), PCW_VERSION, true);
			wp_localize_script('pcw', 'pcw_ajax_object', array(
				'url' 	=> admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('pcw_nonce'),
			));
		}
	}

	public function render_background()
	{
		$background = get_post_meta(get_the_ID(), 'pcw_background', true);
		if ($background)
		{
			?>
			<style>
				.flex-viewport {
					background-image: url('<?php echo esc_attr($background); ?>');
					background-size: cover;
					background-position: center;
				}
			</style>
			<?php
		}
	}

	public function render_colors()
	{
		$colors = get_post_meta(get_the_ID(), 'pcw_colors', true);
		if (!empty($colors) && is_array($colors))
		{
			$colors_html = '<a class="pcw_color" style="background-color:#FFFFFF"></a>';
			foreach ($colors as $color)
			{
				$colors_html .= sprintf('<a class="pcw_color" title="%s" style="background-color:%s"></a>', $color['name'], $color['value']);
			}
			echo '<div id="pcw_color_container">' . $colors_html . '</div>';
		}
	}

	public function render_layers()
	{
		$layers_data = get_post_meta(get_the_ID(), 'pcw_layers', true);
		if (!empty($layers_data) && is_array($layers_data))
		{
			$option_template_path = PCW_ABSPATH . 'public/views/templates/option.php';

			$layers_menu_items = '';
			$layers_html = '';
			foreach ($layers_data as $layer_data)
			{
				$layers_menu_items .= sprintf('<li class="pcw_layer_menu_item" data-layer-id="%s">%s</li>', $layer_data['id'], $layer_data['layer']);

				$options_html = '';
				foreach ($layer_data['options'] as $option_data)
				{

					$option_colors_html = '';
					foreach ($option_data['colors'] as $color_data)
					{
						$option_colors_html .= sprintf('<span class="pcw_option_color" title="%s" style="background-color: %s"></span>', $color_data['name'], $color_data['value']);
					}

					$options_html .= str_replace(
						array('<%= optionId %>',	'<%= optionName %>', 	'<%= optionCost %>', 	'<%= optionColors %>', 	'<%= imageFront %>', 				'<%= imageBack %>'),
						array($option_data['id'], 	$option_data['name'], 	$option_data['cost'], 	$option_colors_html, 	$option_data['images']['front'], 	$option_data['images']['back']),
						file_get_contents($option_template_path)
					);
				}

				$layers_html .= sprintf('<div class="pcw_layer" data-layer-id="%s">%s</div>', $layer_data['id'], $options_html);
			}
			$layers_menu = sprintf('<ul id="pcw_layers_menu">%s</ul>', $layers_menu_items);

			echo $layers_menu;
			echo '<div id="pcw_layers_container">' . $layers_html . '</div>';
		}

		// $product_id = get_the_ID();
		// $customizations = WC()->session->get("pcw_customizations_{$product_id}");

		// if (is_array($customizations) && isset($customizations['images']))
		// {
		// 	$front_image = $customizations['images']['front'] ?? null;
		// 	$back_image = $customizations['images']['back'] ?? null;

		// 	echo '<img src="' . $front_image . '" alt="Front Image">';
		// 	echo '<img src="' . $back_image . '" alt="Back Image">';
		// }
	}

	public function render_uploads()
	{
		?>
		<div id="pcw_uploads_container">
			<div class="pcw_upload_drop_area front" id="pcw_upload_front">
				<p><strong>Frente</strong> <br> Solte sua logo aqui ou</p>
				<label for="pcw_button_upload_front" class="pcw_button_upload"><?php _e('Enviar imagem', 'pcw'); ?></label>
				<input type="file" id="pcw_button_upload_front" class="pcw_upload_input" accept="image/png, image/svg+xml, application/pdf">
			</div>
			<div class="pcw_upload_drop_area back" id="pcw_upload_back">
				<p><strong>Costas</strong> <br> Solte sua arte aqui ou</p>
				<label for="pcw_button_upload_back" class="pcw_button_upload"><?php _e('Enviar imagem', 'pcw'); ?></label>
				<input type="file" id="pcw_button_upload_back" class="pcw_upload_input" accept="image/png, image/svg+xml, application/pdf">
			</div>
		</div>
		<?php
	}

	public function save_customizations()
	{
		if (!isset($_POST['product_id']) || !isset($_POST['customizations'])) {
			wp_send_json_error('Dados inválidos');
		}

		$product_id = intval($_POST['product_id']);
		$customizations = json_decode(stripslashes($_POST['customizations']), true);

		WC()->session->set("pcw_customizations_{$product_id}", $customizations);

		do_action('pcw_customizations_updated', $customizations, $product_id);
		wp_send_json_success('Customizações salvas com sucesso');
	}
}