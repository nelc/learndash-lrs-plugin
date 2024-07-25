<?php

add_action('wp_ajax_ajax_check_if_profile_complete', 'ajax_check_if_profile_complete');
add_action('wp_ajax_nopriv_ajax_check_if_profile_complete', 'ajax_check_if_profile_complete');
function ajax_check_if_profile_complete(){
	global $current_user;


	$user_name = $current_user->first_name .' '. $current_user->last_name;

    if ( $current_user ) {

        $permission = get_user_meta( $current_user->ID, 'nelc_national_id' , true );
                
        if ( empty( $permission ) || empty( $user_name ) || $user_name == ' ' ) {
            wp_send_json_error( get_option('lnx_xapi_complete_profile') );
        }


    }else {
        wp_send_json_error( get_option('lnx_xapi_complete_profile') );
    }

    wp_send_json_success();
}

add_action('tutor_before_enroll', 'check_if_userprofile_complete');
function check_if_userprofile_complete(){
	global $current_user;


	$user_name = $current_user->first_name .' '. $current_user->last_name;

    if ( $current_user ) {

        $permission = get_user_meta( $current_user->ID, 'nelc_national_id' , true );
                
        if ( empty( $permission ) || empty( $user_name ) || $user_name == ' ' ) {
            wp_send_json_error( get_option('lnx_xapi_complete_profile') );
        }


    }else {
        wp_send_json_error();
    }

}

add_action('tutor_after_enroll', 'nelec_register_statemente_tutor');
function nelec_register_statemente_tutor ( $course_id )
{

    //wp_send_json_error();

    global $post;
    global $current_user;

    $plt_lang = str_contains(get_locale(), 'ar') ? 'ar-SA' : 'en-US';
    $platform = get_option( 'lnx_xapi_platform' );
	$platformAr = get_option( 'lnx_xapi_platform_ar_name' );
	$platformEn = get_option( 'lnx_xapi_platform_en_name' );

    // Get student info
    $user_email = $current_user->user_email;
    $user_name = $current_user->first_name .' '. $current_user->last_name;
    $ntd = get_user_meta( $current_user->ID, 'nelc_national_id' , true );

    // Get author info
    $author_id = $post->post_author;
	$author_info = get_userdata($author_id);

	$author_name = $author_info->first_name .' '. $author_info->last_name;
	$author_email = $author_info->user_email;

    // Get course info
    $course_title = get_the_title($course_id);
	$course_disc = wp_trim_words( get_post_field('post_content', $course_id), 50, NULL );

    $body = NELC_Integration()->register_statment( 'register', [
        'name' => strval($ntd),
        'email' => strval($user_email),
        'courseId' => strval($course_id),
        'lang' => strval($plt_lang),
        'courseName' => strval($course_title),
        'courseDesc' => strval($course_disc),
        'instructor' => strval($author_name),
        'inst_email' => strval($author_email),
        'pltForm' => strval($platform),
        'plat_arname' => strval($platformAr),
        'plat_enname' => strval($platformEn)
    ]);
    

    $response = NELC_Integration()->register_interactions( $body );
    print_r( '-------------------------------------------------' );
    echo '<br>';
    
    print_r( $response );
    



}
