<?php
/**
 * Plugin Name: NELC Integration
 * Version: 1.0.2
 * Plugin URI: https://wa.me/00201062332549
 * Description: Lamoud NELC Integration wordprees plugin, It was launched specifically to link with the National Center for E-Learning in Saudi Arabia, so that the tool sends all the activities of the trainees, starting from registering for the course until obtaining the certificate.
 * Author: Mahmoud Hassan
 * Author URI: https://wa.me/00201062332549
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: lamoud-nelc-xapi
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Mahmoud Hassan
 * @since 1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin class files.
require_once 'includes/class-lamoud-nelc-xapi.php';
require_once 'includes/class-lamoud-nelc-xapi-settings.php';
//require_once 'includes/lamoud-nelc-xapi-integration-tutor-hooks.php';
require_once 'includes/lamoud-nelc-xapi-integration-learndash-hooks.php';

// Load plugin libraries.
require_once 'includes/lib/class-lamoud-nelc-xapi-admin-api.php';
require_once 'includes/lib/class-lamoud-nelc-xapi-post-type.php';
require_once 'includes/lib/class-lamoud-nelc-xapi-statements.php';
require_once 'includes/lib/class-lamoud-nelc-xapi-interactions.php';

/**
 * Returns the main instance of NELC_Integration to prevent the need to use globals.
 *
 * @since  1.0.2
 * @return object NELC_Integration
 */
function nelc_integration() {
	$instance = NELC_Integration::instance( __FILE__, '1.0.2' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = NELC_Integration_Settings::instance( $instance );
	}

	return $instance;
}

nelc_integration();

$plt_lang = str_contains(get_locale(), 'ar') ? 'ar-SA' : 'en-US';
$platform = get_option( 'lnx_xapi_platform' );
$platformAr = get_option( 'lnx_xapi_platform_ar_name' );
$platformEn = get_option( 'lnx_xapi_platform_en_name' );

add_action('wp_ajax_lamoud_notify_action', 'lamoud_notify_action_callback');
add_action('wp_ajax_nopriv_lamoud_notify_action', 'lamoud_notify_action_callback');

function lamoud_notify_action_callback() {

	$uuid = get_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', true);
    //$message = $meta_data;
	
	if ( is_valid_uuid($uuid) ) {
		delete_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action');
		wp_send_json_success( __('The report has been sent to NELC', 'lamoud-nelc-xapi') );
	} else {
		delete_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action');
		wp_send_json_error( __('The report was not sent to NELC', 'lamoud-nelc-xapi') );
	}

	//delete_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action');
}


function is_valid_uuid($uuid) {
    $uuid_pattern = '/^\["\w{8}-\w{4}-\w{4}-\w{4}-\w{12}"\]$/';

    return (bool) preg_match($uuid_pattern, $uuid);
}

function add_btn_to_footer()
{
	?><button id="lamoud_notify_action_check" style="display: none;"></button><?php
}
add_action('wp_footer', 'add_btn_to_footer');

add_action('wp_head', 'myplugin_ajaxurl');
function myplugin_ajaxurl() {

   echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}

add_action('wp_footer', 'lamoud_notify_action_check');
function lamoud_notify_action_check() {
	$meta_data = get_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', true);
	if ( $meta_data) {

		?>
		<script>
			console.log('NELC: Notice found')
			send_notf = true;
			setInterval(() => {
				if( document.querySelector('#lamoud_notify_action_check') && send_notf ){
					document.querySelector('#lamoud_notify_action_check').click();
					send_notf = false;
				}
			}, 1000);
		</script>
		<?php
	}else{
		?><script>console.log('NELC: No notification found')</script><?php
	}

}

