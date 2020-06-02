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
		
		$has_access = false;
		
		$has_access = ( $has_access === false ) ? $this->compare_client_membership_status( $membership_types ) : true;
		
		$has_access = ( $has_access === false ) ? $this->compare_client_purchase_status( $purchase_types ) : true;
		
		//$has_access = ( $has_access === false ) ? $this->compare_client_contract_status( $contract_types ) : true;
		
        return $has_access;
        
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
				
		if ( false == (bool) $memberships['ClientMemberships'] ) return false;
		
		foreach( $memberships['ClientMemberships'] as $membership ) {
			if ( in_array($membership['Name'], $membership_types) ) return true;
		}
		
        return false;
        
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
    
    	MZ\MZMBO()->helpers->print($purchase_types);
						
		$purchase_types = is_array($purchase_types) ? $purchase_types : [$purchase_types];
		
		$purchases = $this->get_client_purchases();
		
    	MZ\MZMBO()->helpers->print($purchases);
    	
    	return false;
				
		if ( false == (bool) $memberships['ClientMemberships'] ) return false;
		
		foreach( $memberships['ClientMemberships'] as $membership ) {
			if ( in_array($membership['Name'], $membership_types) ) return true;
		}
		
        return false;
        
    }
    
    // get_client_contracts
    // get_client_purchases

}

?>
