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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function add_script()
	{
		$wc_product = wc_get_product(get_the_ID());
		if ($wc_product->is_on_backorder()) {
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/pcw-public.js', array('jquery', 'woocommerce'), $this->version, true);
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/pcw-public.css', array(), $this->version, 'all');
		}
	}

	public function render_background()
	{
		$background = get_post_meta(get_the_ID(), 'pcw_background', true);
		if ($background) {
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
				$colors_html .= sprintf('<a class="pcw_color" style="background-color:%s"></a>', $color['value']);
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
				$options_data = $layer_data['options'];
				foreach($options_data as $option_data)
				{
					$currentOption = str_replace(
						array('<%= optionId %>',	'<%= optionName %>', 	'<%= optionCost %>', 	'<%= optionColors %>', '<%= imageFront %>', 				'<%= imageBack %>'),
						array($option_data['id'], 	$option_data['name'], 	$option_data['cost'], 	'', 					$option_data['image']['front'], 	$option_data['image']['back']),
						file_get_contents($option_template_path)
					);
					$options_html .= $currentOption;
				}

				$layers_html .= sprintf('<div class="pcw_layer" data-layer-id="%s">%s</div>', $layer_data['id'], $options_html);
			}
			$layers_menu = sprintf('<ul id="pcw_layers_menu">%s</ul>', $layers_menu_items);

			echo $layers_menu;
			echo '<div id="pcw_layers_container">' . $layers_html . '</div>';
		}
	}
}
