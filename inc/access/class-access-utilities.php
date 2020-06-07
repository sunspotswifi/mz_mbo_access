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
     * Since 2.5.7
     *
     * return true if client account matches received requirements
     * 
     * @param TODO $membership_types string or array of membership types 
     * 
     *
     * @return bool
     */
    public function check_access_permissions( $membership_types = [], $purchase_types = [], $contract_types = [] ){
		
		//$this->compare_client_service_status( $membership_types );
		//$this->compare_client_membership_status( $membership_types );
		if ( 2 === $this->compare_client_contract_status( $contract_types ) ) {
			return 2;
		} else {
			return $this->compare_client_membership_status( $membership_types );
		}
				        
    }
    
    /**
     * Compare Client Membership Status
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
    public function compare_client_membership_status( $membership_types = [] ){
						
		$membership_types = is_array($membership_types) ? $membership_types : [$membership_types];
		
		$memberships = $this->get_active_client_memberships();
		
		if ( false == (bool) $memberships['ClientMemberships'] ) return 0;
		
		MZ\MZMBO()->helpers->log("memberships");
		MZ\MZMBO()->helpers->log(count($memberships['ClientMemberships']));
		MZ\MZMBO()->helpers->log("membership types");
		MZ\MZMBO()->helpers->log($membership_types);
		foreach( $memberships['ClientMemberships'] as $membership ) {
		MZ\MZMBO()->helpers->log("membership");
		MZ\MZMBO()->helpers->log($membership['Name']);
		MZ\MZMBO()->helpers->log(in_array($membership['Name'], $membership_types));
			if ( in_array($membership['Name'], $membership_types) ) return 1;
		}
		
        return 0;
        
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
    public function compare_client_service_status( $membership_types = [] ){
						
		$membership_types = is_array($membership_types) ? $membership_types : [$membership_types];
		
		$services = $this->get_client_services();
		
		MZ\MZMBO()->helpers->log("services");
		MZ\MZMBO()->helpers->log($services);
		
        return 0;
        
    }
    
    /**
     * Compare Client Contract Status
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
    public function compare_client_contract_status( $contract_types = [] ){
						
		$contract_types = is_array($contract_types) ? $contract_types : [$contract_types];
		
		$contracts = $this->get_client_contracts();
		
		if ( false == (bool) $contracts[0]['ContractName'] ) return 0;
		
		MZ\MZMBO()->helpers->log("contracts");
		MZ\MZMBO()->helpers->log(count($contracts['ClientMemberships']));
		MZ\MZMBO()->helpers->log("contract_types types");
		MZ\MZMBO()->helpers->log($contract_types);
		foreach( $contracts as $contract ) {
		MZ\MZMBO()->helpers->log("contract");
		MZ\MZMBO()->helpers->log($contract['ContractName']);
		MZ\MZMBO()->helpers->log(in_array($contract['ContractName'], $contract_types));
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
