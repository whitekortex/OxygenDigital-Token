<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://oxygendigital.co.nz
 * @since      1.0.4
 *
 * @package    Oxygendigital_Token
 * @subpackage Oxygendigital_Token/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Oxygendigital_Token
 * @subpackage Oxygendigital_Token/fields
 * @author     Oxygen <Digital>
 */
class Oxygendigital_odmt_Fields {


    /*
     * @odmt_set_ID_schedule
     * Set schedule
     *
     * @since     1.0.1
     * @return    string    schedule for the cron
     */
    public function odmt_get_field_Frequency($SetSchedValue) {
        $SetSched = get_option($SetSchedValue[0]);

        switch ( $SetSched ) {
            case "60": $crontime = '1 min'; break;
            case "300": $crontime = '5 mins'; break;
            case "360": $crontime = '6 mins'; break;
            case "420": $crontime = '7 mins'; break;
            case "480": $crontime = '8 mins'; break;
            case "540": $crontime = '9 mins'; break;
            case "600": $crontime = '10 mins'; break;
        }
        ?>

        <select id="<?php echo esc_html( $SetSchedValue[0] ); ?>" name="<?php echo esc_html( $SetSchedValue[0] ); ?>">
            
            <option value="<?php echo esc_html( $SetSched ); ?>"><?php echo esc_html( $crontime  ); ?></option>
            <option value="60">1 minute</option>
            <option value="300">5 minutes</option>
            <option value="360">6 minutes</option>
            <option value="420">7 minutes</option>
            <option value="480">8 minutes</option>
            <option value="540">9 minutes</option>
            <option value="600">10 minutes</option>
        </select>
    <?php
    }   

    /*
     * @odmt_prefix_do_this_hourly
     * Set schedule
     *
     * @since     1.0.1
     * @return    string    api key when saved
     */
    public function odmt_get_apikeystatus() {
        $geturl = site_url();
        $tokenkey = get_option('odmt_getapi_id');
        $api = 'GenieKey ' . $tokenkey;

        /** The recurring script once it is activated */
        $replace = ['http://','https://','www.'];
        $with = '';
        $website_url = str_replace($replace,$with,$geturl);
        
        $args = array(
            'headers' => array(
                'Authorization' => $api )
        );      
        $fullURL = 'https://api.opsgenie.com/v2/heartbeats/'.$website_url.'/ping';
        
        $response = wp_remote_post( $fullURL,  $args );
        $http_code = wp_remote_retrieve_response_code( $response );

        if ( $http_code == 202 ) {
            ?><p style="color:green;">Connected</p>
        <?php } else {
            ?><p style="color:red;">Disconnected</p>
        <?php } 
    }


    public function odmt_set_name_field($args) {
        $setSiteName = get_option($args[0]);
        $siteName = site_url();
        if ( !empty($setSiteName) ) {
            $displaySiteName = $setSiteName;
        } else {
            $displaySiteName = $siteName;
        }
        ?>
        <input type="text" id="<?php echo esc_html( $args[0] ); ?>" name="<?php echo esc_html( $args[0] ); ?>" value="<?php echo esc_html( $displaySiteName ); ?>" /> <p> example "domain.co.nz" only</p>
        <?php
    }

    public function odmt_set_apikey_field($args) {
        $tokenkey = get_option($args[0]);

        ?>
        <input type="text" id="<?php echo esc_html( $args[0] ); ?>" name="<?php echo esc_html( $args[0] ); ?>" value="<?php echo esc_html( $tokenkey ); ?>" />
        <?php
    }

    public function odmt_set_heartbeat_respons_echo() {
        $geturl = get_option('odmt_getsite_name'); // $geturl = site_url();
        $tokenkey = get_option('odmt_getapi_id');
        $api = 'GenieKey ' . $tokenkey;

        $replace = ['http://','https://','www.'];
        $with = '';
        $website_url = str_replace($replace,$with,$geturl);

        $args = array(
            'headers' => array(
                'Authorization' => $api )
        );      
        $fullURL = 'https://api.opsgenie.com/v2/heartbeats/'.$website_url.'/ping';
        
        $response = wp_remote_post( $fullURL,  $args );
        $http_code = wp_remote_retrieve_response_code( $response );

        ?>
        <p><strong>Site URL: </strong><?php esc_html_e( $geturl ); ?></p>
        <p><strong>Api Response : </strong><?php esc_html_e( $http_code ); ?></p>         
        <?php
    }


    public function odmt_webhook_render_settings_page_button() {
        ?>
        <h2>Settings</h2>

        <?php
            echo "\n Current Memory Consumption is <span style='color:green;'> ";
            echo round(memory_get_usage()/1048576,2).''.' MB </span>';
        ?>
        <form action="options.php" method="post">
        <?php 
        settings_fields( 'odmt_webhook_plugin' );
        do_settings_sections( 'odmt_webhook_plugin' ); 
        ?>
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Submit' ); ?>" />
        </form>
        <?php
    }
}