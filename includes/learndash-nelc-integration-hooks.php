<?php

/*
* Hooking into the 'learndash_update_user_activity' action to handle course started activity.
* This function is responsible for processing user activity when a course is started.
* @param array $args An array containing the activity details.
*/
add_action( 'learndash_update_user_activity', 'learndash_course_started_hook', 10, 3 );
function learndash_course_started_hook( $args )
{
    // Extracting necessary data from the $args array.
    $course_id = $args['course_id'];
    $post_id = $args['post_id'];
    $user_id = $args['user_id'];
    $activity_type = $args['activity_type'];
    $activity_action = $args['activity_action'];
    $activity_meta = $args['activity_meta'];
    
    // Checking if the user has already started the course.
    $started = learndash_activity_course_get_earliest_started($user_id, $course_id);

    // Retrieving current user and course information.
    $user = wp_get_current_user();
    $course = get_post( $course_id );
    $author  = get_userdata($course->post_author);
	
    // Processing user activity when the course is accessed for the first time.
    if( $course_id == $post_id && !$started && $activity_type == 'access' && $activity_action == 'insert' ){
        // Creating a statement for the registered activity.
        $body = learndash_nelc_integration()->register_statement( 'registered', [
            'name' => "$user->display_name",
            'email' => "$user->user_email",
            'courseId' => "$course->ID",
            'courseName' => "$course->post_title",
            'courseDesc' =>  strip_tags($course->post_content),    
            'instructor' => "$author->display_name",
            'inst_email' => "$author->user_email",
        ]);
        
        // Registering the interaction and updating user meta accordingly.
        $response = learndash_nelc_integration()->register_interactions( $body );
        if (is_wp_error($response)) {
            update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
        } else {
            update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
        }
		
    }

    // Processing user activity when the course is initialized for the first time.
    if( $course_id === $post_id && !$started && $activity_type == 'course' && $activity_action == 'insert' ){	
        // Creating a statement for the initialized activity.
        $body = learndash_nelc_integration()->register_statement( 'initialized', [
            'name' => "$user->display_name",
            'email' => "$user->user_email",
            'courseId' => "$course->ID",
            'courseName' => "$course->post_title",
            'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,    
            'instructor' => "$author->display_name",
            'inst_email' => "$author->user_email",
        ]);
        
        // Registering the interaction and updating user meta accordingly.
        $response = learndash_nelc_integration()->register_interactions( $body );
        if (is_wp_error($response)) {
            update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
        } else {
            update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
        }

    }
}

/*
* Hooking into the 'learndash_lesson_completed' action to handle lesson completion activity.
* This function is responsible for processing user activity when a lesson is completed.
* @param array $lesson_data An array containing the lesson details.
*/

//add_action( 'learndash_lesson_completed', 'learndash_lesson_completed_hook', 10, 3 );

