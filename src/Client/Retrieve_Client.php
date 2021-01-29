<?php
namespace MZ_MBO_Access\Client;

use MZ_Mindbody as MZ;
use MZ_MBO_Access as NS; 
use MZ_MBO_Access\Session as Session;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;
use EAMann\Sessionz as Sessionz;


/*
 * Class that holds Client Interface Methods
 *
 *
 */
class Retrieve_Client extends Interfaces\Retrieve {

    /**
     * The Mindbody API Object
     *
     * @access private
     */
    private $mb;

    /**
     * Client ID
     *
     * The MBO ID of the Current User/Client
     *
     * @access private
     */
    private $clientID;
    
    /**
     * MBO Client
     *
     * GetClient result from MBO
     *
     * @access private
     */
    private $mbo_client;
    
    /**
     * Client Services
     *
     * Services returned from MBO
     *
     * @access private
     */
    private $services;

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
     * Instance of our $_Session.
     *
     * @since    1.0.1
     * @access   public
     * @var      object $session
     */
    public $session;

    /**
     * Class constructor
     *
     * Since 1.0.1
     */
    public function __construct(){
        $this->date_format = Core\MZ_Mindbody_Api::$date_format;
        $this->time_format = Core\MZ_Mindbody_Api::$time_format;
        $this->session = Session\MZ_Access_Session::instance();
    }
    
    /**
     * Client Login – using API VERSION 5!
     *
     * Since 1.0.1
     *
     * @param array $credentials with username and password
     *
     * @return array - result type and message  
     */
    public function log_client_in( $credentials = ['username' => '', 'password' => ''] ){
    
    	$valid_credentials = $this->validate_login_fields($this->sanitize_login_fields($credentials));
    	
		if ($valid_credentials === 2 ){
			return ['type' => 'error', 'message' => __("Badly formed email.", NS\PLUGIN_TEXT_DOMAIN)];
		} else if ($valid_credentials === 3 ){
			return ['type' => 'error', 'message' => __("All Mindbody passwords must contain 8 to 15 characters and must include both letters and numbers.", NS\PLUGIN_TEXT_DOMAIN)];
		}
		
        $validateLogin = $this->validate_client($valid_credentials);

		if ( !empty($validateLogin['ValidateLoginResult']['GUID']) ) {
		
		    // Get Client Details as Well here through second call to API
		    // This is so we can have Payment and other info
		    $deeper_client_info = $this->get_clients([$validateLogin['ValidateLoginResult']['Client']['ID']])[0];
		    
			if ( $this->create_client_session( $validateLogin, $deeper_client_info ) ) {
				return [
				        'type' => 'success', 'message' => __('Welcome', NS\PLUGIN_TEXT_DOMAIN) . ', ' . $validateLogin['ValidateLoginResult']['Client']['FirstName'],
				        'client_id' => $validateLogin['ValidateLoginResult']['Client']['ID'],
				        'deeper_client_info' => $deeper_client_info
				 ];
			}
			return ['type' => 'error', 'message' => sprintf(__('Whoops. Please try again, %1$s.', NS\PLUGIN_TEXT_DOMAIN),
            					$validateLogin['ValidateLoginResult']['Client']['FirstName'])];
		} else {
			// Otherwise error message
			if ( !empty($validateLogin['ValidateLoginResult']['Message'] ) ) {

				return ['type' => 'error', 'message' => $validateLogin['ValidateLoginResult']['Message']];

			} else {
				// Default fallback message.
				return ['type' => 'error', 'message' => __('Invalid Login', NS\PLUGIN_TEXT_DOMAIN) . '<br/>'];

			}
		}
	}
	
	
    /**
     * Validate Client - API VERSION 5!
     *
     * Since 1.0.1
     *
     * @param $validateLoginResult array with result from MBO API
     */
    public function validate_client( $validateLoginResult ){
		
		// Create the MBO Object using API VERSION 5!
        $this->get_mbo_results(5);

		$result = $this->mb->ValidateLogin(array(
			'Username' => $validateLoginResult['Username'],
			'Password' => $validateLoginResult['Password']
		));
				
		return $result;
		
    }
	
	
    /**
     * Get Client
     *
     * Since 1.0.1
     *
     * @param $clientIDs array with result from MBO API
     */
    public function get_clients( $clientIDs ){
		
		// Create the MBO Object using API VERSION 5!
        $this->get_mbo_results();

		$result = $this->mb->GetClients(array(
			'ClientIds' => $clientIDs,
		));
				
		return $result['Clients'];
		
    }
    

