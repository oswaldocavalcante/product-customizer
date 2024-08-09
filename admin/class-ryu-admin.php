<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://oswaldocavalcante.com
 * @since      1.0.0
 *
 * @package    Ryu
 * @subpackage Ryu/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ryu
 * @subpackage Ryu/admin
 * @author     Oswaldo Cavalcante <contato@oswaldocavalcante.com>
 */
class Ryu_Admin
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

	function add_customization_tab($tabs)
	{
		// Insert the new tab before the "Advanced" tab
		$tabs['customization'] = array(
			'label'    => __('Customizations', 'ryu'),
			'target'   => 'ryu_product_customizations',
			'class'    => array('show_if_simple', 'show_if_variable'),
			'priority' => 10, // Priority to appear between "Variations" and "Advanced"
		);

		return $tabs;
	}

	public function add_customization_panel()
	{
		wp_enqueue_script('ryu-admin-customization', plugin_dir_url(__FILE__) . 'js/ryu-admin-customization.js', array('jquery'), $this->version, false);
		wp_enqueue_style('ryu-admin-customization', plugin_dir_url(__FILE__) . 'css/ryu-admin-customization.css', array(), $this->version, 'all');

		?>
		<div id="ryu_product_customizations" class="panel wc-metaboxes-wrapper">
			<div class="toolbar toolbar-top">
				<div id="message" class="inline notice woocommerce-message is-dismissible" style="display: none;">
					<p class="help">
						Adicione as variações de personalização para este produto. <button type="button" class="notice-dismiss"><span class="screen-reader-text">Esconder esta mensagem.</span></button>
					</p>
				</div>
				<span class="expand-close">
					<a href="#" class="expand_all">Expandir</a> / <a href="#" class="close_all">Fechar</a>
				</span>
				<div class="actions">
					<input type="text" id="new_customization_name" placeholder="<?php _e('Enter customization', 'ryu'); ?>" />
					<button type="button" class="add_customization_option button"><?php _e('Add new', 'woocommerce'); ?></button>
				</div>
			</div>
			<div id="customization_options" class="wc-metaboxes ui-sortable">
				<?php
				// Aqui você pode carregar as opções salvas e criar uma div para cada uma
				$customizations = get_post_meta(get_the_ID(), '_customization_options', true);

				if (!empty($customizations) && is_array($customizations)) {
					foreach ($customizations as $key => $customization) {
				?>
						<div class="woocommerce_variation wc-metabox closed">
							<h3>
								<a href="#" class="remove_row delete"><?php _e('Remove', 'ryu'); ?></a>
								<div class="handlediv" aria-label="Click to toggle"><br></div>
								<strong><?php echo esc_html($customization['name']); ?></strong>
							</h3>
							<div class="options_group">
								<p class="form-field">
									<label for="customization_name_<?php echo $key; ?>"><?php _e('Option Name', 'ryu'); ?></label>
									<input type="text" class="option_name" name="customization_name[]" id="customization_name_<?php echo $key; ?>" value="<?php echo esc_attr($customization['name']); ?>" />

									<label for="customization_image_<?php echo $key; ?>"><?php _e('Option Image', 'ryu'); ?></label>
									<input type="hidden" class="option_image" name="customization_image[]" id="customization_image_<?php echo $key; ?>" value="<?php echo esc_attr($customization['image']); ?>" />
									<button type="button" class="upload_image_button button"><?php _e('Upload Image', 'ryu'); ?></button>
								<div class="image_preview">
									<?php if ($customization['image']) : ?>
										<img src="<?php echo esc_url($customization['image']); ?>" style="max-width: 100px;">
									<?php endif; ?>
								</div>
								</p>
							</div>
						</div>
				<?php
					}
				}
				?>
			</div>
			<div class="toolbar toolbar-buttons">
				<button type="button" class="save_customizations button button-primary"><?php _e('Save customizations', 'ryu'); ?></button>
			</div>

		</div>
		<?php
	}

	public function save_customization($post_id)
	{
		// Verifica se é um autosave para evitar sobrescrever dados
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		// Verifica as permissões do usuário
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}

		// Verifica o tipo de post
		if ('product' !== get_post_type($post_id)) {
			return;
		}

		// Verifica se a aba Customization foi preenchida
		if (isset($_POST['customization_name']) && isset($_POST['customization_image'])) {
			$customization_options = array();

			foreach ($_POST['customization_name'] as $index => $name) {
				$customization_options[] = array(
					'name'  => sanitize_text_field($name),
					'image' => sanitize_text_field($_POST['customization_image'][$index]),
				);
			}

			// Salva as opções de personalização como meta dados
			update_post_meta($post_id, '_customization_options', $customization_options);
		}
	}
}
