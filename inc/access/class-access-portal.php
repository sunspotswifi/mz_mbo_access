<?php
namespace MZ_MBO_Access\Inc\Access;

use MZ_MBO_Access as NS;
use MZ_Mindbody as MZ;
use MZ_MBO_Access\Inc\Core as Core;
use MZ_Mindbody\Inc\Client as Client;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Access_Portal extends Access_Utilities
{
	
    /**
     * Check Access Permissions
     *
     * Since 2.5.7
     *
     * return true if active membership matches one in received array (or string)
     * 
     * @param $membership_types string or array of membership types 
     * 
     *
     * @return bool
     */
    public function ajax_login_check_access_permissions( ){
    
        check_ajax_referer($_REQUEST['nonce'], "mz_mbo_access_nonce", false);

        // Crate the MBO Object
        $this->get_mbo_results();
		
		$result = array();
		
        // Init message
        $result['logged'] = '';
        
        $result['client_access_level'] = 0;

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
        	        	
			$result['logged'] = $login['message'];

        }		
				
		$membership_types = json_decode(stripslashes($_REQUEST['membership_types']));
				
		$purchase_types = json_decode(stripslashes($_REQUEST['purchase_types']));
				
		$contract_types = json_decode(stripslashes($_REQUEST['contract_types']));
		
		$access_level = $this->check_access_permissions( $membership_types, $purchase_types, $contract_types );
		
		if (  0 !== $access_level ) {
			$result['client_access_level'] = $access_level;
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
     * Check Access Permissions
     *
     * Since 2.5.7
     *
     * return true if active membership matches one in received array (or string)
     * 
     * @param $membership_types string or array of membership types 
     * 
     *
     * @return bool
     */
    public function ajax_check_access_permissions(  ){

        check_ajax_referer($_REQUEST['nonce'], "mz_mbo_access_nonce", false);

        // Crate the MBO Object
        $this->get_mbo_results();
        
        $result = array();

        // Init message
        $result['logged'] = '';
        
        $result['client_access_level'] = 0;

        $result['type'] = 'success';
		
		$access_level = $this->check_access_permissions();
		
		if (  0 !== $access_level ) {
			$result['client_access_level'] = $access_level;
		}

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
