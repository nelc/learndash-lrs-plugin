<?php
namespace LearndashLrsPlugin\Interactions;
require_once(plugin_dir_path(__FILE__) . 'Initialized.php');


use LearndashLrsPlugin\Interactions\Initialized;

class LearndashXapiRequest
{
    public $verb;
    public $actor;
    public $actorEmail;
    public $courseId;
    public $courseTitle;
    public $courseDesc;
    public $instructor;
    public $instructorEmail;

    public $body;

    public function __construct(string $verb, array $parameters = [])
    {

        $defaults = [
            'actor' => null,
            'actorEmail' => null,
            'courseId' => null,
            'courseTitle' => null,
            'courseDesc' => null,
            'instructor' => null,
            'instructorEmail' => null,
        ];

        $parameters = array_merge($defaults, $parameters);

        $this->verb = $verb ?? 'initialized';
        $this->actor = $parameters['actor'];
        $this->actorEmail = $parameters['actorEmail'];
        $this->courseId = $parameters['courseId'];
        $this->courseTitle = $parameters['courseTitle'];
        $this->courseDesc = $parameters['courseDesc'];
        $this->instructor = $parameters['instructor'];
        $this->instructorEmail = $parameters['instructorEmail'];

        $instance = new Initialized();

        $body = $instance->send( 
            $this->actor,
            $this->actorEmail,
            $this->courseId,
            $this->courseTitle,
            $this->courseDesc,
            $this->instructor,
            $this->instructorEmail
        );

        return $this->post( $body );
        
        switch ($this->verb) {
            case 'initialized':
                $this->initialized();
                break;
            
            default:
                $this->initialized();
                break;
        }

    }

    public function post($body)
    {
        return learndash_nelc_integration()->register_interactions( $body );
        
    }

    public function initialized()
    {
        $instance = new Initialized();

        $body = $instance->send( 
            $this->actor,
            $this->actorEmail,
            $this->courseId,
            $this->courseTitle,
            $this->courseDesc,
            $this->instructor,
            $this->instructorEmail
        );

        return $this->post( $body );
    }


}