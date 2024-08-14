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

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	function add_tab($tabs)
	{
		// Insert the new tab before the "Advanced" tab
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
		wp_enqueue_script('pcw-admin-customization', plugin_dir_url(__FILE__) . 'js/pcw-admin-customization.js', array('jquery'), $this->version, false);
		wp_enqueue_style('pcw-admin-customization', plugin_dir_url(__FILE__) . 'css/pcw-admin-customization.css', array(), $this->version, 'all');

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
						<div class="data">
							<p class="upload_image">
								<input type="hidden" class="pcw_upload_image" name="pcw_background" id="pcw_background" />
								<a class="pcw_button_upload_image button">
									<?php
									$background = get_post_meta(get_the_ID(), 'pcw_background', true);

									if ($background) : ?>
										<img src="<?php echo esc_url($background); ?>" style="max-width: 100px;">
									<?php else: _e('Upload Image', 'pcw');
									endif; ?>
								</a>
							</p>
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
						<div class="toolbar">
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
										array('<%= colorName %>', '<%= colorValue %>'),
										array($color['name'], $color['value']),
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

					$option_template_path = PCW_ABSPATH . 'admin/views/templates/option.php';
					$layer_template_path = PCW_ABSPATH . 'admin/views/templates/layer.php';

					$layers = get_post_meta(get_the_ID(), 'pcw_layers', true);
					if (!empty($layers) && is_array($layers))
					{
						foreach ($layers as $layerIndex => $layer)
						{
							$optionsTemplate = '';
							$currentOption = '';
							$layerOptions = $layer['options'];
							foreach ($layerOptions as $option)
							{
								$currentOption = str_replace(
									array('<%= layerIndex %>', '<%= imageFront %>', '<%= imageBack %>', '<%= name %>', '<%= cost %>'),
									array($layerIndex, $option['image']['front'], $option['image']['back'], $option['name'], $option['cost']),
									file_get_contents($option_template_path)
								);
								$optionsTemplate .= $currentOption;
							}

							$layerTemplate = str_replace(
								array('<%= layerIndex %>', '<%= layerName %>', '<%= layerOptions %>'),
								array($layerIndex, esc_html($layer['layer']), $optionsTemplate),
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

		// Verifica as permissões do usuário
		if (!current_user_can('edit_post', $post_id))
		{
			return;
		}

		// Verifica o tipo de post
		if ('product' !== get_post_type($post_id))
		{
			return;
		}

		// Verifica se a aba Customization foi preenchida
		if (isset($_POST['pcw_layer']))
		{
			$pcw_layers = array();
			$pcw_options = array();

			foreach ($_POST['pcw_layer'] as $index_l => $layer)
			{
				foreach ($_POST['pcw_option_name'][$index_l] as $index => $name)
				{
					$pcw_options[] = array(
						'name'  => sanitize_text_field($_POST['pcw_option_name'][$index_l][$index]),
						'cost' 	=> sanitize_text_field($_POST['pcw_option_cost'][$index_l][$index]),
						'image' => array(
							'front' => sanitize_text_field($_POST['pcw_option_image_front'][$index_l][$index]),
							'back' 	=> sanitize_text_field($_POST['pcw_option_image_back'][$index_l][$index]),
						)
					);
				}

				$pcw_layers[] = array(
					'layer' => $layer,
					'options' => $pcw_options
				);

				$pcw_options = array();
			}

			// Salva as camadas de personalização como meta dados
			update_post_meta($post_id, 'pcw_layers', $pcw_layers);
		}

		if (isset($_POST['pcw_color_name']) && isset($_POST['pcw_color_value']))
		{
			$pcw_colors = array();

			foreach ($_POST['pcw_color_name'] as $index => $name)
			{
				$pcw_colors[] = array(
					'name' 	=> sanitize_text_field($name),
					'value' => sanitize_text_field($_POST['pcw_color_value'][$index]),
				);
			}

			// Salva as opções de cores como meta dados
			update_post_meta($post_id, 'pcw_colors', $pcw_colors);
		}

		if (isset($_POST['pcw_background']))
		{
			$pcw_background = $_POST['pcw_background'];

			// Salva o background como meta dados
			update_post_meta($post_id, 'pcw_background', $pcw_background);
		}
	}
}
