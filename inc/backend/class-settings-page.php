<?php
namespace MZ_MBO_Access\Inc\Backend;

use MZ_Mindbody as MZ;
use MZ_MBO_Access as NS;
use MZ_MBO_Access\Inc\Core as Core;
use MZ_MBO_Access\Inc\Common as Common;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_MBO_Access\Inc\Schedule as Schedule;

/**
 * This file contains the class which holds all the actions and methods to create the admin dashboard sections
 *
 * This file contains all the actions and functions to create the admin dashboard sections.
 * It should probably be refactored to use oop approach at least for the sake of consistency.
 *
 * @since 2.1.0
 *
 * @package MZ_MBO_Access
 *
 */
/**
 * Actions/Filters
 *
 * Related to all settings API.
 *
 * @since  1.0.0
 */

class Settings_Page {

    static protected $wposa_obj;

    public function __construct() {
        self::$wposa_obj = MZ\Inc\Core\MZ_Mindbody_Api::$settings_page::$wposa_obj;
    }

    public function addSections() {
		
		 // Section: Basic Settings.
        self::$wposa_obj->add_section(
            array(
                'id'    => 'mz_mbo_access',
                'title' => __( 'MZ MBO Access Settings', 'mz-mindbody-api' ),
            )
        );
        
        // Field: Regenerate Class Owners
        self::$wposa_obj->add_field(
            'mz_mbo_access',
            array(
                'id'      => 'mbo_access_shortcodes',
                'type'    => 'html',
                'name'    => __( 'Shortcodes and Atts', 'mz-mindbody-api' ),
                'desc'    => $this->access_codes()
            )
        );
    }
    
    private function access_codes(){
        $return = '';
        $return .= '<p>'.sprintf('[%1$s] %2$s [%3$s]', 'mbo-client-access', __("Restricted content here between both tags", 'mz-mindbody-api'), '/mbo-client-access').'</p>';
        $return .= "<ul>";
        $return .= "<li><strong>class_redirect</strong>: " . __("(url string) URL to redirect users to with Class Purchases matching purchases attribute.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>membership_redirect</strong>: " . __("((url string) URL to redirect users to with Membership matching membership attribute. ", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>memberships</strong>: " . __("(string) Comma-separated string of MBO membership types required for access.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>purchases</strong>: " . __("(string) Comma-separated string of MBO purchase types required for access.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>call_to_action</strong>: " . __("(string) Message inviting user to submit form.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>denied_message</strong>: " . __("(string) Message preceding list of items required for access.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>access_expired</strong>: " . __("(string) Message alerting client that access has expired.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>access_level</strong>: " . sprintf(__('(int) Defaults to %1$d, level for Class Purchase, the higher level, %2$d is for Memberships.', 'mz-mindbody-api'), 1, 2)."</li>";
        $return .= "</ul>";
        $return .= sprintf('[%1$s %2$s]%3$s[%4$s]', 'mbo-client-access', 'memberships="Corporate Membership, Monthly Membership - Gym Access"', 'Restricted Content', '/mbo-client-access');
        $return .= '<h3>'. __('Note', 'mz-mindbody-api') . '</h3>';
        $return .= sprintf(__('If %1$s or %2$s are included, the login form will redirect to one of those urls instead of revealing content.', 'mz-mindbody-api'), '<code>class_redirect</code>', '<code>membership_redirect</code>');
        return $return;
    }

}
