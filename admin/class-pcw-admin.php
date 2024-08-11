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
			'target'   => 'pcw_product_customizations',
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
		<div id="pcw_product_customizations" class="panel wc-metaboxes-wrapper">

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
					<input type="text" id="new_customization_name" placeholder="<?php _e('Enter customization', 'pcw'); ?>" />
					<button type="button" class="add_customization_option button"><?php _e('Add new', 'woocommerce'); ?></button>
				</div>
			</div>

			<div id="customization_options" class="wc-metaboxes ui-sortable">

				<?php include_once PCW_ABSPATH . 'admin/views/templates/customization-metabox.php' ?>

				<div class="woocommerce_variation wc-metabox closed">
					<h3>
						<div class="handlediv" aria-label="Click to toggle"><br></div>
						<strong><?php esc_html_e('Colors', 'pcw'); ?></strong>
					</h3>
					<div class="wc-metabox-content hidden">
						<div class="toolbar">
								<input type="text" placeholder="#FF0000" />
								<button type="button" class="button">Add color</button>
						</div>
						<div class="pwc_color_display"></div>
					</div>
				</div>

				<div class="woocommerce_variation wc-metabox closed">
					<h3>
						<div class="handlediv" aria-label="Click to toggle"><br></div>
						<strong><?php esc_html_e('Background', 'pcw'); ?></strong>
					</h3>
					<div class="wc-metabox-content hidden">
						<div class="data">
							Background
						</div>
					</div>
				</div>

			</div>

			<div class="toolbar toolbar-buttons">
				<button type="button" class="save_customizations button button-primary"><?php _e('Save customizations', 'pcw'); ?></button>
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
		if (isset($_POST['customization_name']) && isset($_POST['customization_image'])) {
			$customization_options = array();

			foreach ($_POST['customization_name'] as $index => $name) {
				$customization_options[] = array(
					'name'  => sanitize_text_field($name),
					'cost' 	=> sanitize_text_field($_POST['customization_cost'][$index]),
					'image' => sanitize_text_field($_POST['customization_image'][$index]),
				);
			}

			// Salva as opções de personalização como meta dados
			update_post_meta($post_id, '_customization_options', $customization_options);
		}
	}
}
