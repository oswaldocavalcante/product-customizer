<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://oswaldocavalcante.com
 * @since      1.0.0
 *
 * @package    Pcw
 * @subpackage Pcw/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Pcw
 * @subpackage Pcw/public
 * @author     Oswaldo Cavalcante <contato@oswaldocavalcante.com>
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

	public function add_layers()
	{
		$layers = get_post_meta(get_the_ID(), 'pcw_layers', true);
		if (!empty($layers) && is_array($layers))
		{
			foreach ($layers as $layer)
			{
				
			}
		}
	}

	public function add_colors()
	{
		$colors = get_post_meta(get_the_ID(), 'pcw_colors', true);
		if (!empty($colors) && is_array($colors)) {
			echo '<a class="pcw_color" style="background-color: #FFFFFF"></a>';

			foreach ($colors as $color) {
				echo '<a class="pcw_color" style="background-color:' . $color['value'] . '"></a>';
			}
		}
	}

	public function add_background()
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
}