function learndash_lesson_completed_hook( $lesson_data )
{

    // Extracting necessary data from the $lesson_data array.
    $user = $lesson_data['user']->data;
    $course = $lesson_data['course'];
    $lesson = $lesson_data['lesson'];
    $progress = $lesson_data['progress'];
    $author  = get_userdata($lesson_data['course']->post_author);
    $percentage = ($progress['completed'] / $progress['total']);

    // Creating a statement for the 'completed' activity.
    $body1 = learndash_nelc_integration()->register_statement( 'completed', [
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

    // Registering the interaction for 'completed' activity.
    $response1 = learndash_nelc_integration()->register_interactions( $body1 );

    // Creating a statement for the 'progressed' activity.
    $body = learndash_nelc_integration()->register_statement( 'progressed', [
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

    // Registering the interaction for 'progressed' activity.
    $response = learndash_nelc_integration()->register_interactions( $body );

    // Handling errors and updating user meta accordingly for 'completed' activity.
    if (is_wp_error($response1)) {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
    } else {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response1['body']);
    }

    // Handling errors and updating user meta accordingly for 'progressed' activity.
    if (is_wp_error($response)) {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
    } else {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
    }
    
}

/*
* Hooking into the 'learndash_topic_completed' action to handle topic & lesson completion & progressed activity.
* This function is responsible for processing user activity when a topic is completed.
* @param array $lesson_data An array containing the topic details.
*/
add_action( 'learndash_topic_completed', 'learndash_topic_completed_hook', 10, 3 );
function learndash_topic_completed_hook( $lesson_data )
{
    // Extracting necessary data from the $lesson_data array.
    $user = $lesson_data['user']->data;
    $course = $lesson_data['course'];
    $lesson = $lesson_data['lesson'];
    $progress = $lesson_data['progress'];
    $author  = get_userdata($lesson_data['course']->post_author);
    $percentage = ($progress['completed'] / $progress['total']);

    // Creating a statement for the 'completed' activity.
    $body1 = learndash_nelc_integration()->register_statement( 'completed', [
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
    // Registering the interaction for 'completed' activity.
    $response1 = learndash_nelc_integration()->register_interactions( $body1 );

    // Creating a statement for the 'progressed' activity.
    $body = learndash_nelc_integration()->register_statemen( 'progressed', [
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
    // Registering the interaction for 'progressed' activity.
    $response = learndash_nelc_integration()->register_interactions( $body );

    // Handling errors and updating user meta accordingly for 'completed' activity.
    if (is_wp_error($response1)) {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
    }else{
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response1['body']);
    }

    // Handling errors and updating user meta accordingly for 'progressed' activity.
    if (is_wp_error($response)) {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
    }else{
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
    }
    
}

/*
* Hooking into the 'learndash_course_completed' action to handle course completion activity.
* This function is responsible for processing user activity when a course is completed.
* @param array $args An array containing the user, course, and progress details.
*/
add_action( 'learndash_course_completed', 'learndash_course_completed_hook', 10, 3 );
function learndash_course_completed_hook( $args = array() )
{
    /*
    * The $args array contains:
    * - 'user': The current user object.
    * - 'course': The course post object.
    * - 'progress': The progress of the course.
    */
    $user = $args['user'];
    $course = $args['course'];
    $author  = get_userdata($course->post_author);
    $progress = $args['progress'];

    // Getting the certificate link for the completed course.
    $certificate_link = learndash_get_course_certificate_link($course->ID, $user->ID);

    // Creating a statement for the 'completedCourse' activity.
    $body = learndash_nelc_integration()->register_statement( 'completedCourse', [
        'name' => "$user->display_name",
        'email' => "$user->user_email",
        'courseId' => "$course->ID",
        'courseName' => "$course->post_title",
        'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,
        'instructor' => "$author->display_name",
        'inst_email' => "$author->user_email",
    ]);
    
    // Registering the interaction for 'completedCourse' activity.
    $response = learndash_nelc_integration()->register_interactions( $body );
    // Creating a statement for the 'earned' activity.
    $body1 = learndash_nelc_integration()->register_statement( 'earned', [
        'name' => "$user->display_name",
        'email' => "$user->user_email",
        'certUrl' => "$certificate_link",
        'certName' => "$certificate_link",
        'courseId' => "$course->ID",
        'courseName' => "$course->post_title",
        'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,
    ]);
	
    // Registering the interaction for 'earned' activity.
    $response1 = learndash_nelc_integration()->register_interactions( $body1 );

    // Handling errors and updating user meta accordingly for 'completedCourse' activity.
    if (is_wp_error($response)) {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
    }else{
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
    }
}

/*
* Hooking into the 'wp_insert_comment' action to handle course rating activity.
* This function is responsible for processing user activity when a course is rated.
* @param int $comment_id The ID of the comment being inserted.
* @param object $comment_object The comment object.
*/
add_action('wp_insert_comment','learndash_course_rated_hook', 100, 3);
function learndash_course_rated_hook($comment_id, $comment_object) {

    // Checking if the comment type is 'ld_review'.
    if ( $comment_object->comment_type == 'ld_review' ) {

        // Getting the current user.
        $user = wp_get_current_user();
        // Getting the course post.
        $course = get_post( $comment_object->comment_post_ID );
        // Getting the author of the course.
        $author  = get_userdata($course->post_author);
        // Retrieving the saved rating from the comment or defaulting to 3.
        $saved_rating =  $_POST['rating'] ?? 3;

        // Creating a statement for the 'rated' activity.
        $body = learndash_nelc_integration()->register_statement( 'rated', [
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
        
        // Registering the interaction for 'rated' activity.
        $response = learndash_nelc_integration()->register_interactions( $body );

        // Handling errors and updating user meta accordingly for 'rated' activity.
        if (is_wp_error($response)) {
            update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
        }else{
            update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
        }

    }
}
/*
* Hooking into the 'learndash_quiz_completed' action to handle quiz completion activity.
* This function is responsible for processing user activity when a quiz is completed.
* @param array $quizdata An array containing quiz data.
* @param object $user The user object.
*/
add_action('learndash_quiz_completed', 'learndash_quiz_completed_hook', 10, 3);
function learndash_quiz_completed_hook( $quizdata, $user) {

    // Getting the current user.
    $user = wp_get_current_user();
    // Getting the course post associated with the quiz.
    $course = $quizdata['course'];
    // Getting the author of the course.
    $author  = get_userdata($course->post_author);
    // Retrieving the quiz post data.
    $quiz_data = get_post( $quizdata['quiz'] );
    // Retrieving the quiz attempts for the current user.
	$atempts = learndash_get_user_quiz_attempts( $user->ID,  $quiz_data->ID );

    // Creating a statement for the 'attempted' activity.
    $body = learndash_nelc_integration()->register_statement( 'attempted', [
        'name' => "$user->display_name",
        'email' => "$user->user_email",
        'quizUrl' => get_site_url() . "/units/quiz/$quiz_data->ID",
        'quizName' => "$quiz_data->post_title",
        'quizDesc' => strip_tags($quiz_data->post_content),
        'instructor' => "$author->display_name",
        'inst_email' => "$author->user_email",
        'attempNumber' => count($atempts), // Counting the quiz attempts.
        'courseId' => "$course->ID",
        'courseName' => "$course->post_title",
        'courseDesc' => substr( strip_tags($course->post_content), 0, 50 ) ?? $course->post_title,
        'scaled' => $quizdata['percentage'] / 100, // Scaling the percentage to a range of 0 to 1.
        'raw' => $quizdata['points'], // Storing the raw points achieved.
        'min' => $quizdata['total_points'] / 2, // Minimum possible points required for passing.
        'max' => $quizdata['total_points'], // Maximum possible points.
        'completion' => true, // Indicating quiz completion.
        'success' => $quizdata['pass'] == 1 ? true : false, // Indicating if the quiz was passed.
    ]);
    
    // Registering the interaction for 'attempted' activity.
    $response = learndash_nelc_integration()->register_interactions( $body );
    
    // Handling errors and updating user meta accordingly for 'attempted' activity.
    if( get_option('lrs_xapi_notific', true) !== 'on' ){
        return;
    }
    if (is_wp_error($response)) {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', 'error');
    } else {
        update_user_meta(get_current_user_id(), 'lamoud_nelc_xapi_notify_action', $response['body']);
    }
}

// add_action('wp_footer', 'test_test2');
// function test_test2()
// {
// 	echo '<pre>';
// 	echo "quizUrl: ";
//     print_r( get_user_meta(get_current_user_id(), 'notf_notf', true) );
// 	echo '</pre>';
	

	
// }
