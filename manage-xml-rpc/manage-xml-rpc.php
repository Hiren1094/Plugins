<?php
/*
Plugin Name: Manage XML-RPC
Plugin URI: http://www.brainvire.com
Description: Enable/Disable XML-RPC for IP specific control and disable XML-RPC Pingback method.
Version: 1.0
Author: brainvireinfo
Author URI:  http://www.brainvire.com
License: GPL2
*/


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;
define( 'MXR_PLUGIN_PATH', plugins_url( __FILE__ ) );

// create custom plugin settings menu
add_action('admin_menu', 'mxr_create_menu');

function mxr_create_menu() {

	//create new top-level menu
	add_menu_page('XML-RPC Settings', 'XML-RPC Settings', 'manage_options', 'manage_xml_rpc_page', 'mxr_page_function' ,'dashicons-shield' );

	//call register settings function
	add_action( 'admin_init', 'mxr_register_settings' );
}


function mxr_register_settings() {
	//register our settings
	register_setting( 'manage-xml-rpc-settings-group', 'allowed_ip' );
	register_setting( 'manage-xml-rpc-settings-group', 'allow_disallow' );
	register_setting( 'manage-xml-rpc-settings-group', 'disallowed_ip' );
	register_setting( 'manage-xml-rpc-settings-group', 'allow_disallow_pingback' );
}

function mxr_flush_rewrites() {
 global $wp_rewrite;
 $wp_rewrite->flush_rules();
}
add_action('admin_init', 'mxr_flush_rewrites');

function mxr_page_function() {
?>
<div class="wrap">
<h2>XML-RPC Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'manage-xml-rpc-settings-group' ); ?>
    <?php do_settings_sections( 'manage-xml-rpc-settings-group' ); ?>
	<?php 
		$home_path = get_home_path(); 
		global $wp_rewrite;
	?>
	<table class="form-table">
        		
		<tr valign="top">
        <th scope="row">Disable XML-RPC Pingback: </th>
        <td>
        <input type="checkbox" name="allow_disallow_pingback" value="disallow" <?php echo esc_attr( get_option('allow_disallow_pingback') ) == 'disallow' ? 'checked' : '' ; ?> />
		<span class="description">(recommended) Check this if you want to remove pingback.ping  and pingback.extensions.getPingbacks and X-Pingback from HTTP headers.</span>
		</td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Disable XML-RPC: </th>
        <td>
        <input type="checkbox" name="allow_disallow" value="disallow" <?php echo esc_attr( get_option('allow_disallow') ) == 'disallow' ? 'checked' : '' ; ?> />
		<span class="description">Only check this if you want to block/disable all XML-RPC request.</span>
		</td>
        </tr>
	
		<tr valign="top">
        <th scope="row">Enable XML-RPC for: </th>
        <td>
		<textarea name="allowed_ip" rows="4" cols="60" placeholder="IP comma separated eg. 192.168.10.242, 192.168.10.241"><?php echo esc_attr( get_option('allowed_ip') ); ?></textarea>
		</td>
        </tr>
        <tr valign="top">
        <th scope="row">Disable XML-RPC for : </th>
        <td>
		<textarea name="disallowed_ip" rows="4" cols="60" placeholder="IP comma separated eg. 192.168.10.242, 192.168.10.241"><?php echo esc_attr( get_option('disallowed_ip') ); ?></textarea>
		</td>
        </tr>

    </table>
    
    <?php submit_button(); ?>
	<?php
		add_filter('mod_rewrite_rules', 'mxr_htaccess_contents');
		$existing_rules  = file_get_contents( $home_path . '.htaccess');;
		$new_rules       = mxr_extract_from_array( explode( "\n", $wp_rewrite->mod_rewrite_rules() ), 'Protect XML-RPC' ) ;

		$start = '\# BEGIN Protect XML-RPC';
		$end   = '\# END Protect XML-RPC';
		$htaccess_content = preg_replace('#('.$start.')(.*)('.$end.')#si', '$1 '.$new_rules.' $3', $existing_rules);

		$update_required = ( $new_rules !== $existing_rules );
		$writable = false;
		if ( ( ! file_exists( $home_path . '.htaccess' ) && is_writable( $home_path ) ) || is_writable( $home_path . '.htaccess' ) ) {
			$writable = true;
		}
		
		if ( ! $writable && $update_required ) 
		{ ?>
			<p><?php _e('If your <code>.htaccess</code> file were <a href="https://codex.wordpress.org/Changing_File_Permissions">writable</a>, we could do this automatically, but it isn&#8217;t so these are the mod_rewrite rules you should have in your <code>.htaccess</code> file. Click in the field and press <kbd>CTRL + a</kbd> to select all.') ?></p>
			<p><textarea rows="6" class="large-text readonly" name="rules" id="rules" readonly="readonly"><?php echo  $htaccess_content ; ?></textarea></p>
		<?php 
		}
		?>

