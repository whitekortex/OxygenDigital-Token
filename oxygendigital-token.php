<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://oxygendigital.co.nz
 * @since             1.0.0
 * @package           Oxygendigital_Token
 *
 * @wordpress-plugin
 * Plugin Name:       OxygenDigital Token
 * Plugin URI:        https://jonathanentila@bitbucket.org/jonathanentila/od-web-hook.git
 * Description:       This is for web monitoring purposes only for free version.
 * Version:           1.0.4
 * Author:            Oxygen Digital
 * Author URI:        https://oxygendigital.co.nz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       oxygendigital-token
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.4 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'OXYGENDIGITAL_TOKEN_VERSION', '1.0.4' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-oxygendigital-token-activator.php
 */
function activate_oxygendigital_token() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-oxygendigital-token-activator.php';
    Oxygendigital_Token_Activator::activate();

    /*
     * Activate CURL when plugin activated
     */
    Oxygendigital_Token_Activator::odmt_activate_system_CURL();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-oxygendigital-token-deactivator.php
 */
function deactivate_oxygendigital_token() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-oxygendigital-token-deactivator.php';
    Oxygendigital_Token_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_oxygendigital_token' );
register_deactivation_hook( __FILE__, 'deactivate_oxygendigital_token' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-oxygendigital-token.php';

/**
 * @set all registered fields
 * @since   1.0.1
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-oxygendigital-token-fields.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_oxygendigital_token() {

    $plugin = new Oxygendigital_Token();
    $plugin->run();

}
run_oxygendigital_token();

/**
 * Schedules
 *
 * @param array $schedules
 *
 * @return array
 */
function odmt_db_crontest_schedules( $schedules ) {
    $SetSched = get_option('odmt_set_ID_schedule');
    $schedules['five_minutes'] = array(
        'interval' => $SetSched,
        'display'  => 'Once Every ' . $SetSched . ' Minutes',
    );

    return $schedules;
}
add_filter( 'cron_schedules', 'odmt_db_crontest_schedules', 10, 1 );

if ( ! wp_next_scheduled( 'prefix_hourly_event' ) ) {
    wp_schedule_event( time(), 'five_minutes', 'prefixhourlyevent');       
}
add_action( 'prefixhourlyevent', 'odmt_prefix_do_this_hourly' );

function odmt_prefix_do_this_hourly(){
    Oxygendigital_odmt_Fields::odmt_get_apikeystatus();
}

/**
 * Activate
 * @since      1.0.0
 */
function odmt_db_crontest_activate() {
    if ( ! wp_next_scheduled( 'prefixhourlyevent' ) ) {
        wp_schedule_event( time(), 'five_minutes', 'prefixhourlyevent' );
    }
}
register_activation_hook( __FILE__, 'odmt_db_crontest_activate' );

/**
 * Deactivate
 * @since      1.0.0
 */
function odmt_db_crontest_deactivate() {
    wp_unschedule_event( wp_next_scheduled( 'prefixhourlyevent' ), 'prefixhourlyevent' );
}
register_deactivation_hook( __FILE__, 'odmt_db_crontest_deactivate' );

/*
 * Menu > Settings > OD Web Hook 
 * @since      1.0.0
 */
function odmt_add_settings_page() {
    add_options_page( 
        'OD Web Hook', 
        'OD Web Hook Menu', 
        'manage_options', 
        'od_webhook', 
        'odmt_webhook_render_settings_page' 
    );
}
add_action( 'admin_menu', 'odmt_add_settings_page' );

/* Content */
function odmt_webhook_render_settings_page() {
    Oxygendigital_odmt_Fields::odmt_webhook_render_settings_page_button();
}

add_action('admin_init', 'odmt_webhook_general_section');  
function odmt_webhook_general_section() {  
    add_settings_section(  
        'odmt_settings_section',          // Section ID 
        'Oxygen Digital Hook Tool',       // Section Title
        'odmt_section_options_callback',  // Callback
        'odmt_webhook_plugin'             // What Page? This makes the section show up on the General Settings Page
    );

    add_settings_field(             // odmt_set_ID_schedule 4
        'odmt_set_ID_schedule',     // Option ID
        'Frequency : ',             // Label
        'odmt_set_schedule_time',   // !important - This is where the args go!
        'odmt_webhook_plugin',      // Page it will be displayed
        'odmt_settings_section',    // Name of our section (General Settings)
        array(                      // The $args
            'odmt_set_ID_schedule'  // Should match Option ID
        )  
    );

    add_settings_field(             // API Key
        'odmt_getapi_id',           // Option ID
        'API Key : ',               // Label
        'odmt_api_webname_callback',// !important - This is where the args go!
        'odmt_webhook_plugin',      // Page it will be displayed
        'odmt_settings_section',    // Name of our section (General Settings)
        array(                      // The $args
            'odmt_getapi_id'              // Should match Option ID
        )  
    ); 


    add_settings_field(             // Name
        'odmt_getsite_name',        // Option ID
        'Name : ',                  // Label
        'odmt_name_webname_callback',// !important - This is where the args go!
        'odmt_webhook_plugin',      // Page it will be displayed
        'odmt_settings_section',    // Name of our section (General Settings)
        array(                      // The $args
            'odmt_getsite_name'     // Should match Option ID
        )  
    ); 

    add_settings_field(             // Option 1
        'odtm_heartbeat_response',  // Option ID
        'Set URL : ',               // Label
        'odmt_textbox_callback',    // !important - This is where the args go!
        'odmt_webhook_plugin',      // Page it will be displayed (General Settings)
        'odmt_settings_section',    // Name of our section
        array(                      // The $args
            'odtm_heartbeat_response'  // Should match Option ID
        )  
    );

    add_settings_field(             // Option 3
        'odmt_display_status',      // Option ID
        'API Activated :',          // Label
        'odmt_prefix_do_this_hourly',    // !important - This is where the args go!
        'odmt_webhook_plugin',      // Page it will be displayed
        'odmt_settings_section',    // Name of our section (General Settings)
        array(                      // The $args
            'odmt_display_status'              // Should match Option ID
        )  
    );



    /*
     * Save Settings
     */
    register_setting('odmt_webhook_plugin','odmt_set_ID_schedule', 'esc_attr');  
    register_setting('odmt_webhook_plugin','odmt_getapi_id', 'esc_attr');
    register_setting('odmt_webhook_plugin','odmt_getsite_name', 'esc_attr');
    register_setting('odmt_webhook_plugin','odtm_heartbeat_response', 'esc_attr');
    register_setting('odmt_webhook_plugin','odmt_display_status', 'esc_attr');
  
}
/*
 * @odmt_settings_section
 * Set schedule
 * @since       1.0.0
 */
function odmt_section_options_callback() { 
    echo esc_html_e( 'This tool is for internal use only.', 'oxygendigital-token' );  
}

/*
 * @odmt_set_ID_schedule
 * Set schedule
 *
 * @since     1.0.1
 * @return    string    schedule for the cron
 */
function odmt_set_schedule_time($SetSchedValue) {
    Oxygendigital_odmt_Fields::odmt_get_field_Frequency($SetSchedValue);
}   

/*
 * @odmt_getapi_id
 * Set API Key
 *
 * @since     1.0.1
 * @return    string    schedule for the cron
 */
function odmt_api_webname_callback($args) {
    Oxygendigital_odmt_Fields::odmt_set_apikey_field($args);
}


/*
 * @odtm_heartbeat_response
 * Heartbeat response
 * Text Call Back
 * @since      1.0.1
 */
function odmt_name_webname_callback($args) {
    Oxygendigital_odmt_Fields::odmt_set_name_field($args);
}

/*
 * @odtm_heartbeat_response
 * Heartbeat response
 * Text Call Back
 * @since      1.0.1
 */
function odmt_textbox_callback() {
    Oxygendigital_odmt_Fields::odmt_set_heartbeat_respons_echo();
}