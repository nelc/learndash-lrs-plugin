<?php

/**
 * The public-facing functionality of the plugin.
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
class lamoud_nelc_xapi_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.3
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.3
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.3
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.3
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in lamoud_nelc_xapi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The lamoud_nelc_xapi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lamoud-nelc-xapi-public.css?'.$this->version, array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.3
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in lamoud_nelc_xapi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The lamoud_nelc_xapi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lamoud-nelc-xapi-public.js?'.$this->version, array(), $this->version, false );

	}

}
//include_once(ABSPATH . 'wp-includes/pluggable.php');
add_action('init', 'xapi_current_user_id');
function xapi_current_user_id(){

 $user_ID= get_current_user_id();   

   return $user_ID;
}

// Start if user registered (course)
function nelec_register_statemente_tutor($course_id){
	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/lamoud-nelc-xapi-config.php';
	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/interactions.php';
	$endpoint = get_option( 'xapi_endpoint' );
	$username = get_option( 'xapi_username' );
	$secret = get_option( 'xapi_secret' );
	$platform = get_option( 'xapi_platform' );
	$platformAr = get_option( 'xapi_platform_ar_name' );
	$platformEn = get_option( 'xapi_platform_en_name' );

	$headers = array (
		'Content-type'=> 'Application/json',
		'Authorization' => 'Basic ' . base64_encode( $username . ':' . $secret ),
	);


	global $post;
	$author_id = $post->post_author;
	$author_info = get_userdata($author_id);

	$user_id = xapi_current_user_id();
	$user_info = get_userdata($user_id);
  	$user_email = $user_info->user_email;
  	$ntd = get_the_author_meta( 'nelc_national_id', $user_id );

	$author_name = $author_info->first_name .' '. $author_info->last_name;
	$author_email = $author_info->user_email;

	$course_title = get_the_title($course_id);
	$course_disc = wp_trim_words( get_post_field('post_content', $course_id), 50, NULL );

	$plt_lang = str_contains(get_locale(), 'ar') ? 'ar-SA' : 'en-US';
	
	if(is_user_logged_in()){

		$registerNew = register_statement(
			strval($ntd), 
			strval($user_email), 
			strval($course_id),
			strval($plt_lang), 
			strval($course_title),
			strval($course_disc),
			strval($author_name),
			strval($author_email),
			strval($platform),
			strval($platformAr),
			strval($platformEn)
		);

		$jsonStm = json_encode($registerNew);
		$response = wp_remote_post( $endpoint, array (
			'method'  => 'POST',
			'headers' => $headers,
			'body'    =>  $jsonStm
		));
		update_user_meta( $user_id, "course_init_$course_id", 'no' );
		$res_code = $response['response']['code'];

		if($res_code == 200){
			?>
						<div class="lamoud-nelc-xapi-alert-container" style="width: 100%; margin: 0; min-height: 100vh; background: black; padding: 0; position: relative; direction: rtl;">
						<div class="nelc-xapi-box-info" style="width: 500px; max-width: 100%; background: #fff; position: absolute; top: 10%; left: 50%; transform: translate(-50%, 10%);">
							<h2 style="margin: 0; padding: 16px; text-align: center; background: #00bcd4; color: #fff; font-size: 24px;">
							<?php echo __('Delivery to NELC', 'lamoud-nelc-xapi'); ?>
							</h2>
				
							<p style="padding: 16px;">
								<span  style="color: blue"><?php echo __('Status: ', 'lamoud-nelc-xapi'); ?></span>
								<span><?php echo __('You have been registered in the course, and a report has been sent to the National Center NELC', 'lamoud-nelc-xapi'); ?></span>
							</p>
				
							<hr>
							<p style="padding: 0 16px; font-size: 12px; color: gray; margin: 0;">
								<span  style="color: black"><?php echo __('Status Code: ', 'lamoud-nelc-xapi'); ?></span>
								<span><?php echo $res_code; ?></span>
							</p>
							<p style="padding: 8px 16px; font-size: 12px; color: gray; margin: 0;">
								<span style="color: black"><?php echo __('Body: ', 'lamoud-nelc-xapi'); ?></span>
								<span><?php echo $response['body']; ?></span>
							</p>
				
							<div style="display: flex; align-items: center; justify-content: center; gap: 16px;">
								<span onclick="location.href=location.href" style="background: #00bcd4; padding: 8px 24px; margin: 16px; text-align: center; color: #fff; border-radius: 4px; cursor: pointer; }">
								<?php echo __('Continue', 'lamoud-nelc-xapi'); ?>
							</span>
							</div>
						</div>
					</div>
					<?php
					exit("");
		}else{
			?>
<div class="lamoud-nelc-xapi-alert-container" style="width: 100%; margin: 0; min-height: 100vh; background: black; padding: 0; position: relative; direction: rtl;">
<div class="nelc-xapi-box-info" style="width: 500px; max-width: 100%; background: #fff; position: absolute; top: 10%; left: 50%; transform: translate(-50%, 10%);">
	<h2 style="margin: 0; padding: 16px; text-align: center; background: #00bcd4; color: #fff; font-size: 24px;">
	<?php echo __('Delivery to NELC', 'lamoud-nelc-xapi'); ?>
	</h2>

	<p style="padding: 16px;">
		<span  style="color: blue"><?php echo __('Status: ', 'lamoud-nelc-xapi'); ?></span>
		<span><?php echo __('No report has been sent to NELC, please ensure your profile is complete or contact us immediately.', 'lamoud-nelc-xapi'); ?></span>
	</p>

	<hr>
	<p style="padding: 0 16px; font-size: 12px; color: gray; margin: 0;">
		<span  style="color: black"><?php echo __('Status Code: ', 'lamoud-nelc-xapi'); ?></span>
		<span><?php echo $res_code; ?></span>
	</p>
	<p style="padding: 8px 16px; font-size: 12px; color: gray; margin: 0;">
		<span style="color: black"><?php echo __('Body: ', 'lamoud-nelc-xapi'); ?></span>
		<span><?php echo $response['body']; ?></span>
	</p>

	<div style="display: flex; align-items: center; justify-content: center; gap: 16px;">
		<span onclick="location.href=location.href" style="background: #00bcd4; padding: 8px 24px; margin: 16px; text-align: center; color: #fff; border-radius: 4px; cursor: pointer; }">
		<?php echo __('Continue', 'lamoud-nelc-xapi'); ?>
	</span>
	</div>
</div>
</div>
<?php
exit("");
		}
	}
}
add_action('tutor_after_enroll', 'nelec_register_statemente_tutor');

// Start if user registered (course)initialize_statement
function nelec_initialize_statemente_tutor($courseid){
	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/lamoud-nelc-xapi-config.php';
	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/interactions.php';
	$endpoint = get_option( 'xapi_endpoint' );
	$username = get_option( 'xapi_username' );
	$secret = get_option( 'xapi_secret' );
	$platform = get_option( 'xapi_platform' );
	$platformAr = get_option( 'xapi_platform_ar_name' );
	$platformEn = get_option( 'xapi_platform_en_name' );

	$headers = array (
		'Content-type'=> 'Application/json',
		'Authorization' => 'Basic ' . base64_encode( $username . ':' . $secret ),
	);

	global $post;
	$course_id = $post->post_parent;
	$author_id = $post->post_author;
	$author_info = get_userdata($author_id);


	$user_id = xapi_current_user_id();
	$user_info = get_userdata($user_id);
  	$user_email = $user_info->user_email;
  	$ntd = get_the_author_meta( 'nelc_national_id', $user_id );

	$author_name = $author_info->first_name .' '. $author_info->last_name;
	$author_email = $author_info->user_email;

	$course_title = get_the_title($course_id);
	$course_disc = wp_trim_words( get_post_field('post_content', $course_id), 50, NULL );

	$plt_email = get_bloginfo('admin_email');
	$plt_lang = str_contains(get_locale(), 'ar') ? 'ar-SA' : 'en-US';


	$is_init = get_user_meta( $user_id, "course_init_$course_id", true );

	if($is_init && $is_init == 'yes'){
		//echo '<hr>';
		//update_user_meta( $user_id, "course_init_$course_id", 'no' );
		//exit("init: $is_init ..course: $course_title ..auth: $author_email ..lang: $plt_lang");
	}else{
		update_user_meta( $user_id, "course_init_$course_id", 'yes' );
		// echo 'gooooooooooooo: '.$post->post_parent;
		// echo '<hr>';
		// exit('inttttttttttttt');
		if(is_user_logged_in()){
	
			$registerNew = initialize_statement(
				strval($ntd), 
				strval($user_email), 
				strval($course_id),
				strval($plt_lang), 
				strval($course_title),
				strval($course_disc),
				strval($author_name),
				strval($author_email),
				strval($platform),
				strval($platformAr),
				strval($platformEn)
			);
				$jsonStm = json_encode($registerNew);
				$response = wp_remote_post( $endpoint, array (
					'method'  => 'POST',
					'headers' => $headers,
					'body'    =>  $jsonStm
				));
	
				$res_code = $response['response']['code'];
				//$res_msg = json_encode($response['response']['message']);
	
			if($res_code == 200){
				?>
				<script>alert("<?php echo __('The report was submitted to the NELC successfully', 'lamoud-nelc-xapi'); ?>");</script>
				<?php
			}else{
			}
	
		}
	}
//////////////////////////////////////////////////////

}
//add_action('tutor/course/started', 'nelec_initialize_statemente_tutor');
add_action('tutor/lesson_list/before/topic', 'nelec_initialize_statemente_tutor');

// start if user start leson

function after_user_end_leson_tutor($lesson_id){
	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/lamoud-nelc-xapi-config.php';
	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/interactions.php';
	$endpoint = get_option( 'xapi_endpoint' );
	$username = get_option( 'xapi_username' );
	$secret = get_option( 'xapi_secret' );
	$platform = get_option( 'xapi_platform' );
	$platformAr = get_option( 'xapi_platform_ar_name' );
	$platformEn = get_option( 'xapi_platform_en_name' );

	$headers = array (
		'Content-type'=> 'Application/json',
		'Authorization' => 'Basic ' . base64_encode( $username . ':' . $secret ),
	);

	global $post;

	$wp_lesone = get_post( $lesson_id );
	$lesone_title = $wp_lesone->post_title;
	$lesone_disc = wp_trim_words($wp_lesone->post_content, 50, NULL );

	$unit_Id = $wp_lesone->post_parent;
	$unit_title = get_post( $unit_Id )->post_title;

	//$course_Id = tutor_utils()->get_course_id_by_subcontent( $lesson_id );
	$course_Id = get_post( $unit_Id )->post_parent;

	$wp_course = get_post( $course_Id );
	$course_title = $wp_course->post_title;
	$course_disc = wp_trim_words($wp_course->post_content, 50, NULL );


	$author_id = $post->post_author;
	$author_info = get_userdata($author_id);
	$author_name = $author_info->first_name .' '. $author_info->last_name;
	$author_email = $author_info->user_email;

	$user_id = xapi_current_user_id();
	$user_info = get_userdata($user_id);
  	$user_email = $user_info->user_email;
  	$ntd = get_the_author_meta( 'nelc_national_id', $user_id );

	$plt_email = get_bloginfo('admin_email');
	$plt_lang = str_contains(get_locale(), 'ar') ? 'ar-SA' : 'en-US';

	$browser = get_browser(null, true);
	 $br_os = $browser["platform"];
	 $br_name = $browser["browser"];
	$br_ver = $browser['version'];
	
	//exit("you start leson: $lesson_id title: $lesone_title course: $course_Id course_title: $course_title Id Num:  $br_os");

	if(is_user_logged_in()){

		$registerNew = leson_statement(
			strval($ntd),
			strval($user_email),
			strval($course_Id),
			strval($plt_lang),
			strval($unit_Id),
			strval($lesson_id),
			strval($lesone_title),
			strval($lesone_disc),
			strval($br_os),
			strval($br_name),
			strval($br_ver),
			strval($course_title),
			strval($course_disc),
			strval($author_name),
			strval($author_email),
			strval($platform),
			strval($platformAr),
			strval($platformEn)
		);
		
		$jsonStm = json_encode($registerNew);
		$response = wp_remote_post( $endpoint, array (
			'method'  => 'POST',
			'headers' => $headers,
			'body'    =>  $jsonStm
		));

		

		$res_code = $response['response']['code'];
		// //$res_msg = json_encode($response['response']['message']);
		if($res_code == 200){
?>
<div class="lamoud-nelc-xapi-alert-container" style="width: 100%; margin: 0; min-height: 100vh; background: black; padding: 0; position: relative; direction: rtl;">
<div class="nelc-xapi-box-info" style="width: 500px; max-width: 100%; background: #fff; position: absolute; top: 10%; left: 50%; transform: translate(-50%, 10%);">
	<h2 style="margin: 0; padding: 16px; text-align: center; background: #00bcd4; color: #fff; font-size: 24px;">
	<?php echo __('Delivery to NELC', 'lamoud-nelc-xapi'); ?>
	</h2>

	<p style="padding: 16px;">
		<span  style="color: blue"><?php echo __('Status: ', 'lamoud-nelc-xapi'); ?></span>
		<span><?php echo __('The report was submitted to the NELC successfully', 'lamoud-nelc-xapi'); ?></span>
	</p>

	<hr>
	<p style="padding: 0 16px; font-size: 12px; color: gray; margin: 0;">
		<span  style="color: black"><?php echo __('Status Code: ', 'lamoud-nelc-xapi'); ?></span>
		<span><?php echo $res_code; ?></span>
	</p>
	<p style="padding: 8px 16px; font-size: 12px; color: gray; margin: 0;">
		<span style="color: black"><?php echo __('Body: ', 'lamoud-nelc-xapi'); ?></span>
		<span><?php echo $response['body']; ?></span>
	</p>

	<div style="display: flex; align-items: center; justify-content: center; gap: 16px;">
		<span onclick="location.href=location.href" style="background: #00bcd4; padding: 8px 24px; margin: 16px; text-align: center; color: #fff; border-radius: 4px; cursor: pointer; }">
		<?php echo __('Continue', 'lamoud-nelc-xapi'); ?>
	</span>
	</div>
</div>
</div>
<?php
exit(" ");
		}else{

?>
<div class="lamoud-nelc-xapi-alert-container" style="width: 100%; margin: 0; min-height: 100vh; background: black; padding: 0; position: relative; direction: rtl;">
<div class="nelc-xapi-box-info" style="width: 500px; max-width: 100%; background: #fff; position: absolute; top: 10%; left: 50%; transform: translate(-50%, 10%);">
	<h2 style="margin: 0; padding: 16px; text-align: center; background: #00bcd4; color: #fff; font-size: 24px;">
	<?php echo __('Delivery to NELC', 'lamoud-nelc-xapi'); ?>
	</h2>

	<p style="padding: 16px;">
		<span  style="color: blue"><?php echo __('Status: ', 'lamoud-nelc-xapi'); ?></span>
		<span><?php echo __('No report has been sent to NELC, please ensure your profile is complete or contact us immediately.', 'lamoud-nelc-xapi'); ?></span>
	</p>

	<hr>
	<p style="padding: 0 16px; font-size: 12px; color: gray; margin: 0;">
		<span  style="color: black"><?php echo __('Status Code: ', 'lamoud-nelc-xapi'); ?></span>
		<span><?php echo $res_code; ?></span>
	</p>
	<p style="padding: 8px 16px; font-size: 12px; color: gray; margin: 0;">
		<span style="color: black"><?php echo __('Body: ', 'lamoud-nelc-xapi'); ?></span>
		<span><?php echo $response['body']; ?></span>
	</p>

	<div style="display: flex; align-items: center; justify-content: center; gap: 16px;">
		<span onclick="location.href=location.href" style="background: #00bcd4; padding: 8px 24px; margin: 16px; text-align: center; color: #fff; border-radius: 4px; cursor: pointer; }">
		<?php echo __('Continue', 'lamoud-nelc-xapi'); ?>
	</span>
	</div>
</div>
</div>
<?php
exit(" ");
		}

		//exit('ffffff'.$res_code);
	}
}
add_action('tutor_lesson_completed_after', 'after_user_end_leson_tutor');
//add_action('tutor_lesson_completed_after', 'check_user_profile');

function after_quiz_finish($attempt_id){

	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/lamoud-nelc-xapi-config.php';
	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/interactions.php';
	$endpoint = get_option( 'xapi_endpoint' );
	$username = get_option( 'xapi_username' );
	$secret = get_option( 'xapi_secret' );
	$platform = get_option( 'xapi_platform' );
	$platformAr = get_option( 'xapi_platform_ar_name' );
	$platformEn = get_option( 'xapi_platform_en_name' );

	$headers = array (
		'Content-type'=> 'Application/json',
		'Authorization' => 'Basic ' . base64_encode( $username . ':' . $secret ),
	);

	global $post;

	$user_id = xapi_current_user_id();
	$user_info = get_userdata($user_id);
  	$user_email = $user_info->user_email;
  	$ntd = get_the_author_meta( 'nelc_national_id', $user_id );


	$attempt_data = tutor_utils()->get_attempt( $attempt_id );

	$quiz_id = tutor_utils()->avalue_dot( 'quiz_id', $attempt_data );

	$wp_quiz = get_post( $quiz_id );
	$quiz_title = $wp_quiz->post_title;
	$quiz_disc = wp_trim_words($wp_quiz->post_content, 50, NULL );

	function get_completed_mmm( int $course_id, int $student_id ): int {
		global $wpdb;
		$course_id  = sanitize_text_field( $course_id );
		$student_id = sanitize_text_field( $student_id );
		$count      = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT attempt_id) AS total
				FROM {$wpdb->prefix}tutor_quiz_attempts
				WHERE quiz_id = %d
				AND user_id = %d
				AND attempt_status = %s
			",
				$course_id,
				$student_id,
				'attempt_ended'
			)
		);
		return (int) $count;
	}

	//$attempt_all = tutor_utils()->get_all_quiz_attempts_by_user( $user_id );
	$attempt_all = get_completed_mmm($quiz_id, $user_id );

	$course_Id = tutor_utils()->avalue_dot( 'course_id', $attempt_data );
	$wp_course = get_post( $course_Id );
	$course_title = $wp_course->post_title;
	$course_disc = wp_trim_words($wp_course->post_content, 50, NULL );

	$author_id = $wp_course->post_author;
	$author_info = get_userdata($author_id);
	$author_name = $author_info->first_name .' '. $author_info->last_name;
	$author_email = $author_info->user_email;

	$plt_email = get_bloginfo('admin_email');
	$plt_lang = str_contains(get_locale(), 'ar') ? 'ar-SA' : 'en-US';

	$browser = get_browser(null, true);
	 $br_os = $browser["platform"];
	 $br_name = $browser["browser"];
	$br_ver = $browser['version'];

	$unit_Id = $wp_quiz->post_parent;
	$unit_title = get_post( $unit_Id )->post_title;
	$unit_disc =  wp_trim_words(get_post( $unit_Id )->post_content, 50, NULL );

	$max_scr = tutor_utils()->avalue_dot( 'total_marks', $attempt_data );
	$min_scr = tutor_utils()->avalue_dot( 'total_marks', $attempt_data );
	$scr_row = tutor_utils()->avalue_dot( 'earned_marks', $attempt_data );
	
	$total_quest = tutor_utils()->avalue_dot( 'total_questions', $attempt_data );
	$total_ans = tutor_utils()->avalue_dot( 'total_answered_questions', $attempt_data );
	
	
	$scr_scale = ($scr_row * 100) / ($max_scr * 100);
	$scr_info = tutor_utils()->avalue_dot( 'attempt_info', $attempt_data );
	$cr_arr = unserialize($scr_info);

	$min = 0;
	$succ = $cr_arr['passing_grade'] /100 <= $scr_scale ? true : false;
	$cmplt = $total_quest === $total_ans ? true : false;

	$attempt_num = $attempt_all;

	$comp_course = tutor_utils()->is_completed_course($course_Id);
	// //print_r($attempt_all);
	//  echo 'attempt_num: '. $attempt_num;
	//  echo '<hr>';
	// // print_r($attempt_data);
	//  print_r($comp_course);
	// // print_r($scr_info);
	//  echo '<hr>';

	// exit("complete: $cmplt...");

	if(is_user_logged_in()){

		$registerNew = attempt_statement(
			strval($ntd),
			strval($user_email),
			strval($course_Id),
			strval($plt_lang),
			strval($unit_Id),
			strval($quiz_id),
			strval($quiz_title),
			strval($quiz_disc),
			strval($br_os),
			strval($br_name),
			strval($br_ver),
			strval($course_title),
			strval($course_disc),
			strval($author_name),
			strval($author_email),
			strval($platform),
			$scr_scale,
			$scr_row,
			$min,
			$max_scr,
			$cmplt,
			$succ,
			$attempt_num,
			$platformAr,
			$platformEn
		);
		
		$jsonStm = json_encode($registerNew);
		$response = wp_remote_post( $endpoint, array (
			'method'  => 'POST',
			'headers' => $headers,
			'body'    =>  $jsonStm
		));

		$res_code = $response['response']['code'];
		// //$res_msg = json_encode($response['response']['message']);
		// print_r($response);
		// echo '<hr>';
	if(($attempt_num <= 1 && $succ) || ($succ)){
		$registerComp = complete_statement(
			strval($ntd),
			strval($user_email),
			strval($course_Id),
			strval($plt_lang),
			strval($unit_Id),
			strval($unit_title),			
			strval($unit_disc),
			strval($br_os),
			strval($br_name),
			strval($br_ver),
			strval($course_title),
			strval($course_disc),
			strval($author_name),
			strval($author_email),
			strval($platform),
			strval($platformAr),
			strval($platformEn)
		);
		
		$jsonStmComp = json_encode($registerComp);
		$responseComp = wp_remote_post( $endpoint, array (
			'method'  => 'POST',
			'headers' => $headers,
			'body'    =>  $jsonStmComp
		));

		$res_codeComp = $responseComp['response']['code'];

		$progCorse = tutor_utils()->get_course_completed_percent( $course_Id, $user_id );
		$compBool = $progCorse == 100 ? true : false;

		$registerProgress = progress_statement(
			strval($ntd),
			strval($user_email),
			strval($course_Id),
			strval($plt_lang),
			$progCorse/100,
			strval($course_title),
			strval($course_disc),
			strval($author_name),
			strval($author_email),
			$compBool,
			strval($platform),
			strval($platformAr),
			strval($platformEn)
		);

		$jsonStmProg = json_encode($registerProgress);
		$responseProg = wp_remote_post( $endpoint, array (
			'method'  => 'POST',
			'headers' => $headers,
			'body'    =>  $jsonStmProg
		));

		$res_codeProg = $responseProg['response']['code'];
		if($res_codeProg == 200 && $res_codeComp == 200){
			?>
			<div class="lamoud-nelc-xapi-alert-container" style="width: 100%; margin: 0; min-height: 100vh; background: black; padding: 0; position: relative; direction: rtl;">
			<div class="nelc-xapi-box-info" style="width: 500px; max-width: 100%; background: #fff; position: absolute; top: 10%; left: 50%; transform: translate(-50%, 10%);">
				<h2 style="margin: 0; padding: 16px; text-align: center; background: #00bcd4; color: #fff; font-size: 24px;">
				<?php echo __('Delivery to NELC', 'lamoud-nelc-xapi'); ?>
				</h2>
			
				<p style="padding: 16px;">
					<span  style="color: blue"><?php echo __('Status: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo __('Your course progress has been reported to NELC.', 'lamoud-nelc-xapi'); ?></span>
				</p>
			
				<hr>
				<p style="padding: 0 16px; font-size: 12px; color: gray; margin: 0;">
					<span  style="color: black"><?php echo __('Status Code: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo $res_codeProg; ?></span>
				</p>
				<p style="padding: 8px 16px; font-size: 12px; color: gray; margin: 0;">
					<span style="color: black"><?php echo __('Body: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo $response['body']; ?></span>
				</p>
			
				<div style="display: flex; align-items: center; justify-content: center; gap: 16px;">
					<span onclick="location.href=location.href" style="background: #00bcd4; padding: 8px 24px; margin: 16px; text-align: center; color: #fff; border-radius: 4px; cursor: pointer; }">
					<?php echo __('Continue', 'lamoud-nelc-xapi'); ?>
				</span>
				</div>
			</div>
			</div>
			<?php
			exit("");
		}else{
			?>
			<div class="lamoud-nelc-xapi-alert-container" style="width: 100%; margin: 0; min-height: 100vh; background: black; padding: 0; position: relative; direction: rtl;">
			<div class="nelc-xapi-box-info" style="width: 500px; max-width: 100%; background: #fff; position: absolute; top: 10%; left: 50%; transform: translate(-50%, 10%);">
				<h2 style="margin: 0; padding: 16px; text-align: center; background: #00bcd4; color: #fff; font-size: 24px;">
				<?php echo __('Delivery to NELC', 'lamoud-nelc-xapi'); ?>
				</h2>
			
				<p style="padding: 16px;">
					<span  style="color: blue"><?php echo __('Status: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo __('Your course progress has not been reported to NELC, please ensure that your profile is complete and contact us immediately.', 'lamoud-nelc-xapi'); ?></span>
				</p>
			
				<hr>
				<p style="padding: 0 16px; font-size: 12px; color: gray; margin: 0;">
					<span  style="color: black"><?php echo __('Status Code: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo $res_codeProg; ?></span>
				</p>
				<p style="padding: 8px 16px; font-size: 12px; color: gray; margin: 0;">
					<span style="color: black"><?php echo __('Body: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo $response['body']; ?></span>
				</p>
			
				<div style="display: flex; align-items: center; justify-content: center; gap: 16px;">
					<span onclick="location.href=location.href" style="background: #00bcd4; padding: 8px 24px; margin: 16px; text-align: center; color: #fff; border-radius: 4px; cursor: pointer; }">
					<?php echo __('Continue', 'lamoud-nelc-xapi'); ?>
				</span>
				</div>
			</div>
			</div>
			<?php
			exit("");
		}
	}else{
		if($res_code == 200){
			?>
			<div class="lamoud-nelc-xapi-alert-container" style="width: 100%; margin: 0; min-height: 100vh; background: black; padding: 0; position: relative; direction: rtl;">
			<div class="nelc-xapi-box-info" style="width: 500px; max-width: 100%; background: #fff; position: absolute; top: 10%; left: 50%; transform: translate(-50%, 10%);">
				<h2 style="margin: 0; padding: 16px; text-align: center; background: #00bcd4; color: #fff; font-size: 24px;">
				<?php echo __('Delivery to NELC', 'lamoud-nelc-xapi'); ?>
				</h2>
			
				<p style="padding: 16px;">
					<span  style="color: blue"><?php echo __('Status: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo __('Your quiz attempt has been reported to NELC.', 'lamoud-nelc-xapi'); ?></span>
				</p>
			
				<hr>
				<p style="padding: 0 16px; font-size: 12px; color: gray; margin: 0;">
					<span  style="color: black"><?php echo __('Status Code: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo $res_code; ?></span>
				</p>
				<p style="padding: 8px 16px; font-size: 12px; color: gray; margin: 0;">
					<span style="color: black"><?php echo __('Body: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo $response['body']; ?></span>
				</p>
			
				<div style="display: flex; align-items: center; justify-content: center; gap: 16px;">
					<span onclick="location.href=location.href" style="background: #00bcd4; padding: 8px 24px; margin: 16px; text-align: center; color: #fff; border-radius: 4px; cursor: pointer; }">
					<?php echo __('Continue', 'lamoud-nelc-xapi'); ?>
				</span>
				</div>
			</div>
			</div>
			<?php
			exit("");
		}else{
			?>
			<div class="lamoud-nelc-xapi-alert-container" style="width: 100%; margin: 0; min-height: 100vh; background: black; padding: 0; position: relative; direction: rtl;">
			<div class="nelc-xapi-box-info" style="width: 500px; max-width: 100%; background: #fff; position: absolute; top: 10%; left: 50%; transform: translate(-50%, 10%);">
				<h2 style="margin: 0; padding: 16px; text-align: center; background: #00bcd4; color: #fff; font-size: 24px;">
				<?php echo __('Delivery to NELC', 'lamoud-nelc-xapi'); ?>
				</h2>
			
				<p style="padding: 16px;">
					<span  style="color: blue"><?php echo __('Status: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo __('Your quiz attempt has not been reported to NELC, please ensure that your profile is complete and contact us immediately.', 'lamoud-nelc-xapi'); ?></span>
				</p>
			
				<hr>
				<p style="padding: 0 16px; font-size: 12px; color: gray; margin: 0;">
					<span  style="color: black"><?php echo __('Status Code: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo $res_code; ?></span>
				</p>
				<p style="padding: 8px 16px; font-size: 12px; color: gray; margin: 0;">
					<span style="color: black"><?php echo __('Body: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo $response['body']; ?></span>
				</p>
			
				<div style="display: flex; align-items: center; justify-content: center; gap: 16px;">
					<span onclick="location.href=location.href" style="background: #00bcd4; padding: 8px 24px; margin: 16px; text-align: center; color: #fff; border-radius: 4px; cursor: pointer; }">
					<?php echo __('Continue', 'lamoud-nelc-xapi'); ?>
				</span>
				</div>
			</div>
			</div>
			<?php
			exit("");
		}
	}

		//echo "لقد تقدمت في الدورة بنسبة $progCorse %";
		//echo '<hr>';

		//exit("$res_code ::: $res_codeComp");


	}
}
//add_action('tutor_quiz_before_finish', 'after_quiz_finish');
add_action('tutor_quiz/attempt_ended', 'after_quiz_finish');

// Start if user complete corse
function after_user_complete_course($course_id){
	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/lamoud-nelc-xapi-config.php';
	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/interactions.php';
	$endpoint = get_option( 'xapi_endpoint' );
	$username = get_option( 'xapi_username' );
	$secret = get_option( 'xapi_secret' );
	$platform = get_option( 'xapi_platform' );
	$platformAr = get_option( 'xapi_platform_ar_name' );
	$platformEn = get_option( 'xapi_platform_en_name' );

	$headers = array (
		'Content-type'=> 'Application/json',
		'Authorization' => 'Basic ' . base64_encode( $username . ':' . $secret ),
	);


	//global $post;
	$courseId = get_post( $course_id );
	$author_id = $courseId->post_author;
	$author_info = get_userdata($author_id);

	$user_id = xapi_current_user_id();
	$user_info = get_userdata($user_id);
  	$user_email = $user_info->user_email;
  	$ntd = get_the_author_meta( 'nelc_national_id', $user_id );

	$student_name = $user_info->first_name .'_'. $user_info->last_name;
	$author_name = $author_info->first_name .' '. $author_info->last_name;
	$author_email = $author_info->user_email;

	$course_title = get_the_title($course_id);
	$course_disc = wp_trim_words( get_post_field('post_content', $course_id), 50, NULL );

	$plt_lang = str_contains(get_locale(), 'ar') ? 'ar-SA' : 'en-US';

	$is_comp = tutor_utils()->is_completed_course( $course_id );
	$cert_name = 'certificate_'.$student_name.'_'.$course_title;
	
	if(is_user_logged_in()){

		$registerNew = completeCourse_statement(
			strval($ntd),
			strval($user_email), 
			strval($course_id),
			strval($plt_lang), 
			strval($course_title),
			strval($course_disc),
			strval($author_name),
			strval($author_email),
			strval($platform),
			strval($platformAr),
			strval($platformEn)
		);

		$jsonStm = json_encode($registerNew);
		$response = wp_remote_post( $endpoint, array (
			'method'  => 'POST',
			'headers' => $headers,
			'body'    =>  $jsonStm
		));

		if($is_comp){
			//$cert_url = $is_comp ? apply_filters( 'tutor_certificate_public_url', $is_comp->completed_hash ) : null;
			$cert_url2 = $is_comp ? esc_url(site_url("/?cert_hash=$is_comp->completed_hash")) : null;
			
			$registerCert = earnCertificate_statement(
				strval($ntd),
				strval($user_email),
				strval($course_id),
				strval($plt_lang),
				strval($cert_url2),
				strval($cert_name),
				strval($course_title),
				strval($course_disc),
				strval($platform),
				strval($platformAr),
				strval($platformEn)
			);

			$jsonReg = json_encode($registerCert);
			$responseReg = wp_remote_post( $endpoint, array (
				'method'  => 'POST',
				'headers' => $headers,
				'body'    =>  $jsonReg
			));
			//print_r($responseReg['response']);
			// //echo $is_comp;
			//echo '<hr>';
			// echo "cert2:  $cert_url2";
			// echo '<hr>';
			//exit("cert name:  $cert_name");
			$res_cert = $responseReg['response']['code'];

			if($res_cert == 200){
			}else{
				//exit('cert Not sent');
			}
		}else{
			//exit('not complete');
		}

		$res_code = $response['response']['code'];

		if($res_code == 200){
			//update_user_meta( $user_id, "course_init_$course_id", 'no' );
			?>	
			<div class="lamoud-nelc-xapi-alert-container" style="width: 100%; margin: 0; min-height: 100vh; background: black; padding: 0; position: relative; direction: rtl;">
			<div class="nelc-xapi-box-info" style="width: 500px; max-width: 100%; background: #fff; position: absolute; top: 10%; left: 50%; transform: translate(-50%, 10%);">
				<h2 style="margin: 0; padding: 16px; text-align: center; background: #00bcd4; color: #fff; font-size: 24px;">
				<?php echo __('Delivery to NELC', 'lamoud-nelc-xapi'); ?>
				</h2>
	
				<p style="padding: 16px;">
					<span  style="color: blue"><?php echo __('Status: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo __('You have completed the course, and a report has been sent to the National Center NELC', 'lamoud-nelc-xapi'); ?></span>
				</p>
	
				<hr>
				<p style="padding: 0 16px; font-size: 12px; color: gray; margin: 0;">
					<span  style="color: black"><?php echo __('Status Code: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo $res_code; ?></span>
				</p>
				<p style="padding: 8px 16px; font-size: 12px; color: gray; margin: 0;">
					<span style="color: black"><?php echo __('Body: ', 'lamoud-nelc-xapi'); ?></span>
					<span><?php echo $response['body']; ?></span>
				</p>
	
				<div style="display: flex; align-items: center; justify-content: center; gap: 16px;">
					<span onclick="location.href=location.href" style="background: #00bcd4; padding: 8px 24px; margin: 16px; text-align: center; color: #fff; border-radius: 4px; cursor: pointer; }">
					<?php echo __('Continue', 'lamoud-nelc-xapi'); ?>
				</span>
				</div>
			</div>
		</div>
		<?php
		exit("");
		}else{
			?>
			<div class="lamoud-nelc-xapi-alert-container" style="width: 100%; margin: 0; min-height: 100vh; background: black; padding: 0; position: relative; direction: rtl;">
<div class="nelc-xapi-box-info" style="width: 500px; max-width: 100%; background: #fff; position: absolute; top: 10%; left: 50%; transform: translate(-50%, 10%);">
	<h2 style="margin: 0; padding: 16px; text-align: center; background: #00bcd4; color: #fff; font-size: 24px;">
	<?php echo __('Delivery to NELC', 'lamoud-nelc-xapi'); ?>
	</h2>

	<p style="padding: 16px;">
		<span  style="color: blue"><?php echo __('Status: ', 'lamoud-nelc-xapi'); ?></span>
		<span><?php echo __('No report has been sent to NELC, please ensure your profile is complete or contact us immediately.', 'lamoud-nelc-xapi'); ?></span>
	</p>

	<hr>
	<p style="padding: 0 16px; font-size: 12px; color: gray; margin: 0;">
		<span  style="color: black"><?php echo __('Status Code: ', 'lamoud-nelc-xapi'); ?></span>
		<span><?php echo $res_code; ?></span>
	</p>
	<p style="padding: 8px 16px; font-size: 12px; color: gray; margin: 0;">
		<span style="color: black"><?php echo __('Body: ', 'lamoud-nelc-xapi'); ?></span>
		<span><?php echo $response['body']; ?></span>
	</p>

	<div style="display: flex; align-items: center; justify-content: center; gap: 16px;">
		<span onclick="location.href=location.href" style="background: #00bcd4; padding: 8px 24px; margin: 16px; text-align: center; color: #fff; border-radius: 4px; cursor: pointer; }">
		<?php echo __('Continue', 'lamoud-nelc-xapi'); ?>
	</span>
	</div>
</div>
</div>
<?php
exit("");
		}
	}

}
add_action('tutor_course_complete_after', 'after_user_complete_course');


function after_ratting($comment_id){
	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/lamoud-nelc-xapi-config.php';
	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/public/interactions.php';
	$endpoint = get_option( 'xapi_endpoint' );
	$username = get_option( 'xapi_username' );
	$secret = get_option( 'xapi_secret' );
	$platform = get_option( 'xapi_platform' );
	$platformAr = get_option( 'xapi_platform_ar_name' );
	$platformEn = get_option( 'xapi_platform_en_name' );

	$headers = array (
		'Content-type'=> 'Application/json',
		'Authorization' => 'Basic ' . base64_encode( $username . ':' . $secret ),
	);

		//global $post;
		$commentId = get_post( $comment_id );
		$comment_content = $commentId->post_content;
		$course_id = $commentId->post_parent;

		$courseId = get_post( $course_id );
		$author_id = $courseId->post_author;
		$author_info = get_userdata($author_id);
	
		$user_id = xapi_current_user_id();
		$user_info = get_userdata($user_id);
		$user_email = $user_info->user_email;
		$ntd = get_the_author_meta( 'nelc_national_id', $user_id );
	
		$author_name = $author_info->first_name .' '. $author_info->last_name;
		$author_email = $author_info->user_email;
	
		$course_title = get_the_title($courseId);
		$course_disc = wp_trim_words( get_post_field('post_content', $courseId), 50, NULL );
	
		$plt_lang = str_contains(get_locale(), 'ar') ? 'ar-SA' : 'en-US';

		$rate_info = tutor_utils()->get_course_rating_by_user($course_Id, $user_id);
		$rate_star = $rate_info->rating;
		$rate_comment = $rate_info->review;
	


		if(is_user_logged_in()){

			$registerNew = rate_statement(
				strval($ntd),
				strval($user_email), 
				strval($course_id),
				strval($plt_lang), 
				strval($course_title),
				strval($course_disc),
				strval($author_name),
				strval($author_email),
				strval($platform),
				$rate_star/5,
				$rate_star,
				0,
				5,
				strval($rate_comment),
				strval($platformAr),
				strval($platformEn),
			);
	
			$jsonStm = json_encode($registerNew);
			$response = wp_remote_post( $endpoint, array (
				'method'  => 'POST',
				'headers' => $headers,
				'body'    =>  $jsonStm
			));
	
			$res_code = $response['response']['code'];
	
			if($res_code == 200){
			}
	}
}
add_action('tutor_after_rating_placed', 'after_ratting');
/*////////////////////////////////////////////////////////////////////////////////////////////
do_action('tutor_course_complete_before', $course_id);
do_action('tutor_course_complete_after', $course_id);
do_action('tutor/course/started', $course_id);

