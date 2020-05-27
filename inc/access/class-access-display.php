<?php
namespace MZ_MBO_Access\Inc\Access;

use MZ_MBO_Access as NS;
use MZ_Mindbody as MZ;
use MZ_MBO_Access\Inc\Core as Core;
use MZ_Mindbody\Inc\Client as Client;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Access_Display extends Interfaces\ShortCode_Script_Loader
{

    /**
     * If shortcode script has been enqueued.
     *
     * @since    1.0.0
     * @access   public
     *
     * @used in handleShortcode, addScript
     * @var      boolean $addedAlready True if shorcdoe scripts have been enqueued.
     */
    static $addedAlready = false;
    
    /**
     * Restricted content.
     *
     * @since    1.0.0
     * @access   public
     *
     * @used in handleShortcode, localizeScript
     * @var      string $restricted_content Content between two shortcode tags.
     */
    public $restricted_content;
    
    /**
     * Shortcode attributes.
     *
     * @since    1.0.0
     * @access   public
     *
     * @used in handleShortcode, localizeScript
     * @var      array $atts Shortcode attributes function called with.
     */
    public $atts;

    /**
     * Data to send to template
     *
     * @since    1.0.0
     * @access   public
     *
     * @used in handleShortcode, 
     * @var      @array    $data    array to send template.
     */
    public $template_data;

    /**
     * Status of client login
     *
     * @since    1.0.0
     * @access   public
     *
     * @used in handleShortcode, localizeScript
     * @var      @array    $data    array to send template.
     */
    public $logged_in;

    /**
     * Status of client access
     *
     * @since    1.0.0
     * @access   public
     *
     * @used in handleShortcode, localizeScript
     * @var      @array    $data    array to send template.
     */
    public $has_access;

    /**
     * Membership Types
     *
     * @since    1.0.0
     * @access   public
     *
     * @used in localizeScript
     * @var      @array    $data    array to send template.
     */
    public $membership_types;

    public function handleShortcode($atts, $content = null)
    {

        $this->atts = shortcode_atts(array(
            'siteid' => '',
            'memberships' => '',
            'contracts' => '',
            'purchases' => '',
            'denied_message' => ''
        ), $atts);
        
        $this->atts = $atts;
        
        $this->siteID = (isset($atts['siteid'])) ? $atts['siteid'] : MZ\MZMBO()::$basic_options['mz_mindbody_siteID'];
        
        // Break memberships, contracts, purchases up into array, if it hasn't already been.
        $this->atts['memberships'] = (!is_array($this->atts['memberships'])) ? explode(',', ($this->atts['memberships'])) : $this->atts['memberships'];
        $this->atts['contracts'] = (!is_array($this->atts['contracts'])) ? explode(',', $this->atts['contracts']) : $this->atts['contracts'];
        $this->atts['purchases'] = (!is_array($this->atts['purchases'])) ? explode(',', $this->atts['purchases']) : $this->atts['purchases'];
        
        $this->atts['memberships'] = array_map(trim, $this->atts['memberships']);
        $this->atts['contracts'] = array_map(trim, $this->atts['contracts']);
        $this->atts['purchases'] = array_map(trim, $this->atts['purchases']);
        
        $this->restricted_content = $content;
        
        $this->membership_types = $this->atts['memberships'];
        
        $this->denied_message = (isset($this->atts['denied_message'])) ? $this->atts['denied_message'] : __('Access to this content requires one of',  'mz-mbo-access');

        // Begin generating output
        ob_start();
        
        $template_loader = new Core\Template_Loader();
		
        $this->template_data = array(
            'atts' => $this->atts,
            'content' => $this->restricted_content,
            'login_to_sign_up'  => "Login with your Mindbody account to access this content.",
            'signup_nonce'  => wp_create_nonce('mz_mbo_signup_nonce'),
            'siteID'  => MZ\MZMBO()::$basic_options['mz_mindbody_siteID'],
            'email'  => "email",
            'password'  => "password",
            'login'  => "Login",
            'logout'  => "Logout",
            'logged_in'  => false,
            'access' => false,
            'client_name' => '',
            'membership_types' => $this->atts['memberships'],
            'denied_message' => $this->denied_message,
            'manage_on_mbo'  => "Visit Mindbody Site"
        );	
        
		$access_utilities = new Access_Utilities;
		$has_access = $access_utilities->check_access_permissions($this->atts['memberships']);
				
		if ($has_access) {
			$this->template_data['access'] = true;
			$this->has_access = true;
		 }
		
		$logged = MZ\MZMBO()->client->check_client_logged();
				
		if ($logged) {
		
			$this->template_data['logged_in'] = true;
			$this->logged_in = true;
			$this->template_data['client_name'] = MZ\MZMBO()->session->get('MBO_CLIENT')['FirstName'];
		 	
		} 
		
        $template_loader->set_template_data($this->template_data);
        $template_loader->get_template_part('access_container');

        // Add Style with script adder
        self::addScript();

        return ob_get_clean();
        
    }

	/*
	 * What is this?
	 */
	private function login_form() {
		
        $template_loader = new Core\Template_Loader();
        
        $template_loader->set_template_data($this->template_data);
	}

    public function addScript()
    {
    
        if (!self::$addedAlready) {
            self::$addedAlready = true;
            
            wp_register_style('mz_mindbody_style', MZ\PLUGIN_NAME_URL . 'dist/styles/main.css');
            wp_enqueue_style('mz_mindbody_style');

            wp_register_script('mz_mbo_access_script', NS\PLUGIN_NAME_URL . 'dist/scripts/main.js', array('jquery'), 1.0, true);
            wp_enqueue_script('mz_mbo_access_script');

            $this->localizeScript();

        }
    }

    public function localizeScript()
    {
        
        $protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';

        $translated_strings = MZ\MZMBO()->i18n->get();

        $params = array(
            'ajaxurl' => admin_url('admin-ajax.php', $protocol),
            'login_nonce' => wp_create_nonce('mz_mbo_access_nonce'),
            'atts' => $this->atts,
            'restricted_content' => $this->restricted_content,
            'siteID' => $this->siteID,
            'logged_in' => $this->logged_in,
            'membership_types' => $this->atts['memberships'],
            'has_access' => $this->has_access,
            'denied_message' => $this->denied_message,
            'membership_types' => json_encode($this->membership_types)
        );
        wp_localize_script('mz_mbo_access_script', 'mz_mindbody_access', $params);
    }

    /**
     * Ajax function to return mbo schedule
     *
     * @since 2.4.7
     *
     * This duplicates a lot of the handle_shortcode function, but
     * is called via AJAX and used when navigating the schedule.
     *
     *
     *
     * Echo json json_encode() version of HTML from template
     */
    public function display_schedule()
    {

        check_ajax_referer($_REQUEST['nonce'], "mz_mbo_access_nonce", false);

        $atts = $_REQUEST['atts'];

        $result['type'] = "success";

        $template_loader = new Core\Template_Loader();

        $this->schedule_object = new Retrieve_Schedule($atts);

        // Call the API and if fails, return error message.
        if (false == $this->schedule_object->get_mbo_results()) echo "<div>" . __("Error returning schedule from Mindbody in Access Display.", 'mz-mindbody-api') . "</div>";

        // Register attributes
        $this->handleShortcode($atts);

        // Update the data array
        $this->template_data['time_format'] = $this->schedule_object->time_format;
        $this->template_data['date_format'] = $this->schedule_object->date_format;

        $template_loader->set_template_data($this->template_data);

        // Initialize the variables, so won't be un-set:
        $horizontal_schedule = '';
        $grid_schedule = '';
        if ($this->display_type == 'grid' || $this->display_type == 'both'):
            ob_start();
            $grid_schedule = $this->schedule_object->sort_classes_by_time_then_date();
            // Update the data array
            $this->template_data['grid_schedule'] = $grid_schedule;
            $template_loader->get_template_part('grid_schedule');
            $result['grid'] = ob_get_clean();
        endif;

        if ($this->display_type == 'horizontal' || $this->display_type == 'both'):
            ob_start();
            $horizontal_schedule = $this->schedule_object->sort_classes_by_date_then_time();
            // Update the data array
            $this->template_data['horizontal_schedule'] = $horizontal_schedule;
            $template_loader->get_template_part('horizontal_schedule');
            $result['horizontal'] = ob_get_clean();
        endif;

        $result['message'] = __('Error. Please try again.', 'mz-mindbody-api');

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();

    }

}

?>
