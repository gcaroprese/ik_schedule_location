<?php
/*

Booking Calendar Template
Created: 16/08/2023
Update: 12/11/2023
Author: Gabriel Caroprese

*/

if ( ! defined('ABSPATH')) exit('restricted access');
$bookings = new Ik_Schedule_Booking;
$default_cal_color = $bookings->default_calendar_color;
$config = $bookings->get_config();
$starts_on_Monday = ($config['calendar_starts_monday']) ? 'true' : 'false';

?>
<div id="ik_sch_book_calendar_controls">
    <button class="button" id="prev-week" style="display:none"><?php echo __( 'Previous Week', 'ik_schedule_location'); ?></button>
    <button class="button" id="prev-month" style="display:none"><?php echo __( 'Previous Month', 'ik_schedule_location'); ?></button>
    <select id="year-select"></select>
    <select id="month-select">
        <option value="1"><?php echo __( 'January', 'ik_schedule_location'); ?></option>
        <option value="2"><?php echo __( 'February', 'ik_schedule_location'); ?></option>
        <option value="3"><?php echo __( 'March', 'ik_schedule_location'); ?></option>
        <option value="4"><?php echo __( 'April', 'ik_schedule_location'); ?></option>
        <option value="5"><?php echo __( 'May', 'ik_schedule_location'); ?></option>
        <option value="6"><?php echo __( 'June', 'ik_schedule_location'); ?></option>
        <option value="7"><?php echo __( 'July', 'ik_schedule_location'); ?></option>
        <option value="8"><?php echo __( 'August', 'ik_schedule_location'); ?></option>
        <option value="9"><?php echo __( 'September', 'ik_schedule_location'); ?></option>
        <option value="10"><?php echo __( 'October', 'ik_schedule_location'); ?></option>
        <option value="11"><?php echo __( 'November', 'ik_schedule_location'); ?></option>
        <option value="12"><?php echo __( 'December', 'ik_schedule_location'); ?></option>
    </select>
    <button class="button-primary" id="go-to-date"><?php echo __( 'Go to Date', 'ik_schedule_location'); ?></button>
    <button class="button" id="next-week" style="display:none"><?php echo __( 'Next Week', 'ik_schedule_location'); ?></button>
    <button class="button" id="next-month" style="display:none"><?php echo __( 'Next Month', 'ik_schedule_location'); ?></button>
    <button class="button" id="week-view"><?php echo __( 'Week View', 'ik_schedule_location'); ?></button>
    <?php echo $bookings->locations->get_locations_select(); ?>

</div>

