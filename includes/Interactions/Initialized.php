<?php

namespace LearndashLrsPlugin\Interactions;
//require_once(plugin_dir_path(__FILE__) . 'StatementConstants.php');


class Initialized
{

    protected $statementConstants;
    protected $platform_in_arabic;
    protected $platform_in_english;
    protected $platform;
    protected $lang;

    public function __construct()
    {
        $this->statementConstants = learndash_nelc_integration()->statementConstants();

        $this->platform_in_arabic = $this->statementConstants->platform_in_arabic;
        $this->platform_in_english = $this->statementConstants->platform_in_english;
        $this->platform = $this->statementConstants->platform;
        $this->lang = $this->statementConstants->lang;

    }

    public function send($actor, $actorEmail, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail) {
        $data =  [
            'actor' => [
                'name' => strval($actor),
                'mbox'  => 'mailto:' . strval($actorEmail),
                'objectType' => 'Agent',
            ],
            'verb' => [
                'id' => 'http://adlnet.gov/expapi/verbs/initialized',
                'display' => ['en-US' => 'initialized'] 
            ],
            'object' => [
                'id'=> strval($courseId),
                'definition' => [
                    'name' => [strval($this->lang) => strval($courseTitle)],
                    'description' => [strval($this->lang) => strval($courseDesc)],
                    'type' => 'https://w3id.org/xapi/cmi5/activitytype/course'
                ],
                'objectType' => 'Activity',
            ],
            'context' => [
                'instructor' => [
                    'name' => strval($instructor),
                    'mbox' => 'mailto:' . strval($instructorEmail),
                ],
                'platform' => strval($this->platform),
                'language' => strval($this->lang),
                "extensions" => [
                    "https://nelc.gov.sa/extensions/platform" => [
                        "name" => [
                            "ar-SA" => strval($this->platform_in_arabic),
                            "en-US" => strval($this->platform_in_english)
                        ]
                    ]
                ]
            ],
            'timestamp' => date('Y-m-d\TH:i:s') . substr((string)microtime(), 1, 4) . '\Z'
        ];
    
        return $data;
    }
    
    
}