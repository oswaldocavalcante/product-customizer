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
	public function session_start()
	{
		if (is_user_logged_in() || is_admin())
		{
			return;
		}
		if (isset(WC()->session))
		{
			if (!WC()->session->has_session())
			{
				WC()->session->set_customer_session_cookie(true);
			}
		}
	}

	public function add_script()
	{
		$wc_product = wc_get_product(get_the_ID());
		$layers_data = get_post_meta(get_the_ID(), 'pcw_layers', true);

		if ($wc_product->is_on_backorder() && !empty($layers_data) && is_array($layers_data))
		{
			wp_enqueue_script('interactjs', 'https://cdn.jsdelivr.net/npm/interactjs@1.10.11/dist/interact.min.js', array(), null, true);
			wp_enqueue_script('html2canvas', 'https://html2canvas.hertzen.com/dist/html2canvas.min.js', array(), '1.4.1', true);

			wp_enqueue_style('pcw', plugin_dir_url(__FILE__) . 'css/pcw-public.css', array(), PCW_VERSION, 'all');
			wp_enqueue_script('pcw', plugin_dir_url(__FILE__) . 'js/pcw-public.js', array('jquery', 'woocommerce', 'html2canvas'), PCW_VERSION, true);
			wp_localize_script('pcw', 'pcw_ajax_object', array(
				'url' 	=> admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('pcw_nonce'),
				'currency_symbol' => get_woocommerce_currency_symbol(),
			));
		}
	}

	public function render_background()
	{
		$background = get_post_meta(get_the_ID(), 'pcw_background', true);

		if ($background) :
			?>
			<style>
				.flex-viewport {
					background-image: url('<?php echo esc_attr($background); ?>');
					background-size: cover;
					background-position: center;
				}
			</style>
			<?php
		endif;
	}

	public function render_summary()
	{
		$this->render_colors();
		$this->render_layers();
		$this->render_uploads();
		$this->render_notes_field();
		$this->render_disclaimer();
	}

	public function render_colors()
	{
		$colors = get_post_meta(get_the_ID(), 'pcw_colors', true);

		if (!empty($colors) && is_array($colors))
		{
			$colors_html = '';
			foreach ($colors as $color)
			{
				$colors_html .= sprintf('<a class="pcw_color" title="%s" style="background-color:%s"></a>', 
				$color['name'], 
				$color['value']);
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
						$option_colors_html .= sprintf('<span class="pcw_option_color" data-option-color-id="%s" title="%s" style="background-color: %s"></span>', $color_data['id'], $color_data['name'], $color_data['value']);
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
	}

	public function render_uploads()
	{
		$printing_methods = get_post_meta(get_the_ID(), 'pcw_printing_methods', true);

		if (!empty($printing_methods) && is_array($printing_methods)) :
		
			$printing_methods_html = '';
			foreach ($printing_methods as $printing_method)
			{
				$printing_methods_html .= sprintf(
					'<option value="%s" data-cost="%s">%s %s</option>',
					esc_attr($printing_method['id']),
					esc_html($printing_method['cost']),
					esc_html($printing_method['name']),
					$printing_method['cost'] ? wc_price($printing_method['cost']) : ''
				);
			}
			?>
			<div id="pcw_uploads_container">
				<div class="pcw_upload_drop_area front" id="pcw_upload_front">
					<p><strong><?php _e('Front', 'pcw'); ?></strong> <br> <?php _e('Drop your logo here or', 'pcw'); ?></p>
					<label for="pcw_button_upload_front" class="pcw_button_upload"><?php _e('Send image', 'pcw'); ?></label>
					<input type="file" id="pcw_button_upload_front" class="pcw_upload_input" accept="image/*, application/pdf">
					<select id="pcw_printing_method_front" class="pcw-printing-method">
						<option value=""><?php _e('Select printing method', 'pcw'); ?></option>
						<?php echo $printing_methods_html; ?>
					</select>
				</div>
				<div class="pcw_upload_drop_area back" id="pcw_upload_back">
					<p><strong><?php _e('Back', 'pcw'); ?></strong> <br> <?php _e('Drop your art here or', 'pcw'); ?></p>
					<label for="pcw_button_upload_back" class="pcw_button_upload"><?php _e('Send image', 'pcw'); ?></label>
					<input type="file" id="pcw_button_upload_back" class="pcw_upload_input" accept="image/*, application/pdf">
					<select id="pcw_printing_method_back" class="pcw-printing-method">
						<option value=""><?php _e('Select printing method', 'pcw'); ?></option>
						<?php echo $printing_methods_html; ?>
					</select>
				</div>
			</div>
			<?php
		endif;
	}

	public function render_notes_field()
	{
		echo '<textarea id="pcw_notes" name="pcw_notes" placeholder="' . __('Write down notes to help us with your quote...', 'pcw') . '"></textarea>';
	}

	public function render_disclaimer()
	{
		$disclaimer = get_post_meta(get_the_ID(), 'pcw_disclaimer', true);
		
		if ($disclaimer)
		{
			echo '<p id="pcw_disclaimer">*' . $disclaimer . '</p>';
		}
	}

	public function save_customizations()
	{
		check_ajax_referer('pcw_nonce', 'nonce');

		$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
		$customizations = isset($_POST['customizations']) ? json_decode(stripslashes($_POST['customizations']), true) : array();

		$upload_overrides = array(
			'test_form' => false,
			'mimes' 	=> array
			(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'png'          => 'image/png',
				'gif'          => 'image/gif',
				'pdf'          => 'application/pdf',
			)
		);

		if (isset($_FILES['logo_front']))
		{
			$movefile = wp_handle_upload($_FILES['logo_front'], $upload_overrides);
			if ($movefile && !isset($movefile['error'])) {
				$customizations['printing_logos']['front'] = $movefile['url'];
			}
			else {
				error_log('Error uploading front logo: ' . $movefile['error']);
			}
		}

		if (isset($_FILES['logo_back']))
		{
			$movefile = wp_handle_upload($_FILES['logo_back'], $upload_overrides);
			if ($movefile && !isset($movefile['error'])) {
				$customizations['printing_logos']['back'] = $movefile['url'];
			}
			else {
				error_log('Error uploading back logo: ' . $movefile['error']);
			}
		}

		WC()->session->set("pcw_customizations_{$product_id}", $customizations);

		wp_send_json_success(__('Customizations saved successfully', 'pcw'));
	}
}
