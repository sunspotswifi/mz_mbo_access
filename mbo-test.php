<?php
/*
Plugin Name: MBO TEST
Plugin URI:  http://link to your plugin homepage
Description: Mike iLL testing new MBO API.
Version:     1.0
Author:      Mike iLL
Author URI:  http://link to your website
License:     GPL2 etc
License URI: https://link to your plugin license

Copyright YEAR Mike iLL (email : your email address)
(Plugin Name) is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
(Plugin Name) is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with (Plugin Name). If not, see (http://link to your plugin license).
*/


// $request = new HttpRequest();
// $request->setUrl('https://api.mindbodyonline.com/public/v6/class/classes');
// $request->setMethod(HTTP_METH_GET);
// 
// $request->setHeaders(array(
//   'authorization' => '{staffUserToken}',
//   'SiteId' => '-99', //43474
//   'Api-Key' => 'a3f5be6229744000b9bc25f603e80c45'
// ));





// curl -X POST \
//   https://api.mindbodyonline.com/public/v6/usertoken/issue \
//   -H 'Content-Type: application/json' \
//   -H 'Api-Key: {yourApiKey}' \
//   -H 'SiteId: {yourSiteId}' \
//   -A '{yourAppName}' \
//   -d '{
//     "Username": "{staffUserName}",
//     "Password": "{staffPassword}"
// }'

//TODO: Test for curl enabled or wordpress requirement
// resource: https://github.com/forestlim12/mindbody_api_php_explore
add_shortcode('accesstoken', 'accesstoken');

function accesstoken( $atts = [], $content = null) {

	$headers = array();
	$headers['Content-Type'] = 'application/json';
	$headers['Api-Key'] = 'a3f5be6229744000b9bc25f603e80c45';
	$headers['Siteid'] = '-99';
	
	$response = wp_remote_post( 'https://api.mindbodyonline.com/public/v6/usertoken/issue', array(
		'method' => 'POST',
		'timeout' => 45,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => $headers,
		'body' => json_encode(array( 'Username' => 'Siteowner', 'Password' => 'apitest1234' )),
    	'cookies' => array()
		)
	);

	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		return "Something went wrong: " . $error_message;
	} else {
		$response_body = json_decode($response['body']);
		
		echo 'Response: <pre>';
		print_r( $response_body );
		echo '</pre>';
		echo 'Access Token: <pre>';
		print_r( $response_body->AccessToken );
		echo '</pre>';
		return;
	}

}

add_shortcode('usersignup', 'usersignup');

function usersignup( $atts = [], $content = null) {

	$headers = array();
	$headers['Content-Type'] = 'application/json';
	$headers['Api-Key'] = 'a3f5be6229744000b9bc25f603e80c45';
	$headers['Siteid'] = '-99';
	
	$response = wp_remote_post( 'https://api.mindbodyonline.com/public/v6/client/requiredclientfields', array(
		'method' => 'GET',
		'timeout' => 45,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => $headers,
		'body' => array( 'Username' => 'Siteowner', 'Password' => 'apitest1234' ),
    	'cookies' => array()
		)
	);

	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		return "Something went wrong: " . $error_message;
	} else {
		$response_body = json_decode($response['body']);
		
		$requiredFields = $response_body->RequiredClientFields;
		
		$requiredFieldsInputs = '';

        if(!empty($requiredFields)) {

            // Force single element $requiredFields into array form
            if (!is_array($requiredFields)){

                $requiredFields = array($requiredFields);
            }

            foreach($requiredFields as $field) {

                $requiredFieldsInputs .= "<label for='$field'>{$field}</label> <input type='text' name='data[Client][$field]' id='$field' required /><br />";

            }
        }
        echo 'Current PHP version: ' . phpversion();
        echo "<form>";
        echo $requiredFieldsInputs;
        echo "</form>";
		echo 'Response: <pre>';
		print_r( $response_body );
		echo '</pre>';
		return;
	}

}
?>