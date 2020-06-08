<?php
namespace MZ_MBO_Access\Inc\Access;

use MZ_MBO_Access as NS;
use MZ_Mindbody as MZ;
use MZ_MBO_Access\Inc\Core as Core;
use MZ_Mindbody\Inc\Client as Client;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Access_Utilities extends Client\Retrieve_Client
{

    /**
     * Check Access Permissions
     *
     * Since 1.0.0
     *
     * @return int indicating client access level, 0, 1 or 2.
     */
    public function check_access_permissions( ){
		$result = $this->set_client_access_level();
		return $result;
				        
    }
        
    /**
     * Compare Client Service Status
     *
     * Since 2.5.8
     *
     * return true if active membership matches one in received array (or string)
     * 
     * @param $membership_types string or array of membership types 
     * 
     *
     * @return bool
     */
    public function set_client_access_level( ){
    						
		// TODO can we avoid doing this here AND in access display?
        $mz_mbo_access_options = get_option('mz_mbo_access');
        $level_1_services = explode(',', $mz_mbo_access_options['level_1_services']);
		$level_2_services = explode(',', $mz_mbo_access_options['level_2_services']);        
        $level_1_services = array_map(trim, $level_1_services);
        $level_2_services = array_map(trim, $level_2_services);	
        	
		$services = $this->get_client_services();
		
		MZ\MZMBO($services)->helpers->log($services);
		
		if ( false == (bool) $services['ClientServices'] ) {
			// Update client session with empty keys just in case
			return $this->update_client_session(0, []);
		}
		
		// Comapre level two services first
		foreach( $services['ClientServices'] as $service ) {
			if ( in_array($service['Name'], $level_2_services) ) {
				// No need to check further
				return $this->update_client_session(2, $services['ClientServices']);
			}
		}
		// If not level two do we have level one access?
		foreach( $services['ClientServices'] as $service ) {
			if ( in_array($service['Name'], $level_1_services) ) {
				// No need to check further
				return $this->update_client_session(1, $services['ClientServices']);
			}
		}
				
        return $this->access_level;
        
    }
    
    /**
     * Add Access Level and Services to Client Session
     *
     * Since 1.0.0
     *
     * @param services array of services returned from MBO
     * @param access_level int access level based on admin configuration
     */
     private function update_client_session($access_level, $services){
     		// Don't love that we call the database twice here,
     		// but not sure if there's a better way.
     		$logged_client = MZ\MZMBO()->session->get( 'MBO_Client' );
			$client_details = array(
				'access_level' => $access_level,
				'services' => $services,
				'mbo_result' => $logged_client['mbo_result']
			);
			MZ\MZMBO()->session->set( 'MBO_Client', $client_details );
    }
    /**
     * Compare Client Contract Status
     *
     * Since 1.0.0
     *
     * return true if active membership matches one in received array (or string)
     * 
     * @param $membership_types string or array of membership types 
     * 
     *
     * @return bool
     */
    public function compare_client_contract_status( $contract_types = [] ){
						
		$contract_types = is_array($contract_types) ? $contract_types : [$contract_types];
		
		$contracts = $this->get_client_contracts();
		
		if ( false == (bool) $contracts[0]['ContractName'] ) return 0;
		
		foreach( $contracts as $contract ) {
			if ( in_array($contract['ContractName'], $contract_types) ) return 2;
		}
		
        return 0;
        
    }
    
    
    /**
     * Compare Client Purchase Status
     *
     * Since 2.5.8
     *
     * return true if TODO active membership matches one in received array (or string)
     * 
     * @param TODO $membership_types string or array of membership types 
     * 
     *
     * @return bool
     */
    public function compare_client_purchase_status( $purchase_types = [] ){
						
		$purchase_types = is_array($purchase_types) ? $purchase_types : [$purchase_types];
		
		$purchases = $this->get_client_purchases();
				    					
		if ( false == (bool) $purchases[0]['Sale'] ) return 0;
		
		foreach( $purchases as $purchase ) {
		MZ\MZMBO()->helpers->log("purchase");
		MZ\MZMBO()->helpers->log($purchase);
			if ( in_array($purchase['Description'], $purchase_types) ) return 1;
		}
		
        return 0;
        
    }
    
    // get_client_contracts
    // get_client_purchases

}

?>
