<?php
/*

Class Ik_Schedule_Available_Days
Created: 20/10/2023
Update: 23/11/2023
Author: Gabriel Caroprese

*/

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

class Ik_Schedule_Available_Days{

    private $db_table_block_dates;
    public $available_days;
    private $locations_data;
    public $timezone;

    public function __construct() {

        global $wpdb;
        $this->db_table_block_dates = $wpdb->prefix . "ik_sch_book_location_block_dates";
        $this->locations_data = new Ik_Schedule_Locations();
        $this->timezone = (empty(get_option('timezone_string'))) ? 'UTC' : get_option('timezone_string');

        $this->available_days = array(
            'day1' => array(
                'opentime' => 'day1openingopen', 
                'closetime' => 'day1openingclose',
                'alwaysclose' => 'closeallday1',			
                'name' => __( 'Monday', 'ik_schedule_location'),			
                ),
            'day2' => array(
                'opentime' => 'day2openingopen', 
                'closetime' => 'day2openingclose',
                'alwaysclose' => 'closeallday2',
                'name' => __( 'Tuesday', 'ik_schedule_location'),			
                ),
            'day3' => array(
                'opentime' => 'day3openingopen', 
                'closetime' => 'day3openingclose',
                'alwaysclose' => 'closeallday3',
                'name' => __( 'Wednesday', 'ik_schedule_location'),			
                ),
            'day4' => array(
                'opentime' => 'day4openingopen', 
                'closetime' => 'day4openingclose',
                'alwaysclose' => 'closeallday4',
                'name' => __( 'Thursday', 'ik_schedule_location'),			
                ),
            'day5' => array(
                'opentime' => 'day5openingopen', 
                'closetime' => 'day5openingclose',
                'alwaysclose' => 'closeallday5',
                'name' => __( 'Friday', 'ik_schedule_location'),			
                ),
            'day6' => array(
                'opentime' => 'day6openingopen', 
                'closetime' => 'day6openingclose',
                'alwaysclose' => 'closeallday6',
                'name' => __( 'Saturday', 'ik_schedule_location'),			
                ),
            'day7' => array(
                'opentime' => 'day7openingopen', 
                'closetime' => 'day7openingclose',
                'alwaysclose' => 'closeallday7',
                'name' => __( 'Sunday', 'ik_schedule_location'),			
                )
        );
    }

    //Get availability table name
    public function get_table_name(){
        return $this->db_table_block_dates;
    }

    //returns the datetime format depending on the type of code language
    public function get_datetime_format($formatType = 'javascript'){
        $formatType = sanitize_text_field($formatType);
        $booking = new Ik_Schedule_Booking();
        $config = $booking->get_config();
        $format_date_id = $config['format_date'];
        $format_date = $booking->format_date($format_date_id,$formatType);

        $datetime = array(
            'format_date' => $format_date,
            'format_time' => $config['format_time'],
            'time_frame' => $config['time_frame'],
        );

        return $datetime;
    }

    //convert date input to DB date format or datetime
    public function sanitize_date_format($date, $datetime = false){
        
        $date_format_visual = $this->get_datetime_format('visual')['format_date'];
        $date_format = $this->get_datetime_format('php')['format_date'];

        //if it includes time
        if($datetime){
            $time_format_option = $this->get_datetime_format()['format_time'];
            $time_format_visual = ($time_format_option == 12) ? '00:00 PM' : '00:00';
            $date_format_visual = $date_format_visual.' '.$time_format_visual;
            $time_format_regex = ($time_format_option == 12) ? '*((0?[1-9]|1[0-2]|2[0-3]):([0-5][0-9]):([0-5][0-9])|((10|11|12):([0-5][0-9])\s*[APap][Mm]))$/' : '*(0?[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/';
            $date_format = ($time_format_option == 12) ? $date_format.' h:i A' : $date_format.' H:i';
            $regex = '/^(0?[1-9]|[12][0-9]|3[01])[-.\/](0?[1-9]|1[0-2])[-.\/](\d{2}|\d{4})\s'.$time_format_regex;

        } else {
            $regex = '/^(0?[1-9]|[12][0-9]|3[01])[-.\/](0?[1-9]|1[0-2])[-.\/](\d{2}|\d{4})$/';
        }

    
        if (preg_match($regex, $date) && strlen($date) === strlen($date_format_visual)) {
            $date_obj = DateTime::createFromFormat($date_format, $date);

            if ($date_obj !== false) {
                if($datetime){
                    $date_db = $date_obj->format('Y-m-d H:i:s');
                } else{
                    $date_db = $date_obj->format('Y-m-d');
                }

                return $date_db;
            }
        }

        return false;
    }

