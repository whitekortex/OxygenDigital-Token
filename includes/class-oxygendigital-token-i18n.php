<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://oxygendigital.co.nz
 * @since      1.0.0
 *
 * @package    Oxygendigital_Token
 * @subpackage Oxygendigital_Token/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Oxygendigital_Token
 * @subpackage Oxygendigital_Token/includes
 * @author     Oxygen <Digital>
 */
class Oxygendigital_Token_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'oxygendigital-token',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
