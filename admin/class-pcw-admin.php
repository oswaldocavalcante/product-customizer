<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://oswaldocavalcante.com
 * @since      1.0.0
 *
 * @package    Pcw
 * @subpackage Pcw/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pcw
 * @subpackage Pcw/admin
 * @author     Oswaldo Cavalcante <contato@oswaldocavalcante.com>
 */
class Pcw_Admin
{
	function add_tab($tabs)
	{
		$tabs['customization'] = array(
			'label'    => __('Customizations', 'pcw'),
			'target'   => 'pcw_metaboxes_wrapper',
			'class'    => array('show_if_simple', 'show_if_variable'),
			'priority' => 10, // Priority to appear between "Variations" and "Advanced"
		);

		return $tabs;
	}

	public function add_panel()
	{
		wp_enqueue_style('pcw-admin', plugin_dir_url(__FILE__) . 'css/pcw-admin.css', array(), PCW_VERSION, 'all');
		wp_enqueue_script('pcw-admin', plugin_dir_url(__FILE__) . 'js/pcw-admin.js', array('jquery'), PCW_VERSION, false);
		wp_localize_script('pcw-admin', 'pcw_ajax_object', array(
			'url'   => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('pcw_nonce'),
		));

?>
		<div id="pcw_metaboxes_wrapper" class="panel wc-metaboxes-wrapper">

			<div class="toolbar toolbar-top">
				<div id="message" class="inline notice woocommerce-message is-dismissible" style="display: none;">
					<p class="help">
						<span><?php esc_html_e('Add custom variations for this product.', 'pcw') ?></span>
						<button type="button" class="notice-dismiss"><span class="screen-reader-text">Esconder esta mensagem.</span></button>
					</p>
				</div>
				<span class="expand-close">
					<a href="#" class="expand_all"><?php esc_html_e('Expand', 'woocommerce'); ?></a> / <a href="#" class="close_all"><?php esc_html_e('Close', 'woocommerce'); ?></a>
				</span>
				<div class="actions">
					<input type="text" id="pwc_new_option_name" placeholder="<?php esc_html_e('Enter customization', 'pcw'); ?>" />
					<button type="button" id="pwc_button_add_layer" class="button"><?php esc_html_e('Add new', 'woocommerce'); ?></button>
				</div>
			</div>

			<div id="pcw-metaboxes" class="wc-metaboxes ui-sortable">

				<!-- Background -->
				<div id="pcw-metabox-background" class="wc-metabox closed">
					<h3>
						<div class="handlediv" aria-label="Click to toggle"><br></div>
						<strong><?php esc_html_e('Background', 'pcw'); ?></strong>
					</h3>
					<div class="wc-metabox-content hidden">
						<div class="woocommerce_variable_attributes">
							<?php $background = get_post_meta(get_the_ID(), 'pcw_background', true); ?>
							<input type="hidden" class="pcw_upload_image" name="pcw_background" value="<?php echo ($background ? $background : '') ?>" />
							<a class="pcw_button_upload_image upload_image_button tips <?php echo ($background ? 'remove' : '') ?>">
								<?php if ($background) : ?>
									<img src="<?php echo esc_url($background); ?>" id="pcw_background_image" class="pcw_uploaded_image" style="display: block" />
								<?php else: _e('Upload Image', 'pcw'); ?>
								<?php endif; ?>
							</a>
						</div>
					</div>
				</div>

				<!-- Colors -->
				<div id="pcw-metabox-colors" class="wc-metabox closed">
					<h3>
						<div class="handlediv" aria-label="Click to toggle"><br></div>
						<strong><?php esc_html_e('Colors', 'pcw'); ?></strong>
					</h3>
					<div class="wc-metabox-content hidden">
						<div class="pcw-metabox-content-toolbar">
							<div id="message" class="inline notice woocommerce-message is-dismissible" style="display: none;">
								<p class="help">
									<span><?php esc_attr_e('Add color options to the product image.', 'pcw'); ?></span>
									<button type="button" class="notice-dismiss"><span class="screen-reader-text">Esconder esta mensagem.</span></button>
								</p>
							</div>
							<div class="actions">
								<input type="text" id="pcw_new_color_value" placeholder="<?php esc_attr_e('Hexadecimal', 'pcw'); ?>: #FF0000" />
								<input type="text" id="pcw_new_color_name" placeholder="<?php esc_attr_e('Color name', 'pcw'); ?>: Red" />
								<button type="button" id="pcw_button_add_color" class="button"><?php esc_attr_e('Add color', 'pcw'); ?></button>
							</div>
						</div>
						<div id="pcw-metabox-content-colors">
							<?php

							$color_template_path = PCW_ABSPATH . 'admin/views/templates/color.php';

							$colors = get_post_meta(get_the_ID(), 'pcw_colors', true);
							if (!empty($colors) && is_array($colors))
							{
								foreach ($colors as $color)
								{
									$colorTemplate = str_replace(
										array('<%= id %>', '<%= colorName %>', '<%= colorValue %>'),
										array($color['id'], $color['name'], $color['value']),
										file_get_contents($color_template_path)
									);
									echo $colorTemplate;
								}
							}

							?>
						</div>
					</div>
					<script type="text/template" id="pcw_color_template">
						<?php include($color_template_path); ?>
					</script>
				</div>

				<!-- Layers -->
				<div id="pcw_metabox_layers">
					<?php

					$layer_template_path = PCW_ABSPATH . 'admin/views/templates/layer.php';
					$option_template_path = PCW_ABSPATH . 'admin/views/templates/option.php';
					$option_color_template_path = PCW_ABSPATH . 'admin/views/templates/option-color.php';

					$layers = get_post_meta(get_the_ID(), 'pcw_layers', true);
					if (!empty($layers) && is_array($layers))
					{

						foreach ($layers as $layer)
						{
							$optionsTemplate = '';
							foreach ($layer['options'] as $option)
							{
								$optionColors = '';
								foreach ($option['colors'] as $color)
								{
									$optionColors .= str_replace(
										array('<%= optionId %>', 	'<%= optionColorId %>', 	'<%= optionColorName %>', 	'<%= optionColorValue %>'),
										array($option['id'], 		$color['id'], 				$color['name'], 			$color['value']),
										file_get_contents($option_color_template_path)
									);
								}

								$optionsTemplate .= str_replace(
									array('<%= layerId %>', '<%= optionId %>', 	'<%= imageFront %>', 			'<%= imageBack %>', 		'<%= name %>', 		'<%= cost %>', 		'<%= optionColors %>'),
									array($layer['id'], 	$option['id'], 		$option['images']['front'], 	$option['images']['back'], 	$option['name'], 	$option['cost'], 	$optionColors),
									file_get_contents($option_template_path)
								);
							}

							$layerTemplate = str_replace(
								array('<%= layerId %>', '<%= layerName %>', 		'<%= layerOptions %>'),
								array($layer['id'], 	esc_html($layer['layer']), 	$optionsTemplate),
								file_get_contents($layer_template_path)
							);
							echo $layerTemplate;
						}
					}

					?>
					<script type="text/template" id="pcw_layer_template">
						<?php include($layer_template_path); ?>
					</script>
					<script type="text/template" id="pcw_option_template">
						<?php include($option_template_path); ?>
					</script>
					<script type="text/template" id="pcw_option_color_template">
						<?php include($option_color_template_path); ?>
					</script>
				</div>

			</div>

			<div class="toolbar toolbar-buttons">
				<button type="button" class="pcw_button_save button button-primary"><?php _e('Save customizations', 'pcw'); ?></button>
			</div>

		</div>
<?php
	}