    /**
     * Create Client Session
     *
     * Since 2.5.7
     *
     * @param $validateLoginResult array with MBO result
     */
    public function create_client_session( $validateLoginResult, $deeper_client_info ){

		if (!empty($validateLoginResult['ValidateLoginResult']['GUID'])) {
			
			$basic_client_info = MZ\MZMBO()->helpers->array_map_recursive('sanitize_text_field', $validateLoginResult['ValidateLoginResult']['Client']);
			$deeper_client_info = MZ\MZMBO()->helpers->array_map_recursive('sanitize_text_field', $deeper_client_info);
			
			// If validated, create session variables and store
			$client_details = array(
				'mbo_result' => array_merge($basic_client_info, $deeper_client_info)
			);

			$this->session->set( 'MBO_Client', $client_details );

			return true;

		} 

    }

    /**
     * Client Log Out
     */
    public function client_log_out(){
        
        // In case sessions not clearing, look into Sessionz\Manager::initialize();
        $this->session->set( 'MBO_Client', []);
        setcookie('PHPSESSID', false);
        
        return true;
    }

    /**
     * Return MBO Account config required fields with what I think are default required fields.
     *
     * since: 1.0.1
     *
     * return array numeric array of required fields
     */
    public function get_signup_form_fields(){

        // Crate the MBO Object
        $this->get_mbo_results();

        $requiredFields = $this->mb->GetRequiredClientFields();
        
        $default_required_fields = [
        	"Email",
        	"FirstName",
        	"LastName"
        ];
        
        return array_merge($default_required_fields, array_map('sanitize_text_field', $requiredFields['RequiredClientFields']));
    }

    /**
     * Create MBO Account
     */
    public function add_client( $client_fields = array() ){

        // Crate the MBO Object
        $this->get_mbo_results();

		$signup_result = $this->mb->AddClient($client_fields);
		
		return $signup_result;
    
    }
    
    /**
     * Sanitize User Credentials via WP helpers.
     *
     * since: 1.0.1
     *
     * return array of sanitized credentials
     */
    public function sanitize_login_fields( $credentials = array() ) {

    	$credentials['Username'] = sanitize_email($credentials['Username']);
    	$credentials['Password'] = sanitize_text_field($credentials['Password']);
    	
    	return $credentials;
    }
    
    
    /**
     * Verify User Credentials.
     *
     * since: 1.0.1
     *
     * return array of sanitized credentials
     */
    public function validate_login_fields( $credentials = array() ) {
		if (false === filter_var($credentials['Username'], FILTER_VALIDATE_EMAIL)) {
			return 2;
		}
		
		if ( false === $this->verify_mbo_pass()){
			return 3;
		}
		
    	$credentials['Username'] = $credentials['Username'];
    	$credentials['Password'] = $credentials['Password'];
    	
    	return $credentials;
    }
    
     /**
     * Check if MBO pass meets their criteria.
     *
     * since: 1.0.1
     *
     * return bool
     */
    public function verify_mbo_pass( $mbo_password = "" ) {
		
		// "All Mindbody passwords must contain 8 to 15 characters and must include both letters and numbers"
    	$re = '/^[A-Z0-9a-z].{7,14}$/m';

		return preg_match($re, $mbo_password);
    }
    
    
    
    /**
     * Get client details.
     *
     * since: 1.0.1
     *
     * return array of client info from MBO or require login
     */
    public function get_client_details() {
    
    	$client_info = $this->session->get('MBO_Client');
        MZ\MZMBO()->helpers->log("get_client_details get");
        MZ\MZMBO()->helpers->log($client_info);

    	if (! (bool) $client_info->mbo_result) return __('Please Login', 'mz-mindbody-api');
    	
    	return $client_info->mbo_result;
    	
    }
    
