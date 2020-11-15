<?php
/*
  Plugin Name: Custom validation error message - CF7  
  Plugin URI: https://wordpress.org/plugins/custom-validation-error-message-cf7/
  Description: This plugins provide custom error messages for each field in cf7.
  Version: 1.0.0
  Author: Brainvire
  Author URI: https://www.brainvire.com/
 */

define( 'CF7CVEMSG_CURRENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CF7CVEMSG_TEXT_DOMAIN', 'contact-form-7' );
require_once( CF7CVEMSG_CURRENT_PLUGIN_DIR . 'cf7-custom-validation-error-setup-action.php' );