	public function save($post_id)
	{
		// Verifica se é um autosave para evitar sobrescrever dados
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		{
			return;
		}

		if (!current_user_can('edit_post', $post_id))
		{
			return;
		}

		if ('product' !== get_post_type($post_id))
		{
			return;
		}

		if (isset($_POST['pcw_layer']))
		{
			$pcw_layers = array();

			foreach ($_POST['pcw_layer'] as $index_layer => $layer)
			{
				$pcw_options = array();

				foreach ($_POST['pcw_option'][$index_layer] as $index_option => $option_id)
				{
					$pcw_option_colors = array();

					foreach($_POST['pcw_option_color'][$option_id] as $index_option_color => $option_color_id)
					{
						$option_color_name = $_POST['pcw_option_color_name'][$option_id][$index_option_color];
						$option_color_value = $_POST['pcw_option_color_value'][$option_id][$index_option_color];

						if(!empty($option_color_value))
						{
							$pcw_option_colors[] = array(
								'id' => uniqid('option_color_', true),
								'name' => sanitize_text_field($option_color_name),
								'value' => sanitize_text_field($option_color_value)
							);
						}
					}

					$option_name = $_POST['pcw_option_name'][$index_layer][$index_option];
					$option_cost = $_POST['pcw_option_cost'][$index_layer][$index_option];
					$option_image_front = $_POST['pcw_option_image_front'][$index_layer][$index_option];
					$option_image_back = $_POST['pcw_option_image_back'][$index_layer][$index_option];

					if (!empty($option_name) || !empty($option_image_front) || !empty($option_image_back)) // Verifica se os campos principais não estão vazios antes de salvar
					{
						$pcw_options[] = array(
							'id' 	=> uniqid('option_', true),
							'name'  => sanitize_text_field($option_name),
							'cost'  => is_numeric($option_cost) ? $option_cost : 0,
							'images' => array(
								'front' => sanitize_text_field($option_image_front),
								'back'  => sanitize_text_field($option_image_back),
							),
							'colors' => $pcw_option_colors,
						);
					}
				}

				if (!empty($pcw_options))
				{
					$pcw_layers[] = array(
						'id' 		=> uniqid('layer_', true),
						'layer' 	=> sanitize_text_field($layer),
						'options' 	=> $pcw_options
					);
				}
			}

			if (!empty($pcw_layers))
			{
				$old_layers = get_post_meta($post_id, 'pcw_layers', true);
				if ($old_layers !== $pcw_layers)
				{
					update_post_meta($post_id, 'pcw_layers', $pcw_layers);
				}
			}
		}

		if (isset($_POST['pcw_color_name']) && isset($_POST['pcw_color_value']))
		{
			$pcw_colors = array();

			foreach ($_POST['pcw_color_name'] as $index => $color_name)
			{
				$pcw_colors[] = array(
					'id' 	=> uniqid('color_', true),
					'name' 	=> sanitize_text_field($color_name),
					'value' => sanitize_text_field($_POST['pcw_color_value'][$index]),
				);
			}

			// Salva as opções de cores como meta dados
			if (!empty($pcw_colors))
			{
				$old_colors = get_post_meta($post_id, 'pcw_colors', true);
				if ($old_colors !== $pcw_colors)
				{
					update_post_meta($post_id, 'pcw_colors', $pcw_colors);
				}
			}
		}

		if (isset($_POST['pcw_background']))
		{
			$pcw_background = sanitize_text_field($_POST['pcw_background']);

			// Salva o background como meta dados
			$old_background = get_post_meta($post_id, 'pcw_background', true);
			if ($old_background !== $pcw_background)
			{
				update_post_meta($post_id, 'pcw_background', $pcw_background);
			}
		}
	}

