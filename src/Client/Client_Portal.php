<?php
namespace MZ_MBO_Access\Client;

use MZ_Mindbody as MZ;
use MZ_MBO_Access as NS; 
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

/*
 * Class that holds Client Interface Methods for Ajax requests
 *
 *
 */
class Client_Portal extends Retrieve_Client {

    /**
     * The Mindbody API Object
     *
     * @access private
     */
    private $mb;

    /**
     * Template Date for sending to template partials
     *
     * @access private
     */
    private $template_data;

    /**
     * Client ID
     *
     * The MBO ID of the Current User/Client
     *
     * @access private
     */
    private $clientID;

    /**
     * Format for date display, specific to MBO API Plugin.
     *
     * @since    1.0.1
     * @access   public
     * @var      string $date_format WP date format option.
     */
    public $date_format;

    /**
     * Format for time display, specific to MBO API Plugin.
     *
     * @since    1.0.1
     * @access   public
     * @var      string $time_format
     */
    public $time_format;

    /**
     * Class constructor
     *
     * Since 2.4.7
     */
    public function __construct(){
        $this->date_format = Core\MZ_Mindbody_Api::$date_format;
        $this->time_format = Core\MZ_Mindbody_Api::$time_format;
        parent::__construct();
    }

    /**
     * Client Log In
     */
    public function ajax_client_log_in(){

        check_ajax_referer($_REQUEST['nonce'], "mz_signup_nonce", false);

        // Create the MBO Object
        $this->get_mbo_results();

        // Init message
        $result['message'] = '';

        $result['type'] = 'success';

        // Parse the serialized form into an array.
        $params = array();
        parse_str($_REQUEST['form'], $params);
        
        if (empty($params) || !is_array($params)) {
        
        	$result['type'] = 'error';
        	
        } else {
        
        	$credentials = ['Username' => $params['email'], 'Password' => $params['password']];
            
        	$login = $this->log_client_in($credentials);
        	
        	if ( $login['type'] == 'error' ) $result['type'] = 'error';
        	        	
			$result['message'] = $login['message'];
			
			$result['client_details'] = $login['deeper_client_info'];
        	        	
			$result['client_id'] = $login['client_id'];

        }
		
		
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();

    }

    /**
     * Client Log Out
     */
    public function ajax_client_log_out(){
    
        check_ajax_referer($_REQUEST['nonce'], "mz_client_log_out", false);

        ob_start();

        $result['type'] = 'success';

        $this->client_log_out();

        // update class attribute to hold logged out status
        $this->client_logged_in = false;

        _e('Logged Out', 'mz-mindbody-api');

        echo '<br/>';

        $result['message'] = ob_get_clean();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }

 

   
    /**
     * Check Client Logged In
     *
     * Function run by ajax to continually check if client is logged in
     */
    public function ajax_check_client_logged(){

        check_ajax_referer($_REQUEST['nonce'], "mz_check_client_logged", false);
        
        $result = array();
        		
        $result['type'] = 'success';
        $result['message'] =  $this->check_client_logged();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }

   
    /**
     * Get Clients
     *
     * Get multiple clients from MBO
     */
    public function ajax_get_clients(){

        check_ajax_referer($_REQUEST['nonce'], "mz_client_request", false);
        
        $result = array();
        		
        $result['type'] = 'success';
        $result['message'] =  $this->get_clients(array($_REQUEST['client_id']));

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }

   
    /**
     * Get Client
     *
     * Like Get Clients (above), but return only the first client.
     */
    public function ajax_get_client_details(){

        check_ajax_referer($_REQUEST['nonce'], "mz_client_request", false);
        
        $result = array();
        		
        $result['type'] = 'success';
        
        $client = $this->get_client($_REQUEST['client_id']);
                
        $result['client'] = $this->update_client_session($client);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }

}