<?php
/**
 *
 * Disables user accounts via email address.
 *
 * @since             1.0.0
 * @package           Disable User Login
 *
 * @wordpress-plugin
 * Plugin Name:       Disable User Login
 * Plugin URI:        http://www.brainvire.com/
 * Description:       Disables user accounts via email address.
 * Version:           1.0.0
 * Author:            brainvireinfo
 * Author URI:        http://www.brainvire.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       disable-wp-user-login
 */

define( 'DWUL_PLUGIN_PATH', plugin_dir_url( __FILE__ ) ); 
require_once( dirname(__FILE__) . '/admin-option.php' ); 
require_once( dirname(__FILE__) . '/custom-ajax.php' );
require_once( dirname(__FILE__) . '/create-user-schema.php' );
register_activation_hook(__FILE__,'dwul_install');
