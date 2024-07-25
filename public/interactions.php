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

 // Start register function (when student enrolled)
 function register_statement($name, $email, $courseId, $lang, $courseName, $courseDesc, $instructor, $inst_email, $pltForm, $plat_arname, $plat_enname){
    $statement =
    array(
        'actor' => array(
                    'name' => $name,
                    'mbox'  => 'mailto:'.$email,
                    'objectType' => 'Agent',
                ),
        'verb' => array(
                    'id' => 'http://adlnet.gov/expapi/verbs/registered',
                    'display' => array('en-US' => 'registered') 
                ),
        'object' => array(
                        'id'=> $courseId,
                        'definition' => array(
                            'name' => array("ar-SA" => $courseName),
                            'description' => array("ar-SA" => $courseDesc),
                            'type' => 'https://w3id.org/xapi/cmi5/activitytype/course'
                        ),
                        'objectType' => 'Activity',
                    ),
        'context' => array(
                        'instructor' => array(
                            'name' => $instructor,
                            'mbox' => 'mailto:'.$inst_email,
                        ),
                        'platform' => $pltForm,
                        'language' => "ar-SA",
                        "extensions" => array(
                            "https://nelc.gov.sa/extensions/platform" => array(
                                "name" => array(
                                    "ar-SA" => $plat_arname,
                                    "en-US" => $plat_enname
                                )
                            )
                        )
                    ),
        'timestamp' => date('Y-m-d\TH:i:s'.substr((string)microtime(), 1, 4).'\Z')
    );

    return $statement;

}
// Start initialize function (when student start course)
 function initialize_statement($name, $email, $courseId, $lang, $courseName, $courseDesc, $instructor, $inst_email, $pltForm, $plat_arname, $plat_enname){
    $statement =
    array(
        'actor' => array(
                    'name' => $name,
                    'mbox'  => 'mailto:'.$email,
                    'objectType' => 'Agent',
                ),
        'verb' => array(
                    'id' => 'http://adlnet.gov/expapi/verbs/initialized',
                    'display' => array('en-US' => 'initialized') 
                ),
        'object' => array(
                        'id'=> $courseId,
                        'definition' => array(
                            'name' => array($lang=>$courseName),
                            'description' => array($lang=> $courseDesc),
                            'type' => 'https://w3id.org/xapi/cmi5/activitytype/course'
                        ),
                        'objectType' => 'Activity',
                    ),
        'context' => array(
                        'instructor' => array(
                            'name' => $instructor,
                            'mbox' => 'mailto:'.$inst_email,
                        ),
                        'platform' => $pltForm,
                        'language' => $lang,
                        "extensions" => array(
                            "https://nelc.gov.sa/extensions/platform" => array(
                                "name" => array(
                                    "ar-SA" => $plat_arname,
                                    "en-US" => $plat_enname
                                )
                            )
                        )
                    ),
        'timestamp' => date('Y-m-d\TH:i:s'.substr((string)microtime(), 1, 4).'\Z')
    );

    return $statement;

}
// Start watch function (when student end lesson or video)
function watch_statement($name, $email, $courseId, $lang, $unitId, $lesonId, $lesonName, $lesonDesc, $brsCodeName, $brsName, $brsVer, $courseName, $courseDesc, $instructor, $inst_email, $pltForm, $comp, $compTime){
    $statement =
    array(
        'actor' => array(
                    'name' => $name,
                    'mbox'  => 'mailto:'.$email,
                    'objectType' => 'Agent',
                ),
        'verb' => array(
                    'id' => 'http://activitystrea.ms/watch',
                    'display' => array("en-US" => "watched") 
                ),
        'object' => array(
                        'id'=> "courses/$courseId/unit/$unitId/leson/$lesonId",
                        'definition' => array(
                            'name' => array($lang=>$lesonName),
                            'description' => array($lang=> $lesonDesc),
                            'type' => 'http://adlnet.gov/expapi/activities/lesson'
                        ),
                        'objectType' => 'Activity',
                    ),
        'context' => array(
                        'instructor' => array(
                            'name' => $instructor,
                            'mbox' => 'mailto:'.$inst_email,
                        ),
                        'platform' => $pltForm,
                        'language' => $lang,
                        'extensions' => array (
                            "http://id.tincanapi.com/extension/browser-info" => array(
                                "code_name" => "$brsCodeName",
                                "name" => "$brsName",  
                                "version" => "$brsVer"
                            )
                        ),
                        'contextActivities' => array(
                            'parent' => array(
                                array (
                                    'id' => $courseId,
                                    'definition' => array(  
                                        'name' => array($lang => $courseName),
                                        'description' => array($lang => $courseDesc),
                                        'type' => 'https://w3id.org/xapi/cmi5/activitytype/course'
                                    ),
                                    'objectType' => "Activity"
                                )
                            )
                        )
                    ),
                    'result' => array(
                        'completion' => $comp,
                        'duration' => $compTime,
                    ),
        'timestamp' => date('Y-m-d\TH:i:s'.substr((string)microtime(), 1, 4).'\Z')
    );

    return $statement;

}
// Start watch function (when student end lesson or video)
function leson_statement($name, $email, $courseId, $lang, $unitId, $lesonId, $lesonName, $lesonDesc, $brsCodeName, $brsName, $brsVer, $courseName, $courseDesc, $instructor, $inst_email, $pltForm, $plat_arname, $plat_enname){
    $statement =
    array(
        'actor' => array(
                    'name' => $name,
                    'mbox'  => 'mailto:'.$email,
                    'objectType' => 'Agent',
                ),
        'verb' => array(
                    'id' => 'http://adlnet.gov/expapi/verbs/completed',
                    'display' => array("en-US" => "completed") 
                ),
        'object' => array(
                        'id'=> "courses/$courseId/unit/$unitId/leson/$lesonId",
                        'definition' => array(
                            'name' => array($lang=>$lesonName),
                            'description' => array($lang=> $lesonDesc),
                            'type' => 'http://adlnet.gov/expapi/activities/lesson'
                        ),
                        'objectType' => 'Activity',
                    ),
        'context' => array(
                        'instructor' => array(
                            'name' => $instructor,
                            'mbox' => 'mailto:'.$inst_email,
                        ),
                        'platform' => $pltForm,
                        'language' => $lang,
                        'extensions' => array (
                            "http://id.tincanapi.com/extension/browser-info" => array(
                                "code_name" => "$brsCodeName",
                                "name" => "$brsName",  
                                "version" => "$brsVer"
                            ),
                            "https://nelc.gov.sa/extensions/platform" => array(
                                "name" => array(
                                    "ar-SA" => $plat_arname,
                                    "en-US" => $plat_enname
                                )
                            )
                        ),
                        'contextActivities' => array(
                            'parent' => array(
                                array (
                                    'id' => $courseId,
                                    'definition' => array(  
                                        'name' => array($lang => $courseName),
                                        'description' => array($lang => $courseDesc),
                                        'type' => 'https://w3id.org/xapi/cmi5/activitytype/course'
                                    ),
                                    'objectType' => "Activity"
                                )
                            )
                        )
                    ),

        'timestamp' => date('Y-m-d\TH:i:s'.substr((string)microtime(), 1, 4).'\Z')
    );

    return $statement;

}
// Start attempt function (when student end quiz)
function attempt_statement($name, $email, $courseId, $lang, $unitId, $quiz, $quizName, $quizDesc, $brsCodeName, $brsName, $brsVer, $courseName, $courseDesc, $instructor, $inst_email, $pltForm, $scaled, $row, $min, $max, $comp, $succ, $attNum, $plat_arname, $plat_enname){
    $statement =
    array(
        'actor' => array(
                    'name' => $name,
                    'mbox'  => 'mailto:'.$email,
                    'objectType' => 'Agent',
                ),
        'verb' => array(
                    'id' => 'http://adlnet.gov/expapi/verbs/attempted',
                    'display' => array("en-US" => "attempted") 
                ),
        'object' => array(
                        'id'=> "courses/$courseId/unit/$unitId/quiz/$quiz",
                        'definition' => array(
                            'name' => array($lang=>$quizName),
                            'description' => array($lang=>$quizDesc),
                            'type' => 'http://id.tincanapi.com/activitytype/unit-test'
                        ),
                        'objectType' => 'Activity',
                    ),
        'context' => array(
                        'instructor' => array(
                            'name' => $instructor,
                            'mbox' => 'mailto:'.$inst_email,
                        ),
                        'platform' => $pltForm,
                        'language' => $lang,
                        'extensions' => array (
                            "http://id.tincanapi.com/extension/attempt-id" => $attNum,
                            "http://id.tincanapi.com/extension/browser-info" => array(
                                "code_name" => "$brsCodeName",
                                "name" => "$brsName",  
                                "version" => "$brsVer"
                            ),
                            "https://nelc.gov.sa/extensions/platform" => array(
                                "name" => array(
                                    "ar-SA" => $plat_arname,
                                    "en-US" => $plat_enname
                                )
                            )
                        ),
                        'contextActivities' => array(
                            'parent' => array(
                                array (
                                    'id' => $courseId,
                                    'definition' => array(  
                                        'name' => array($lang => $courseName),
                                        'description' => array($lang => $courseDesc),
                                        'type' => 'https://w3id.org/xapi/cmi5/activitytype/course'
                                    ),
                                    'objectType' => "Activity"
                                )
                            )
                        )
                    ),
                    'result' => array(
                        "score" => array(
                            "scaled" => $scaled,
                            "raw" => $row,
                            "min" => 0,
                            "max" => $max
                        ),
                        'completion' => $comp,
                        "success" => $succ,
                    ),
        'timestamp' => date('Y-m-d\TH:i:s'.substr((string)microtime(), 1, 4).'\Z')
    );

    return $statement;

}
// Start complete function (when student complete a unit)
function complete_statement($name, $email, $courseId, $lang, $unitId, $unitName, $unitDesc, $brsCodeName, $brsName, $brsVer, $courseName, $courseDesc, $instructor, $inst_email, $pltForm, $plat_arname, $plat_enname){
    $statement =
    array(
        'actor' => array(
                    'name' => $name,
                    'mbox'  => 'mailto:'.$email,
                    'objectType' => 'Agent',
                ),
        'verb' => array(
                    'id' => 'http://adlnet.gov/expapi/verbs/completed',
                    'display' => array("en-US" => "completed") 
                ),
        'object' => array(
                        'id'=> "courses/$courseId/unit/$unitId",
                        'definition' => array(
                            'name' => array($lang=>$unitName),
                            'description' => array($lang=>$unitDesc),
                            'type' => 'http://adlnet.gov/expapi/activities/module'
                        ),
                        'objectType' => 'Activity',
                    ),
        'context' => array(
                        'instructor' => array(
                            'name' => $instructor,
                            'mbox' => 'mailto:'.$inst_email,
                        ),
                        'platform' => $pltForm,
                        'language' => $lang,
                        'extensions' => array (
                            "http://id.tincanapi.com/extension/browser-info" => array(
                                "code_name" => "$brsCodeName",
                                "name" => "$brsName",  
                                "version" => "$brsVer"
                            ),
                            "https://nelc.gov.sa/extensions/platform" => array(
                                "name" => array(
                                    "ar-SA" => $plat_arname,
                                    "en-US" => $plat_enname
                                )
                            )
                        ),
                        'contextActivities' => array(
                            'parent' => array(
                                array (
                                    'id' => $courseId,
                                    'objectType' => "Activity",
                                    'definition' => array(  
                                        'name' => array($lang => $courseName),
                                        'description' => array($lang => $courseDesc),
                                        'type' => 'https://w3id.org/xapi/cmi5/activitytype/course'
                                    ),
                                )
                            )
                        )
                    ),
                    
        'timestamp' => date('Y-m-d\TH:i:s'.substr((string)microtime(), 1, 4).'\Z')
    );

    return $statement;

}
// Start progress function (it's send course progress after unit complete)
function progress_statement($name, $email, $courseId, $lang, $courseProgress, $courseName, $courseDesc, $instructor, $inst_email, $comp, $pltForm, $plat_arname, $plat_enname){
    $statement =
    array(
        'actor' => array(
                    'name' => $name,
                    'mbox'  => 'mailto:'.$email,
                    'objectType' => 'Agent',
                ),
        'verb' => array(
                    'id' => 'http://adlnet.gov/expapi/verbs/progressed',
                    'display' => array("en-US" => "progressed") 
                ),
        'object' => array(
                        'id'=> $courseId,
                        'definition' => array(
                            'name' => array($lang => $courseName),
                            'description' => array( $lang => $courseDesc),
                            'type' => 'https://w3id.org/xapi/cmi5/activitytype/course'
                        ),
                        'objectType' => 'Activity',
                    ),
        'context' => array(
                        'instructor' => array(
                            'name' => $instructor,
                            'mbox' => 'mailto:'.$inst_email,
                        ),
                        'platform' => $pltForm,
                        'language' => $lang,
                        "extensions" => array(
                            "https://nelc.gov.sa/extensions/platform" => array(
                                "name" => array(
                                    "ar-SA" => $plat_arname,
                                    "en-US" => $plat_enname
                                )
                            )
                        )
                    ),
        'result' => array(
                        "score" => array(
                            "scaled" =>  $courseProgress
                            ),
                        "completion" => $comp,
            ),                
        'timestamp' => date('Y-m-d\TH:i:s'.substr((string)microtime(), 1, 4).'\Z')
    );

    return $statement;
}
 // Start completeCourse function (when student complete the course)
 function completeCourse_statement($name, $email, $courseId, $lang, $courseName, $courseDesc, $instructor, $inst_email, $pltForm, $plat_arname, $plat_enname){
    $statement =
    array(
        'actor' => array(
                    'name' => $name,
                    'mbox'  => 'mailto:'.$email,
                    'objectType' => 'Agent',
                ),
        'verb' => array(
                    'id' => 'http://adlnet.gov/expapi/verbs/completed',
                    'display' => array('en-US' => 'completed') 
                ),
        'object' => array(
                        'id'=> $courseId,
                        'definition' => array(
                            'name' => array($lang=>$courseName),
                            'description' => array($lang=> $courseDesc),
                            'type' => 'https://w3id.org/xapi/cmi5/activitytype/course'
                        ),
                        'objectType' => 'Activity',
                    ),
        'context' => array(
                        'instructor' => array(
                            'name' => $instructor,
                            'mbox' => 'mailto:'.$inst_email,
                        ),
                        'platform' => $pltForm,
                        'language' => $lang,
                        "extensions" => array(
                            "https://nelc.gov.sa/extensions/platform" => array(
                                "name" => array(
                                    "ar-SA" => $plat_arname,
                                    "en-US" => $plat_enname
                                )
                            )
                        )
                    ),
        'timestamp' => date('Y-m-d\TH:i:s'.substr((string)microtime(), 1, 4).'\Z')
    );

    return $statement;

}
// Start earn certificate function (when student end quiz)
function earnCertificate_statement($name, $email, $courseId, $lang, $cert, $certName, $courseName, $courseDesc, $pltForm, $plat_arname, $plat_enname){
    $statement =
    array(
        'actor' => array(
                    'name' => $name,
                    'mbox'  => 'mailto:'.$email,
                    'objectType' => 'Agent',
                ),
        'verb' => array(
                    'id' => 'http://id.tincanapi.com/verb/earned',
                    'display' => array("en-US" => "earned") 
                ),
        'object' => array(
                        'id'=> $cert,
                        'definition' => array(
                            'name' => array($lang=>$certName),
                            'type' => 'https://www.opigno.org/en/tincan_registry/activity_type/certificate'
                        ),
                        'objectType' => 'Activity',
                    ),
        'context' => array(
                        'extensions' => array (
                            "http://id.tincanapi.com/extension/jws-certificate-location" => $cert,
                            "https://nelc.gov.sa/extensions/platform" => array(
                                "name" => array(
                                    "ar-SA" => $plat_arname,
                                    "en-US" => $plat_enname
                                )
                            )
                        ),
                        'platform' => $pltForm,
                        'language' => $lang,
                        'contextActivities' => array(
                            'parent' => array(
                                array (
                                    'id' => $courseId,
                                    'definition' => array(  
                                        'name' => array($lang => $courseName),
                                        'description' => array($lang => $courseDesc),
                                        'type' => 'https://w3id.org/xapi/cmi5/activitytype/course'
                                    ),
                                    'objectType' => "Activity"
                                )
                            )
                        )
                    ),
        'timestamp' => date('Y-m-d\TH:i:s'.substr((string)microtime(), 1, 4).'\Z')
    );

    return $statement;

}
 // Start rate function (when student rate the course)
 function rate_statement($name, $email, $courseId, $lang, $courseName, $courseDesc, $instructor, $inst_email, $pltForm, $scaled, $row, $min, $max, $usComment, $plat_arname, $plat_enname){
    $statement =
    array(
        'actor' => array(
                    'name' => $name,
                    'mbox'  => 'mailto:'.$email,
                    'objectType' => 'Agent',
                ),
        'verb' => array(
                    'id' => 'http://id.tincanapi.com/verb/rated',
                    'display' => array('en-US' => 'rated') 
                ),
        'object' => array(
                        'id'=> $courseId,
                        'definition' => array(
                            'name' => array($lang=>$courseName),
                            'description' => array($lang=> $courseDesc),
                            'type' => 'https://w3id.org/xapi/cmi5/activitytype/course'
                        ),
                        'objectType' => 'Activity',
                    ),
        'context' => array(
                        'instructor' => array(
                            'name' => $instructor,
                            'mbox' => 'mailto:'.$inst_email,
                        ),
                        'platform' => $pltForm,
                        'language' => $lang,
                        "extensions" => array(
                            "https://nelc.gov.sa/extensions/platform" => array(
                                "name" => array(
                                    "ar-SA" => $plat_arname,
                                    "en-US" => $plat_enname
                                )
                            )
                        )
                    ),
        "result" => array(
                    "score" => array(
                            "scaled" => $scaled,
                            "raw" => $row,
                            "min" => 0,
                            "max" => 5
                    ),
                    "response" => $usComment
                ),
        'timestamp' => date('Y-m-d\TH:i:s'.substr((string)microtime(), 1, 4).'\Z')
    );

    return $statement;

}
