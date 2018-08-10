<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://laccadive.io/
 * @since      1.0.0
 *
 * @package    Wp_Medical_Records
 * @subpackage Wp_Medical_Records/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Medical_Records
 * @subpackage Wp_Medical_Records/includes
 * @author     Laccadive IO <muhammad@laccadive.io>
 */
class Wp_Medical_Records_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-medical-records',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
