<?php

/**
 *
 * This plugin provides the ability to only allow "wp-admin" access from mention ip. 
 *
 * @since             1.0.0
 * @package          Allow wp-admin access
 *
 * @wordpress-plugin
 * Plugin Name:       Allow wp-admin access
 * Plugin URI:        http://www.brainvire.com
 * Description:       This plugin provides the ability to only allow "wp-admin" access from mention ip. 
 * Version:           1.0.0
 * Author:            brainvireinfo
 * Author URI:        http://www.brainvire.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
define('AWA_ADMINPAGE_URL', 'awa-wp-admin-option-page');

class awa_admin_all_init {

    public function __construct() {

        add_action('admin_menu', array($this, 'awa_admin_filed_init'));
        add_action('admin_init', array($this, 'awa_plugin_settings'));
        add_filter('authenticate', array($this,'awa_ip'), 10, 3);
    }

    public function awa_admin_filed_init() {

        add_menu_page('Allow wp-admin access Settings', 'Allow wp-admin access Settings', 'administrator', AWA_ADMINPAGE_URL, array($this, 'awa_plugin_settings_page'));
    }

    public function awa_plugin_settings() {
        //register our settings
        register_setting('awa-plugin-settings-group', 'awa-ip-field');
    }

    public function awa_create_ip_field($value) {


        $resip = esc_attr(get_option('awa-ip-field'));

        echo '<tr valign="top">';
        echo '<th scope="row">' . $value['lable'] . '</th>';
        echo ' <td><textarea rows="4" cols="50" id="' . $value['id'] . '" name="' . $value['name'] . '"  >' . $resip . '</textarea></td>';
        echo '</tr>';
    }

    public function awa_form_field() {

        $options = array(
            array("name" => "awa-ip-field",
                "desc" => "Enter Allow ip",
                "id" => "allowip",
                "type" => "textarea",
                "lable" => "Enter Allow Ip",
            ),
        );

        foreach ($options as $value) {

            switch ($value['name']) {


                case "awa-ip-field":
                    $this->awa_create_ip_field($value);
                    break;
            }
        }
    }

    public function awa_plugin_settings_page() {


        echo '<div class="wrap">';
        echo '<h2>Wp-admin Access Allow Setting  </h2>';

        echo '<form method="post" action="options.php">';
        settings_fields('awa-plugin-settings-group');
        do_settings_sections('awa-plugin-settings-group');

        echo ' <table class="form-table">';

        $this->awa_form_field();

        echo '</table>';
        echo '<span style="margin: 0px 0px 0px 220px;" class="note"><b>Note:</b> You have enter comma separated ip for this format.<br><span style="margin: 0px 0px 0px 219px;"><b>Multiple Ip:</b> 195.167.10.17,182.128.10.159,505.256.63</span>'
        . '   <br><span style="margin: 0px 0px 0px 219px;"><b>Single Ip:</b> 195.167.10.17</span></span>';
        submit_button();

        echo '</form>';
        echo '</div>';
    }

    //Blocks access to admin users unless from certain IPs. Regular users may be from anywhere.
    public function awa_ip($user, $name, $pass) {
         $disableip = get_option('awa-ip-field');       
         $req_uri = $_SERVER['REQUEST_URI'];
         
         $allow_ips = explode(",",$disableip);
        
        if ($disableip !=''){
           
        if (!in_array($_SERVER['REMOTE_ADDR'], $allow_ips) && ( preg_match('#wp-admin#', $req_uri))) {
            
          echo 'Access Forbidden', __('<strong>ERROR</strong>: Access Forbidden.');
          die;
        }
        }
    }
                
}

    $restrict_settings_page = new awa_admin_all_init();
