<?php


class dwul_User_Register_Bloack {

    private $options;

    /**
     * Holds the values to be used in the fields callbacks
     */

    /**
     * Start up
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'dwul_add_plugin_setting_page'));
        add_shortcode('disable-email-list', array($this,'dwul_disable_email_list'));
        add_shortcode('exituseremail', array($this,'dwul_exiting_user_email_list')); 
       
    }

    /**
     * Add options page
     */
    public function dwul_add_plugin_setting_page() {
        // This page will be under "Settings"
        add_options_page(
                'Settings Admin', 'Block User', 'manage_options', 'dwul-block-user-setting', array($this, 'dwul_create_admin_page_form')
        );
    }
    
      
    /*
     * Create Form
     *  
     */
   public function dwul_create_disableuser_field($value){
      
         echo '<label for="' . $value['lable'] . '" style="font-weight: bold; color: rgb(35, 40, 45); font-size: 15px;margin: 0 0 0 -87px;">' . $value['lable'] . ' </label><input type="' . $value['type'] . '" id="' . $value['id'] . '" name="' . $value['name'] . '" value="" style="margin: 12px 0px 0px 50px;" />';
       }
    
   public function dwul_create_submit_buton($value){
          
         echo '<a class=" button-primary"  id="' . $value['id'] . '"  style="float: left; margin: 75px 0px 0px;" />Disableuser</a><br/>';
        
             
         }  
         
     
    
   public function dwul_error_message(){
       
         echo '<span id = "adminroleerror" style="color:red;margin: 0px 0px 0px 83px;display:none;">Not a disable user because this is administrator!..</span>';   
         echo '<span id = "disableerror" style="color:red;margin: 0px 0px 0px 83px;display:none;">User hase been disable!...</span>';
         echo '<span id = "notexit" style="color:red;margin: 0px 0px 0px 85px;display:none;">Not a any exiting email please check all user email..</span><br/>';
         echo '<span id = "enteremail" style="color:red;margin: 0px 0px 0px 85px;display:none;">Please Enter User Email..</span><br/>';
         echo '<span id = "emailnotvalide" style="color:red;margin: 0px 0px 0px 83px;display:none;">User Email Are Not Valid..</span>'; 
         echo '<span id = "notinsert" style="color:red;margin: 0px 0px 0px 83px;display:none;">User Hase Been Not Disable..</span>';
   }                     
     public function dwul_form() {
       
        $options = array(
                    array("name" => "useremail",
                        "desc" => "Enter Useremail",
                        "id" => "useremail",
                        "type" => "text",
                        "lable" => "Enter User Email",
                      ),
                       array("name" => "disableuser",
                        "desc" => "Disable User.",
                        "id" => "disableuser",
                        "type" => "submit",
                        "value"=> "Disableuser"
                      ),
                   
                );

                foreach ($options as $value) {

                    switch ($value['name']) {


                        case "useremail":
                            $this->dwul_create_disableuser_field($value);
                            break;
                        case "disableuser":
                            $this->dwul_create_submit_buton($value);
                            $this->dwul_error_message();
                            echo '<img style="margin-top: 14px; margin-left: 19px;display:none;" id="processimage" src= "'. DWUL_PLUGIN_PATH.'/images/loaderimage.gif">';
                            break;
                        
                    }
                }
       
    }
    
    /**
     * Options page callback
     */
    public function dwul_create_admin_page_form() {
        
        echo '<div class="wrap">';
        echo ' <h2>Disable User</h2>';
        
         echo '<form method="post" action="">';
          
          $this->dwul_form();
            
          echo'</form>';
            
        echo '</div>';  
        echo '<div style="float:left;width:100%">';
        echo '<div style = "float:left; width:50%">';
         echo do_shortcode('[disable-email-list]'); 
         echo '</div><div style = "float:left; width:50%">';
         echo do_shortcode('[exituseremail]');
        echo'</div></div>'; 
    }
    
    /*
     * User Listing
     */
    
    public function dwul_disable_email_list(){
        
        global $wpdb;
        $output = "";
        
        $output.= "<table class='customdisableemail widefat' style='margin: 74px 0 0;width: 100%;display: block;height: 400px;  overflow: auto;'>";
                    
                    $output.= "<thead>";
                         $output.= "<tr>";
                         $output.= "<th>Disable List</th>";
                         $output.= "</tr>";
                        $output.= "<tr>";
                                $output.= "<th scope='col' class='manage-column column-name'>ID</th>";
                                $output.= "<th scope='col' class='manage-column column-name' >Email</th>";
                                $output.= "<th scope='col' class='manage-column column-name' >Remove </th>";
                         $output.= "</tr>";
                   $output.= "</thead>";   
                         
                   
                   $output.= "<tbody>";   
       
                         $tblname = $wpdb->prefix .'dwul_disable_user_email';   
                         $getQuery = "SELECT * FROM $tblname";
                         $getresult = $wpdb->get_results($getQuery);
                           if(count($getresult) > 0 ){
                         foreach ($getresult as $result){
                        
                         
                             $output.= "<tr id='userid".$result->id."'>";
                             
                                    
                                        
                                      $output.= "<td>".$result->id."</td>";
                                      $output.= "<td>".$result->useremail."</td>";
                                      $output.= "<td><a href='javascript:void(0)' id=".$result->id.">Enable User</a></td>";
                                      
                            $output.= "</tr>";
                           
                           
                         }
                         } else{
                             
                               $output.= "<tr><td>No record found..</td></tr>";
                           }
                            
              
                     $output.= "</tbody>";    
        $output.= "</table>";
        
        
        echo $output;
        
        
    }
    
    /* 
     * Exiting User Table
     */
    public function dwul_exiting_user_email_list(){
        
        global $wpdb;
        $exitingoutput = '';
         $exitingoutput.= "<table class=' widefat' style='margin: 74px 0 0;width: 100%;display: block;height: 400px;  overflow: auto;'>";
                    
                    $exitingoutput.= "<thead>";
                         $exitingoutput.= "<tr>";
                         $exitingoutput.= "<th>Exiting User Email</th>";
                         $exitingoutput.= "</tr>";
                        $exitingoutput.= "<tr>";
                                $exitingoutput.= "<th scope='col' class='manage-column column-name'>ID</th>";
                                $exitingoutput.= "<th scope='col' class='manage-column column-name' >Email</th>";
                                $exitingoutput.= "<th scope='col' class='manage-column column-name' >User Role</th>";
                               
                         $exitingoutput.= "</tr>";
                   $exitingoutput.= "</thead>";   
                         
                   
                   $exitingoutput.= "<tbody>";   
       
                        $args = array(
                                   
                                    'orderby'      => 'login',
                                    'order'        => 'ASC',
                                    'fields'       => 'all',
                                    
                             ); 
                      $user_query =  get_users( $args ); 
                 
                         foreach ($user_query as $exiting){
                          
                             
                             $exitingoutput.= "<tr>";
                                     $exitingoutput.= "<td>".$exiting->ID."</td>";
                                     $exitingoutput.= "<td>".$exiting->user_email."</td>";
                                     $exitingoutput.= "<td>".$exiting->roles[0]."</td>";
                                    
                            $exitingoutput.= "</tr>";
                         }
                         
                            
              
                     $exitingoutput.= "</tbody>";    
        $exitingoutput.= "</table>";
        
        echo $exitingoutput;
    }
            

}

if (is_admin())
$wpdru_settings_page = new dwul_User_Register_Bloack();