<div id="ik_sch_book_calendar_container"></div>
<script>
    jQuery(document).ready(function ($) {

        var startOnMonday = <?php echo $starts_on_Monday; ?>;
        var adjustedDayMonth = startOnMonday ? 1 : 0;
        var default_cal_color = '<?php echo $default_cal_color; ?>';
        var ik_sch_book_url_calendar = new URL(window.location.href);
        var week = ik_sch_book_url_calendar.searchParams.get("week");
        if (week === null || week === '') {
            ik_sch_book_week_number = 0;
            var ik_sch_book_url_week = false;
        } else {
            ik_sch_book_week_number = week;
            var ik_sch_book_url_week = true;
        }

        function ik_sch_js_generateCalendar() {
            var calendarContainer = document.getElementById('ik_sch_book_calendar_container');
            var calendarHTML = '';

            let location_id = ik_sch_book_url_calendar.searchParams.get("location_id");
            let month = ik_sch_book_url_calendar.searchParams.get("month");
            let year = ik_sch_book_url_calendar.searchParams.get("year");

            var data = {
                action: 'ik_sch_book_ajax_get_month_data',
                "location_id": location_id,
                "month": month,
                "year": year
            };

            jQuery.post( ajaxurl, data, function(response) {
                if (response){
                    monthData = response;

                    monthData.days.forEach(function (dayName, index) {
                        var adjustedIndex = startOnMonday ? (index + 1) % 7 : index;
                        calendarHTML += '<div class="calendar-day weekday_name">' + monthData.days[adjustedIndex] + '</div>';
                    });

                    var firstDay = new Date(monthData.year, monthData.number - 1, 1);
                    var startDay = firstDay.getDay();
                    var fromlastmonthDay = monthData.weekday_lastmonth + adjustedDayMonth;
                    var lastMonth = monthData.number - 1;
                    var nextMonth = monthData.number + 1;

                    for (var i = 0; fromlastmonthDay <= monthData.last_m_n_days; i++) {
                        var LastMonthDate = new Date(monthData.year, lastMonth - 1, fromlastmonthDay);
                        var dayHasEvent = monthData.last_m_bookings !== false && monthData.last_m_bookings.some(function (booking) {
                            var bookingDate = new Date(booking.booking_date);
                            return bookingDate.toDateString() === bookingDate.toDateString();
                        });

                        calendarHTML += '<div class="calendar-day last-month">';
                        calendarHTML += '<div class="day-number">' + fromlastmonthDay +'</div>';

                        if (dayHasEvent) {
                            calendarHTML += '<div class="event-details">';
                            monthData.last_m_bookings.forEach(function (booking) {
                                var bookingDate = new Date(booking.booking_date);
                                if (LastMonthDate.toDateString() === bookingDate.toDateString()) {

                                    var bookingDateTime = '';
                                    var bookingDate = new Date(booking.booking_date);
                                    var hours = bookingDate.getHours();
                                    var minutes = bookingDate.getMinutes();
                                    
                                    bookingTime = hours + ':' + (minutes < 10 ? '0' : '') + minutes;
                                    bookingDateTime = bookingTime;

                                    let background_color = (booking.color == '') ? default_cal_color : booking.color;

                                    calendarHTML += '<div style="background-color:'+background_color+'" class="ik_sch_book_event" iddata="'+booking.id+'" datetime="'+bookingDateTime+'"><span class="event_data_name">' + booking.f_name + ' ' + booking.lastname + '</span> <span class="dashicons dashicons-edit"></span></div>';
                                }
                            });
                            calendarHTML += '</div>';
                        }
                        calendarHTML += '</div>';
                        fromlastmonthDay = fromlastmonthDay + 1;
                    }
                    var daysInMonth = monthData.n_days;
                    var daysToRender = daysInMonth + startDay;

                    for (var day = 1; day <= daysToRender; day++) {
                        if (day > startDay) {
                            var dayOfMonth = day - startDay;
                            var currentDate = new Date(monthData.year, monthData.number - 1, dayOfMonth);

                            var dayHasEvent = monthData.bookings !== false && monthData.bookings.some(function (booking) {
                                var bookingDate = new Date(booking.booking_date);
                                return bookingDate.toDateString() === currentDate.toDateString();
                            });

                            calendarHTML += '<div class="calendar-day">';
                            calendarHTML += '<div class="day-number">' + dayOfMonth + '</div>';

                            if (dayHasEvent) {
                                calendarHTML += '<div class="event-details">';
                                monthData.bookings.forEach(function (booking) {
                                    var bookingDate = new Date(booking.booking_date);
                                    if (currentDate.toDateString() === bookingDate.toDateString()) {

                                        var bookingDateTime = '';
                                        var bookingDate = new Date(booking.booking_date);
                                        var hours = bookingDate.getHours();
                                        var minutes = bookingDate.getMinutes();
                                        
                                        bookingTime = hours + ':' + (minutes < 10 ? '0' : '') + minutes;
                                        bookingDateTime = bookingTime;

                                        let background_color = (booking.color == '') ? default_cal_color : booking.color;
                                        calendarHTML += '<div style="background-color:'+background_color+'" class="ik_sch_book_event" iddata="'+booking.id+'" datetime="'+bookingDateTime+'"><span class="event_time">' + bookingDateTime + '</span> <span class="event_data_name">' + booking.f_name + ' ' + booking.lastname + '</span> <span class="dashicons dashicons-edit"></span></div>';
                                    }
                                });
                                calendarHTML += '</div>';
                            }

                            calendarHTML += '</div>';
                        }
                    }

                    var endDay = (startDay + daysInMonth) % 7;
                    var nextMonthDay = 1;
                    var next_month_days = monthData.next_m_n_days + adjustedDayMonth;

                    for (var i = 0; i < next_month_days; i++) {

                        var NextMonthDate = new Date(monthData.year, nextMonth - 1, nextMonthDay);
                        var dayHasEvent = monthData.next_m_bookings !== false && monthData.next_m_bookings.some(function (booking) {
                            var bookingDate = new Date(booking.booking_date);
                            return bookingDate.toDateString() === NextMonthDate.toDateString();
                        });

                        calendarHTML += '<div class="calendar-day next-month">';
                        calendarHTML += '<div class="day-number">' + nextMonthDay + '</div>';

                        if (dayHasEvent) {
                            calendarHTML += '<div class="event-details">';
                            monthData.next_m_bookings.forEach(function (booking) {
                                var bookingDate = new Date(booking.booking_date);
                                if (NextMonthDate.toDateString() === bookingDate.toDateString()) {
                                    var bookingDateTime = '';
                                    var bookingDate = new Date(booking.booking_date);
                                    var hours = bookingDate.getHours();
                                    var minutes = bookingDate.getMinutes();
                                    
                                    bookingDateTime = hours + ':' + (minutes < 10 ? '0' : '') + minutes;
                                    let background_color = (booking.color == '') ? default_cal_color : booking.color;

                                    calendarHTML += '<div style="background-color:'+background_color+'" class="ik_sch_book_event" iddata="'+booking.id+'" datetime="'+bookingDateTime+'"><span class="event_time">' + bookingDateTime + '</span> <span class="event_data_name">' + booking.f_name + ' ' + booking.lastname + '</span> <span class="dashicons dashicons-edit"></span></div>';

                                }
                            });
                            calendarHTML += '</div>';
                        }
                        calendarHTML += '</div>';

                        nextMonthDay = nextMonthDay + 1;
                    }

                    calendarContainer.innerHTML = calendarHTML;

                    if (ik_sch_book_url_calendar.searchParams.has("week")) {
                        ik_sch_book_week_number = parseInt(ik_sch_book_url_calendar.searchParams.get("week"));
                        ik_sch_book_url_week = true;
                        ik_sch_book_weekView();
                    } else {
                        var ik_sch_book_url_week = false;
                        ik_sch_book_weekView();
                    }
                    
                }
            }).fail(function() {
                ik_sch_js_generateCalendar();
            });
        }
        var yearSelect = document.getElementById("year-select");
        var monthSelect = document.getElementById("month-select");
        var prevMonthButton = document.getElementById("prev-month");
        var prevWeekButton = document.getElementById("prev-week");
        var nextMonthButton = document.getElementById("next-month");
        var nextWeekButton = document.getElementById("next-week");
        var calendarContainer = document.getElementById("ik_sch_book_calendar_container");
        var go_to_date = document.getElementById("go-to-date");
        var select_location = document.getElementById("ik_sch_book_location_select");

        var currentDate = new Date();
        var currentYear = currentDate.getFullYear();
        var urlParams = new URLSearchParams(window.location.search);
        var urlMonth = urlParams.get('month');
        var urlYear = urlParams.get('year');

        var currentMonth;
        var selectedYear;
        if (urlMonth !== null) {
            currentMonth = parseInt(urlMonth);
        } else {
            currentMonth = currentDate.getMonth() + 1;
        }

        if (urlYear !== null) {
            selectedYear = parseInt(urlYear);
        } else {
            selectedYear = currentYear;
        }

        for (var year = currentYear - 10; year <= currentYear + 10; year++) {
            var option = document.createElement("option");
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        }

        yearSelect.value = selectedYear;
        monthSelect.value = currentMonth;

        function ik_sch_js_updateURL() {
            var selectedYear = yearSelect.value;
            var selectedMonth = monthSelect.value;

            var newURL = window.location.href + "&year=" + selectedYear+"&month="+selectedMonth;
            window.history.replaceState({}, "", newURL);

            window.location.href = newURL;

        }

        prevMonthButton.addEventListener("click", function () {
            var selectedYear = parseInt(yearSelect.value);
            var selectedMonth = parseInt(monthSelect.value);

            if (selectedMonth === 1) {
                yearSelect.value = selectedYear - 1;
                monthSelect.value = 12;
            } else {
                monthSelect.value = selectedMonth - 1;
            }

            ik_sch_js_updateURL();
        });

        prevWeekButton.addEventListener("click", function () {

            if (ik_sch_book_week_number - 1 < 0) {
                ik_sch_book_week_number = 4;
            } else {
                ik_sch_book_week_number = ik_sch_book_week_number - 1;
            }

            var newURL = window.location.href + "&week=" + ik_sch_book_week_number;
            window.history.replaceState({}, "", newURL);
            window.location.href = newURL;
        });

        nextWeekButton.addEventListener("click", function () {

            if (ik_sch_book_week_number + 1 > 4) {
                ik_sch_book_week_number = 0;
            } else {
                ik_sch_book_week_number = ik_sch_book_week_number + 1;
            }

            var newURL = window.location.href + "&week=" + ik_sch_book_week_number;
            window.history.replaceState({}, "", newURL);
            window.location.href = newURL;
        });

        nextMonthButton.addEventListener("click", function () {
            var selectedYear = parseInt(yearSelect.value);
            var selectedMonth = parseInt(monthSelect.value);

            if (selectedMonth === 12) {
                yearSelect.value = selectedYear + 1;
                monthSelect.value = 1;
            } else {
                monthSelect.value = selectedMonth + 1;
            }

            ik_sch_js_updateURL();
        });

        yearSelect.addEventListener("change", function () {
            ik_sch_js_updateURL();
            ik_sch_js_generateCalendar();
        });
        monthSelect.addEventListener("change", function () {
            ik_sch_js_updateURL();
            ik_sch_js_generateCalendar();
        });
        go_to_date.addEventListener("click", function () {
            var baseUrl = window.location.href.split('?')[0];
            var queryParams = new URLSearchParams(window.location.search);
            queryParams.delete('year');
            queryParams.delete('month');
            var newUrl = baseUrl + '?' + queryParams.toString();
            window.location.href = newUrl;
        });
        select_location.addEventListener("change", function () {
            var newURL = window.location.href + "&year=" + yearSelect.value+"&month="+monthSelect.value+"&location_id="+select_location.value;
            window.history.replaceState({}, "", newURL);

            ik_sch_js_generateCalendar();

            window.location.href = newURL;
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
        jQuery("#ik_sch_book_calendar_container").on( "click", '.ik_sch_book_event', function(e) {
                e.preventDefault();
                let button = jQuery(this);
                let iddata = button.attr('iddata');
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
        var toggleViewButton = document.getElementById('week-view');
        toggleViewButton.addEventListener('click', ik_sch_book_weekView_true);
        function ik_sch_book_weekView_true() {
            if(ik_sch_book_url_week){
                ik_sch_book_url_week = false;
            } else {
                ik_sch_book_url_week = true;
            }
            ik_sch_book_weekView();
        }

        function ik_sch_book_weekView() {
            
            if (ik_sch_book_url_week) {
                toggleViewButton.textContent = '<?php echo __( 'Month View', 'ik_schedule_location'); ?>';
                hideNonCurrentWeek();
            } else {
                toggleViewButton.textContent = '<?php echo __( 'Week View', 'ik_schedule_location'); ?>';
                showAllDays();
            }
        }
        function hideNonCurrentWeek() {
            prevMonthButton.style.display = 'none';
            nextMonthButton.style.display = 'none';
            prevWeekButton.style.display = 'initial';
            nextWeekButton.style.display = 'initial';

            var calendarDays = document.querySelectorAll('.calendar-day');
            var from_week_number_days = (ik_sch_book_week_number)*7+7;
            var week_number_days = (ik_sch_book_week_number + 1)*7+7;
            for (var i = 7; i < calendarDays.length; i++) {
                calendarDays[i].style.display = 'none';
            }            
            for (var i = from_week_number_days; i < week_number_days; i++) {
                calendarDays[i].style.display = '';
            }
            var viewportHeight = window.innerHeight-200;

            for (var i = 7; i <= calendarDays.length; i++) {
                calendarDays[i].style.height = viewportHeight + 'px';
            }

        }
        function showAllDays() {
            prevMonthButton.style.display = 'initial';
            nextMonthButton.style.display = 'initial';
            prevWeekButton.style.display = 'none';
            nextWeekButton.style.display = 'none'; 
            var calendarDays = document.querySelectorAll('.calendar-day');
            for (var i = 0; i < calendarDays.length; i++) {
                calendarDays[i].style.display = '';
                calendarDays[i].style.height = '';
            }
            if (ik_sch_book_url_calendar.searchParams.has("week")) {
                ik_sch_book_url_calendar.searchParams.delete('week');
                var newURL = ik_sch_book_url_calendar.toString();
                window.history.replaceState({}, document.title, newURL);
            }
        }
        ik_sch_js_generateCalendar();
    });
</script>