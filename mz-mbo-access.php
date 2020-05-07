<?php
namespace MZ_MBO_Access;

use MZ_Mindbody\Inc\Client as Client;
use MZ_Mindbody as NS;
/*
 * Plugin Name: MZ MBO ACCESS
 * Description: Mike iLL Child Plugin for MZ Mindbody API, which can limit user access to content based on MBO client account details.
 * @package MZMBOACCESS
 *
 * @wordpress-plugin
 * Version: 		1.0.0
 * Author: 			mZoo.org
 * Author URI: 		http://www.mZoo.org/
 * Plugin URI: 		http://www.mzoo.org/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: 	mz-mbo-access
 * Domain Path: 	/languages
*/

if ( !defined( 'WPINC' ) ) {
    die;
}

/**
 * Define Constants
 */

define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );

define( NS . 'PLUGIN_NAME', 'mz-mbo-access' );

define( NS . 'PLUGIN_VERSION', '2.5.7' );

define( NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ) );

define( NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );

define( NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( NS . 'PLUGIN_TEXT_DOMAIN', 'mz-mbo-access' );


class MZ_MBO_Access {

	public function various_tests() {
		return $this->mbo_client_logged();
	}
	
	public function mbo_client_logged() {

		$client_object = new Client\Retrieve_Client;
		
		$logged = $client_object->check_client_logged();
		
		if ($logged) {
		 	return "You are logged in to Mindbody.";
		} else {
			return "You are not logged in to Mindbody.";
		}
	
	}
}

$mbo_access = new MZ_MBO_Access;

add_shortcode('mbo-client-access', [$mbo_access, 'various_tests']);




?>