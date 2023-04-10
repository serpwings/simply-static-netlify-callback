<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.yeswehack.com
 * @since             1.0.0
 * @package           Simply_Static_Callback
 *
 * @wordpress-plugin
 * Plugin Name:       Simply Static Callback
 * Plugin URI:        https://www.yeswehack.com
 * Description:       Send a callback to URI when Simply Static plugin generation is over
 * Version:           1.0.0
 * Author:            Arthur Bouchard
 * Author URI:        https://www.arthurbouchard.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simply-static-callback
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SIMPLY_STATIC_CALLBACK_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-simply-static-callback-activator.php
 */
function activate_simply_static_callback() {
    $plugin = plugin_basename( __FILE__ ); // 'SS callback'
    if ( Simply_Static_Callback::simplyStaticIsActivated() ) {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-simply-static-callback-activator.php';
        Simply_Static_Callback_Activator::activate();
    } else {
        // Plugin was not-active, uh oh, do not allow this plugin to activate
        deactivate_plugins( $plugin ); // Deactivate 'SS callback'
        wp_die('Sorry, but this plugin requires the Simply Static Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
    }
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-simply-static-callback-deactivator.php
 */
function deactivate_simply_static_callback() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simply-static-callback-deactivator.php';
	Simply_Static_Callback_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_simply_static_callback' );
register_deactivation_hook( __FILE__, 'deactivate_simply_static_callback' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-simply-static-callback.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_simply_static_callback() {
    if (!Simply_Static_Callback::simplyStaticIsActivated()) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate 'SS callback'
    }

    $plugin = new Simply_Static_Callback();

    $plugin->run();
}
// Ensure we "boot" our plugin before simply-static plugin boot (priority 10)
add_action('plugins_loaded', 'run_simply_static_callback', 5);
