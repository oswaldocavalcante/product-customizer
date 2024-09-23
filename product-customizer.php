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
 * Plugin Name:       Product Customizer
 * Plugin URI:        https://https://github.com/oswaldocavalcante/product-customizer
 * Description:       Customize products in layers for WooCommerce.
 * Version:           1.3.1
 * Author:            Oswaldo Cavalcante
 * Author URI:        https://oswaldocavalcante.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pcw
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if (!defined('PCW_PLUGIN_FILE')) {
	define('PCW_PLUGIN_FILE', __FILE__);
}

define('PCW_ABSPATH', dirname(PCW_PLUGIN_FILE) . '/');
define('PCW_URL', plugins_url('/', __FILE__));

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PCW_VERSION', '1.3.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pcw-activator.php
 */
function activate_pcw() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pcw-activator.php';
	Pcw_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pcw-deactivator.php
 */
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

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pcw()
{
	$plugin = new Pcw();
}
run_pcw();