    //I make sure time is correct and I sanitize it
    public function sanitize_time_format($time) {
        $time_format_option = $this->get_datetime_format()['format_time'];
        $time_format = ($time_format_option == 12) ? 'h:i A' : 'H:i';
        $time_format_visual = ($time_format_option == 12) ? '00:00 PM' : '00:00';

        $time = sanitize_text_field($time);

        if (strlen($time) === strlen($time_format_visual) && DateTime::createFromFormat($time_format, $time) !== false) {
            $time_obj = DateTime::createFromFormat($time_format, $time);
            return $time_obj->format('H:i:s');
        }
        
        return '00:00:00';
    }


    //convert time from db formt to current format
    public function convert_date_format($date) {
        $date_format = $this->get_datetime_format('php')['format_date'];

        $date = sanitize_text_field($date);

        if (DateTime::createFromFormat('Y-m-d H:i:s', $date) !== false) {
            $date_obj = DateTime::createFromFormat('Y-m-d H:i:s', $date);

            return $date_obj->format($date_format);
        }
        
        return '';
    }


    //convert time from db formt to current format
    public function convert_time_format($time) {
        $time_format_option = $this->get_datetime_format()['format_time'];
        $time_format = ($time_format_option == 12) ? 'h:i A' : 'H:i';

        $time = sanitize_text_field($time);

        if (DateTime::createFromFormat('H:i:s', $time) !== false) {
            $time_obj = DateTime::createFromFormat('H:i:s', $time);

            return $time_obj->format($time_format);
        }
        
        if($time_format_option == 12){
            return '00:00 PM';
        } else {
            return '00:00';
        }
    }

    //convert date and time from db format to current format
    public function convert_datetime_format($datetime) {

        $date = $this->convert_date_format($datetime);
        $booking_time = substr($datetime, -8); //00:00:00
        $time = $this->convert_time_format($booking_time);
        
        $datetime_formatted = $date.' | '.$time.' '.__( 'Hr', 'ik_schedule_location');

        return $datetime_formatted;
    }



    //to validate there's a record created for location ID
    private function update_db_availability($location_id, $days){
        $location_id = absint($location_id);

        $days = maybe_serialize($days);

        $location_table = $this->locations_data->get_table_name();

        //if location exists
        global $wpdb;
        $query_db_location_availability = "SELECT availability FROM ".$location_table." WHERE id = ".$location_id;
        $location_edit = $wpdb->get_results($query_db_location_availability);

        if(isset($location_edit[0]->availability)){
            $data_days = array (
                'availability'  => $days
            );
            
            global $wpdb;
            $where = [ 'id' => $location_id ];
            $rowResult = $wpdb->update($location_table, $data_days, $where);   

            if($rowResult !== false){
                return $location_id;
            }

        }

        return false;
    }

    //to delete old records about block dates for location ID
    private function delete_db_blockdates($location_id){
        $location_id = absint($location_id);

        global $wpdb;
        $wpdb->delete( $this->db_table_block_dates , array( 'location_id' => $location_id ) );
        
        return true;
    }

