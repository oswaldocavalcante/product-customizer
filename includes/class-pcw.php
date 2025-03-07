<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://oswaldocavalcante.com
 * @since      1.0.0
 *
 * @package    Pcw
 * @subpackage Pcw/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Pcw
 * @subpackage Pcw/includes
 * @author     Oswaldo Cavalcante <contato@oswaldocavalcante.com>
 */
class Pcw
{
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Pcw_i18n. Defines internationalization functionality.
	 * - Pcw_Admin. Defines all hooks for the admin area.
	 * - Pcw_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pcw-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pcw-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pcw-public.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Pcw_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new Pcw_i18n();

		add_action('plugins_loaded', array($plugin_i18n, 'load_plugin_textdomain'));
	}

	private function define_admin_hooks()
	{
		$plugin_admin = new Pcw_Admin();

		add_action('before_woocommerce_init',       	array($plugin_admin, 'declare_wc_compatibility'));
		add_filter('woocommerce_integrations', 			array($plugin_admin, 'add_woocommerce_integration'));
		add_filter('woocommerce_product_data_tabs', 	array($plugin_admin, 'add_tab'));
		add_action('woocommerce_product_data_panels', 	array($plugin_admin, 'add_panel'));
		add_action('woocommerce_process_product_meta', 	array($plugin_admin, 'save'));
		
		add_action('wp_ajax_pcw_delete_color', 			array($plugin_admin, 'delete_color_callback'));
		add_action('wp_ajax_pcw_delete_printing_method',array($plugin_admin, 'delete_printing_method_callback'));
		add_action('wp_ajax_pcw_delete_layer', 			array($plugin_admin, 'delete_layer_callback'));
		add_action('wp_ajax_pcw_delete_option', 		array($plugin_admin, 'delete_option_callback'));
		add_action('wp_ajax_pcw_delete_option_color', 	array($plugin_admin, 'delete_option_color_callback'));
	}

	private function define_public_hooks()
	{
		$plugin_public = new Pcw_Public();

		add_action('woocommerce_init', 							array($plugin_public, 'session_start'), 	1);
		add_action('woocommerce_after_single_product', 			array($plugin_public, 'add_scripts')		);
		add_action('woocommerce_before_single_product_summary', array($plugin_public, 'render_background'), 5);
		add_action('woocommerce_single_product_summary', 		array($plugin_public, 'render_customizations'), 	40);

		add_action('wp_ajax_pcw_save_customizations', 			array($plugin_public, 'save_customizations'));
		add_action('wp_ajax_nopriv_pcw_save_customizations', 	array($plugin_public, 'save_customizations'));
	}
}
