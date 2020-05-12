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
    public function ajax_check_access_permissions( $membership_types = [] ){

        check_ajax_referer($_REQUEST['nonce'], "mz_access_nonce", false);

        // Crate the MBO Object
        $this->get_mbo_results();

        $result['type'] = 'success';
				
		if ( $this->check_access_permissions() ) {
			$result['message'] = 'granted';
		} else {
			$result['message'] = 'denied';
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