do_action('tutor_lesson_completed_before', $lesson_id);
do_action('tutor_lesson_completed_after', $lesson_id);

do_action('tutor_mark_lesson_complete_before', $post_id, $user_id);
do_action('tutor_mark_lesson_complete_after', $post_id, $user_id);

do_action('tutor_course_complete_before', $course_id);
do_action('tutor_course_complete_after', $course_id);
do_action('tutor/course/started', $course_id);

do_action('tutor_quiz_before_finish', $attempt_id, $quiz_id, $attempt->user_id);

do_action('tutor_before_rating_placed');
do_action('tutor_after_rating_placed', $comment_id);
////////////////////////////////////////////////////////////////////////////////////////////////////*/
function check_user_profile(){
	global $post;

	$user_id = xapi_current_user_id();
	$user_info = get_userdata($user_id);
	$user_name = $user_info->first_name .' '. $user_info->last_name;

	$ntd = get_the_author_meta( 'nelc_national_id', $user_id );

	$location = '/?notomplete=true';

	if($ntd === '' || $ntd == 0 || $user_name === ''){
		header("Location: ./?account=notcomplete");
		die();
	}


}
add_action('tutor_before_enroll', 'check_user_profile');

add_action('tutor_profile_edit_input_before', 'test_add');
function test_add($user) {
	$user_id = xapi_current_user_id();
	?>

		<div class="tutor-form-row">
			<div class="tutor-form-col-6">
				<div class="tutor-form-group">
					<label><?php esc_html_e( 'National ID', 'lamoud-nelc-xapi' ); ?></label>
					<input type="text"
						id="nelc_national_id"
						name="nelc_national_id"
						value="<?php echo get_the_author_meta( 'nelc_national_id', $user_id ); ?>"
						class="input"
						required
					/>
				</div>
			</div>
		</div>
	
	<?php
	if(! empty( $_POST['nelc_national_id'] )){
		update_user_meta( $user_id, 'nelc_national_id', intval( $_POST['nelc_national_id'] ) );
	}
}
add_action( 'tutor_profile_update_after', 'my_profile_update', 10, 2 );
function my_profile_update( $user_id) {
	if(! empty( $_POST['nelc_national_id'] )){
		update_user_meta( $user_id, 'nelc_national_id', intval( $_POST['nelc_national_id'] ) );
	}
}


