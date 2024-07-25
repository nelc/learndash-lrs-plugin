<?php
add_action( 'learndash_update_user_activity', 'course_started_hook', 10, 3 );
function course_started_hook( $args )
{
    $course_id = $args['course_id'];
    $post_id = $args['post_id'];
    $user_id = $args['user_id'];
    $activity_type = $args['activity_type'];
    $activity_action = $args['activity_action'];
    $activity_meta = $args['activity_meta'];
    $started = learndash_activity_course_get_earliest_started($user_id, $course_id);

    $user = wp_get_current_user();
    $course = get_post( $course_id );
    $author  = get_userdata($course->post_author);
	
    if( $course_id == $post_id && !$started && $activity_type == 'access' && $activity_action == 'insert' ){
        $body = NELC_Integration()->register_statment( 'registered', [
            'name' => "$user->display_name",
            'email' => "$user->user_email",
            'courseId' => "$course->ID",
            'courseName' => "$course->post_title",
            'courseDesc' =>  strip_tags($course->post_content),    
            'instructor' => "$author->display_name",
            'inst_email' => "$author->user_email",
        ]);
        
        $response = NELC_Integration()->register_interactions( $body );
        if (is_wp_error($response)) {
            update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
        }else{
            update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
        }
		
    }

    if( $course_id === $post_id && !$started && $activity_type == 'course' && $activity_action == 'insert' ){	
		
        $body = NELC_Integration()->register_statment( 'initialized', [
            'name' => "$user->display_name",
            'email' => "$user->user_email",
            'courseId' => "$course->ID",
            'courseName' => "$course->post_title",
            'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,    
            'instructor' => "$author->display_name",
            'inst_email' => "$author->user_email",
        ]);
        
        $response = NELC_Integration()->register_interactions( $body );
        if (is_wp_error($response)) {
            update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
        }else{
            update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
        }

    }
}

//add_action( 'learndash_lesson_completed', 'lesson_completed_hook', 10, 3 );
function lesson_completed_hook( $lesson_data )
{

    $user = $lesson_data['user']->data;
    $course = $lesson_data['course'];
    $lesson = $lesson_data['lesson'];
    $progress = $lesson_data['progress'];
    $author  = get_userdata($lesson_data['course']->post_author);
    $percentage = ($progress['completed'] / $progress['total']);

    $body1 = NELC_Integration()->register_statment( 'completed', [
        'name' => "$user->display_name",
        'email' => "$user->user_email",
        'lessonUrl'=> "$lesson->guid",
        'lessonName'=> "$lesson->post_title",
        'lessonDesc'=> strip_tags($lesson->post_content),
        'instructor' => "$author->display_name",
        'inst_email' => "$author->user_email",
        'courseId' => "$course->ID",
        'courseName' => "$course->post_title",
        'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,
    ]);
    $response1 = NELC_Integration()->register_interactions( $body1 );

    $body = NELC_Integration()->register_statment( 'progressed', [
        'name' => "$user->display_name",
        'email' => "$user->user_email",
        'courseId' => "$course->ID",
        'courseName' => "$course->post_title",
        'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,
        'instructor' => "$author->display_name",
        'inst_email' => "$author->user_email",
        'scaled' => round($percentage, 2),
        'completion' => $progress['completed'] === 'in_progress' ? false : true,
    ]);
    $response = NELC_Integration()->register_interactions( $body );

    if (is_wp_error($response1)) {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
    }else{
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response1['body']);
    }

    if (is_wp_error($response)) {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
    }else{
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
    }
    
}

add_action( 'learndash_topic_completed', 'topic_completed_hook', 10, 3 );
function topic_completed_hook( $lesson_data )
{

    $user = $lesson_data['user']->data;
    $course = $lesson_data['course'];
    $lesson = $lesson_data['lesson'];
    $progress = $lesson_data['progress'];
    $author  = get_userdata($lesson_data['course']->post_author);
    $percentage = ($progress['completed'] / $progress['total']);

    $body1 = NELC_Integration()->register_statment( 'completed', [
        'name' => "$user->display_name",
        'email' => "$user->user_email",
        'lessonUrl'=> "$lesson->guid",
        'lessonName'=> "$lesson->post_title",
        'lessonDesc'=> strip_tags($lesson->post_content),
        'instructor' => "$author->display_name",
        'inst_email' => "$author->user_email",
        'courseId' => "$course->ID",
        'courseName' => "$course->post_title",
        'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,
    ]);
    $response1 = NELC_Integration()->register_interactions( $body1 );

    $body = NELC_Integration()->register_statment( 'progressed', [
        'name' => "$user->display_name",
        'email' => "$user->user_email",
        'courseId' => "$course->ID",
        'courseName' => "$course->post_title",
        'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,
        'instructor' => "$author->display_name",
        'inst_email' => "$author->user_email",
        'scaled' => round($percentage, 2),
        'completion' => $progress['completed'] === 'in_progress' ? false : true,
    ]);
    $response = NELC_Integration()->register_interactions( $body );

    if (is_wp_error($response1)) {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
    }else{
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response1['body']);
    }

    if (is_wp_error($response)) {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
    }else{
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
    }
	