    //update location schedules
    public function update_availability($location_id){

        //default not update
        $update = false;    

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ik_sch_book_user_permissions()){

            if (isset($_POST['availability_form'])){
        
                foreach ($this->available_days as $data_day_id => $data_day_opening){
                    
                    if (isset($_POST[$data_day_opening['opentime']]) && isset($_POST[$data_day_opening['closetime']])){
                        $openTimes = $_POST[$data_day_opening['opentime']];
                        $closeTimes = $_POST[$data_day_opening['closetime']];      
                    } else {
                        $alwaysclose = true;
                    }
                                
                    //If store is close that day or days are not set
                    if (isset($_POST[$data_day_opening['alwaysclose']])){
                        $alwaysclose = true;
                    } else {
                        $alwaysclose = false;
                    }
                    
                    //If store is open I save hours
                    if ($alwaysclose == false && is_array($openTimes) && is_array($closeTimes)){
                        foreach ($openTimes as $key => $openTime){

                            $openT[$data_day_id][] = $this->sanitize_time_format($openTime);
                            $closeT[$data_day_id][] = $this->sanitize_time_format($closeTimes[$key]);

                        }
                        
                    } else {
                        $openT[$data_day_id][] = '00:00:00';
                        $closeT[$data_day_id][] = '00:00:00';
                    }

                    //if hours are 00 always is close
                    if(count($openT[$data_day_id]) == 1 && $openT[$data_day_id][0] == '00:00:00' && $closeT[$data_day_id][0] == '00:00:00' ){
                        $alwaysclose = true;
                    }
                    
                    $days[$data_day_id] = array (
                        'opentime' => $openT[$data_day_id],
                        'closetime' => $closeT[$data_day_id],
                        'alwaysclose' => $alwaysclose
                        );
                }
            }
        
            //validate existence of loopenTimescation ID
            $location_id = absint($location_id);
            $location = $this->locations_data->get_location($location_id);

            if($location){

                if(isset($days)){

                    //Create records for location if doesn't exist
                    $this->update_db_availability($location_id, $days);
                     
                }

                //Blocked dates
                if (isset($_POST['dateblock'])){

                    if (is_array($_POST['dateblock'])){

                        //Delete old blocks
                        $this->delete_db_blockdates($location_id);

                        $dateblocks = $_POST['dateblock'];
                        foreach($dateblocks as $key => $dateblock){
                            $block_date = (isset($dateblock)) ? $this->sanitize_date_format($dateblock) : false;
                            
                            $timeblock_start = (isset($_POST['timeblock_start'][$key])) ? date("H:i:s", strtotime(strval($_POST['timeblock_start'][$key]))) : '00:00:00';
                            $timeblock_end = (isset($_POST['timeblock_end'][$key])) ? date("H:i:s", strtotime(strval($_POST['timeblock_end'][$key]))) : '23:59:59';
                            $timeblock_end = ($timeblock_start == $timeblock_end && $timeblock_end == '00:00:00') ? '23:59:59' : $timeblock_end;
                            $block_all = (isset($_POST['block_all'][$key])) ? true : false;

                            if($block_date !== false && $timeblock_start !== $timeblock_end && $timeblock_start < $timeblock_end){

                                if($block_all){
                                    $timeblock_start = '00:00:00';
                                    $timeblock_end = '23:59:59';
                                }

                                $data_block = array(
                                    'location_id'	=> $location_id,
                                    'block_from'	=> $block_date.' '.$timeblock_start,
                                    'block_to'	    => $block_date.' '.$timeblock_end,
                                );

                                global $wpdb;
                                $rowResult = $wpdb->insert($this->db_table_block_dates, $data_block , $format = NULL);

                                if($rowResult !== false){
                                    $update = true;
                                }
             
                            }                        
                        }
                    }    
                }   
            }     
            
            if (isset($_POST['service']) && isset($_GET['location_id']) ){

                $location_id = intval($_GET['location_id']);

                if(is_array($_POST['service']) && $location_id > 0){

                    $count_services = 0;
                    
                    foreach($_POST['service'] as $service){

                        $service = absint($service);

                        if($service > 0 && !isset($service_processed[$service])){
                            $custom_price = (isset($_POST['custom_price'][$count_services])) ? number_format( floatval($_POST['custom_price'][$count_services]), 2, '.', ''  ) : -1;

                            $services_location[] = array(
                                'service_id' =>  $service,
                                'custom_price' =>  $custom_price
                            );
                            
                            //to avoid duplicates
                            $service_processed[$service] = true;
                        }

                        $count_services = $count_services + 1;
                    }

                    if(isset($services_location)){
                        //Delete repeated values
                        $uniqueArrayServices = array();
                        $services_id = array();
                    
                        foreach ($services_location as $subServiceArray) {
                            $service_id = $subServiceArray['service_id'];
                            if (!in_array($service_id, $services_id)) {
                                $uniqueArrayServices[] = $subServiceArray;
                                $services_id[] = $service_id;
                            }
                        }
                        $services = $this->locations_data->update_services($location_id, $services_location);

                        $update = true;
                    }

                }
            } 
            
        }

