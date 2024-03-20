<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/* 
Book - Schedule Locatons Form Shortcode to book by location showing services available
Created: 06/08/2022
Last Update: 18/03/2024
Author: Gabriel Caroprese
*/

//Form form_id
function ik_sch_book_by_location_form($atts = [], $content = null, $tag = ''){
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $data_location = shortcode_atts(['id' => '0',], $atts, $tag);

    $output = '';

    $location_id = absint($data_location['id']);
        
    $booking = new Ik_Schedule_Booking();

    $format_datetime = $booking->available_days->get_datetime_format();
    $format_date = $format_datetime['format_date'];
    $format_time = $format_datetime['format_time'];

    $config_data = $booking->get_config();
    $limit_days = intval($config_data['limit_booking']);
    $show_prices_aspopup = ($config_data['prices_popup']) ? 'true' : 'false';
    $time_frame = $format_datetime['time_frame'];
    $showampm = ($format_time == '24') ? 'HH:mm' : 'hh:mm p';
    $show24hs = ($format_time == '24') ? 'true' : 'false';
    $actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    $services_list = $booking->get_service_select_list($location_id);

    if($services_list){
        
        $output = '
        <style>
        #ik_sch_book_data_services_book .container {
            width: 100%;
        }
        #ik_sch_book_data_services_book .col-md-4{
            padding-left:0px !important;
        }
        .ik_sch_book_data_services_category_wrapper{
            display: block;
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            position: relative;
        }
        .ik_sch_book_data_services_from_value{
            position: absolute;
            top: 5px;
            right: 10%;
        }
        .show_services .ik_sch_book_data_services_from_value{
            display: none;
        }
        .ik_sch_book_data_services_category_name{
            position: relative;
            cursor: pointer;
        }
        .ik_sch_book_data_services_category_name i{
            position: absolute;
            right: 14px;
            top: 10px;
        }
        .ik_sch_book_data_services_data_left, .ik_sch_book_data_services_data_right{
            display:block;
        }
        .ik_sch_book_data_services_data_left {
            width: 65%;
            min-width: 200px;
            padding-left: 7px;
        }
        .ik_sch_book_data_services_data_right{
            min-width: 98px;
            vertical-align: middle;
        }
        .ik_sch_field-wrap {
            margin-bottom: 20px;
            color: #333;
            padding: 10px 30px;
            line-height: 1.5em;
            font-weight: 600;
            font-size: 1.2em!important;
        }
        .ik_sch_book_data_services_category_name h3 {
            font-size: 17px;
            font-weight: 700;
            box-shadow: inset 0 -1px 0 0 #e0e0e0;
            border-radius: 3px;
        }
        .ik_sch_book_data_services_category_wrapper:last-child .ik_sch_book_data_services_category_name h3{
            box-shadow: none;
        }   
        .ik_sch_book_data_services_category_name h3 {
            padding: 10px 12px 20px;
            margin-bottom: 20px;
        }   
        .ik_sch_book_data_services_data {
            padding: 7px 12px;        
        }            
		.ik_sch_book_data_services_data:not(.show_services) {
			display: none;
		}
        .ik_sch_book_data_services_data {
            width: 100%;
            border-bottom: 0.5px solid #f1f1f1;
        }
        .ik_sch_book_data_services_data.show_services{
            display: inline-block;
        }
        .ik_sch_book_data_services_data:first-child{
            padding-top: 20px;
        }    
        .ik_sch_book_data_services_data:last-child {
            border-bottom: none;
        }
        .ik_sch_book_data_services_name {
            font-weight: 500;
        }
        .ik_sch_book_data_services_delivery_time {
            font-style: italic;
            font-size: 17px;
        }        
        .ik_sch_book_data_select_service, .ik_sch_book_data_select_wc_service{
            border: 1px solid #945d1a;
            background: #fff;
            color: #945d1a;
            padding: 5px 5px;
            border-radius: 3px;
            font-size: 15px;
            margin: 0 7px;
            text-transform: uppercase;
        }
        #ik_sch_book_modal .ik_sch_book_data_services_data {
            display: inline-block;
			position: relative;
        }
        #ik_sch_book_modal .ik_sch_book_data_services_category_name, #ik_sch_book_modal .ik_sch_book_data_services_from_value {
            display: none;
        }
        .ik_sch_book_data_select_service.selected_btn, .ik_sch_book_data_select_wc_service.selected_btn{
            background: #945d1a;
            color: #fff;
        }
        .ik_sch_book_data_select_service:not(.selected_btn):hover,.ik_sch_book_data_select_service.added_service, .ik_sch_book_data_select_wc_service:not(.selected_btn):hover,.ik_sch_book_data_select_wc_service.added_service{
            background: #945d1a;
            color: #fff;
            transition: background 1.5s ease;
        }
        ul#menu-branch-menu>li a {
            margin-top: 8px;
            background: #000;
            color: #fff;
            padding: 12px 30px;
            border: 0;
            border-radius: 4px;
        }
        #ik_sch_book_book_service_panel {
            position: fixed;
            bottom: 0;
            z-index: 9999999999;
            width: 100%;
        }
        #ik_sch_book_book_service_panel .ik_sch_book_book_service_panel_content {
            background: #000;
            z-index: 9999999999;
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
            padding: 20px;
            display: flow-root;
            opacity: 1;
            transition: opacity 0.6s ease-in-out;
            box-shadow: 0 0 3px 0 rgb(0 0 0/.3), 0 0 0 1px rgb(0 0 0/.05);
            border-radius: 4px 4px 0px 0px;
        }
        #ik_sch_book_book_service_panel .ik_sch_book_book_service_panel_content.hidden {
            opacity: 0;
        }
        #ik_sch_book_book_service_panel .ik_sch_book_book_service_panel_services_count, #ik_sch_book_book_service_panel .ik_sch_book_book_service_panel_services_total_price{
            float: left;
            margin-right: 12px;
        }
        .ik_sch_field_form_modal_btn_act {
            float: right;
            font-size: 22px;
            padding: 7px 20px;
            text-transform: uppercase;
            border-radius: 4px;
            border: 0 solid #945d1a;
            background: #945d1a;
            color: #fff;
            transition: background 1.2s ease;
        }
        .ik_sch_field_form_modal_btn_act:hover{
            background: #fff;
            border: 0 solid #000;
            color: #333;
        }    
        #ik_sch_book_modal .modal-header{
            padding: 0 8px;
        }
        #ik_sch_book_modal .ik_sch_field-wrap input {
            width: 90%;
            padding: 4px 12px;
            font-size: 16px;
            line-height: 1;
        }
        #ik_sch_book_modal h5.modal_data_input {
            font-size: 16px;
            padding-bottom: 4px;
        }
        #ik_sch_book_modal .ik_sch_field-wrap {
            margin-bottom: 5px;
        }
        #ik_sch_book_book_service_panel .ik_sch_book_book_service_panel_services_data{
            font-size: 20px;
            padding: 7px 0;
            display: inline-flex;
        }
        #ik_sch_book_book_service_panel .ik_sch_book_book_service_panel_services_count{
            font-style: italic;
            color: #f1f1f1;
        }
        #ik_sch_book_book_service_panel .ik_sch_book_book_service_panel_services_total_price{
            color: #fff;
        }
        #ik_sch_book_modal{
            z-index: 999999999999999;
        }
        #ik_sch_book_modal .modal-footer .btn-primary, #ik_sch_modalConfirmation button{
            background: #945d1a;
            border: 1px solid #945d1a;
        }  
        #ik_sch_modalConfirmation button{
            color: #fff;
        }  
        #ik_sch_book_modal .modal-content {
            transform: translateY(20%);
            max-width: 1200px;
            margin: 0 auto;
        }  
        #ik_sch_book_modal .modal-body .container .row:first-child {
            max-height: 370px;
            overflow: auto;
        }
        #ik_sch_book_modal .modal-header h4{
            font-size: 24px;
            padding-top: 10px;
            padding-left: 10px;
        }
        #ik_sch_book_modal .modal-header .close {
            border: 0 solid transparent;
            border-radius: 50px;
            line-height: 20px;
            font-size: 25px;
            padding: 6px 8px;
        }
        #ik_sch_book_modal .ik_sch_book_modal_services_modal_item{
            box-shadow: inset 0 -1px 0 0 #f1f1f1;
            padding: 7px 0px 7px 3px;
        }
        #ik_sch_book_modal .ik_sch_book_modal_services_name {
            font-weight: 600;
            color: #000;
        }
        #ik_sch_book_modal .ik_sch_book_modal_remove_service {
            border: 0 solid transparent;
            background: transparent;
            background-color: transparent;
            font-size: 20px;
        }
        #ik_sch_book_modal .modal-footer {
            justify-content: flex-start;
        }
        #ik_sch_book_modal ::-webkit-scrollbar {
            background-color: #f1f1f1;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
          }
        #ik_sch_book_modal ::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 5px;
            transition: background-color 0.2s ease;
            background-color: #888;
            width: 10px;
        }
        #ik_sch_book_modal ::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }
        .ui-timepicker-container, #ui-datepicker-div{
            z-index: 99999999999999999! important;
        }   
        #ik_sch_book_modal input.invalid_data {
            border-color: red;
            margin: 0;
        }
        #ik_sch_modalConfirmation {
            display: block;
            z-index: 9999999999;
            top: 200px;
            width: 80%;
            max-width: 500px;
            margin: 0 auto;
            position: fixed;
            left: 37%;
            height: 100%;
            overflow: auto;
        }
        #ik_sch_modalConfirmation .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
        }
        #ik_sch_book_modal .ik_sch_book_modal_services_price {
            font-style: italic;
        }
        #ik_sch_book_modal .ik_sch_book_modal_services_price:before{
            content: "|";
            padding: 0 10px;
            font-style: normal;
        }
        #ik_sch_book_modal .add-more-modal {
            text-align: left;
            background: transparent;
            color: #945d1a;
            border: none;
            padding: 8px 0;
            margin-top: 20px;
            width: auto;
        }
        #ik_sch_book_service_cat_filter {
            display: inline-block;
            background-color: #F8FAFA;
            padding: 20px;
            width: 100%;
        }
        #ik_sch_book_service_cat_filter .service_cat_filter_box_selector {
            border-radius: 4px;
            margin: 7px;
            padding: 21px 9px;
            width: 169px;
            font-size: 0.7em;
            line-height: 1.2;
            box-shadow: rgba(0, 0, 0, 0.1) 10px 10px 20px 0px;
            box-sizing: border-box;
            text-align: center;
            display: inline-block;
            cursor: pointer;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #ik_sch_book_service_cat_filter .service_cat_filter_box_selector.selected {
            border: 1px solid #333;
        }
        #ik_sch_book_service_cat_filter .service_cat_filter_box_selector .service_cat_filter_box_icon {
            padding-bottom: 6px;
            display: block;
            height: 40px;
        }
        #ik_sch_book_service_cat_filter .service_cat_filter_box_selector .service_cat_filter_box_icon svg, #ik_sch_book_service_cat_filter .service_cat_filter_box_selector .service_cat_filter_box_icon img{
            max-width: 30px;
            padding-bottom: 3px;
        }
        @media (max-width: 1170px){
            .ik_sch_book_data_services_price_data{
                padding: 20px 7px;
            }
        }
        @media (min-width: 767px){
            #ik_sch_book_modal .modal-dialog {
                width: 90%;
                --bs-modal-width: 90%;
            }
            .ik_sch_book_data_services_data_right{
				width: 35%;
			}
			.ik_sch_book_data_services_data_left, .ik_sch_book_data_services_data_right{
				float: left;
			}
			.ik_sch_book_data_services_data_right{
				text-align: right;
			}
        }  
        @media screen and (max-width: 600px) { 
            .ik_sch_field-wrap .col-md-8
            {
                padding: 0px !Important;
            }
            .ik_sch_book_data_services_category_wrapper {
                padding: 0px !important; 
            }
            #ik_sch_book_service_cat_filter .service_cat_filter_box_selector {
				width: calc(50% - 14px)! important;
			}
        }
        @media (max-width: 767px){
            .ik_sch_book_data_services_from_value {
                display: none;
            }
            .ik_sch_book_data_services_category_name h3 {
                    font-size: 16px! important;
                margin-right: 40px;
            }
            #ik_sch_book_book_service_panel .ik_sch_book_book_service_panel_content{
                text-align: center;
            }
            #ik_sch_book_book_service_panel_book_btn{
                text-align: center;
                margin: 0 auto;
                float: unset;
            }
			.ik_sch_book_data_select_wc_service{
				position: absolute;
				right: 6px;
				top: 21px;
			}
        }
        </style>
        '.$services_list.'
        <script>
        var format_date = "'.$format_date.'";
        var format_timepmam = "'.$showampm.'";
        var show24hs = "'.$show24hs.'";
        var time_frame = "'.$time_frame.'";
        var limit_days = "+'.$limit_days.'d";
        var ik_sch_book_branch_id = '.$location_id.';
        var ik_sch_book_branch_url = "'.$actual_link.'";
        var ik_show_prices_popup = '.$show_prices_aspopup.';

        var ik_sch_book_select_text = "'.__( 'Select', 'ik_schedule_location').'";
        var ik_sch_book_selected_text = "'.__( 'Selected', 'ik_schedule_location').'";
        var ik_sch_book_close_text = "'.__( 'Close', 'ik_schedule_location').'";
        var ik_sch_book_confirmation_text = "'.__( 'Booking Confirmation', 'ik_schedule_location').'";
        jQuery(document).ready(function($) {
		
            var serviceCatFilters = document.querySelectorAll(".service_cat_filter_box_selector");

            if(serviceCatFilters != null){

                serviceCatFilters.forEach(function(filter) {
                    filter.addEventListener("click", function(event) {
                        var catId = filter.getAttribute("cat_id");
                        var clickedBox = event.target.closest(".service_cat_filter_box_selector");
                        
                        if(!ik_show_prices_popup){

                            if(catId !== "ik_sch_book_service_cat_0"){
                                var allCategories = document.querySelectorAll(".ik_sch_book_data_services_category_wrapper");
                                allCategories.forEach(function(category) {
                                    category.style.display = "none";
                                });
                            } else {
                                var allCategories = document.querySelectorAll(".ik_sch_book_data_services_category_wrapper");
                                allCategories.forEach(function(category) {
                                    category.style.display = "block";
                                });
                            }

                            var all_boxes = document.querySelectorAll(".service_cat_filter_box_selector");
                            all_boxes.forEach(function(box) {
                                box.classList.remove("selected");
                            });

                            clickedBox.classList.add("selected");

                            var selectedCategory = document.getElementById(catId);

                            if (selectedCategory) {
                                selectedCategory.style.display = "block";
                
                                var categoryNames = selectedCategory.querySelectorAll(".ik_sch_book_data_services_category_name");
                                categoryNames.forEach(function (categoryName) {
                                    if (!categoryName.classList.contains("show_services")) {
                                        ik_sch_book_toggleCategory(categoryName);
                                    }
                                });
                            }
							
                        } else {
                            var ik_sch_book_services_popup = document.querySelectorAll("#ik_sch_book_modal");
                            ik_sch_book_services_popup.forEach(function (popup) {
                                popup.parentNode.removeChild(popup);
                            });
                            var selectedCategory = document.getElementById(catId);

                            if(selectedCategory){
                                var ik_sch_book_services_popup_title = selectedCategory.querySelector(".ik_sch_book_data_services_category").innerHTML;
                                
                                var ik_sch_book_screenHeight = parseInt(window.innerHeight)-187;
                                var ik_sch_book_screen_top = (window.innerWidth > 750) ? 60 : 0;

                                var ik_sch_book_services_popup_element = document.createElement("div");
                                ik_sch_book_services_popup_element.innerHTML =
                                \'<div class="modal" id="ik_sch_book_modal" tabindex="-1" role="dialog" aria-labelledby="ik_sch_book_modallLabel" aria-hidden="true" style="display:block;top: \'+ik_sch_book_screen_top+\'px"><div class="modal-content" style="max-width: 762px;overflow-y: auto;max-height: \'+ik_sch_book_screenHeight+\'px;"><div class="modal-header"><h4 class="modal-title" id="ik_sch_book_modalLabel">\'+ik_sch_book_services_popup_title+\'</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div><div class="modal-body"><div class="ik_sch_book_list_services">\'+selectedCategory.innerHTML+\'</div><div class="container-fluid"></div></div></div>\';
                                
                                document.body.appendChild(ik_sch_book_services_popup_element);
                                var ik_sch_book_services_close_popup = ik_sch_book_services_popup_element.querySelector(".close");
                                ik_sch_book_services_close_popup.onclick = function() {
                                    ik_sch_book_services_popup_element.style.display = "none";
                                };
                                var categoryNames = selectedCategory.querySelectorAll(".ik_sch_book_data_services_category_name");
                                categoryNames.forEach(function (categoryName) {
                                    if (!categoryName.classList.contains("show_services")) {
                                        ik_sch_book_toggleCategory(categoryName);
                                    }
                                });
								
								const container = document.querySelector("#ik_sch_book_modal");
								const servicesData = container.querySelectorAll(".ik_sch_book_data_services_data");
								const listServices = container.querySelector(".ik_sch_book_list_services");
								const servicesArray = Array.from(servicesData).filter(service => service.hasAttribute("menu_order"));
								servicesArray.sort((a, b) => {
									const menuOrderA = parseInt(a.getAttribute("menu_order"));
									const menuOrderB = parseInt(b.getAttribute("menu_order"));
									return menuOrderA - menuOrderB;
								});
								while (listServices.firstChild) {
									listServices.removeChild(listServices.firstChild);
								}
								servicesArray.forEach(service => {
									listServices.appendChild(service);
								});
                            }
                        }
                    });    
                });
            }

            function ik_sch_book_update_date_ajax() {

                jQuery.ajax({
                    url: "'.admin_url("admin-ajax.php").'",
                    type: "POST",
                    data: {
                        action: "ik_sch_book_ajax_get_location_dates",
                        branch_id: ik_sch_book_branch_id
                    },
                    success: function(response) {
                        let form_modal = jQuery("#ik_sch_book_modal_booking_form");
                        form_modal.find(".datepicker").val( "" );
                        form_modal.find(".timepicker").prop( "disabled", true);
                        form_modal.find(".timepicker").val( "" );
                        form_modal.find(".datepicker").datepicker( "destroy" );
                        form_modal.find(".datepicker").prop( "disabled", false);
            
                        form_modal.find(".datepicker").datepicker({
                            minDate: "+1d",               
                            dateFormat: format_date,
                            maxDate: limit_days,
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
            jQuery("body").on("change", "#ik_sch_book_modal_booking_form .datepicker", function() {
                var selectedDate = jQuery(this).val();
                var form_element = jQuery("#ik_sch_book_modal_booking_form");
                jQuery(this).removeClass("invalid_data");

                jQuery.ajax({
                    url: "'.admin_url('admin-ajax.php').'",
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
            
            function ik_sch_update_hours(hours) {
                const times = jQuery("#ik_sch_timepicker_modal").find(".ui-menu-item a");

                times.each(function() {
                    let time = jQuery(this).text();
                    if (!ik_sch_non_available_time(time, hours)) {
                        jQuery(this).parent().remove();
                    }
                });
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
            function updateUserSessionPanel() {
                jQuery.ajax({
                    url: "'.admin_url("admin-ajax.php").'",
                    type: "POST",
                    data: {
                        action: "ik_sch_book_ajax_service_selected_session",
                        branch_id: ik_sch_book_branch_id
                    },
                    success: function(response) {
                        jQuery("#ik_sch_book_book_service_panel").remove();
                        jQuery("#ik_sch_book_modal").modal("hide");
                        jQuery("#ik_sch_book_modal").remove();
                        jQuery("body").append("<div id=\"ik_sch_book_book_service_panel\"></div>");
                        jQuery("#ik_sch_book_book_service_panel").html(response.panel_html);
                        jQuery("#ik_sch_book_modal").appendTo("body");
                        jQuery("#ik_sch_book_data_services_book .ik_sch_book_data_select_service.selected_btn").each(function() {
                            jQuery(this).removeClass("selected_btn");
                            jQuery(this).text(ik_sch_book_select_text);
                        });
                        jQuery("#ik_sch_book_data_services_book .ik_sch_book_data_select_wc_service.selected_btn").each(function() {
                            jQuery(this).removeClass("selected_btn");
                            jQuery(this).text(ik_sch_book_select_text);
                        });
                        if (response.hasOwnProperty("services") && Array.isArray(response.services)) {
                            response.services.forEach(service => {
                                jQuery(".ik_sch_book_data_services_data[data_id="+service+"]").find(".ik_sch_book_data_select_service").addClass("selected_btn");
                                jQuery(".ik_sch_book_data_services_data[data_id="+service+"]").find(".ik_sch_book_data_select_service").text(ik_sch_book_selected_text);
                                jQuery(".ik_sch_book_data_services_data[data_id="+service+"]").find(".ik_sch_book_data_select_wc_service").addClass("selected_btn");
                                jQuery(".ik_sch_book_data_services_data[data_id="+service+"]").find(".ik_sch_book_data_select_wc_service").text(ik_sch_book_selected_text);
                            });
                        }
                        jQuery("#ik_sch_book_book_service_panel .ik_sch_book_book_service_panel_content").removeClass("hidden");
                    }
                });
            }
            jQuery("body").on("click", "#ik_sch_book_book_service_panel_book_btn", function() {
              jQuery("#ik_sch_book_book_service_panel .ik_sch_book_book_service_panel_content").addClass("hidden");
              ik_sch_book_update_date_ajax();
            });
            jQuery("body").on("shown.bs.modal", "#ik_sch_book_modal", function() {
                jQuery("#ik_sch_book_book_service_panel .ik_sch_book_book_service_panel_content").addClass("hidden");
                ik_sch_book_update_date_ajax();
              });
            jQuery("body").on("hidden.bs.modal", "#ik_sch_book_modal", function() {
              jQuery("#ik_sch_book_book_service_panel .ik_sch_book_book_service_panel_content").removeClass("hidden");
            });
            updateUserSessionPanel();

            function ik_sch_book_toggleCategory(category) {
                var allCategoryNames = document.querySelectorAll(".ik_sch_book_data_services_category_name");
                var allDataElements = document.querySelectorAll(".ik_sch_book_data_services_data");
            
                var show = (category.classList.contains("show_services")) ? false : true;

                var relatedDataElements = category.parentElement.querySelectorAll(".ik_sch_book_data_services_data");
            
                allDataElements.forEach(function(dataElement) {
                    dataElement.classList.remove("show_services");
                });
            
                allCategoryNames.forEach(function(category) {
                    var icon = category.querySelector("i");
                    icon.classList.remove("fa-chevron-down");
                    icon.classList.add("fa-chevron-up");
                    category.classList.remove("show_services");
                });
            
                if(show){
                    relatedDataElements.forEach(function(dataElement) {
                        dataElement.classList.add("show_services");
                    });
                    var icon = category.querySelector("i");
                    icon.classList.remove("fa-chevron-up");
                    icon.classList.add("fa-chevron-down");
                    category.classList.add("show_services");
                }
            
            }
            
            document.getElementById("ik_sch_book_data_services_book").addEventListener("click", function(event) {
                var clickedCategory = event.target.closest(".ik_sch_book_data_services_category_name");
                if (clickedCategory) {
                    ik_sch_book_toggleCategory(clickedCategory);
                }
                event.preventDefault();
            });

            jQuery("#ik_sch_book_data_services_book").on("click", ".ik_sch_book_data_select_service", function(){	
                let service_id = jQuery(this).parent().parent().attr("data_id");
                let action_btn_select = "";
                let button = jQuery(this);
                button.attr("disabled","disabled");
                if (jQuery(this).hasClass("selected_btn")) {
                    action_btn_select = "remove";
                } else {
                    action_btn_select = "add";                
                }

                jQuery.ajax({
                    url: "'.admin_url('admin-ajax.php').'",
                    type: "POST",
                    data: {
                    action: "ik_sch_book_ajax_add_service_for_location",
                        service_id: service_id,
                        branch_id: ik_sch_book_branch_id,
                        action_btn_select: action_btn_select
                    },
                    success: function(response) {
                        updateUserSessionPanel();
                        button.removeAttr("disabled");
                    },
                    error: function(xhr, status, error) {
                    console.error(error);
                    }
                });	
				return false;
			}); 
            jQuery("body").on("click", ".ik_sch_book_data_select_wc_service", function(){	
                let service_id = jQuery(this).parent().parent().attr("data_id");
                let action_btn_select = "";
                let button = jQuery(this);
                button.attr("disabled","disabled");
                button.addClass("adding_to_cart");
                if (jQuery(this).hasClass("selected_btn")) {
                    action_btn_select = "remove";
                } else {
                    action_btn_select = "add";
                }

                jQuery.ajax({
                    url: "'.admin_url('admin-ajax.php').'",
                    type: "POST",
                    data: {
                    action: "ik_sch_book_ajax_add_wc_service_for_location",
                        service_id: service_id,
                        branch_id: ik_sch_book_branch_id,
                        action_btn_select: action_btn_select,
                        url_location: ik_sch_book_branch_url
                    },
                    success: function(response) {
                        button.removeClass("adding_to_cart");
                        updateUserSessionPanel();
                        jQuery("body").trigger("wc_fragment_refresh");
                        button.removeAttr("disabled");
                    },
                    error: function(xhr, status, error) {
                    console.error(error);
                    }
                });	
				return false;
			}); 
            jQuery("body").on("click", ".ik_sch_book_modal_remove_service", function(){	
                let service_id = jQuery(this).attr("data_id");
                jQuery.ajax({
                    url: "'.admin_url('admin-ajax.php').'",
                    type: "POST",
                    data: {
                    action: "ik_sch_book_ajax_add_service_for_location",
                        service_id: service_id,
                        branch_id: ik_sch_book_branch_id,
                        action_btn_select: "remove"
                    },
                    success: function(response) {
                        updateUserSessionPanel();
                    },
                    error: function(xhr, status, error) {
                    console.error(error);
                    }
                });	
				return false;
			}); 
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
            jQuery(document).on("blur", "#ik_sch_book_modal_booking_form .ik_sch_field", function(){
                let input_field = jQuery(this);
                let input_name = input_field.attr("name");
                let valid_value = false;

                if(input_name == "name_customer"){
                    valid_value = ik_sch_get_input_form("ik_sch_field_form_name_customer");
                } else if(input_name == "email_address"){
                    valid_value = ik_sch_get_input_form("ik_sch_field_form_email_address");
                } else if(input_name == "phone"){
                    valid_value = ik_sch_get_input_form("ik_sch_field_form_phone");
                }
                if(valid_value){
                    jQuery.ajax({
                        url: "'.admin_url('admin-ajax.php').'",
                        type: "POST",
                        data: {
                        action: "ik_sch_book_ajax_update_book_session_data_fields",
                            input_name: input_name,
                            input_value: valid_value,
                        },
                        success: function(response) {
                            return;
                        },
                        error: function(xhr, status, error) {
                        console.error(error);
                        }
                    });	
                }
            });
            jQuery("body").on("click", "#ik_sch_field_form_modal_submit", function(){	
                let phone = ik_sch_get_input_form("ik_sch_field_form_phone");
                let email = ik_sch_get_input_form("ik_sch_field_form_email_address");
                let name = ik_sch_get_input_form("ik_sch_field_form_name_customer");
                let date_input = ik_sch_get_input_form("ik_sch_field_form_date");
                let time_input = ik_sch_get_input_form("ik_sch_field_form_time");
                let btn_submit = jQuery(this);

                if(phone && email && name && date_input && time_input){
                    btn_submit.attr("disabled","disabled");

                    jQuery.ajax({
                        url: "'.admin_url('admin-ajax.php').'",
                        type: "POST",
                        data: {
                        action: "ik_sch_book_ajax_submit_form",
                            selectedDate: date_input,
                            selectedTime: time_input,
                            name: name,
                            email: email,
                            phone: phone,
                            branch_id: ik_sch_book_branch_id,
                            type: "modal"
                        },
                        success: function(response) {
                            if(response.result == true){
                                jQuery("#ik_sch_book_modal").modal("hide").remove();
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

                            var modalConfirmation = document.getElementById("ik_sch_modalConfirmation");
                            if (modalConfirmation) {
                              modalConfirmation.parentNode.removeChild(modalConfirmation);
                            }
        
                            var modalConfirm = document.createElement("div");
                            modalConfirm.id = "ik_sch_modalConfirmation";
                            modalConfirm.className = "modal";                
                            var modalContent = document.createElement("div");
                            modalContent.className = "modal-content";
                            var title = document.createElement("h2");
                            title.textContent = ik_sch_book_confirmation_text;
                            var message = document.createElement("p");
                            message.textContent = response.message;
                        
                            var closeButton = document.createElement("button");
                            closeButton.textContent = ik_sch_book_close_text;
                            closeButton.onclick = function() {
                                modalConfirm.style.display = "none";
                            };
                        
                            modalContent.appendChild(title);
                            modalContent.appendChild(message);
                            modalContent.appendChild(closeButton);
                            modalConfirm.appendChild(modalContent);
                            document.body.appendChild(modalConfirm);
                            modalConfirm.style.display = "block";

                            updateUserSessionPanel();

                        },
                        error: function(xhr, status, error) {
                        console.error(error);
                        }
                    });	
                }
				return false;
			}); 
            document.addEventListener("DOMContentLoaded", function() {
                const datepickerDiv = document.querySelector("#ui-datepicker-div");
                    if (datepickerDiv) {
                    const rect = datepickerDiv.getBoundingClientRect();
                    const topPosition = rect.top;
            
                    if (topPosition > 450) {
                        const schFieldFormDate = document.querySelector("#ik_sch_field_form_date");
                        if (schFieldFormDate) {
                            const newTopPosition = schFieldFormDate.offsetTop + schFieldFormDate.offsetHeight;
                            datepickerDiv.style.top = `${newTopPosition}px`;
                        }
                    }
                }
            });
            
            
        });
        </script>';
    }

    return $output;
    
}
add_shortcode('IK_BOOKING_LOCATION_FORM', 'ik_sch_book_by_location_form');