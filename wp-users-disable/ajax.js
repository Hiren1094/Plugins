 jQuery(document).ready(function() {
     
     
                function validateEmail(email) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
                }
                jQuery("#disableuser").click(function() {

                   
                    var useremail = jQuery("#useremail").val();
                    
                    if(useremail == ''){
                        
                        jQuery('#enteremail').fadeIn().delay(2000).fadeOut('slow');
                       
                        return false;
                    }
                    
                    if(! validateEmail (useremail)){
                        
                         jQuery('#emailnotvalide').fadeIn().delay(2000).fadeOut('slow');
                       
                        return false;
                    }
                    
                    
                    var url = ajaxurl;
                    jQuery.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            action: 'dwul_action_callback',
                            useremail: useremail,
                        },
                        beforeSend: function() {
                            
                            jQuery("#processimage").show();
                        
                        },
                        success: function(response) {
                            
                            
                            
                             if(response == 11){
                              
                              jQuery("#adminroleerror").fadeIn().delay(2000).fadeOut('slow');  
                              jQuery("#processimage").hide();
                              return false;  
                            }
                           
                           if(response == 12){
                              
                              jQuery("#notexit").fadeIn().delay(2000).fadeOut('slow');  
                              jQuery("#processimage").hide();
                              return false;  
                            } 
                            if(response == 15){
                                
                              jQuery("#notinsert").fadeIn().delay(2000).fadeOut('slow');  
                              jQuery("#processimage").hide();
                              return false;  
                            }
                            
                            
                           
                            if(response == 1){
                            location.reload();
                            jQuery("#disableerror").show();
                            jQuery("#useremail").val('');
                            jQuery("#processimage").hide();
                        }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {

                            console.log(textStatus, errorThrown);
                        }
                    });
                    return false;
                });
                
                 jQuery(".customdisableemail td a").click(function() {

                 var acivateid = jQuery(this).attr('id');
                 
                    var url = ajaxurl;
                    jQuery.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            action: 'dwul_enable_user_email',
                            activateuserid: acivateid
                        },
                        beforeSend: function() {
                            

                        },
                        success: function(userresponse) {
                            
                            if(userresponse == 1){
                                
                                 jQuery("#userid"+acivateid ).fadeOut();
                            }
                            
                        },
                        error: function(jqXHR, textStatus, errorThrown) {

                            console.log(textStatus, errorThrown);
                        }
                    });
                    return false;
                });
});
