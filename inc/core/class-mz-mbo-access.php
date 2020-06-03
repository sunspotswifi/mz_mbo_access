<?php
namespace MZ_MBO_Access\Inc\Core;

use MZ_MBO_Access as NS;
use MZ_MBO_Access\Inc\Access as Access;

use MZ_MBO_Access\Inc\Backend as Backend;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Client as Client;
use MZ_Mindbody\Inc\Session as Session;
use MZ_Mindbody\Inc\Libraries\Rarst\WordPress\DateTime as DateTime;

/**
 * The core plugin class.
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @link       http://mzoo.org
 * @since      1.0.0
 *
 * @author     Mike iLL/mZoo.org
 */
class MZ_MBO_Access
{
    /**
     * @var MZ_Mindbody_API The one true MZ_Mindbody_API
     * @since 2.4.7
     */
    private static $instance;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @var      Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    2.4.7
     * @access   protected
     * @var      string $plugin_base_name The string used to uniquely identify this plugin.
     */
    protected $plugin_basename;

    /**
     * The current version of the plugin.
     *
     * @since    2.4.7
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Format for date display, specific to MBO API Plugin.
     *
     * @since    2.4.7
     * @access   public
     * @var      string $date_format WP date format option.
     */
    public static $date_format;

    /**
     * Format for time display, specific to MBO API Plugin.
     *
     * @since    2.4.7
     * @access   public
     * @var      string $time_format
     */
    public static $time_format;

    /**
     * Timezone string returned by wordpress get_timezone function.
     *
     * For example 'US/Eastern'
     *
     * @since    2.4.7
     * @access   protected
     * @var      string $timezone PHP Date formatting string.
     */
    public static $timezone;

    /**
     * Wordpress option for start of week.
     *
     * @since    2.4.7
     * @access   protected
     * @var      integer $start_of_week.
     */
    public static $start_of_week;

    /**
     * Initialize and define the core functionality of the plugin.
     */
    public function __construct()
    {

        $this->plugin_name = NS\PLUGIN_NAME;
        $this->version = NS\PLUGIN_VERSION;
        $this->plugin_basename = NS\PLUGIN_BASENAME;
        $this->plugin_text_domain = NS\PLUGIN_TEXT_DOMAIN;

        $this->load_dependencies();
        $this->set_locale();
        // $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->register_shortcodes();
        $this->add_settings_page();

    }

    /**
     * Loads the following required dependencies for this plugin.
     *
     * - Loader - Orchestrates the hooks of the plugin.
     * - Internationalization_I18n - Defines internationalization functionality.
     * - Admin - Defines all hooks for the admin area.
     * - Frontend - Defines all hooks for the public side of the site.
     *
     * @access    private
     */
    private function load_dependencies()
    {
        $this->loader = new Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Internationalization_I18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @access    private
     */
    private function set_locale()
    {

        $plugin_i18n = new Internationalization_I18n($this->plugin_text_domain);

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @access    private
     */
    private function define_admin_hooks()
    {

        /*
         * Additional Hooks go here
         *
         * e.g.
         *
         * //admin menu pages
         * $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
         *
         *  //plugin action links
         * $this->loader->add_filter( 'plugin_action_links_' . $this->plugin_basename, $plugin_admin, 'add_additional_action_link' );
         *
         */
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @access    private
     */
    private function define_public_hooks()
    {
        $access_portal = new Access\Access_Portal;

        // Start Ajax Access Management
        $this->loader->add_action('wp_ajax_nopriv_ajax_login_check_access_permissions', $access_portal, 'ajax_login_check_access_permissions');
        $this->loader->add_action('wp_ajax_ajax_login_check_access_permissions', $access_portal, 'ajax_login_check_access_permissions');
        
        $this->loader->add_action('wp_ajax_nopriv_ajax_check_access_permissions', $access_portal, 'ajax_check_access_permissions');
        $this->loader->add_action('wp_ajax_ajax_check_access_permissions', $access_portal, 'ajax_check_access_permissions');

    }


    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Retrieve the text domain of the plugin.
     *
     * @since     1.0.0
     * @return    string    The text domain of the plugin.
     */
    public function get_plugin_text_domain()
    {
        return $this->plugin_text_domain;
    }

    /**
     * Add our settings page
     *
     * @since     1.0.0
     */
    public function add_settings_page()
    {
        $settings_page = new Backend\Settings_Page();
        $settings_page->addSections();
    }

    /**
     * Registers all the plugins shortcodes.
     *
     * - Events - The Events Class which displays events and loads necessary assets.
     *
     * @access    private
     */
    private function register_shortcodes()
    {
        $Access_Display = new Access\Access_Display();
        $Access_Display->register('mbo-client-access');
    }

}
