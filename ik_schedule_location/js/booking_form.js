jQuery(document).ready(function ($) {
    jQuery('#ik_prt_message_form').attr('style', 'display:none');

    jQuery('#ik_prt_form').on('click', '#ik_prt_submit_form', function ()
    {
        ik_submit_prt_form();
    });
    
    if (jQuery('#recaptcha_data_confirm').length){
        setInterval(function(){
            if (jQuery('#recaptcha_data_confirm').val() == 'done'){
                jQuery('#recaptcha_data_confirm').val('');
                ik_submit_prt_form(true);
            }
        }, 1000);
    }
    
    
    function ik_submit_prt_form(recaptchaInvisible = false){
        
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
        var recaptcha = jQuery('#ik_prt_form #g-recaptcha-response').val();
        
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
            
            if (jQuery('#ik_prt_form .g-recaptcha').attr('data-size') === "invisible" && recaptchaInvisible !== true){
                if (!grecaptcha.getResponse()) {
                    grecaptcha.reset();
                    grecaptcha.execute();
                    submit_button.prop('disabled', false);
    			    submit_button.removeClass('sending_data');
                    return false;
                }   
            }
            
    		var data = {
    			action: "ik_prt_location_ajax_insert_form_data",
    			"post_type": "post",
    			"zipcode": zipcode,
    			"city": city,
    			"state": state,
    			"num_positive": num_positive,
    			"phone": phone,
    			"email": email,
    			"recaptcha": recaptcha,
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
                        jQuery('#ik_prt_phone').val('');
                        jQuery('#ik_prt_email').val('');
                        var button_min_disabled = true;
                    } else {
                        var button_min_disabled = false;
                    }
                    
                    setTimeout(function(){
                        jQuery('#ik_prt_message_form').fadeOut(400);
                        jQuery('#ik_prt_message_form').text('');
                    }, 7000);
                    
                    
                    if (jQuery('#ik_prt_form_emailgodaddy form').length && email !== ''){
                        jQuery('#ik_prt_form_emailgodaddy form input[data-label=Email]').val(email);
                        jQuery('#ik_prt_form_emailgodaddy form').submit();
                    } 
                    
                    if (button_min_disabled === true){
                        setTimeout(function(){
                            submit_button.prop('disabled', false);
                        }, 60000);  
                    } else {
                        submit_button.prop('disabled', false);
                    }
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
    
    jQuery('#ik_prt_form').on('focusout', '#ik_prt_phone', function (){
        var phoneNumberInput = jQuery(this);
        ik_prt_form_check_phone(phoneNumberInput);
    });
    
    jQuery('#ik_prt_form').on('focusout', '#ik_prt_email', function (){
        var email_element = jQuery(this);
        
        ik_prt_form_check_email(email_element);
    });
    
    function ik_prt_form_check_email(email_element){
        var email = email_element.val();
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    
        if (!filter.test(email)) {
            email_element.val('Wrong Email');
            setTimeout(function(){
                email_element.val('');
            }, 1500);
            return false;
        } else {
            return true;
        }

    }
    
    function ik_prt_form_check_phone(phoneNumberInput){
        var phoneNumber = phoneNumberInput.val();
        if (phoneNumber !== ''){
            var phoneistrue = true;
            phoneNumber = phoneNumber.replace(/[^0-9]+/g, "");
            phoneNumber = phoneNumber.replace("-", "");
            if (phoneNumber.length == 10){
                if (phoneNumber.length == 10){ // 3524102921
                    var first3Numbers = phoneNumber.substr(0, 3);
                    var second3Numbers = phoneNumber.substr(3, 3);
                    var last4Numbers = phoneNumber.substr(6, 4);
                    var phone_formated = '('+first3Numbers+') '+second3Numbers+'-'+last4Numbers; // (352) 410-2921
                } else {
                    jQuery(phoneNumberInput).val('Wrong Number');
                    var phoneistrue = false;
                    setTimeout(function(){
                        jQuery(phoneNumberInput).val('');
                    }, 1500);
                }
                
                if (phoneistrue == true){
                    jQuery(phoneNumberInput).val(phone_formated);
                    return true;
                } else {
                    return false;
                }
                
            } else {
                jQuery(phoneNumberInput).val('Wrong Number');
                setTimeout(function(){
                    jQuery(phoneNumberInput).val('');
                }, 1500);
                return false;
            }
        }
        
        return false;

    }
    jQuery(function() {
        var date = new Date();
        var dayNo = date.getDay();
        var mindate = (7 - dayNo);
        var d = ["sun", "mon", "tue", "wed", "th", "fr", "sat" ];
             
        jQuery(".ik_sch_book .datepicker").datepicker({
            dateFormat: "yy-mm-dd",
            firstDay: 1,
            minDate: mindate,
            onSelect: function(dateText, inst) {
              var today = new Date(dateText);
              console.log(d[today.getDay()]);
              jQuery(".ik_sch_book .datepicker").val(dateText + "  " +d[today.getDay()]);//If you only want day remove dateText concat part
            }
        
        });
    });
    jQuery(function() {
        jQuery(".ik_sch_book .datepicker").datepicker({
            minDate: 0, // ban dates in the past
            maxDate: "+1M" // allow only 1 month in the future
        });
    });
    var array = ["2019-09-14","2019-09-15","2019-09-16"]
    
    jQuery(".ik_sch_book .datepicker").datepicker({
        beforeShowDay: function(date){
            var string = jQuery.datepicker.formatDate("yy-mm-dd", date);
            return [ array.indexOf(string) == -1 ]
        }
    });
    jQuery('.ik_sch_book .timepicker').timepicker({
        'minTime': '09:00',
        'maxTime': '19:30',
        'interval': 15,
        'lang': 'decimal',
        'show2400': true,
        'timeFormat': 'HH:mm',
        'showDuration': true
    });
  
});