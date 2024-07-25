<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wa.me/00201062332549
 * @since      1.0.3
 *
 * @package    lamoud_nelc_xapi
 * @subpackage lamoud_nelc_xapi/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.3
 * @package    lamoud_nelc_xapi
 * @subpackage lamoud_nelc_xapi/includes
 * @author     Mahmoud Hassan <ing.moudy@gmail.com>
 */
class lamoud_nelc_xapi_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.3
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'lamoud-nelc-xapi',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/lang/'
		);

	}



}
