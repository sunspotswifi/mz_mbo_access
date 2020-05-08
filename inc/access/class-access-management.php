<?php
namespace MZ_MBO_Access\Inc\Access;

use MZ_Mindbody as MZ;
use MZ_MBO_Access\Inc\Core as Core;
use MZ_Mindbody\Inc\Client as Client;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Access_Management extends Interfaces\ShortCode_Script_Loader
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
     * Shortcode content.
     *
     * @since    1.0.0
     * @access   public
     *
     * @used in handleShortcode, localizeScript, display_schedule
     * @var      string $shortcode_content Content between two shortcode tags.
     */
    public $shortcode_content;
    
    /**
     * Shortcode attributes.
     *
     * @since    1.0.0
     * @access   public
     *
     * @used in handleShortcode, localizeScript, display_schedule
     * @var      array $atts Shortcode attributes function called with.
     */
    public $atts;

    /**
     * Data to send to template
     *
     * @since    1.0.0
     * @access   public
     *
     * @used in handleShortcode, display_schedule
     * @var      @array    $data    array to send template.
     */
    public $template_data;

    public function handleShortcode($atts, $content = null)
    {

        $this->atts = shortcode_atts(array(
            'some_attribute' => 'foobar'
        ), $atts);
        
        $this->atts = $atts;
        
        $this->shortcode_content = $content;
		
		$logged = MZ\MZMBO()->client->check_client_logged();
				
		MZ\MZMBO()->helpers->mz_pr(MZ\MZMBO()::$basic_options);
		
		
		if ($logged) {
		 	return "You are logged in to Mindbody. " . $content;
		} else {
	
        // Begin generating output
        ob_start();

        $template_loader = new Core\Template_Loader();

		
        $this->template_data = array(
            'atts' => $this->atts,
            'content' => $this->shortcode_content,
            'login_to_sign_up'  => "Login with your Mindbody account to access this content.",
            'signup_nonce'  => "signup_nonce",
            'siteID'  => "siteID",
            'email'  => "email",
            'password'  => "password",
            'login'  => "login",
            'registration_button'  => "registration_button",
            'manage_on_mbo'  => "manage_on_mbo"
        );

        $template_loader->set_template_data($this->template_data);
        $template_loader->get_template_part('login_form');

        // Add Style with script adder
        self::addScript();

        return ob_get_clean();
		}
    }

	private function login_form() {
		
        $template_loader = new Core\Template_Loader();
        
        $template_loader->set_template_data($this->template_data);
	}

    public function addScript()
    {
        if (!self::$addedAlready) {
            self::$addedAlready = true;

            wp_register_script('mz_mbo_access_script', MZ\PLUGIN_NAME_URL . 'dist/scripts/main.js', array('jquery'), 1.0, true);
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
            'nonce' => wp_create_nonce('mz_mbo_access_nonce'),
            'atts' => $this->atts
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
        if (false == $this->schedule_object->get_mbo_results()) echo "<div>" . __("Mindbody plugin settings error.", 'mz-mindbody-api') . "</div>";

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
