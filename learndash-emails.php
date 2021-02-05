<?php
/**
* Plugin Name: Learndash Emails
* Plugin URI: https://mynameisgregg.com
* Description: This plugin provides the ability to send custom emails at triggered times
* Version: 1.0.0
* Author: Greggory Hogan
* Author URI: https://mynameisgregg.com/
**/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lde-activator.php
 */
function activate_lde() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lde-activator.php';
	Wp_Lde_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lde-deactivator.php
 */
function deactivate_lde() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lde-deactivator.php';
	Wp_Lde_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_lde' );
register_deactivation_hook( __FILE__, 'deactivate_lde' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-lde.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lde() {

	$plugin = new Wp_Lde();
	$plugin->run();

}
run_lde();