		return $update;
        
    }

    //get available times for location
    private function get_available_times($location_id){
        $location_id = absint($location_id);

        global $wpdb;
        $query_availability = "SELECT * FROM ".$this->locations_data->get_table_name()." WHERE id = ".$location_id;
        $availability_location = $wpdb->get_results($query_availability);

        // if exist_location is false location is created
        if (isset($availability_location[0]->id)){

            //make sure is not empty and I have serialized data
            if(is_serialized($availability_location[0]->availability)){

                $availability = maybe_unserialize($availability_location[0]->availability);
                
                return $availability;

            }
 
        }

        return false;
    }

    //return the number id of days enabled for location
    public function get_enabled_days($location_id){
        $location_id = absint($location_id);

        $available_days = $this->get_available_times($location_id);

        if(is_array($available_days)){

            $days = '';
            //I check the days
            foreach($available_days as $key=> $available_day){
                //sundays are 0 in js so I have to convert day7 to day0
                $key = ($key == 'day7') ? 'day0' : $key;

                if(isset($available_day['opentime']) && isset($available_day['closetime']) && isset($available_day['alwaysclose'])){
                    //I validate the day the business is operative and not closed
                    if($available_day['opentime'] !== $available_day['closetime'] && !$available_day['alwaysclose']){
                        $day = substr($key, 3, 4);
                        $days .= $day.', ';
                    }
                }
            }

            //I check if something was added to $days
            $length_days_var = strlen($days);

            if ($length_days_var > 2) {
                //I convert into something like [1, 4]
                $days = substr($days, 0, $length_days_var - 2);
                $days = '['.$days.']';

                return $days;
            }

        }

        return false;
    }

    //list available times for location
    public function list_available_times($location_id){
        $location_id = absint($location_id);

        //initialize content
        $availability_list = '';

        $available_times = $this->get_available_times($location_id);

        // if exist_location is false location is created
        $counterDays = 1;

        foreach($this->available_days as $dayName => $available_day){

            if(isset($available_times[$dayName]['alwaysclose'])){
                $checkedClosed = (rest_sanitize_boolean($available_times[$dayName]['alwaysclose'])) ? 'checked loco' : '';
            } else {
                $checkedClosed = '';
            }

            $columnHours = '';

            if(isset($available_times[$dayName]['opentime']) && isset($available_times[$dayName]['closetime'])){
                $openTimes = $available_times[$dayName]['opentime'];
                $closetimes = $available_times[$dayName]['closetime'];

                $counterTimes = 0;
                foreach ($openTimes as $key => $openTime){

                    $openTime = $this->convert_time_format($openTime);
                    $closetime = $this->convert_time_format($available_times[$dayName]['closetime'][$key]);
                    
                    if($counterTimes == 0){
                        $columnHours .= '
                        <div class="ik_sch_book_css_columnhours">
                            <div class="ik_sch_book_css_inputopen inputopen0">
                                <input type="text" autocomplete="off" name="'. $available_day['opentime'].'[]" class="timepicker" placeholder="00:00" value="'.$openTime.'">
                                <span class="time_separator">-</span>
                            </div>
                            <div class="ik_sch_book_css_inputclose inputclose0">
                                <input type="text" autocomplete="off" name="'. $available_day['closetime'].'[]" class="timepicker" placeholder="00:00" value="'.$closetime.'">
                            </div>
                            <div class="ik_sch_book_css_addhours">
                                <span class="dashicons dashicons-plus"></span>
                            </div>
                            <div class="ik_sch_book_css_closeallday">
                                <label>
                                    <input type="checkbox" '.$checkedClosed.' name="closeallday'.$counterDays .'" value="1">
                                    <span>'. __( 'Closed', 'ik_schedule_location') .'</span>
                                </label>
                            </div>
                        </div>';
                    } else {
                        $columnHours .= '
                        <div class="ik_sch_book_css_columnhours ik_sch_book_additionalhours">
                            <div class="ik_sch_book_css_inputopen inputopen'.$counterTimes.'">
                                <input type="text" autocomplete="off" name="'. $available_day['opentime'].'[]" placeholder="00:00"  value="'.$openTime.'">
                                <span class="time_separator">-</span> 
                            </div>
                            <div class="ik_sch_book_css_inputclose inputclose'.$counterTimes.'">
                                <input type="text" autocomplete="off" name="'. $available_day['closetime'].'[]" placeholder="00:00"  value="'.$closetime.'">
                            </div>
                            <div class="ik_sch_book_css_deletehours">
                                <span class="dashicons dashicons-trash"></span>
                            </div>
                        </div>';
                    }

                    $counterTimes = $counterTimes + 1;
                }
            } else {
                $columnHours .= '
                <div class="ik_sch_book_css_columnhours">
                    <div class="ik_sch_book_css_inputopen inputopen0">
                        <input type="text" autocomplete="off" name="'. $available_day['opentime'].'[]" class="timepicker" placeholder="00:00"">
                        <span class="time_separator">-</span>
                    </div>
                    <div class="ik_sch_book_css_inputclose inputclose0">
                        <input type="text" autocomplete="off" name="'. $available_day['closetime'].'[]" class="timepicker" placeholder="00:00"">
                    </div>
                    <div class="ik_sch_book_css_addhours">
                        <span class="dashicons dashicons-plus"></span>
                    </div>
                    <div class="ik_sch_book_css_closeallday">
                        <label>
                            <input type="checkbox" '.$checkedClosed.' name="closeallday'.$counterDays .'" value="1">
                            <span>'. __( 'Closed', 'ik_schedule_location') .'</span>
                        </label>
                    </div>
                </div>';
            }

            $availability_list .= '
            <div class="ik_sch_book_css_rowday" data_type1="openingopen" data_type2="openingclose" day_n="'.$counterDays .'">
                <div class="ik_sch_book_css_columnday">
                    <span>'. $available_day['name'] .'</span>
                </div>
                '.$columnHours.'
            </div>';

            $counterDays = $counterDays + 1;
        
        }

        return $availability_list;
    }

    //get blocked date and times for location
    public function get_blocked_datetimes($location_id){
        $location_id = absint($location_id);

        global $wpdb;
        $query_blockedates = "SELECT * FROM ".$this->db_table_block_dates." WHERE location_id = ".$location_id;
        $blocked_datetimes_location = $wpdb->get_results($query_blockedates);

        // if exist_location is false location is created
        if (isset($blocked_datetimes_location[0]->id)){

            foreach ($blocked_datetimes_location as $key => $blockedDate){

                $date = $this->convert_date_format($blockedDate->block_from);
                $time_from_db_format = date("H:i:s", strtotime($blockedDate->block_from));
                $time_from = $this->convert_time_format($time_from_db_format);
                $time_to_db_format = date("H:i:s", strtotime($blockedDate->block_to));
                $time_to = $this->convert_time_format($time_to_db_format);

                $datesblocked_array[$key]['date'] = $date;
                $datesblocked_array[$key]['fromtime'] = $time_from;
                $datesblocked_array[$key]['totime'] = $time_to;
                $datesblocked_array[$key]['allday'] = ($time_from_db_format === '00:00:00' && $time_to_db_format === '23:59:59') ? true : false;

            }

            $datesblocked = (object) $datesblocked_array;


            return $datesblocked;

        }

        return false;
    }

    //get blocked dates for location for datepicker
    public function get_blocked_dates_js($location_id){
        $location_id = absint($location_id);

        $blocked_datetimes = $this->get_blocked_datetimes($location_id);

        if ($blocked_datetimes){

            $date_blocked = '';
            foreach ($blocked_datetimes as $key => $blockedDate){

                $date = $this->convert_date_format($blockedDate->block_from);
                $time_from_db_format = date("H:i:s", strtotime($blockedDate->block_from));
                $time_from = $this->convert_time_format($time_from_db_format);
                $time_to_db_format = date("H:i:s", strtotime($blockedDate->block_to));
                $time_to = $this->convert_time_format($time_to_db_format);

                $datesblocked_array[$key]['date'] = $date;
                $datesblocked_array[$key]['fromtime'] = $time_from;
                $datesblocked_array[$key]['totime'] = $time_to;
                $datesblocked_array[$key]['allday'] = ($time_from_db_format === '00:00:00' && $time_to_db_format === '23:59:59') ? true : false;

                if($blockedDate['allday'] == true){
                    $date_blocked .= '"'.$blockedDate['date'].'", ';
                }

            }

            //I check if something was added to $days
            $length_blockeddates_var = strlen($date_blocked);

            if ($length_blockeddates_var > 2) {
                //I convert into something like [1, 4]
                $date_blocked = substr($date_blocked, 0, $length_blockeddates_var - 2);
                $date_blocked = '['.$date_blocked.']';

                return $date_blocked;
            }

        }

        return '[]';
    }

    //get available times for specific date and location for datepiker
    public function get_available_times_js($location_id, $date, $delivery_time, $format_time = 24){
        $delivery_time = absint($delivery_time);
        $location_id = absint($location_id);
        date_default_timezone_set($this->timezone);

        //I figure out which day is that date
        $day_number = $date->format('N');

        $days_array = [
            1 => 'day1',
            2 => 'day2',
            3 => 'day3',
            4 => 'day4',
            5 => 'day5',
            6 => 'day6',
            7 => 'day7',
        ];
        $day_selected = $days_array[$day_number];

        //return opening hours
        $available_times = $this->get_available_times($location_id);

        if($available_times){

            //if there're services with delivery time to discount from time to make sure there's time to deliver the service
            if($delivery_time > 0){

                //depending on the time format
                $type_time = ($format_time == '24') ? 'H:i:s' : 'h:i A';

                //discount delivery time to close time
                foreach($available_times[$day_selected]['closetime'] as $closing_time){

                    // Convert time to object
                    $time_close_obj = DateTime::createFromFormat('H:i:s', $closing_time);

                    // I discount the delivery time
                    $time_close_obj->modify("-" . $delivery_time . " minutes");

                    // get the closing time considering delivery of service
                    $close_time_to_deliver[] = $time_close_obj->format($type_time);
                    $last_time_to_deliver = $time_close_obj->format($type_time);
                }
                $openingTimes = array(
                    'times_data' => array(
                        'opentime' => $available_times[$day_selected]['opentime'],
                        'closetime' => $close_time_to_deliver,
                        'alwaysclose' => $available_times[$day_selected]['alwaysclose']
                    ),
                    'mintime' => $available_times[$day_selected]['opentime'][0],
                    'maxtime' => $last_time_to_deliver,
                );
            } else {
                //count the amount to go to the last record
                $last_index_available_times = count($available_times[$day_selected]['closetime']) - 1;
                $last_time_to_deliver = $available_times[$day_selected]['closetime'][$last_index_available_times];

                $openingTimes = array(
                    'times_data' => $available_times[$day_selected],
                    'mintime' => $available_times[$day_selected]['opentime'][0],
                    'maxtime' => $last_time_to_deliver,
                );
            }

            return json_encode($openingTimes);
        }

        return 'error';
    }

    //validates if date is available for specific location
    public function is_date_available($location_id, $date){

        return true; //for now for testing
    }

    //validates if time is available for specific location
    public function is_time_available($location_id, $date, $time){

        return true; //for now for testing
    }

    // Return services delivered in specifid location
    public function get_location_services($location_id = 0){
        $location_id = absint($location_id);

        $services_data = new Ik_Schedule_Services();
        $services = $services_data->get_services_by_location($location_id);

        return $services;
    }

    //Return select with services
    public function get_location_services_select($selected_id = 0){
        $selected_id = sanitize_text_field($selected_id);

        $options_data_list = '<select name="service[]" class="ik_sch_book_service_select"><option>'. __( 'Select Service', 'ik_schedule_location').'</option>';

        $services_data = new Ik_Schedule_Services();
        $services = $services_data->get_services_list();

        if($services){

            foreach( $services as $service ) {
                $select = ($selected_id === $service['id']) ? 'selected' : '';
                $cat_name_show = ($service['cat_name'] == 'None') ? '' : ' | '.$service['cat_name'];
            
                $options_data_list .= '<option '.$select.' value="'.$service['id'].'">#'.$service['id'].' - '.$service['name'].$cat_name_show.' </option>';
            }
        } else {
            $options_data_list .= '<option>'. __( 'No Services Added', 'ik_schedule_location').'</option>';            
        }
        
        $options_data_list .= '</select>';    
        
        return $options_data_list;
    }
    
}

?>