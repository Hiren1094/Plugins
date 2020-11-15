<?php

class dwul_user_register_ajax_call_back {

    private $options;
  
   

    /**
     * Holds the values to be used in the fields callbacks
     */

    /**
     * Start up
     */
    public function __construct() {
        
       
        
        add_action('wp_ajax_dwul_action_callback', array($this, 'dwul_action_callback'));
        add_action('wp_ajax_nopriv_dwul_action_callback', array($this, 'dwul_action_callback'));
        add_action('wp_ajax_dwul_enable_user_email', array($this, 'dwul_enable_user_email'));
        add_action('wp_ajax_nopriv_dwul_enable_user_email', array($this, 'dwul_enable_user_email'));
        add_action('admin_enqueue_scripts', array($this, 'dwul_ajax_script'));
        add_action( 'wp_login',   array( $this, 'dwul_disable_user_call_back'), 10, 2 );
        add_filter( 'login_message',array( $this, 'dwul_disable_user_login_message'));
    }

    /**
     * Ajax Action
     */
    public function dwul_action_callback() {

        global $wpdb;
        global $disableemail;
        $exitingarray = array();
        $disableemail = $_REQUEST['useremail'];
        $table_name = $wpdb->prefix . dwul_disable_user_email; 
        $exitingusertbl =  $wpdb->prefix .users; 
        $exitinguserquery = "SELECT user_email FROM $exitingusertbl"; 
        $getexiting = $wpdb->get_col($exitinguserquery);
        
        $user = get_user_by( 'email', $disableemail );
        
        
        if($user->roles[0] == 'administrator'){
            
             $successresponse = "11";
            
        }else{
            
       
            
        foreach ($getexiting as $exitinguser){
            
           $exitingarray[] = $exitinguser;
        
         }
          if(!in_array($disableemail, $exitingarray)){
              
              $successresponse = "12";
              
          }else{
        
         
                $insertdata = $wpdb->insert($table_name, array('useremail' => $disableemail), array('%s'));
                if($insertdata){

                    $successresponse =  "1";

                }else{

                    $successresponse =  "15";
                }
          }
        }
       echo $successresponse;
        die();
    }

    public function dwul_ajax_script() { 

        wp_enqueue_script('user_custom_script', DWUL_PLUGIN_PATH . 'ajax.js');
    }

    public function dwul_disable_user_call_back($user_login, $user = null) {

       
        global $wpdb;
        $array = array();
        $usertable = $wpdb->prefix .dwul_disable_user_email;
        
        if (!$user) {
            $user = get_user_by('login', $user_login);
        }
        if (!$user) {
            // not logged in - definitely not disabled
            return;
        }
     
    
        $query = "SELECT useremail FROM $usertable ";
       
        $get = $wpdb->get_col($query);
       
        foreach ($get as $email){
          
          $result =  get_user_by('email', $email);
         
         $array[] = $result->data->user_login;
        }
        
        
        // Is the use logging in disabled?
        if (in_array($user_login, $array)) {
            // Clear cookies, a.k.a log user out
            wp_clear_auth_cookie();

            // Build login URL and then redirect
            $login_url = site_url('wp-login.php', 'login');
            $login_url = add_query_arg('disabled', '1', $login_url);
            wp_redirect($login_url);
            exit;
        }
    }
    
    public function dwul_disable_user_login_message( $message ) {

		// Show the error message if it seems to be a disabled user
		if ( isset( $_GET['disabled'] ) && $_GET['disabled'] == 1 ) 
			$message =  '<div id="login_error">' . apply_filters( 'ja_disable_users_notice', __( 'User Account Disable', 'ja_disable_users' ) ) . '</div>';

		return $message;
	}
        
    public function dwul_enable_user_email(){
     
     global $wpdb;   
     $tblname = $wpdb->prefix .dwul_disable_user_email; 
     $activateuserid = $_REQUEST['activateuserid'];
     $delquery = $wpdb->query($wpdb->prepare("DELETE FROM $tblname WHERE id = %d",$activateuserid));   
      
     if($delquery){
         
         $response = "1";
     }else{
           
           $response =  "20";
           
       }
       echo $response;
     die();
        
    }    

}
 $wpdru_ajax_call_back = new dwul_user_register_ajax_call_back();