//////////////////////////////////////////////////////////////////
add_action( 'register_form', 'nelc_national_id_form' );
function nelc_national_id_form() {

	$nid = ! empty( $_POST['nelc_national_id'] ) ? intval( $_POST['nelc_national_id'] ) : '';

	?>
	<p>
		<label for="nelc_national_id"><?php esc_html_e( 'National ID', 'lamoud-nelc-xapi' ) ?><br/>
			<input type="number"
			       id="nelc_national_id"
			       name="nelc_national_id"
			       value="<?php echo esc_attr( $nid ); ?>"
			       class="input"
				   required
			/>
		</label>
	</p>
	<?php
}

add_filter( 'registration_errors', 'nelc_national_id_errors', 10, 3 );
function nelc_national_id_errors( $errors, $sanitized_user_login, $user_email ) {

	if ( empty( $_POST['nelc_national_id'] ) ) {
		$errors->add( 'nelc_national_id_error', __( 'Please enter your National ID.', 'lamoud-nelc-xapi' ) );
	}

	return $errors;
}

add_action( 'user_register', 'nelc_national_id_register' );
function nelc_national_id_register( $user_id ) {
	if ( ! empty( $_POST['nelc_national_id'] ) ) {
		update_user_meta( $user_id, 'nelc_national_id', intval( $_POST['nelc_national_id'] ) );
	}
}










add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) { ?>
    <table class="form-table">
    <tr>
    <th><label for="nelc_national_id"><?php esc_html_e( 'National ID', 'lamoud-nelc-xapi' ) ?></th>
        <td>
            <input type="number"
			       id="nelc_national_id"
			       name="nelc_national_id"
			       value="<?php echo esc_attr( get_the_author_meta( 'nelc_national_id', $user->ID ) ); ?>"
			       class="input"
				   required
			/>
			<p class="description"><?php _e("Please enter your national ID."); ?></p>
        </td>
    </tr>
    </table>
<?php }

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

function save_extra_user_profile_fields( $user_id ) {
    
	if( ! empty( $_POST['nelc_national_id'] )){
		update_user_meta( $user_id, 'nelc_national_id', $_POST['nelc_national_id'] );
	}
    
}