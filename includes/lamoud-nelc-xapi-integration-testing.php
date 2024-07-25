
<?php
$test = '';

if( isset( $_POST['lnx_xapi_select_statement'] ) ){
    $test = $_POST['lnx_xapi_select_statement'];

    $response = null;
    switch ($test) {
        case 'register':
            $body = NELC_Integration()->register_statment( 'registered', [
                'name' => 'Mahmoud Hassan',
                'email' => 'betalamoud@gmail.com',
                'courseId' => '123',
                'courseName' => 'Test course',
                'courseDesc' => 'course Desc',
                'instructor' => 'Mr Hassan',
                'inst_email' => 'mrhassan@test.com',
            ]);
            
            $response = NELC_Integration()->register_interactions( $body );
        case 'initialized':
            $body = NELC_Integration()->register_statment( 'initialized', [
                'name' => 'Mahmoud Hassan',
                'email' => 'betalamoud@gmail.com',
                'courseId' => '123',
                'courseName' => 'Test course',
                'courseDesc' => 'course Desc',
                'instructor' => 'Mr Hassan',
                'inst_email' => 'mrhassan@test.com',
            ]);
            
            $response = NELC_Integration()->register_interactions( $body );
        break;
        case 'watched':
            $body = NELC_Integration()->register_statment( 'watched', [
                'name' => 'Mahmoud Hassan',
                'email' => 'betalamoud@gmail.com',
                'lessonUrl'=> '/courseID/unitId/lessonId',
                'lessonName'=> 'Test lesson',
                'lessonDesc'=> 'Lesson Desc',
                'instructor' => 'Mr Hassan',
                'inst_email' => 'mrhassan@test.com',
                'courseId' => '123',
                'courseName' => 'Test course',
                'courseDesc' => 'course Desc',
                'completion' => true,
                'duration' => 'PT15M',
            ]);
            
            $response = NELC_Integration()->register_interactions( $body );
        break;
        case 'completed_lesson':
            $body = NELC_Integration()->register_statment( 'completed', [
                'name' => 'Mahmoud Hassan',
                'email' => 'betalamoud@gmail.com',
                'lessonUrl'=> '/courseID/unitId/lessonId',
                'lessonName'=> 'Test lesson',
                'lessonDesc'=> 'Lesson Desc',
                'instructor' => 'Mr Hassan',
                'inst_email' => 'mrhassan@test.com',
                'courseId' => '123',
                'courseName' => 'Test course',
                'courseDesc' => 'course Desc',
            ]);
            
            $response = NELC_Integration()->register_interactions( $body );
        break;
        case 'completed_unit':
            $body = NELC_Integration()->register_statment( 'completedUnit', [
                'name' => 'Mahmoud Hassan',
                'email' => 'betalamoud@gmail.com',
                'unitUrl'=> '/courseID/unitId',
                'unitName'=> 'Test unit',
                'unitDesc'=> 'Unit Desc',
                'instructor' => 'Mr Hassan',
                'inst_email' => 'mrhassan@test.com',
                'courseId' => '123',
                'courseName' => 'Test course',
                'courseDesc' => 'course Desc',
            ]);
            
            $response = NELC_Integration()->register_interactions( $body );
        break;
        case 'progressed':
            $body = NELC_Integration()->register_statment( 'progressed', [
                'name' => 'Mahmoud Hassan',
                'email' => 'betalamoud@gmail.com',
                'courseId' => '123',
                'courseName' => 'Test course',
                'courseDesc' => 'course Desc',
                'instructor' => 'Mr Hassan',
                'inst_email' => 'mrhassan@test.com',
                'scaled' => '0.9',
                'completion' => true,
            ]);
            
            $response = NELC_Integration()->register_interactions( $body );
        break;
        case 'attempted':
            $body = NELC_Integration()->register_statment( 'attempted', [
                'name' => 'Mahmoud Hassan',
                'email' => 'betalamoud@gmail.com',
                'quizUrl' => '/unitId/quizId',
                'quizName' => 'Test quiz',
                'quizDesc' => 'quiz Desc',
                'instructor' => 'Mr Hassan',
                'inst_email' => 'mrhassan@test.com',
                'attempNumber' => '1',
                'courseId' => '123',
                'courseName' => 'Test course',
                'courseDesc' => 'course Desc',
                'scaled' => '0.9',
                'raw' => '50',
                'min' => '25',
                'max' => '50',
                'completion' => true,
                'success' => true,
            ]);
            
            $response = NELC_Integration()->register_interactions( $body );
        break;
        case 'completed_course':
            $body = NELC_Integration()->register_statment( 'completedCourse', [
                'name' => 'Mahmoud Hassan',
                'email' => 'betalamoud@gmail.com',
                'courseId' => '123',
                'courseName' => 'Test course',
                'courseDesc' => 'course Desc',
                'instructor' => 'Mr Hassan',
                'inst_email' => 'mrhassan@test.com',
            ]);
            
            $response = NELC_Integration()->register_interactions( $body );
        break;
        case 'earned':
            $body = NELC_Integration()->register_statment( 'earned', [
                'name' => 'Mahmoud Hassan',
                'email' => 'betalamoud@gmail.com',
                'certUrl' => '/path/to/certificate',
                'certName' => 'Test certificate',
                'courseId' => '123',
                'courseName' => 'Test course',
                'courseDesc' => 'course Desc'
            ]);
            
            $response = NELC_Integration()->register_interactions( $body );
        break;
        case 'rated':
            $body = NELC_Integration()->register_statment( 'rated', [
                'name' => 'Mahmoud Hassan',
                'email' => 'betalamoud@gmail.com',
                'courseId' => '123',
                'courseName' => 'Test course',
                'courseDesc' => 'course Desc',
                'instructor' => 'Mr Hassan',
                'inst_email' => 'mrhassan@test.com',
                'scaled' => 0.8,
                'raw' => 4,
                'min' => 0,
                'max' => 5,
                'comment' => 'good course',
            ]);
            
            $response = NELC_Integration()->register_interactions( $body );
        break;
        
        default:
            $response = new WP_Error();
            $response->add('custom_error', 'Please select a valid statement');
        break;
    }

        //print_r($response);

        if (is_wp_error($response)) {
            // $result is a WP_Error
            $error_message = $response->get_error_message();
            // Handle the error as needed
            //echo 'Error: ' . $error_message;
            $html .= '<h2 style="direction: ltr;">Error</h2>';
            $html .= '<pre style="background: #fff; direction: ltr;padding: 16px;">';
                $html .= $error_message;
            $html .= '</pre>';
        } else {
            $html .= '<h2 style="direction: ltr;">'.$test.'</h2>';
            $html .= '<h2 style="direction: ltr;">Response</h2>';
            $html .= '<pre style="background: #fff; direction: ltr;padding: 16px;">';
                $html .= !is_wp_error( $response['response'] ) && json_encode($response['response']) ? json_encode($response['response']) : 'خطأ غير متوقع، برجاء التأكد من بيانات الإتصال';
            $html .= '</pre>';
    
            $html .= '<h2 style="direction: ltr;">Body</h2>';
            $html .= '<pre style="background: #fff; direction: ltr;padding: 16px;">';
                $html .= !is_wp_error( $response['body'] ) ? $response['body'] : 'خطأ غير متوقع، برجاء التأكد من بيانات الإتصال';
            $html .= '</pre>';
    
            $html .= '<h2 style="direction: ltr;">Statement</h2>';
            $html .= '<pre style="background: black; color:#fff; direction: ltr;padding: 16px;">';
                $html .= json_encode($body);
            $html .= '</pre>';
        }


    

}


