<?php
/**
 * Plugin Name: Learndash NELC Integration
 * Version: 1.0.0
 * Plugin URI: https://wa.me/00201062332549
 * Description: The Learndash NELC Integration WordPress plugin is tailored to seamlessly connect with the National Center for E-Learning in Saudi Arabia. It facilitates the transmission of all learner activities, from course registration to certificate attainment, ensuring a smooth and efficient process.
 * Author: Mahmoud Hassan
 * Author URI: https://wa.me/00201062332549
 * Requires at least: 4.9
 * Tested up to: 6.3.1
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Text Domain: learndash-nelc-integration
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Mahmoud Hassan
 * @since 1.0.0
 */

use LearndashLrsPlugin\Interactions\lib\Learndash_NELC_integration_Admin_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin libraries.
require_once 'includes/lib/class-learndash-nelc-integration-admin-api.php';
require_once 'includes/lib/class-learndash-nelc-integration-statements.php';
require_once 'includes/lib/class-learndash-nelc-integration-interactions.php';
require_once 'includes\Interactions\LearndashXapiRequest.php';
require_once 'includes\Interactions\StatementConstants.php';

// Load plugin class files.
require_once 'includes/class-learndash-nelc-integration.php';
require_once 'includes/class-learndash-nelc-integration-settings.php';
require_once 'includes/learndash-nelc-integration-hooks.php';


/**
 * Returns the main instance of learndash_nelc_integration to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object learndash_nelc_integration
 */
function learndash_nelc_integration() {
	$instance = Learndash_NELC_integration::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Learndash_NELC_integration_Settings::instance( $instance );
	}

	return $instance;
}

learndash_nelc_integration();

// AJAX to handle notifications
add_action('wp_ajax_learndash_notify_action', 'learndash_notify_action_callback');
add_action('wp_ajax_nopriv_learndash_notify_action', 'learndash_notify_action_callback');
function learndash_notify_action_callback() {

	// Getting the current user's ID
	$uuid = get_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', true);
	// Checking the validity of the notification ID and sending JSON response
	if ( learndash_is_valid_uuid($uuid) ) {
		delete_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action');
		wp_send_json_success( __('The report has been sent to NELC', 'learndash-nelc-integration') );
	} else {
		delete_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action');
		wp_send_json_error( __('The report was not sent to NELC', 'learndash-nelc-integration') );
	}
}

// Checking UUID validity
function learndash_is_valid_uuid($uuid) {
    $uuid_pattern = '/^\["\w{8}-\w{4}-\w{4}-\w{4}-\w{12}"\]$/';

    return (bool) preg_match($uuid_pattern, $uuid);
}

// Adding a button to the footer
add_action('wp_footer', 'learndash_add_btn_to_footer');
function learndash_add_btn_to_footer()
{
	?><button id="learndash_notify_action_check" style="display: none;"></button><?php
}

// Setting the AJAX URL variable in the page header
add_action('wp_head', 'learndash_nelc_ajaxurl');
function learndash_nelc_ajaxurl() {

   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}

// Checking for new notifications
add_action('wp_footer', 'learndash_notify_action_check');
function learndash_notify_action_check() {
	$meta_data = get_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', true);
	if ( $meta_data) {

		?>
		<script>
			console.log('NELC: Notice found')
			send_notf = true;
			setInterval(() => {
				if( document.querySelector('#learndash_notify_action_check') && send_notf ){
					document.querySelector('#learndash_notify_action_check').click();
					send_notf = false;
				}
			}, 1000);
		</script>
		<?php
	}else{
		?><script>console.log('NELC: No notification found')</script><?php
	}

}