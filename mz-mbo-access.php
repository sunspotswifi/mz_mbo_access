<?php
/*
 * Plugin Name: Mindbody Access Management
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
namespace MZ_MBO_Access;

use MZ_MBO_Access as NS;
use MZ_Mindbody;
use MZ_Mindbody\Inc\Client as Client;
use MZ_Mindbody\Inc\Core as Core;

if ( !defined( 'WPINC' ) ) {
    die;
}

// TODO make more eloquent appoach like EDD JILT work!
//	 * Based on http://wptavern.com/how-to-prevent-wordpress-plugins-from-activating-on-sites-with-incompatible-hosting-environments

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

add_action( 'admin_init', __NAMESPACE__ . '\mbo_access_has_mindbody_api' );

function mbo_access_has_mindbody_api() {
	if ( is_admin() && current_user_can( 'activate_plugins' ) && !is_plugin_active( 'mz-mindbody-api/mz-mindbody.php' ) ) {
		add_action( 'admin_notices', 'mbo_access_child_plugin_notice' );

		deactivate_plugins( plugin_basename( __FILE__ ) ); 

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}

function mbo_access_child_plugin_notice(){
		?><div class="error"><p>Sorry, but MZ MBO Access plugin requires the parent plugin, MZ Mindbody API, to be installed and active.</p></div><?php
}

/**
 * Autoload Classes
 */

require_once( NS\PLUGIN_NAME_DIR . 'inc/libraries/autoloader.php' );

/**
 * Register Activation and Deactivation Hooks
 * This action is documented in inc/core/class-activator.php
 */

register_activation_hook( __FILE__, array( NS . 'Inc\Core\Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented inc/core/class-deactivator.php
 */

register_deactivation_hook( __FILE__, array( NS . 'Inc\Core\Deactivator', 'deactivate' ) );


class MZ_MBO_Access {
	/**
	 * The instance of the plugin.
	 *
	 * @since    2.4.7
	 * @var      Init $init Instance of the plugin.
	 */
	private static $instance;

    /**
     * Main MZ_Mindbody Instance.
     *
     * Insures that only one instance of MZ_Mindbody exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * Totally borrowed from Easy_Digital_Downloads, and certainly used with some ignorance
     * as EDD doesn't actually include a construct in it's class.
     *
     * @since 2.4.7
     * @static
     * @staticvar array $instance
     * @see MZMBO()
     * @return object|MZ_MBO_Access The one true MZ_MBO_Access
     */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof MZ_MBO_Access ) ) {
			self::$instance = new Inc\Core\MZ_MBO_Access;
			self::$instance->run();
		}

		return self::$instance;
	}

}

/**
 * Begins execution of the plugin
 *
 * The main function for that returns MZ_MBO_Access
 *
 * The main function responsible for returning the one true MZ_MBO_Access
 * Instance to functions everywhere.
 *
 * Borrowed from Easy_Digital_Downloads.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $mZmbo = MZ_MBO_Access\MZ_MBO_Access(); ?>
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Also returns copy of the app object so 3rd party developers
 * can interact with the plugin's hooks contained within.
 *
 * @since 1.4
 * @return object|MZ_MBO_Access The one true MZ_MBO_Access Instance.
 **/
function MZ_MBO_Access() {
		return MZ_MBO_Access::instance();
}

$min_php = '7.1';

// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, $min_php, '>=' ) ) {
	
	add_action('init', __NAMESPACE__ . '\mz_mbo_access_plugin_init');

}

function mz_mbo_access_plugin_init(){
	if (defined('MZ_Mindbody\PLUGIN_NAME_DIR')) {
		//plugin is activated, add the hooks
		// Get MZ_MBO_Access Instance.
		MZ_MBO_Access();
	} else {
		\deactivate_plugins( plugin_basename( __FILE__ ) );
		die("Missing Plugin Dependency MZ Mindbody Api.");
	}
}


?>