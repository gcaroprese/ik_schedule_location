<?php
/*

Booking Entries Template
Created: 14/08/2023
Update: 17/08/2023
Author: Gabriel Caroprese

*/

if ( ! defined('ABSPATH')) exit('restricted access');

?>
<div id ="ik_sch_book_existing_records">
    <h1><?php echo __( 'Booking - Records', 'ik_schedule_location'); ?></h1>
    <?php

    $bookings = new Ik_Schedule_Booking;
    $default_cal_color = $bookings->default_calendar_color;
    $format_datetime = $bookings->available_days->get_datetime_format();
    $format_date = $format_datetime['format_date'];
    $format_time = $format_datetime['format_time'];
    $time_frame = $format_datetime['time_frame'];
    $showampm = ($format_time == '24') ? 'HH:mm' : 'hh:mm p';
    $show24hs = ($format_time == '24') ? 'true' : 'false';

    $bookings_lists = $bookings->show_entries_backend(30);
    if($bookings_lists){
        echo $bookings_lists;
    } else {
        ?>
        <div id="ik_sch_book_existing">
            <p><?php echo __( 'Nothing yet!', 'ik_schedule_location'); ?></p>
        </div>';
        <?php
    }

    ?>
</div>
<script>
    jQuery(document).ready(function ($) {
        var format_date = '<?php echo $format_date; ?>';
        var format_timepmam = '<?php echo $showampm; ?>';
        var show24hs = '<?php echo $show24hs; ?>';
        var time_frame = '<?php echo $time_frame; ?>';
        var ik_sch_book_branch_id = 0;
        var default_cal_color = '<?php echo $default_cal_color; ?>';
        var ik_sch_book_select_text = "'.__( 'Select', 'ik_schedule_location').'";
        var ik_sch_book_selected_text = "'.__( 'Selected', 'ik_schedule_location').'";
        var ik_sch_book_close_text = "'.__( 'Close', 'ik_schedule_location').'";
        var ik_sch_book_confirmation_text = "'.__( 'Booking Confirmation', 'ik_schedule_location').'";

        jQuery("#ik_sch_book_existing_records .datepicker").datepicker({
			format: format_date,
			dateFormat: format_date,
		});

        function ik_sch_book_update_data(){
            fetch(window.location.href)
            .then(response => response.text())
            .then(data => {
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = data;     
                if (document.querySelector("#ik_sch_book_existing")) {
                    const tableContent = tempDiv.querySelector("#ik_sch_book_existing").innerHTML;
                    document.querySelector("#ik_sch_book_existing").innerHTML = tableContent;
                }
                if (document.querySelector("#ik_sch_book_calendar_container")) {
                    ik_sch_js_generateCalendar()
                }
            });
            return;
        }

        jQuery("#ik_sch_book_existing_records th .select_all").on( "click", function() {
            if (jQuery(this).attr('selectedrecord') != 'yes'){
                jQuery('#ik_sch_book_existing_records th .select_all').prop('checked', true);
                jQuery('#ik_sch_book_existing_records th .select_all').attr('checked', 'checked');
                jQuery('#ik_sch_book_existing_records tbody tr').each(function() {
                    jQuery(this).find('.select_data').prop('checked', true);
                    jQuery(this).find('.select_data').attr('checked', 'checked');
                });        
                jQuery(this).attr('selectedrecord', 'yes');
            } else {
                jQuery('#ik_sch_book_existing_records th .select_all').prop('checked', false);
                jQuery('#ik_sch_book_existing_records th .select_all').removeAttr('checked');
                jQuery('#ik_sch_book_existing_records tbody tr').each(function() {
                    jQuery(this).find('.select_data').prop('checked', false);
                    jQuery(this).find('.select_data').removeAttr('checked');
                });   
                jQuery(this).attr('selectedrecord', 'no');
                
            }
        });
        
        jQuery("#ik_sch_book_existing_records .ik_sch_book_button_delete_bulk ").on( "click", function() {
            var confirm_delete = confirm('<?php echo __( 'Are you sure to delete?', 'ik_schedule_location'); ?>');
            if (confirm_delete == true) {
                jQuery('#ik_sch_book_existing_records tbody tr').each(function() {
                var elemento_borrar = jQuery(this).parent();
                    if (jQuery(this).find('.select_data').prop('checked') == true){
                        
                        var registro_tr = jQuery(this);
                        var iddata = registro_tr.attr('iddata');
                        
                        var data = {
                            action: "ik_sch_book_ajax_delete_booking",
                            "post_type": "post",
                            "iddata": iddata,
                        };  
            
                        jQuery.post( ajaxurl, data, function(response) {
                            if (response){
                                registro_tr.fadeOut(700);
                                registro_tr.remove();
                            }        
                        });
                    }
                });
            }
            jQuery('#ik_sch_book_existing_records th .select_all').attr('selected', 'no');
            jQuery('#ik_sch_book_existing_records th .select_all').prop('checked', false);
            jQuery('#ik_sch_book_existing_records th .select_all').removeAttr('checked');
            return false;
        });
	
        jQuery('#ik_sch_book_existing_records').on('click','td .ik_sch_book_button_delete', function(e){
            e.preventDefault();
            var confirm_delete =confirm('<?php echo __( 'Are you sure to delete?', 'ik_schedule_location'); ?>');
            if (confirm_delete == true) {
                var iddata = jQuery(this).parent().attr('iddata');
                var registro_tr = jQuery('#ik_sch_book_existing_records tbody').find('tr[iddata='+iddata+']');
                
                var data = {
                    action: 'ik_sch_book_ajax_delete_booking',
                    "iddata": iddata,
                };  
        
                jQuery.post( ajaxurl, data, function(response) {
                    if (response){
                        registro_tr.fadeOut(700);
                        registro_tr.remove();
                    }        
                });
            }
        });
 
        jQuery("#ik_sch_book_existing_records").on( "click", '.ik_sch_book_button_view_modal', function(e) {
            e.preventDefault();
            let button = jQuery(this);
            let iddata = button.parent().parent().attr('iddata');
            button.find('.dashicons').removeClass('dashicons-edit');
            button.find('.dashicons').addClass('dashicons-update');
            var data = {
                action: 'ik_sch_book_ajax_show_booking_data',
                "iddata": iddata,
            };
    
            jQuery.post( ajaxurl, data, function(response) {
                if (response){
                    jQuery("#ik_sch_book_modal").modal("hide");
                    jQuery("#ik_sch_book_modal").remove();
                    jQuery("body").append(response);
                    jQuery("#ik_sch_book_modal").modal("show");
                    button.find('.dashicons').removeClass('dashicons-update');
                    button.find('.dashicons').addClass('dashicons-edit');
                }
            });
            return false;
        });

        jQuery("body").on( "click", '#ik_sch_book_modal .edit_data_info', function(e) {
            e.preventDefault();
            let button = jQuery(this);
            let type_data = jQuery(this).attr('type_data');
            let iddata = jQuery('#ik_sch_book_modal').attr('iddata');
            button.removeClass('dashicons-edit');
            button.addClass('dashicons-update');
            
            var data = {
                action: 'ik_sch_book_ajax_add_edit_booking_field',
                "iddata": iddata,
                "type_data": type_data,
            };
    
            jQuery.post( ajaxurl, data, function(response) {
                if (response){
                    button.parent().html(response);
                }
            });
            return false;
        });

        jQuery("body").on( "click", '#ik_sch_book_modal .button_cancel_edit', function(e) {
            e.preventDefault();
            let button = jQuery(this);
            let type_data = jQuery(this).parent().find('.ik_sch_book_edit_field').attr('name');
            let iddata = jQuery('#ik_sch_book_modal').attr('iddata');
            button.find('.dashicons').removeClass('dashicons-undo');
            button.find('.dashicons').addClass('dashicons-update');
            let save = false;
            
            var data = {
                action: 'ik_sch_book_ajax_add_reset_booking_field',
                "iddata": iddata,
                "type_data": type_data,
                "save": save,
            };
    
            jQuery.post( ajaxurl, data, function(response) {
                if (response){
                    button.parent().html(response);
                }
            });
            return false;
        });

        jQuery("body").on( "click", '#ik_sch_book_modal .button_save_edit', function(e) {
            e.preventDefault();
            let button = jQuery(this);
            let type_data = jQuery(this).parent().find('.ik_sch_book_edit_field').attr('name');
            let value_edit = button.parent().find('.ik_sch_book_edit_field').val();
            let send_email = false;
            if (jQuery(this).parent().find('.ask_email_field input').prop('checked')) {
                send_email = true;
            }

            let iddata = jQuery('#ik_sch_book_modal').attr('iddata');
            button.find('.dashicons').removeClass('dashicons-yes');
            button.find('.dashicons').addClass('dashicons-update');
            let save = true;
            
            var data = {
                action: 'ik_sch_book_ajax_add_reset_booking_field',
                "iddata": iddata,
                "type_data": type_data,
                "save": save,
                "send_email": send_email,
            };
            if (button.parent().find('.ik_sch_book_edit_field').is('select') && type_data.includes('[')) {
                let selectedValues = button.parent().find('.ik_sch_book_edit_field option:selected').map(function() {
                    return jQuery(this).val();
                }).get();

                data.value_edit = selectedValues;
            } else {
                data.value_edit = value_edit;
            }
            jQuery.post( ajaxurl, data, function(response) {
                if (response){
                    button.parent().parent().html(response);
                    ik_sch_book_update_data();
                }
            });
            return false;
        });

        jQuery('#ik_sch_book_existing_records').on('click','th.worder', function(e){
            e.preventDefault();

            var order = jQuery(this).attr('order');
            var urlnow = window.location.href;
            
            if (order != undefined){
                if (jQuery(this).hasClass('desc')){
                    var direc = 'asc';
                } else {
                    var direc = 'desc';
                }
                if (order == 'id'){
                    var orderLink = '&order=id&orderdir='+direc;
                    window.location.href = urlnow+orderLink;
                } else if (order == 'name'){
                    var orderLink = '&order=name&orderdir='+direc;
                    window.location.href = urlnow+orderLink;
                } else if (order == 'branch'){
                    var orderLink = '&order=branch&orderdir='+direc;
                    window.location.href = urlnow+orderLink;
                } else if (order == 'booking_date'){
                    var orderLink = '&order=booking_date&orderdir='+direc;
                    window.location.href = urlnow+orderLink;
                } else if (order == 'phone'){
                    var orderLink = '&order=phone&orderdir='+direc;
                    window.location.href = urlnow+orderLink;
                } else if (order == 'email'){
                    var orderLink = '&order=email&orderdir='+direc;
                    window.location.href = urlnow+orderLink;
                } else if (order == 'ip'){
                    var orderLink = '&order=ip&orderdir='+direc;
                    window.location.href = urlnow+orderLink;
                } else if (order == 'accepted'){
                    var orderLink = '&order=accepted&orderdir='+direc;
                    window.location.href = urlnow+orderLink;
                }
            }

        });
        jQuery('body').on('click', '#ik_sch_book_modal .ik_sch_book_delete_field', function(){
            jQuery(this).parent().remove();
            return false;
        });
        jQuery('body').on('click', '#ik_sch_book_add_service_field', function() {
            let selectsWrapper = jQuery(this).parent().find('.ik_sch_book_services_wrapper');
            let selectToCopy = selectsWrapper.find('.ik_sch_book_services_select_wrapper:first-child');            
            let newSelect = selectToCopy.clone();
            newSelect.appendTo(selectsWrapper);
            
            newSelect.find('select').val('');
                        
            return false;
        });   
        jQuery('body').on('change', '#ik_booking_modal_colorPicker', function(){
            let iddata = jQuery('#ik_sch_book_modal').attr('iddata');
            let color_selected = jQuery(this).val();
            var data = {
                action: 'ik_sch_book_ajax_update_booking_color',
                "iddata": iddata,
                "color": color_selected,
            };        
            jQuery.post( ajaxurl, data, function(response) {
                if (response){
                    ik_sch_book_update_data();
                }
            });
        });
        jQuery('body').on('click', '#turn-back-color', function(){
            let iddata = jQuery('#ik_sch_book_modal').attr('iddata');
            let color_selected = default_cal_color;
            jQuery(this).parent().find('input').val(color_selected);
            var data = {
                action: 'ik_sch_book_ajax_update_booking_color',
                "iddata": iddata,
                "color": color_selected,
            };        
            jQuery.post( ajaxurl, data, function(response) {
                if (response){
                    ik_sch_book_update_data();
                }
            });
            return false;
        });
        jQuery("body").on( "click", "#ik_sch_book_remove_entry", function() {
            var confirm_delete = confirm('<?php echo __( 'Are you sure to delete?', 'ik_schedule_location'); ?>');
            if (confirm_delete == true) {
                let iddata = jQuery('#ik_sch_book_modal').attr('iddata');
                
                var data = {
                    action: "ik_sch_book_ajax_delete_booking",
                    "post_type": "post",
                    "iddata": iddata,
                };  

                jQuery.post( ajaxurl, data, function(response) {
                    if (response){
                        jQuery("#ik_sch_book_modal").modal("hide");
                        jQuery("#ik_sch_book_modal").remove();
                        ik_sch_book_update_data();
                    }        
                });
            }
            return false;
        });
        jQuery("#ik_sch_book_existing_records").on( "click", "#ik_sch_book_add_new_record", function() {
            let button = jQuery(this);
            button.attr('disabled', 'disabled');
            var data = {
                action: 'ik_sch_book_ajax_show_new_booking_modal',
            };
    
            jQuery.post( ajaxurl, data, function(response) {
                if (response){
                    jQuery("#ik_sch_book_modal").modal("hide");
                    jQuery("#ik_sch_book_modal").remove();
                    jQuery("body").append(response);
                    jQuery("#ik_sch_book_modal").modal("show");
                    button.removeAttr('disabled');
                }
            });
            return false;
        });

        function ik_sch_book_update_date_by_location() {

            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: "ik_sch_book_ajax_get_location_dates",
                    branch_id: ik_sch_book_branch_id
                },
                success: function(response) {
                    let form_modal = jQuery("#ik_sch_book_modal");
                    form_modal.find(".datepicker").val( "" );
                    form_modal.find(".timepicker").prop( "disabled", true);
                    form_modal.find(".timepicker").val( "" );
                    form_modal.find(".datepicker").datepicker( "destroy" );
                    form_modal.find(".datepicker").prop( "disabled", false);

                    form_modal.find(".datepicker").datepicker({
                        minDate: "+1d",               
                        dateFormat: format_date,
                        maxDate: "+1Y",
                        beforeShowDay: function(date) {
                            var day = date.getDay();
                            var dateString = jQuery.datepicker.formatDate(format_date, date);
                            var isEnabledDay = response.enabled_days.includes(day.toString());
                            var isDisabledDate = response.disabled_dates.includes(dateString);
                    
                            return [isEnabledDay && !isDisabledDate];
                        }
                    });
                }
            });
        }

        function ik_sch_book_update_services_by_location() {

            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: "ik_sch_book_ajax_get_service_select",
                    branch_id: ik_sch_book_branch_id
                },
                success: function(response) {
                    select_wrapper = jQuery('#ik_sch_book_modal #ik_sch_book_modal_services_wrapper .ik_sch_book_services_wrapper');
                    select_wrapper.html(response);
                    if(select_wrapper.find('select option').length > 0){
                        jQuery('#ik_sch_book_add_service_field').removeAttr('disabled');
                    } else {
                        jQuery('#ik_sch_book_add_service_field').attr('disabled', 'disabled');
                    }
                }
            });
        }
        function ik_sch_non_available_time(time, openingTimes) {
            if(show24hs == "true"){
                for (var i = 0; i < openingTimes.length; i++) {
                    if (time >= openingTimes[i].opentime && time <= openingTimes[i].closetime) {
                        return true;
                    }
                }
            } else {

                const selectedTime = new Date("1970-01-01 " + time);
            
                for (var i = 0; i < openingTimes.length; i++) {
                    const openTime = new Date("1970-01-01 " + openingTimes[i].opentime);
                    const closeTime = new Date("1970-01-01 " + openingTimes[i].closetime);
            
                    if (selectedTime >= openTime && selectedTime <= closeTime) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        function ik_sch_get_input_form(type) {

            if(type == "ik_sch_field_form_phone"){
                let input_phone = document.getElementById(type);
                if(input_phone){
                    let pattern_phone = /^[0-9+()\-\s]{7,22}$/;
                    if (pattern_phone.test(input_phone.value)) {
                        input_phone.classList.remove("invalid_data");
                        return input_phone.value;
                    } else {
                        input_phone.classList.add("invalid_data");
                    }
                }
            } else if(type == "ik_sch_field_form_email_address"){
                let input_email = document.getElementById(type);
                if(input_email){
                    let pattern_email = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (pattern_email.test(input_email.value)) {
                        input_email.classList.remove("invalid_data");
                        return input_email.value;
                    } else {
                        input_email.classList.add("invalid_data");
                    }
                }
            } else if(type == "ik_sch_field_form_name_customer"){
                let input_name = document.getElementById(type);
                if(input_name){
                    let pattern_name = /^[a-zA-Z\s\'’`]{1,60}$/;
                    if (pattern_name.test(input_name.value)) {
                        input_name.classList.remove("invalid_data");
                        return input_name.value;
                    } else {
                        input_name.classList.add("invalid_data");
                    }
                }
            } else if(type == "ik_sch_field_form_date"){
                let input_date = document.getElementById(type);
                if(input_date){
                    if (input_date.value.trim() !== ""){
                        input_date.classList.remove("invalid_data");
                        return input_date.value;
                    } else {
                        input_date.classList.add("invalid_data");
                    }
                }
            } else if(type == "ik_sch_field_form_time"){
                let input_time = document.getElementById(type);
                if(input_time){
                    if (input_time.value.trim() !== ""){
                        input_time.classList.remove("invalid_data");
                        return input_time.value;
                    } else {
                        input_time.classList.add("invalid_data");
                    }
                }
            }

            return false;
        }
        function ik_sch_book_update_time(form, hours, mintime, maxtime) {  
            var timepicker_input = jQuery(form).find(".timepicker");
            timepicker_input.removeClass("invalid_data");
            timepicker_input.val("");
            timepicker_input.timepicker("destroy");
            timepicker_input.prop("disabled", false);
        
            timepicker_input.timepicker({
                interval: time_frame,
                lang: "decimal",
                show2400: show24hs,
                timeFormat: format_timepmam,
                showDuration: true,
                show: true,
                minTime: mintime,
                maxTime: maxtime,
            });
            jQuery(".ui-timepicker-container").attr("id", "ik_sch_timepicker_modal");
            timepicker_input.focus();
            ik_sch_update_hours(hours);
            
        }
        function ik_sch_update_hours(hours) {
            const times = jQuery("#ik_sch_timepicker_modal").find(".ui-menu-item a");

            times.each(function() {
                let time = jQuery(this).text();
                if (!ik_sch_non_available_time(time, hours)) {
                    jQuery(this).parent().remove();
                }
            });
        }
        jQuery("body").on( "change", "#ik_sch_book_modal #branch_select", function() {
            let select_location = parseInt(jQuery(this).val());
            ik_sch_book_branch_id = select_location;

            ik_sch_book_update_date_by_location();
            ik_sch_book_update_services_by_location();

            return false;
        });
        document.addEventListener('click', function(event) {
            var datePicker = document.getElementById('ui-datepicker-div');
            var excludedElement = document.getElementById('ik_sch_field_form_date'); 

            if (!datePicker.contains(event.target) && event.target !== excludedElement && !event.target.classList.contains('datepicker')) {
                datePicker.style.display = 'none';
            }
        });

        jQuery("body").on("change", "#ik_sch_book_modal .datepicker", function() {
            var selectedDate = jQuery(this).val();
            var form_element = jQuery("#ik_sch_book_modal");
            jQuery(this).removeClass("invalid_data");

            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: "ik_sch_book_ajax_update_time",
                    selectedDate: selectedDate,
                    branch_id: ik_sch_book_branch_id,
                    session_data: true
                },
                success: function(response) {
                    if(response != "error"){
                        const responseObject = JSON.parse(response);
                        var data_hours = [];

                        if (!responseObject.times_data.alwaysclose) {
                            var opentimes = responseObject.times_data.opentime;
                            var closetimes = responseObject.times_data.closetime;

                            if (opentimes.length === closetimes.length) {
                                for (var i = 0; i < opentimes.length; i++) {
                                    var opentime = opentimes[i].substr(0, 5);
                                    var closetime = closetimes[i].substr(0, 5);
                                    data_hours.push({ opentime: opentime, closetime: closetime });

                                    ik_sch_book_update_time(form_element, data_hours, responseObject.mintime, responseObject.maxtime);
                                }
                            }
                        }
                    }
                },
                error: function(xhr, status, error) {
                console.error(error);
                }
            });
        });
        jQuery("body").on("click", "#ik_sch_book_modal #ik_sch_field_form_modal_submit", function(){	
            let phone = ik_sch_get_input_form("ik_sch_field_form_phone");
            let email = ik_sch_get_input_form("ik_sch_field_form_email_address");
            let name = ik_sch_get_input_form("ik_sch_field_form_name_customer");
            let date_input = ik_sch_get_input_form("ik_sch_field_form_date");
            let time_input = ik_sch_get_input_form("ik_sch_field_form_time");
            let note = jQuery("#ik_sch_book_modal_interal_note").val();

            let select_service = jQuery('#ik_sch_book_modal_services_wrapper select');
            let service_ids = '';
            let service_approved = true;
            if(select_service.length > 0){
                select_service.each(function() {
                    if(parseInt(jQuery(this).val()) > 0){
                        service_ids = service_ids+jQuery(this).val()+',';
                        jQuery(this).removeClass('invalid_data');
                    } else {
                        jQuery(this).addClass('invalid_data');
                        service_approved = false;
                    }
                });
            } else {
                service_approved = false;
            }
            let btn_submit = jQuery(this);

            if(phone && email && name && date_input && time_input && service_approved){
                btn_submit.attr("disabled","disabled");

                jQuery.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: {
                    action: "ik_sch_book_ajax_submit_form",
                        selectedDate: date_input,
                        selectedTime: time_input,
                        name: name,
                        email: email,
                        phone: phone,
                        branch_id: ik_sch_book_branch_id,
                        service_ids: service_ids,
                        note: note,
                        type: "modal"
                    },
                    success: function(response) {
                        if(response.result == true){
                            jQuery("#ik_sch_book_modal").modal("hide").remove();
                            location.reload();
                        } else {
                            btn_submit.removeAttr("disabled");

                            if(response.fields.selectedDate == false){
                                jQuery("#ik_sch_field_form_date").val("");
                                jQuery("#time_input").val("");
                            }
                            if(response.fields.selectedTime == false){
                                jQuery("#time_input").val("");
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                    console.error(error);
                    }
                });	
            }
            return false;
        }); 
    });
</script>