    /**
     * Get client active memberships.
     *
     * Memberships will be an array, each of which contain among other stuff:
     *
     * [Name] => Monthly Membership - Gym Access
     *      [PaymentDate] => 2020-05-06T00:00:00
     *      [Program] => Array
     *          (
     *              [Id] => 21
     *              [Name] => Gym Membership
     *              [ScheduleType] => Arrival
     *              [CancelOffset] => 0
     *          )
     * [Remaining] => 1000, etc..
     *
     * since: 1.0.1
     *
     * return array numeric array of active memberships
     */
    public function get_client_active_memberships() {
    
    	$client = $this->get_client_details();

        // Create the MBO Object
        $this->get_mbo_results();
		
		$result = $this->mb->GetActiveClientMemberships(['clientId' => $client->ID]); // UniqueID ??
				
		return $result['ClientMemberships'];
    }
    
    /**
     * Get client account balance.
     *
     * since: 1.0.1
     *
     * This wraps a method for getting balances for multiple accounts, but 
     * we just get it for one.
     *
     * return string client account balance
     */
    public function get_client_account_balance() {
    
    	$client = $this->get_client_details();

        // Create the MBO Object
        $this->get_mbo_results();
		
		// Can accept a list of client id strings
		$result = $this->mb->GetClientAccountBalances(['clientIds' => $client->ID]); // UniqueID ??
		
		// Just return the first (and only) result
		return $result['Clients'][0]['AccountBalance'];
    }
    
    /**
     * Get client contracts.
     *
     * Since 2.5.7
     *
     * Returns an array of items that look like this:
     *
     * [AgreementDate] => 2020-05-06T00:00:00
     * [AutopayStatus] => Active
     * [ContractName] => Monthly Membership - 12 Months
     * [EndDate] => 2021-05-06T00:00:00
     * [Id] => 15040
     * [OriginationLocationId] => 1
     * [StartDate] => 2020-05-06T00:00:00
     * [SiteId] => -99
     * [UpcomingAutopayEvents] => Array
     *     (
     *         [0] => Array
     *             (
     *                 [ClientContractId] => 15040
     *                 [ChargeAmount] => 75
     *                 [PaymentMethod] => DebitAccount
     *                 [ScheduleDate] => 2020-06-06T00:00:00
     *             )
     * etc...
     * [LocationId] => 1
	 * [Payments] => Array
	 * (
	 * 	[0] => Array
	 * 		(
	 * 			[Id] => 158015
	 * 			[Amount] => 75
	 * 			[Method] => 16
	 * 			[Type] => Account
	 * 			[Notes] => 
	 * 		)
	 * 
	 * )
     *
     * return array numeric array of client contracts
     */
    public function get_client_contracts() {
    
    	$client = $this->get_client_details();

        // Create the MBO Object
        $this->get_mbo_results();
		
		$result = $this->mb->GetClientContracts(['clientId' => $client->ID]); // UniqueID ??
				
		return $result['Contracts'];
    }
    
    /**
     * Get client purchases.
     *
     * Since 2.5.7
     *
     * Returns an array of items that look like this:
     * [Sale] => Array
     *     (
     *         [Id] => 100160377
     *         [SaleDate] => 2020-05-06T00:00:00Z
     *         [SaleTime] => 23:46:45
     *         [SaleDateTime] => 2020-05-06T23:46:45Z
     *         [ClientId] => 100015683
     *         [PurchasedItems] => Array
     *             (
     *                 [0] => Array
     *                     (
     *                         [Id] => 1198
     *                         [IsService] => 1
     *                         [BarcodeId] => 
     *                     )
     *             )
     *         [LocationId] => 1
     *         [Payments] => Array
     *             (
     *                 [0] => Array
     *                     (
     *                         [Id] => 158015
     *                         [Amount] => 75
     *                         [Method] => 16
     *                         [Type] => Account
     *                         [Notes] => 
     *                     )
     *             )
     *     )
     * [Description] => Monthly Membership - Gym Access
     * [AccountPayment] => 
     * [Price] => 75
     * [AmountPaid] => 75
     * [Discount] => 0
     * [Tax] => 0
     * [Returned] => 
     * [Quantity] => 1
     *
     * return array numeric array of client purchases
     */
    public function get_client_purchases() {
    
    	$client = $this->get_client_details();

        // Create the MBO Object
        $this->get_mbo_results();
		
		$result = $this->mb->GetClientPurchases(['clientId' => $client->ID]); // UniqueID ??
				
		return $result['Purchases'];
    }
    