	function delete_option_callback()
	{
		// Verifica as permissões do usuário
		if (!current_user_can('edit_posts'))
		{
			wp_send_json_error('No permission');
			return;
		}

		// Verifica se o ID da opção foi passado
		if (isset($_POST['option_id']))
		{
			$post_id 	= sanitize_text_field($_POST['post_id']);
			$option_id 	= sanitize_text_field($_POST['option_id']);

			$pcw_layers = get_post_meta($post_id, 'pcw_layers', true); // Obter as camadas salvas
			if (!empty($pcw_layers) && is_array($pcw_layers))
			{
				foreach ($pcw_layers as &$layer)
				{
					// Filtrar as opções para remover a opção com o ID fornecido
					$layer['options'] = array_filter($layer['options'], function ($option) use ($option_id)
					{
						return $option['id'] != $option_id;
					});
				}
				update_post_meta($post_id, 'pcw_layers', $pcw_layers);
				wp_send_json_success('Option deleted');
			}
			else
			{
				wp_send_json_error('No layers found');
			}
		}
		else
		{
			wp_send_json_error('No option ID specified');
		}

		wp_die(); // Termina a execução do script
	}

	function delete_color_callback()
	{
		if (!current_user_can('edit_posts'))
		{
			wp_send_json_error('No permission');
			return;
		}

		if (isset($_POST['color_id']))
		{
			$post_id 	= sanitize_text_field($_POST['post_id']);
			$color_id 	= sanitize_text_field($_POST['color_id']);

			$pcw_colors = get_post_meta($post_id, 'pcw_colors', true); // Obter as camadas salvas
			if (!empty($pcw_colors) && is_array($pcw_colors))
			{

				$new_colors = array_filter($pcw_colors, function ($color) use ($color_id)
				{
					return $color['id'] != $color_id;
				});

				update_post_meta($post_id, 'pcw_colors', $new_colors);
				wp_send_json_success('Color deleted');
			}
			else
			{
				wp_send_json_error('No color found');
			}
		}
		else
		{
			wp_send_json_error('No color ID specified');
		}

		wp_die(); // Termina a execução do script
	}

	function delete_layer_callback()
	{
		if (!current_user_can('edit_posts'))
		{
			wp_send_json_error('No permission');
			return;
		}

		if (isset($_POST['layer_id']))
		{
			$post_id 	= sanitize_text_field($_POST['post_id']);
			$layer_id 	= sanitize_text_field($_POST['layer_id']);

			$pcw_layers = get_post_meta($post_id, 'pcw_layers', true); // Obter as camadas salvas
			if (!empty($pcw_layers) && is_array($pcw_layers))
			{

				$new_layers = array_filter($pcw_layers, function ($layer) use ($layer_id)
				{
					return $layer['id'] != $layer_id;
				});

				update_post_meta($post_id, 'pcw_layers', $new_layers);
				wp_send_json_success('Layer deleted');
			}
			else
			{
				wp_send_json_error('No layer found');
			}
		}
		else
		{
			wp_send_json_error('No layer ID specified');
		}

		wp_die();
	}
}
