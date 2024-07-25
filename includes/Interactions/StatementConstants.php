<?php
namespace LearndashLrsPlugin\Interactions;
use Wolfcast\BrowserDetection;


// Define a class for statement constants
class StatementConstants {
    // Declare protected properties
    public $platform; // Platform name
    public $platform_in_arabic; // Platform name in Arabic
    public $platform_in_english; // Platform name in English
    public $lang; // User's language
    public $br_os; // User's browser operating system
    public $br_name; // User's browser name
    public $br_ver; // User's browser version

    // Constructor to initialize properties
    public function __construct() {
        // Retrieve platform name from WordPress options
        $this->platform = get_option('lrs_xapi_platform');
        
        // Retrieve platform name in Arabic from WordPress options
        $this->platform_in_arabic = get_option('lrs_xapi_platform_ar_name');
        
        // Retrieve platform name in English from WordPress options
        $this->platform_in_english = get_option('lrs_xapi_platform_en_name');
        
        // Determine user's language
        $this->lang = str_contains(get_locale(), 'ar') ? 'ar-SA' : 'en-US';
        
        // Retrieve user's browser information
        require_once(plugin_dir_path(__FILE__) . '../lib/BrowserDetection.php');

        $browser = new BrowserDetection();
        
            // Assign user's browser operating system
            $this->br_os = $browser->getPlatform();

            // Assign user's browser name
            $this->br_name = $browser->getName();

            // Assign user's browser version
            $this->br_ver = $browser->getVersion();

    }
}

?>