</form>
</div>
<?php } 

function mxr_htaccess_contents( $rules )
{
	global $wp_rewrite;
	$home_path = get_home_path();
	$allowed_ips = esc_attr( get_option('allowed_ip') );
	$disallowed_ips = esc_attr( get_option('disallowed_ip') );
	$wp_rewrite->flush_rules( false );
	
	if( esc_attr( get_option('allow_disallow') ) == 'disallow' )
	{
		$new_rules  = '# BEGIN Protect XML-RPC'. "\n";
		$new_rules .= '<Files "xmlrpc.php">'. "\n";
		$new_rules .= 'Order Deny,Allow'. "\n";
		$new_rules .= 'Deny from all'. "\n";
		$new_rules .= '</Files>'. "\n";
		$new_rules .= '# END Protect XML-RPC'. "\n";
	}
	elseif( !empty($allowed_ips))
	{
		$htaccess_allowed_ip = '';
		$ips = explode(",",$allowed_ips);
		foreach ($ips as $ip)
		{
			$ip = trim($ip);
			if(filter_var( $ip, FILTER_VALIDATE_IP) !== false){
				$htaccess_allowed_ip .= "Allow from ".$ip. "\n"; 
			}
		}
		$new_rules  = '# BEGIN Protect XML-RPC'. "\n";
		$new_rules .= '<Files "xmlrpc.php">'. "\n";
		$new_rules .= 'Order deny,allow'. "\n";
		$new_rules .= 'Deny from all'. "\n";
		$new_rules .= $htaccess_allowed_ip;
		$new_rules .= '</Files>'. "\n";
		$new_rules .= '# END Protect XML-RPC'. "\n";
	}
	elseif( !empty($disallowed_ips))
	{
		$htaccess_disallowed_ip = '';
		$ips = explode(",",$disallowed_ips);
		foreach ($ips as $ip)
		{
			$ip = trim($ip);
			if(filter_var( $ip, FILTER_VALIDATE_IP) !== false){
				$htaccess_disallowed_ip .= "Deny from ".$ip. "\n"; 
			}
		}
		$new_rules  = '# BEGIN Protect XML-RPC'. "\n";
		$new_rules .= '<Files "xmlrpc.php">'. "\n";
		$new_rules .= 'Order Deny,Allow'. "\n";
		$new_rules .= $htaccess_disallowed_ip;
		$new_rules .= '</Files>'. "\n";
		$new_rules .= '# END Protect XML-RPC'. "\n";
	}
	
	else{
		$new_rules = '';
	}

	return  $rules ."\n". $new_rules . "\n";

}

add_filter('mod_rewrite_rules', 'mxr_htaccess_contents');

function mxr_extract_from_htaccess( $filename, $marker ) {
	$result = array ();

	if (!file_exists( $filename ) ) {
		//return $result;
	}

	if ( $markerdata = explode( "\n", implode( '', file( $filename ) ) ));
	{
		$state = false;
        foreach ( $markerdata as $markerline ) {
            if (strpos($markerline, '# END ' . $marker) !== false)
                $state = false;
            if ( $state )
                $result[] = $markerline;
            if (strpos($markerline, '# BEGIN ' . $marker) !== false)
                $state = true;
        }
		
	}

	return $result;
}

function mxr_extract_from_array( $inputArray, $marker ) {
	$result = ''."\n";

	if ( empty($inputArray) ) {
		return $result;
	}

	if ( !empty($inputArray))
	{
		$state = false;
        foreach ( $inputArray as $markerline ) {
            if (strpos($markerline, '# END ' . $marker) !== false)
                $state = false;
            if ( $state )
                $result .= $markerline ."\n";
            if (strpos($markerline, '# BEGIN ' . $marker) !== false)
                $state = true;
        }
		
	}
	return $result;
}

function mxr_disable_xmlrpc_pingback( $methods ) {
   unset( $methods['pingback.ping'] );
   unset( $methods['pingback.extensions.getPingbacks'] );
   return $methods;
}

function mxr_remove_X_pingback_header( $headers ) {
   unset( $headers['X-Pingback'] );
   return $headers;
}

function mxr_disable_ping_onpage_load(){
	if( esc_attr( get_option('allow_disallow_pingback') ) == 'disallow' )
	{
		add_filter( 'xmlrpc_methods', 'mxr_disable_xmlrpc_pingback' );
		add_filter( 'wp_headers', 'mxr_remove_X_pingback_header' );
	}
}

add_action('init', 'mxr_disable_ping_onpage_load');
?>