// 	echo "</br>";
// 	echo "lesson_completed";
// 	echo "<pre>";
// 	print_r($response1['body']);
// 	echo "</pre>";
// 	echo "</br>";
// 	echo "=========================================================";
// 	echo "</br>";
// 	echo "progressed";
// 	echo "</br>";
// 	echo "<pre>";
// 	print_r($response['body']);
// 	echo "</pre>";
// 	exit;
    
}

add_action( 'learndash_course_completed', 'course_completed_hook', 10, 3 );
function course_completed_hook( $args = array() )
{
    /*
    $args = array(
    'user' => $current_user,
    'course' => get_post( $course_id ),
    'progress' => $course_progress,
    )
    */
    $user = $args['user'];
    $course = $args['course'];
    $author  = get_userdata($course->post_author);
    $progress = $args['progress'];

    $certificate_link = learndash_get_course_certificate_link($course->ID, $user->ID);

    $body = NELC_Integration()->register_statment( 'completedCourse', [
        'name' => "$user->display_name",
        'email' => "$user->user_email",
        'courseId' => "$course->ID",
        'courseName' => "$course->post_title",
        'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,
        'instructor' => "$author->display_name",
        'inst_email' => "$author->user_email",
    ]);
    
    $response = NELC_Integration()->register_interactions( $body );

    $body1 = NELC_Integration()->register_statment( 'earned', [
        'name' => "$user->display_name",
        'email' => "$user->user_email",
        'certUrl' => "$certificate_link",
        'certName' => "$certificate_link",
        'courseId' => "$course->ID",
        'courseName' => "$course->post_title",
        'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,
    ]);
	
    //update_user_meta(get_current_user_id(), 'notf_notf', $certificate_link);
	
    $response1 = NELC_Integration()->register_interactions( $body1 );

    if (is_wp_error($response)) {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
    }else{
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
    }
}


add_action('wp_insert_comment','course_rated_hook', 100, 3);
function course_rated_hook($comment_id, $comment_object) {

    if ( $comment_object->comment_type == 'ld_review' ) {
        
        $user = wp_get_current_user();
        $course = get_post( $comment_object->comment_post_ID );
        $author  = get_userdata($course->post_author);
        $saved_rating =  $_POST['rating'] ?? 3;

        $body = NELC_Integration()->register_statment( 'rated', [
            'name' => "$user->display_name",
            'email' => "$user->user_email",
            'courseId' => "$course->ID",
            'courseName' => "$course->post_title",
            'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,    
            'instructor' => "$author->display_name",
            'inst_email' => "$author->user_email",
            'scaled' => $saved_rating / 5,
            'raw' => $saved_rating,
            'min' => 0,
            'max' => 5,
            'comment' => $comment_object->comment_content,
        ]);
        

        $response = NELC_Integration()->register_interactions( $body );
        if (is_wp_error($response)) {
            update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
        }else{
            update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
        }

    }
}

add_action('learndash_quiz_completed', 'quiz_rated_hook', 10, 3);

function quiz_rated_hook( $quizdata, $user) {

    $user = wp_get_current_user();
    $course = $quizdata['course'];
    $author  = get_userdata($course->post_author);

    $quiz_data = get_post( $quizdata['quiz'] );
	$atempts = learndash_get_user_quiz_attempts( $user->ID,  $quiz_data->ID );
    //$all_meta = learndash_get_quiz_questions( $quizdata['quiz'] );

	
    $body = NELC_Integration()->register_statment( 'attempted', [
        'name' => "$user->display_name",
        'email' => "$user->user_email",
        'quizUrl' => get_site_url() . "/units/quiz/$quiz_data->ID",
        'quizName' => "$quiz_data->post_title",
        'quizDesc' => strip_tags($quiz_data->post_content),
        'instructor' => "$author->display_name",
        'inst_email' => "$author->user_email",
        'attempNumber' => count($atempts),
        'courseId' => "$course->ID",
        'courseName' => "$course->post_title",
        'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,
        'scaled' => $quizdata['percentage'] / 100,
        'raw' => $quizdata['points'],
        'min' => $quizdata['total_points'] / 2,
        'max' => $quizdata['total_points'],
        'completion' => true,
        'success' => $quizdata['pass'] == 1 ? true : false,
    ]);
    
    $response = NELC_Integration()->register_interactions( $body );
    if( get_option('lnx_xapi_notific', true) !== 'on' ){
		return;
	}
    if (is_wp_error($response)) {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
    }else{
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
    }

	
    //update_user_meta(get_current_user_id(), 'notf_notf', get_site_url() . "/units/quiz/$quiz_data->ID");


}

// add_action('wp_footer', 'test_test2');
// function test_test2()
// {
// 	echo '<pre>';
// 	echo "quizUrl: ";
//     print_r( get_user_meta(get_current_user_id(), 'notf_notf', true) );
// 	echo '</pre>';
	

	
// }