    /**
     * Get client services.
     *
     * since: 1.0.1
     *
     * return array numeric array of required fields
     */
    public function get_client_services() {
    
    	$client = $this->get_client_details();

        // Create the MBO Object
        $this->get_mbo_results();
		
		$result = $this->mb->GetClientServices(['clientId' => $client->ID]); // UniqueID ??
				
		return $result;
    }

    /**
     * Create MBO Account
     * since 5.4.7
     *
     * param array containing 'UserEmail' 'UserFirstName' 'UserLastName'
     *
     * return array either error or new client details
     */
    public function password_reset_email_request( $clientID = array() ){

        // Crate the MBO Object
        $this->get_mbo_results();

		$result = $this->mb->SendPasswordResetEmail($clientID);
		
		return $result;
    
    }
    
    
    /**
     * Check Client Logged In
     *
     * Since 2.5.7
     * Is there a session containing the MBO_GUID of current user
     *
     * @return bool
     */
    public function check_client_logged(){
    
		$client_info = $this->session->get('MBO_Client');
		
		if (empty($client_info)) return false;
		
        return ( 1 == (bool) $client_info->mbo_result ) ? 1 : 0;
        
    }

    /**
     * Get API version, create API Interface Object
     *
     * @since 1.0.1
     *
     * @param $api_version int in case we need to call on API v5 as in for client login
     *
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results( $api_version = 6 ){
		
		if ( $api_version == 6 ) {
        	$this->mb = $this->instantiate_mbo_API();
		} else {
			$this->mb = $this->instantiate_mbo_API( 5 );
		}
		
        if ( !$this->mb || $this->mb == 'NO_API_SERVICE' ) return false;

        return true;
    }
    
     /**
     * Return an array of MBO Class Objects, ordered by date, then time.
     *
     * This is a limited version of the Retrieve Classes method used in horizontal schedule
     *
     *
     * @param @type array $mz_classes
     *
     * @return @type array of Objects from Single_event class, in Date (and time) sequence.
     */
    public function sort_classes_by_date_then_time($client_schedule = array()) {

        $classesByDateThenTime = array();

        /* For some reason, when there is only a single class in the client
         * schedule, the 'Visits' array contains that visit, but when there are multiple
         * visits then the array of visits is under 'Visits'/'Visit'
         */
        if (is_array($client_schedule['GetClientScheduleResult']['Visits']['Visit'][0])){
            // Multiple visits
            $visit_array_scope = $client_schedule['GetClientScheduleResult']['Visits']['Visit'];
        } else {
            $visit_array_scope = $client_schedule['GetClientScheduleResult']['Visits'];
        }


        foreach($visit_array_scope as $visit)
        {
            // Make a timestamp of just the day to use as key for that day's classes
            $dt = new \DateTime($visit['StartDateTime']);
            $just_date =  $dt->format('Y-m-d');

            /* Create a new array with a key for each date YYYY-MM-DD
            and corresponding value an array of class details */

            $single_event = new Schedule\Mini_Schedule_Item($visit);

            if(!empty($classesByDateThenTime[$just_date])) {
                array_push($classesByDateThenTime[$just_date], $single_event);
            } else {
                $classesByDateThenTime[$just_date] = array($single_event);
            }
        }

        /* They are not ordered by date so order them by date */
        ksort($classesByDateThenTime);

        foreach($classesByDateThenTime as $classDate => &$classes)
        {
            /*
             * $classes is an array of all classes for given date
             * Take each of the class arrays and order it by time
             * $classesByDateThenTime should have a length of seven, one for
             * each day of the week.
             */
            usort($classes, function($a, $b) {
                if($a->startDateTime == $b->startDateTime) {
                    return 0;
                }
                return $a->startDateTime < $b->startDateTime ? -1 : 1;
            });
        }

        return $classesByDateThenTime;
    }
    
    
    /**
     * Make Numeric Array
     *
     * Make sure that we have an array
     *
     * @param $data
     * @return array
     */
    private function make_numeric_array($data) {

        return (isset($data[0])) ? $data : array($data);

    }


}