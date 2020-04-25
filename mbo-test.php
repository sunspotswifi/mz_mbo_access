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

define('MB_API_KEY', 'a3f5be6229744000b9bc25f603e80c45'); // INSERT_API_KEY_HERE
define('MB_SITE_ID', 'a3f5be6229744000b9bc25f603e80c45'); // INSERT_API_KEY_HERE


$endpoint_classes = 'https://api.mindbodyonline.com/public/v6/class/classes';


//add_shortcode('accesstoken', 'accesstoken');

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

function get_accesstoken( $atts = [], $content = null) {

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
		return False;
	} else {
		$response_body = json_decode($response['body']);
		return $response_body->AccessToken;
	}

}

// add_shortcode('usersignup', 'usersignup');

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

add_shortcode('classesToday', 'classesToday');


class DateRange
{
  var $start_date, $end_date;

  public function __construct() {}
  public function __destruct()  {}

  public function set_to_one_week()
  {
    date_default_timezone_set('America/New_York');

    $this->start_date = date('Y/m/d');
    $this->end_date   = date('Y/m/d', strtotime('+6 days'));
  } 
}

class DanceClass
{
  const URL_PREFIX = 'https://clients.mindbodyonline.com/classic/ws?studioid='.MB_SITE_ID.'&classid=';

  var $id, $name, $dateTime, $day, $start_time, $end_time, $instructor, $description;

  public function __construct() {}
  public function __destruct()  {}

  public function parse($class_data)
  {
    $this->id          = $class_data['ClassScheduleId'];
    $this->class_name  = $class_data['ClassDescription']['Name'];
    $this->dateTime    = $class_data['StartDateTime'];
    $this->day         = date('D', strtotime($this->dateTime));
    $this->start_time  = substr($this->dateTime, 11, 5);
    $this->end_time    = substr($class_data['EndDateTime'], 11, 5);
    $this->description = $class_data['ClassDescription']['Description'];
    $this->instructor  = $class_data['Staff']['FirstName'] . ' ' .
                         $class_data['Staff']['LastName'];
  }

  public function toString()
  {
    return $this->id    . ' | ' . 
      $this->day        . ' '   .
      $this->result     . ' '   .
      $this->start_time . '-'   .
      $this->end_time   . ' '   .
      $this->class_name . ' by '.
      $this->instructor . ' '   .
      '<br />';
  }

  public function toLinks()
  {
    return '<a href="'.self::URL_PREFIX.$this->id.'">'.
      $this->day        . ' '   .
      $this->start_time . '-'   .
      $this->end_time   . ' '   .
      $this->class_name . ' by '.
      $this->instructor . ' '   .
      '</a><br />';
  }

  public function toRow()
  {
    return '<tr>'.
      '<td><a href="'.self::URL_PREFIX.$this->id.'">Sign Up</a></td>'.
      '<td>'.$this->day.'</td>'.
      '<td>'.$this->start_time.'-'.$this->end_time.'</td>'.
      '<td>'.$this->class_name.'</td>'.
      '<td>'.$this->instructor.'</td>'.
      "</tr>\r\n";
  }
}

class MindbodySchedule
{
  // member variables
  var $schedule;

  public function __construct()
  { 
  	$this->schedule = [];
  }

  public function __destruct() {}

  function get_data($range)
  {	  
		$headers = array();
		$headers['Content-Type'] = 'application/json';
		$headers['Authorization'] = get_accesstoken();
		$headers['Api-Key'] = MB_API_KEY;
		$headers['Siteid'] = '-99';

		$response = wp_remote_post( 'https://api.mindbodyonline.com/public/v6/class/classes?'.
			'StartDateTime='.$range->start_date.'&EndDateTime='.$range->end_date.
			  '&HideCanceledClasses=true', array(
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
			return $this->get_classes($response);
		}
	}

  function get_classes($response)
  {
    $data        = json_decode($response['body'], true);
    $classes     = $data["Classes"];
    $num_classes = count($classes);
		
    for($x = 0; $x < $num_classes; $x++)
    {
      $class = new DanceClass();
      $class->parse($classes[$x]);
      array_push($this->schedule, $class);
    }

    usort($this->schedule, function ($a, $b)
    {
      if ($a->dateTime == $b->dateTime) {
        return 0;
      }

      return ($a->dateTime < $b->dateTime) ? -1 : 1;
    });
  }

  function toString()
  {
    $num_classes = count($this->schedule);

    for($x = 0; $x < $num_classes; $x++)
    {
      echo $this->schedule[$x]->toString();
    }
  }

  function toLinks()
  {
    $num_classes = count($this->schedule);

    for($x = 0; $x < $num_classes; $x++)
    {
      echo $this->schedule[$x]->toLinks();
    }
  }

  function toTable()
  {
    $num_classes = count($this->schedule);

    if (0 < $num_classes)
    {
      $result = '<table style="border: 1px solid black;">'."\r\n".
           "  <tr><th>Register</th><th>Day</th><th>Time</th><th>Class</th><th>Instructor</th></tr>\r\n";

      for($x = 0; $x < $num_classes; $x++)
      {
        $result .= $this->schedule[$x]->toRow();
      }

      $result .= "</table>\r\n";
    }
  }
}

add_shortcode('classesToday', 'classesToday');

function classesToday( $atts = [], $content = null) {

	$range = new DateRange;
	$range->set_to_one_week();

	$schedule = new MindbodySchedule;
	$schedule->get_data($range);

	// return $schedule->toString();
	return $schedule->toLinks();
	return $schedule->toTable();

}
?>