<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wa.me/00201062332549
 * @since      1.0.2
 *
 * @package    lamoud_nelc_xapi
 * @subpackage lamoud_nelc_xapi/admin/partials
 */  
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <div id="icon-themes" class="icon32"></div>  
    <h2><?php _e('NELC integration test', 'lamoud-nelc-xapi'); ?></h2>  
        <!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
    <?php settings_errors(); ?> 

    <form method="POST" action="">  
        <input type="hidden" name="nelc_test_auth" value="nelc_test_auth">            
        <?php submit_button(__( 'Test Auth', 'lamoud-nelc-xapi' ), 'nelc_test_auth button-primary'); ?>  
    </form>

    <?php if (isset($_POST["nelc_test_auth"])) :?>
        <?php

$endpoint = get_option( 'xapi_endpoint' );
$username = get_option( 'xapi_username' );
$secret = get_option( 'xapi_secret' );
$platform = get_option( 'xapi_platform' );
$platformAr = get_option( 'xapi_platform_ar_name' );
$platformEn = get_option( 'xapi_platform_en_name' );
$plt_lang = str_contains(get_locale(), 'ar') ? 'ar-SA' : 'en-US';

$headers = array (
    'Content-type'=> 'Application/json',
    'Access-Control-Allow-Origin'=> '*',
    'Authorization' => 'Basic ' . base64_encode( $username . ':' . $secret ),
);
            require_once plugin_dir_path( dirname( __FILE__ ) ) . '../public/lamoud-nelc-xapi-config.php';
            $ntd = '28809201502437';
            $user_email = 'ing.moudy@gmail.com';
            $user_email = 'ing.moudy@gmail.com';
            // $registerNew = register_statement(
            //     'Mahmoud Hassan', 
            //     'ing.moudy@gmail.com', 
            //     '5292', 'ar-SA', 
            //     'Test course',
            //     'Test course',
            //     'Adam Ali',
            //     'info@lamoud.com',
            //     $platform,
            //     $platformAr,
            //     $platformEn
            // );
            // $registerNew = initialize_statement(
            //     strval($ntd), 
            //     strval($user_email), 
            //     strval('88'),
            //     strval('ar-SA'), 
            //     strval('$course_title'),
            //     strval('$course_disc'),
            //     strval('$author_name'),
            //     strval('info@autor.com'),
            //     strval($platform)
            // );

            // $registerNew = leson_statement(
            //     strval($ntd),
            //     strval($user_email),
            //     strval('222'),
            //     $plt_lang,
            //     strval('55'),
            //     strval('88'),
            //     strval('test leson'),
            //     strval('test leson tesy'),
            //     strval('win'),
            //     strval('chrome'),
            //     strval('9.0.0'),
            //     strval('test coutse'),
            //     strval('test course disc'),
            //     strval('Autor name'),
            //     strval('info@autor.com'),
            //     strval($platform),
            //     $platformAr,
            //     $platformEn
            // );

            $registerNew = attempt_statement(
                strval($ntd),
                strval($user_email),
                strval('222'),
                strval("ar-SA"),
                strval('55'),
                strval('88'),
                strval('test quiz'),
                strval('quiz test'),
                strval('win'),
                strval('chrome'),
                strval('9.0.0'),
                strval('test course'),
                strval('test course disc'),
                strval('Autor name'),
                strval('info@autor.com'),
                strval($platform),
                0.70,
                7,
                0,
                10,
                true,
                true,
                1,
                $platformAr,
                $platformEn
            );
            // $registerNew = complete_statement(
            //     strval($ntd),
            //     strval($user_email),
            //     strval('222'),
            //     strval("ar-SA"),
            //     strval('55'),
            //     strval('unit test'),
            //     strval('unit disc'),
            //     strval('win'),
            //     strval('chrome'),
            //     strval('9.0.0'),
            //     strval('test course'),
            //     strval('test course disc'),
            //     strval('Autor name'),
            //     strval('info@autor.com'),
            //     strval($platform)
            // );
            // $registerNew = progress_statement(
            //     strval($ntd),
            //     strval($user_email),
            //     strval('222'),
            //     strval("ar-SA"),
            //     strval('20'),
            //     strval('Test course'),
            //     strval('course disc'),
            //     strval('Autor name'),
            //     strval('info@autor.com'),
            //     false,
            //     strval($platform)
            // );
            // $registerNew = completeCourse_statement(
            //     'Mahmoud Hassan', 
            //     'ing.moudy@gmail.com', 
            //     '5292','ar-SA', 
            //     'Test course',
            //     'Test course',
            //     'Adam Ali',
            //     'info@lamoud.com',
            //     $platform
            // );
            // $registerNew = earnCertificate_statement(
            //     'Mahmoud Hassan', 
            //     'ing.moudy@gmail.com',
            //     '5292',
            //     'ar-SA', 
            //     'https://lamoud.com/cert01',
            //     'cert Test course',
            //     'cert Test course disc',
            //     'Test course',
            //     'Test course disc'
            // );
            // $registerNew = rate_statement(
            //     'Mahmoud Hassan', 
            //     'ing.moudy@gmail.com',
            //     '5292',
            //     'ar-SA',
            //     'test course',
            //     'test course Disc',
            //     'Author test',
            //     'Author@mail.com',
            //     strval($platform),
            //     0.8,
            //     6,
            //     1,
            //     5,
            //     'user comment'
            // );
            //$endpoint = get_option( 'xapi_endpoint' );
            $jsonStm = json_encode($registerNew);
            $response = wp_remote_post( $endpoint, array (
                'method'  => 'POST',
                'httpversion' => '1.0.2',
                'headers' => $headers,
                'body'    =>  $jsonStm
            ));
            
            // echo '<pre>';
            // print_r($response['body']);
        ?>
            <h2 style="direction: ltr;">Response</h2>
            
            <pre style="background: #fff; direction: ltr;">
                <?php echo json_encode($response['response']); ?>
            </pre>
            <h2 style="direction: ltr;">Body</h2>
            <pre style="background: #fff; direction: ltr;">
                <?php print_r($response['body']); ?>
            </pre>
            <br>
            <h2 style="direction: ltr;">Statement</h2>
            <pre style="background: black; color:#fff; direction: ltr;">
                <?php print_r($registerNew); ?>
            </pre>
            <script>
                console.log(<?php echo $jsonStm; ?>);
            </script>
    <?php endif ?>
</div>
