<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://oswaldocavalcante.com
 * @since      1.0.0
 *
 * @package    Ryu
 * @subpackage Ryu/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ryu
 * @subpackage Ryu/public
 * @author     Oswaldo Cavalcante <contato@oswaldocavalcante.com>
 */
class Ryu_Public {

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
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ryu-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		
	}

	public function add_customizer()
	{
		$wc_product = wc_get_product(get_the_ID());

		if($wc_product->is_on_backorder()) {
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ryu-public.js', array('jquery', 'woocommerce'), $this->version, true);
		}
	}

	public function add_customizer_options()
	{
		echo '<div id="ryu_option">Golas</div>';
	}

}
