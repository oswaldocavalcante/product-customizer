<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://oswaldocavalcante.com
 * @since             1.0.0
 * @package           Pcw
 *
 * @wordpress-plugin
 * Plugin Name:       Product Customizer for WooCommerce
 * Plugin URI:        https://https://github.com/oswaldocavalcante/product-customizer
 * Description:       Customize products in layers for WooCommerce.
 * Version:           1.4.1
 * Author:            Oswaldo Cavalcante
 * Author URI:        https://oswaldocavalcante.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pcw
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 * Tested up to: 6.6.2
 * Requires PHP: 7.2
 * WC requires at least: 4.0
 * WC tested up to: 9.3.3
 */

// If this file is called directly, abort.
if (!defined( 'WPINC')) { die; }
if (!defined('PCW_PLUGIN_FILE')) { define('PCW_PLUGIN_FILE', __FILE__); }
define('PCW_ABSPATH', dirname(PCW_PLUGIN_FILE) . '/');
define('PCW_URL', plugins_url('/', __FILE__));
define( 'PCW_VERSION', '1.4.1' );

function activate_pcw() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pcw-activator.php';
	Pcw_Activator::activate();
}

function deactivate_pcw() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pcw-deactivator.php';
	Pcw_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pcw' );
register_deactivation_hook( __FILE__, 'deactivate_pcw' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pcw.php';

function run_pcw()
{
	$plugin = new Pcw();
}

run_pcw();
