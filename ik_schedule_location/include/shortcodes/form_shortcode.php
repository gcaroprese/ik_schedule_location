<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/* 
Book - Schedule Locatons Form Shortcode
Created: 06/08/2022
Last Update: 08/12/2022
Author: Gabriel Caroprese
*/

//Form form_id
function ik_sch_book_location_form(){
    
    $form_id = 'ik_sch_book'.strtotime(date("Y-m-d H:i:s"));
    
    $booking = new Ik_Schedule_Booking();

    $format_datetime = $booking->available_days->get_datetime_format();
    $format_date = $format_datetime['format_date'];
    $format_time = $format_datetime['format_time'];
    $time_frame = $format_datetime['time_frame'];
    $showampm = ($format_time == '24') ? 'HH:mm' : 'hh:mm p';
    $show24hs = ($format_time == '24') ? 'true' : 'false';

    $output = '

    <style>
    .ik_sch_book .row {
        --bs-gutter-x: 0.4rem;
        margin-bottom: 7px;
    }
    .ik_sch_book .branches-form{
        width: 100%;
        display: grid;
        max-width: 400px;
        margin: 7px auto;
    }
    .ik_sch_book .branches-form select, .ik_sch_book input:not(.ik_sch-submit), .ik_sch_book textarea{
        padding: 7px 15px 7px 15px;
        width: 100%;
    }
    .ik_sch_book .ik_sch_field_services select{
        margin-left: 2px;
        width: calc(100% - 198px);
    }
    .ik_sch_book .ik_sch_field_services button{
        padding: 12px 13px;
        width: 190px;
    }
    </style>
    <div role="form" class="ik_sch_book" id="'.$form_id.'" status="start">
        <form action="/termin-vereinbaren/#'.$form_id.'" method="post" class="ik_sch_book_form">
            <div class="container">
                <div class="row">
                    <div class="branches-form">
                    '.$booking->locations->get_location_select().'
                    </div>                
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="ik_sch_field-wrap" data-name="name">
                            <input type="text" name="name"  placeholder="'. __( 'Fist Name', 'ik_schedule_location').' *" value="" class="ik_sch_field ik_sch-text ik_sch-validates-as-required cf7-fa-icon-fa-user" aria-required="true" aria-invalid="false">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ik_sch_field-wrap" data-name="lastname">
                            <input type="text" name="lastname" placeholder="'. __( 'Last Name', 'ik_schedule_location').' *" value="" class="ik_sch_field ik_sch-text ik_sch-validates-as-required" aria-required="true" aria-invalid="false">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="ik_sch_field-wrap" data-name="email">
                            <input type="email" name="email" placeholder="'. __( 'Email', 'ik_schedule_location').' *" value="" class="ik_sch_field ik_sch-text ik_sch-email ik_sch-validates-as-required" aria-required="true" aria-invalid="false">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ik_sch_field-wrap" data-name="phone">
                            <input type="text" name="phone" value="" class="ik_sch_field ik_sch-text" aria-required="true" aria-invalid="false" placeholder="'. __( 'Phone', 'ik_schedule_location').' *">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="ik_sch_field-wrap ik_sch_field_services" data-name="services">
                        <select name="services" class="ik_sch_field ik_sch-select ik_sch-validates-as-required select2-hidden-accessible" aria-required="true" aria-invalid="false" tabindex="-1" aria-hidden="true">
                            <option>'. __( 'Select Service', 'ik_schedule_location').' *</option>
                        </select>
                        <button class="ik_sch_field_add_services">+ '. __( 'Add Service', 'ik_schedule_location').'</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="ik_sch_field-wrap" data-name="date">
                            <input type="text" disabled name="date" value="" placeholder="'. __( 'Select Date', 'ik_schedule_location').' *" class="ik_sch_field ik_sch-text datepicker" aria-required="true">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ik_sch_field-wrap" data-name="time">
                            <input type="text" disabled name="time" value="" placeholder="'. __( 'Select Time', 'ik_schedule_location').' *" class="ik_sch_field ik_sch-text timepicker" aria-required="true">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="ik_sch_field-wrap" data-name="message">
                            <textarea name="message" rows="5" class="ik_sch_field ik_sch-textarea" aria-invalid="false" placeholder="'. __( 'Message', 'ik_schedule_location').'"></textarea>
                        </div> 
                    </div>                
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <if-recaptcha>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <input type="submit" value="'. __( 'Submit', 'ik_schedule_location').'" class="ik_sch_field ik_sch-submit">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
    jQuery(document).ready(function($) {
        jQuery(".ik_sch_book").on("click", ".ik_sch_field_services .ik_sch_field_add_services", function() {
            var button_clicked = jQuery(this);

            button_clicked.parent().clone().appendTo(button_clicked.parent().parent());
            var createdField = button_clicked.parent().parent().find(".ik_sch_field_services:last-child");
            createdField.find("select").val("");
            createdField.find("select option:first-child").prop("selected", true);
            createdField.find("button").text("- '. __( 'Remove', 'ik_schedule_location').'");
            createdField.find("button").removeClass("ik_sch_field_add_services");
            createdField.find("button").addClass("ik_sch_field_remove_services");
            return false;

        });
        jQuery(".ik_sch_book").on("click", ".ik_sch_field_services .ik_sch_field_remove_services", function() {
            var button_clicked = jQuery(this);

            button_clicked.parent().remove();
            button_clicked.parent().parent().find(".ik_sch_field_add_services:first-child").text("+ '. __( 'Add Service', 'ik_schedule_location').'");
            button_clicked.parent().parent().find(".ik_sch_field_add_services:first-child").addClass("ik_sch_field_add_services");
            button_clicked.parent().parent().find(".ik_sch_field_add_services:first-child").removeClass("ik_sch_field_remove_services");

            return false;

        });
        jQuery(".ik_sch_book").on("click", ".ik_sch-submit", function() {
            return false;
        });

        function ik_validate_date(date) {
            var dateObj = new Date(date);
            return dateObj.toString() !== "Invalid Date";
        }
 
        jQuery("#branch_select").on("change", function() {
            var selectedLocation = jQuery(this).val();
            var form_element = jQuery(this).parent().parent().parent();

            jQuery.ajax({
                url: "'.admin_url('admin-ajax.php').'",
                type: "POST",
                data: {
                action: "ik_sch_book_ajax_update_form",
                branch_id: selectedLocation
                },
                success: function(response) {
                    ik_sch_book_update_services(form_element, response.service_options);
                    if(response.enabled_days !== false){
                        ik_sch_book_update_date(form_element, response.enabled_days, response.disabled_dates);
                    }
                },
                error: function(xhr, status, error) {
                console.error(error);
                }
            });
        });

        jQuery(".ik_sch_book .datepicker").on("change", function() {
            var selectedDate = jQuery(this).val();
            var selectedLocation = jQuery("#branch_select").val();
            var form_element = jQuery(this).parent().parent().parent().parent();

            if(ik_validate_date(selectedDate)){
                jQuery.ajax({
                    url: "'.admin_url('admin-ajax.php').'",
                    type: "POST",
                    data: {
                    action: "ik_sch_book_ajax_update_time",
                    selectedDate: selectedDate,
                    branch_id: selectedLocation
                    },
                    success: function(response) {
                        ik_sch_book_update_time(form_element, response.minTime, response.maxTime, response.intminTime, response.intmaxTime);
                    },
                    error: function(xhr, status, error) {
                    console.error(error);
                    }
                });
            }
        });

        jQuery(".ik_sch_book .ik_sch-submit").on("click", function() {
            var dataforms = "";
            var form_element = jQuery(this).parent().parent().parent();

            jQuery.ajax({
                url: "'.admin_url('admin-ajax.php').'",
                type: "POST",
                data: {
                action: "ik_sch_book_ajax_submit_form",
                dataforms: dataforms
                },
                success: function(response) {
                    console.log("Sent");
                },
                error: function(xhr, status, error) {
                console.error(error);
                }
            });
        });

        var format_date = "'.$format_date.'";
        var format_timepmam = "'.$showampm.'";
        var show24hs = "'.$show24hs.'";
        var time_frame = "'.$time_frame.'";
        
        jQuery(".ik_sch_book .datepicker").datepicker({
            minDate: 0, // ban dates in the past
            format: format_date,
            dateFormat: format_date,
            maxDate: "+1Y"
        });
        jQuery(".ik_sch_book .timepicker").timepicker({
            "interval": time_frame,
            "lang": "decimal",
            "show2400": show24hs,
            "timeFormat": format_timepmam,
            "showDuration": true
        });
        
        function ik_sch_book_update_services(form, select_options){
            jQuery(form).find(".ik_sch_field_services select").html(\'<select name="services" class="ik_sch_field ik_sch-select ik_sch-validates-as-required select2-hidden-accessible" aria-required="true" aria-invalid="false" tabindex="-1" aria-hidden="true"><option>'. __( 'Select Service', 'ik_schedule_location').' *</option>\'+select_options+\'</select>\');
        }
        function DisableDates(date) {
            var string = jQuery.datepicker.formatDate(format_date, date);
            return [dates.indexOf(string) == -1];
        }
        function ik_sch_book_update_date(form, enabledDays, disabledDates) {

            jQuery(form).find(".datepicker").val( "" );
            jQuery(form).find(".timepicker").prop( "disabled", true);
            jQuery(form).find(".timepicker").val( "" );
            jQuery(form).find(".datepicker").datepicker( "destroy" );
            jQuery(form).find(".datepicker").prop( "disabled", false);

            jQuery(form).find(".datepicker").datepicker({
                minDate: 0,               
                dateFormat: format_date,
                maxDate: "+1Y",
                beforeShowDay: function(date) {
                    var day = date.getDay();
                    var dateString = jQuery.datepicker.formatDate(format_date, date);
                    var isEnabledDay = enabledDays.includes(day.toString());
                    var isDisabledDate = disabledDates.includes(dateString);
              
                    return [isEnabledDay && !isDisabledDate];
                  }
            });
        }
        function ik_sch_book_update_time(form, minTime, maxTime, intminTime, intmaxTime) {
    
            var timepicker_input = jQuery(form).find(".timepicker");
            timepicker_input.val("");
            timepicker_input.timepicker("destroy");
            timepicker_input.prop("disabled", false);
        
            timepicker_input.timepicker({
                interval: time_frame,
                lang: "decimal",
                show2400: show24hs,
                timeFormat: format_timepmam,
                showDuration: true,
                minTime: "8",
                maxTime: "16",
            });
            timepicker_input.on("click", function(){
                jQuery(".ui-timepicker-standard li").each(function() {
                    var time = jQuery(this).text();
                    var hour = parseInt(time.split(":")[0]);
                    if (hour >= 12 && hour < 13) {
                        jQuery(this).remove();
                    }
                });
            });
        }
        document.addEventListener("DOMContentLoaded", function() {
            const datepickerDiv = document.querySelector("#ui-datepicker-div");
        
            if (datepickerDiv) {
                const rect = datepickerDiv.getBoundingClientRect();
                const topPosition = rect.top;
        
                if (topPosition > 450) {
                    const schFieldFormDate = document.querySelector("#ik_sch_field_form_date");
                    if (schFieldFormDate) {
                        schFieldFormDate.parentNode.insertBefore(datepickerDiv, schFieldFormDate.nextSibling);
                    }
                }
            }
        });
        
    });
    </script>';

    return $output;
    
}
add_shortcode('IK_BOOKING_FORM', 'ik_sch_book_location_form');

?>