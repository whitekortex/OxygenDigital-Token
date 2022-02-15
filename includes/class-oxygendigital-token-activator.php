<?php

/**
 * Fired during plugin activation
 *
 * @link       https://oxygendigital.co.nz
 * @since      1.0.4
 *
 * @package    Oxygendigital_Token
 * @subpackage Oxygendigital_Token/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Oxygendigital_Token
 * @subpackage Oxygendigital_Token/includes
 * @author     Oxygen <Digital>
 */
class Oxygendigital_Token_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        /*** Initial setup on save of plugin in wordpress **/
	}

    public function odmt_activate_system_CURL(){
        /*** Initial setup on save of plugin in wordpress **/
        $geturl = site_url();

        $replace = ['http://','https://','www.'];
        $with = '';
        $website_url = str_replace($replace,$with, $geturl);

        $body = array(
            'name' => $website_url, 
            'description' => strtoupper($website_url).' heartbeat monitor for wordpress websites for downtime of more than 10 minutes',
            'interval' => 10,
            'intervalUnit' => 'minutes',
            'enabled' => true,
            'alertMessage' => strtoupper($website_url).' has been down or has not responded for more than 10 minutes. Please investigate!',
            'alertPriority' => 'P3'
        );

        $authAPI    = 'GenieKey 1abe013f-94e5-450d-a6c5-dfa8532f0e16';
        $authJSON   = 'Content-Type: application/json';

        $args = array(
            'body'        => $body,
            'timeout'     => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array( 'Authorization' => $api, 'Content-Type' => $authJSON ),
            'cookies'     => array(),
        );

        $fullURL = 'https://api.opsgenie.com/v2/heartbeats/'.$website_url.'/ping';
        $response = wp_remote_post( $fullURL,  $args );
        $http_code = wp_remote_retrieve_response_code( $response );

    }
}
