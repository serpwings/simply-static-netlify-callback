<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       www.yeswehack.com
 * @since      1.0.0
 *
 * @package    Simply_Static_Callback
 * @subpackage Simply_Static_Callback/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Simply_Static_Callback
 * @subpackage Simply_Static_Callback/includes
 * @author     Arthur Bouchard <a.bouchard@yeswehack.com>
 */
class Simply_Static_Callback_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'simply-static-callback',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
