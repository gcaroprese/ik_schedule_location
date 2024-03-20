jQuery(document).ready(function ($) {
    jQuery('#ik_prt_message_form').attr('style', 'display:none');

    jQuery('.wp-admin #ik_prt_form').on('click', '#ik_prt_submit_form', function ()
    {
        ik_submit_prt_form();
    });
    
    
    function ik_submit_prt_form(){
        
        var submit_button = jQuery('#ik_prt_submit_form');
        jQuery('#ik_prt_message_form').fadeOut(400);
        submit_button.prop('disabled', true);
        submit_button.addClass('sending_data');
        
        
        var zipcode = jQuery('#ik_prt_zip_code').val();
        var city = jQuery('#ik_prt_city').val();
        var state = jQuery('#ik_prt_state').val();
        var num_positive = parseInt(jQuery('#ik_prt_positive_cases').val());
        var phone = jQuery('#ik_prt_phone').val();
        var email = jQuery('#ik_prt_email').val();

        if (num_positive < 1){
            jQuery('#ik_prt_message_form').text('Wrong # of People Positive');
            jQuery('#ik_prt_message_form').fadeIn(400);
        
            setTimeout(function(){
                jQuery('#ik_prt_message_form').fadeOut(400);
                jQuery('#ik_prt_message_form').text('');
            }, 7000);  
            submit_button.prop('disabled', false);
            return false;
        }
        if (email !== ''){
            var email_check = ik_prt_form_check_email(jQuery('#ik_prt_email'));
            
            if (email_check === false){
                jQuery('#ik_prt_message_form').text('Wrong Email Address');
                jQuery('#ik_prt_message_form').fadeIn(400);
            
                setTimeout(function(){
                    jQuery('#ik_prt_message_form').fadeOut(400);
                    jQuery('#ik_prt_message_form').text('');
                }, 7000);  
                submit_button.prop('disabled', false);
                return false;
            }
        }
        
        if (phone !== ''){
            var phone_check = ik_prt_form_check_phone(jQuery('#ik_prt_phone'));
            
            if (phone_check === false){
                jQuery('#ik_prt_message_form').text('Wrong Phone Number');
                jQuery('#ik_prt_message_form').fadeIn(400);
            
                setTimeout(function(){
                    jQuery('#ik_prt_message_form').fadeOut(400);
                    jQuery('#ik_prt_message_form').text('');
                }, 7000);  
                submit_button.prop('disabled', false);
                
                return false;
            }
        }
        
        
        if (zipcode !== '' && city !== '' && state !== '' && num_positive > 0){
            
    		var data = {
    			action: "ik_prt_location_ajax_insert_form_data",
    			"post_type": "post",
    			"zipcode": zipcode,
    			"city": city,
    			"state": state,
    			"num_positive": num_positive,
    			"admin_panel": 1,
    		};  
    
    		jQuery.post( ik_prt_location_ajaxurl.ajaxurl, data, function(response) {
    			if (response){		
    			    submit_button.removeClass('sending_data');
    			    if (jQuery('.g-recaptcha').length > 0) {
    			        grecaptcha.reset();
    			    }
                    jQuery('#ik_prt_message_form').text(response);
                    jQuery('#ik_prt_message_form').fadeIn(400);
                    
                    if(response.indexOf('Error') < 0){
                        jQuery('#ik_prt_zip_code').val('');
                        jQuery('#ik_prt_city').val('');
                        jQuery('#ik_prt_state').val('');
                        jQuery('#ik_prt_positive_cases').val('');
                    }
                    
                    setTimeout(function(){
                        jQuery('#ik_prt_message_form').fadeOut(400);
                        jQuery('#ik_prt_message_form').text('');
                    }, 7000);
                    
    			}
    		}, "json");    
        } else {
            jQuery('#ik_prt_message_form').text('Complete the required fields.');
            jQuery('#ik_prt_message_form').fadeIn(400);
            submit_button.prop('disabled', false);
            submit_button.removeClass('sending_data');
            setTimeout(function(){
                jQuery('#ik_prt_message_form').fadeOut(400);
                jQuery('#ik_prt_message_form').text('');
            }, 7000);
        }
    }
    
    jQuery('#ik_prt_form').on('focusout', '#ik_prt_zip_code', function ()
    {
        var submit_button = jQuery('#ik_prt_submit_form');
        submit_button.prop('disabled', true);
        var zipcode_input = jQuery(this);
        var zipcode = zipcode_input.val();
        
        if (zipcode !== '' && zipcode !== undefined){
    		var data = {
    			action: "ik_prt_location_ajax_zip_check",
    			"post_type": "post",
    			"zipcode": zipcode
    		};  
    
    		jQuery.post( ik_prt_location_ajaxurl.ajaxurl, data, function(response) {
    			if (response){		
    			    submit_button.prop('disabled', false);
    			    if (response.result != 'OK'){
                        alert(response.result);
                        zipcode_input.val('');
    			    } else {
                        var city = jQuery('#ik_prt_city').val(response.city);
                        var state = jQuery('#ik_prt_state').val(response.state);
    			    }
    			}
    		}, "json");    
            
        }
        
    });
  
});