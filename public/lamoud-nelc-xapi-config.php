<?php

/**
 * The config file  for NELC.
 *
 * @link       https://lamoud.com
 * @since      1.0.3
 *
 * @package    lamoud_nelc_xapi
 * @subpackage lamoud_nelc_xapi/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    lamoud_nelc_xapi
 * @subpackage lamoud_nelc_xapi/public
 * @author     Mahmoud Hassan <ing.moudy@gmail.com>
 */
$path = preg_replace( '/wp-content(?!.*wp-content).*/', '', __DIR__ );
require_once( $path . 'wp-load.php' );
require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/interactions.php';


$endpoint = get_option( 'xapi_endpoint' );
$username = get_option( 'xapi_username' );
$secret = get_option( 'xapi_secret' );
$platform = get_option( 'xapi_platform' );

$headers = array (
    'Content-type'=> 'Application/json',
    'Authorization' => 'Basic ' . base64_encode( $username . ':' . $secret ),
);

// function registerStatement($email, $name, $courseId, $courseName, $courseDesc, $instructor, $inst_email, $pltForm){
//     $statement =
//         array(
//             'actor' => array(
//                         'name' => $name,
//                         'mbox'  => 'mailto:'.$email,
//                         'objectType' => 'Agent',
//                     ),
//             'verb' => array(
//                         'id' => 'http://adlnet.gov/expapi/verbs/registered',
//                         'display' => array('en-US' => 'registered') 
//                     ),
//             'object' => array(
//                             'id'=> $courseId,
//                             'definition' => array(
//                                 'name' => array('en-US'=>$courseName),
//                                 'description' => array('en-US'=> $courseDesc),
//                                 'type' => 'https://w3id.org/xapi/cmi5/activitytype/course'
//                             ),
//                             'objectType' => 'Activity',
//                         ),
//             'context' => array(
//                             'instructor' => array(
//                                 'name' => $instructor,
//                                 'mbox' => 'mailto:'.$inst_email,
//                             ),
//                             'platform' => $pltForm,
//                             'language' => 'ar-SA'
//                         ),
//         );

//     return $statement;
// }

// $response = wp_remote_post( $endpoint, array (
//     'method'  => 'POST',
//     'headers' => $headers,
//     'body'    =>  $data
// ));

