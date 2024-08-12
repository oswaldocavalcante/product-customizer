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

	function add_customization_tab($tabs)
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

	public function add_customization_panel()
	{
		wp_enqueue_script('pcw-admin-customization', plugin_dir_url(__FILE__) . 'js/pcw-admin-customization.js', array('jquery'), $this->version, false);
		wp_enqueue_style('pcw-admin-customization', plugin_dir_url(__FILE__) . 'css/pcw-admin-customization.css', array(), $this->version, 'all');

?>
		<div id="pcw_metaboxes_wrapper" class="panel wc-metaboxes-wrapper">

			<div class="toolbar toolbar-top">
				<div id="message" class="inline notice woocommerce-message is-dismissible" style="display: none;">
					<p class="help">
						<span>Adicione as variações de personalização para este produto.</span>
						<button type="button" class="notice-dismiss"><span class="screen-reader-text">Esconder esta mensagem.</span></button>
					</p>
				</div>
				<span class="expand-close">
					<a href="#" class="expand_all">Expandir</a> / <a href="#" class="close_all">Fechar</a>
				</span>
				<div class="actions">
					<input type="text" id="pwc_new_option_name" placeholder="<?php _e('Enter customization', 'pcw'); ?>" />
					<button type="button" id="pwc_button_add_option" class="button"><?php _e('Add new', 'woocommerce'); ?></button>
				</div>
			</div>

			<div id="customization_options" class="wc-metaboxes ui-sortable">

				<!-- Added options -->
				<?php include_once PCW_ABSPATH . 'admin/views/templates/customization-metabox.php'; ?>

				<!-- Colors -->
				<div class="woocommerce_variation wc-metabox closed">
					<h3>
						<div class="handlediv" aria-label="Click to toggle"><br></div>
						<strong><?php esc_html_e('Colors', 'pcw'); ?></strong>
					</h3>
					<div class="wc-metabox-content hidden">
						<div class="toolbar">
							<div id="message" class="inline notice woocommerce-message is-dismissible" style="display: none;">
								<p class="help">
									<span>Adicione as variações de cor para a imagem principal do produto.</span>
									<button type="button" class="notice-dismiss"><span class="screen-reader-text">Esconder esta mensagem.</span></button>
								</p>
							</div>
							<div class="actions">
								<input type="text" id="pwc_new_color_value" placeholder="Hexadecimal color: #FF0000" />
								<input type="text" id="pwc_new_color_name" placeholder="Color name: Red velvet" />
								<button type="button" id="pwc_button_add_color" class="button">Add color</button>
							</div>
						</div>
						<div id="pwc_colors_container">
							<?php include_once PCW_ABSPATH . 'admin/views/templates/color-display.php'; ?>
						</div>
					</div>
				</div>

				<!-- Background -->
				<div class="woocommerce_variation wc-metabox closed">
					<h3>
						<div class="handlediv" aria-label="Click to toggle"><br></div>
						<strong><?php esc_html_e('Background', 'pcw'); ?></strong>
					</h3>
					<div class="wc-metabox-content hidden">
						<div class="data">
							<p class="upload_image">
								<input type="hidden" class="pcw_image" name="pcw_background" id="pcw_background" />
								<a class="pcw_button_upload_image button">
									<?php 
									$background = get_post_meta(get_the_ID(), 'pcw_background', true);

									if ($background) : ?>
										<img src="<?php echo esc_url($background); ?>" style="max-width: 100px;">
									<?php else: _e('Upload Image', 'pcw');
									endif; ?>
								</a>
							</p>
							<div class="image_preview"></div>
						</div>
					</div>
				</div>

			</div>

			<div class="toolbar toolbar-buttons">
				<button type="button" class="pcw_button_save_customizations button button-primary"><?php _e('Save customizations', 'pcw'); ?></button>
			</div>

		</div>
<?php
	}

	public function save_customizations($post_id)
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
		if (isset($_POST['pwc_option_name']) && isset($_POST['pwc_option_image']) && isset($_POST['pwc_option_cost'])) {
			$customization_options = array();

			foreach ($_POST['pwc_option_name'] as $index => $name) {
				$customization_options[] = array(
					'name'  => sanitize_text_field($name),
					'cost' 	=> sanitize_text_field($_POST['pwc_option_cost'][$index]),
					'image' => sanitize_text_field($_POST['pwc_option_image'][$index]),
				);
			}

			// Salva as opções de personalização como meta dados
			update_post_meta($post_id, 'pcw_options', $customization_options);
		}

		if (isset($_POST['pcw_color_name']) && isset($_POST['pcw_color_value'])) {
			$pcw_colors = array();

			foreach ($_POST['pcw_color_name'] as $index => $name) {
				$pcw_colors[] = array(
					'name' 	=> sanitize_text_field($name),
					'value' => sanitize_text_field($_POST['pcw_color_value'][$index]),
				);
			}

			// Salva as opções de personalização como meta dados
			update_post_meta($post_id, 'pcw_colors', $pcw_colors);
		}

		if (isset($_POST['pcw_background'])) {
			$pcw_background = $_POST['pcw_background'];

			update_post_meta($post_id, 'pcw_background', $pcw_background);
		}
	}
}
