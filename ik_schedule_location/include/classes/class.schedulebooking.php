<?php
/*

Class Ik_Schedule_Booking
Created: 20/11/2022
Update: 18/03/2024
Author: Gabriel Caroprese

*/

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

class Ik_Schedule_Booking{
    
    public $config;
    public $qtyListing;
    private $db_table_requests;
    public $services;
    public $locations;
    public $staff;
    public $available_days;
    private $dateFormats;
    private $timeFrames;
    private $currencies;
    private $currencies_specs;
    public $requests_admin_url;
    public $default_calendar_color;
    public $dayNames;
    public $dayNamesAbv;
    public $monthNames;

    public function __construct() {

        global $wpdb;
        $this->db_table_requests = $wpdb->prefix . "ik_sch_booking_requests";
        $this->services = new Ik_Schedule_Services();
        $this->locations = new Ik_Schedule_Locations();
        $this->staff = new Ik_Schedule_Staff();
        $this->available_days = new Ik_Schedule_Available_Days();
        $this->default_calendar_color = '#3498db';
        $this->config = 'ik_sch_book_config';
        $this->qtyListing = 30;
        $this->requests_admin_url = get_admin_url().'admin.php?page='.IK_SCH_MENU_VAL_ENTRIES;
        $this->dateFormats = array(
            0 => array( 'visual' => 'mm-dd-yy', 'javascript' => 'mm-dd-y', 'php' => 'm-d-y', ), 1 => array( 'visual' => 'mm.dd.yy', 'javascript' => 'mm.dd.y', 'php' => 'm.d.y', ), 2 => array( 'visual' => 'mm/dd/yy', 'javascript' => 'mm/dd/y', 'php' => 'm/d/y', ), 3 => array( 'visual' => 'mm-dd-yyyy', 'javascript' => 'mm-dd-yy', 'php' => 'm-d-Y', ), 4 => array( 'visual' => 'mm.dd.yyyy', 'javascript' => 'mm.dd.yy', 'php' => 'm.d.Y', ), 5 => array( 'visual' => 'mm/dd/yyyy', 'javascript' => 'mm/dd/yy', 'php' => 'm/d/Y', ), 6 => array( 'visual' => 'dd-mm-yy', 'javascript' => 'dd-mm-y', 'php' => 'd-m-y', ), 7 => array( 'visual' => 'dd.mm.yy', 'javascript' => 'dd.mm.y', 'php' => 'd.m.y', ), 8 => array( 'visual' => 'dd/mm/yy', 'javascript' => 'dd/mm/y', 'php' => 'd/m/y', ), 9 => array( 'visual' => 'dd-mm-yyyy', 'javascript' => 'dd-mm-yy', 'php' => 'd-m-Y', ), 10 => array( 'visual' => 'dd.mm.yyyy', 'javascript' => 'dd.mm.yy', 'php' => 'd.m.Y', ), 11 => array( 'visual' => 'dd/mm/yyyy', 'javascript' => 'dd/mm/yy', 'php' => 'd/m/Y', ),
        );
        $this->timeFrames = array('1', '5', '10', '15', '20', '25', '30', '45', '60', '90', '120');
        $this->currencies = array(
            0 => 'USD', 1 => 'EUR', 2 => 'CHF', 3 => 'AFN', 4 => 'DZD', 5 => 'ARS', 6 => 'AMD', 7 => 'AWG', 8 => 'AUD', 9 => 'AZN', 10 => 'BSD', 11 => 'BHD', 12 => 'THB', 13 => 'PAB', 14 => 'BBD', 15 => 'BYN', 16 => 'BZD', 17 => 'BMD', 18 => 'BTC', 19 => 'VED', 20 => 'VEF', 21 => 'BOB', 22 => 'BRL', 23 => 'BND', 24 => 'BGN', 25 => 'BIF', 26 => 'CVE', 27 => 'CAD', 28 => 'KYD', 29 => 'XOF', 30 => 'XAF', 31 => 'XPF', 32 => 'CLP', 33 => 'COP', 34 => 'KMF', 35 => 'CDF', 36 => 'BAM', 37 => 'NIO', 38 => 'CRC', 39 => 'CUP', 40 => 'CZK', 41 => 'GMD', 42 => 'DKK', 43 => 'MKD', 44 => 'DJF', 45 => 'STN', 46 => 'DOP', 47 => 'VND', 48 => 'XCD', 49 => 'EGP', 50 => 'SVC', 51 => 'ETH', 52 => 'ETB', 53 => 'FKP', 54 => 'FJD', 55 => 'HUF', 56 => 'GHS', 57 => 'GIP', 58 => 'HTG', 59 => 'PYG', 60 => 'GNF', 61 => 'GYD', 62 => 'HKD', 63 => 'UAH', 64 => 'ISK', 65 => 'INR', 66 => 'IRR', 67 => 'IQD', 68 => 'JMD', 69 => 'JOD', 70 => 'KES', 71 => 'PGK', 72 => 'LAK', 73 => 'HRK', 74 => 'KWD', 75 => 'MWK', 76 => 'AOA', 77 => 'MMK', 78 => 'GEL', 79 => 'LBP', 80 => 'ALL', 81 => 'HNL', 82 => 'SLE', 83 => 'LRD', 84 => 'LYD', 85 => 'SZL', 86 => 'LSL', 87 => 'MGA', 88 => 'MYR', 89 => 'MUR', 90 => 'MXN', 91 => 'MDL', 92 => 'MAD', 93 => 'MZN', 94 => 'BOV', 95 => 'NGN', 96 => 'ERN', 97 => 'NAD', 98 => 'NPR', 99 => 'ANG', 100 => 'ILS', 101 => 'TWD', 102 => 'NZD', 103 => 'BTN', 104 => 'KPW', 105 => 'NOK', 106 => 'PEN', 107 => 'MRU', 108 => 'TOP', 109 => 'PKR', 110 => 'MOP', 111 => 'CUC', 112 => 'UYU', 113 => 'PHP', 114 => 'GBP', 115 => 'BWP', 116 => 'QAR', 117 => 'GTQ', 118 => 'ZAR', 119 => 'OMR', 120 => 'KHR', 121 => 'RON', 122 => 'MVR', 123 => 'IDR', 124 => 'RUB', 125 => 'RWF', 126 => 'SHP', 127 => 'SAR', 128 => 'XDR', 129 => 'RSD', 130 => 'SCR', 131 => 'SGD', 132 => 'SBD', 133 => 'KGS', 134 => 'SOS', 135 => 'TJS', 136 => 'SSP', 137 => 'LKR', 138 => 'XSU', 139 => 'SDG', 140 => 'SRD', 141 => 'SEK', 142 => 'XUA', 143 => 'SYP', 144 => 'BDT', 145 => 'WST', 146 => 'TZS', 147 => 'KZT', 148 => 'TTD', 149 => 'MNT', 150 => 'TND', 151 => 'TRY', 152 => 'TMT', 153 => 'AED', 154 => 'UGX', 155 => 'CLF', 156 => 'COU', 157 => 'UZS', 158 => 'VUV', 159 => 'CHE', 160 => 'CHW', 161 => 'KRW', 162 => 'YER', 163 => 'JPY', 164 => 'CNY', 165 => 'ZMW', 166 => 'ZWL', 167 => 'PLN',
        );
        $this->currencies_specs = array(
            'USD' => array('sign' => '$', 'side' => 'left',), 
            'EUR' => array('sign' => '&euro;', 'side' => 'right',),
            'CHF' => array('sign' => 'CHF', 'side' => 'left-space',),
        );
        $this->dayNames = array(
            __( 'Sunday', 'ik_schedule_location'),
            __( 'Monday', 'ik_schedule_location'),
            __( 'Tuesday', 'ik_schedule_location'),
            __( 'Wednesday', 'ik_schedule_location'),
            __( 'Thursday', 'ik_schedule_location'),
            __( 'Friday', 'ik_schedule_location'),
            __( 'Saturday', 'ik_schedule_location')
        );
        $this->dayNamesAbv = array(
            __( 'Sun.', 'ik_schedule_location'),
            __( 'Mon.', 'ik_schedule_location'),
            __( 'Tues.', 'ik_schedule_location'),
            __( 'Wed.', 'ik_schedule_location'),
            __( 'Thurs.', 'ik_schedule_location'),
            __( 'Fri.', 'ik_schedule_location'),
            __( 'Sat.', 'ik_schedule_location')
        );
        $this->monthNames = array(
            __('January', 'textdomain'),
            __('February', 'textdomain'),
            __('March', 'textdomain'),
            __('April', 'textdomain'),
            __('May', 'textdomain'),
            __('June', 'textdomain'),
            __('July', 'textdomain'),
            __('August', 'textdomain'),
            __('September', 'textdomain'),
            __('October', 'textdomain'),
            __('November', 'textdomain'),
            __('December', 'textdomain')
        );
    }

    //Get booking requests table name
    public function get_table_name(){
        return $this->db_table_requests;
    }

    //If not exist create the tables
    public function create_db_tables(){
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $sql_bookings_table = "
        CREATE TABLE IF NOT EXISTS ".$this->get_table_name()." (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            branch_id bigint(20) NOT NULL,
            service_ids longtext NOT NULL,
            staff_id longtext NOT NULL,
            request_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            booking_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            last_edit datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            f_name varchar(255) NOT NULL,
            lastname varchar(255) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(22) DEFAULT '-' NOT NULL,
            ip varchar(39) NOT NULL,
            message longtext NOT NULL,
            accepted int(2)  DEFAULT '0' NOT NULL,
            internal_note longtext NOT NULL, 
            color varchar(20) NOT NULL,
            wc_order bigint(20) NOT NULL,
            UNIQUE KEY id (id)
        ) ".$charset_collate.";";
        dbDelta( $sql_bookings_table );

        $sql_services_table = "
        CREATE TABLE IF NOT EXISTS ".$this->services->get_table_name()." (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            cat_name varchar(200) NOT NULL,
            name varchar(100) NOT NULL,
            price decimal(10,2) NOT NULL,
            currency_id mediumint(9) NOT NULL,
            delivery_time int(4) NOT NULL,
            UNIQUE KEY id (id)
        ) ".$charset_collate.";";
        dbDelta( $sql_services_table );

        $sql_locations_table = "
        CREATE TABLE IF NOT EXISTS ".$this->locations->get_table_name()." (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            address longtext NOT NULL,
            map_link longtext NOT NULL,
            map_embed_src longtext NOT NULL,
            service_ids longtext NOT NULL,
            availability longtext NOT NULL,
            UNIQUE KEY id (id)
        ) ".$charset_collate.";";
        dbDelta( $sql_locations_table );

        $sql_staff_table = "
        CREATE TABLE IF NOT EXISTS ".$this->staff->get_table_name()." (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            display_name varchar(100) NOT NULL,
            location_id longtext NOT NULL,
            service_ids longtext NOT NULL,
            availability longtext NOT NULL,
            added_datetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            edit_datetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            UNIQUE KEY id (id)
        ) ".$charset_collate.";";
        dbDelta( $sql_staff_table );

        $sql_available_days_table = "
        CREATE TABLE IF NOT EXISTS ".$this->available_days->get_table_name()." (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            location_id bigint(20) NOT NULL,
            block_from datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            block_to datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            UNIQUE KEY id (id)
        ) ".$charset_collate.";";
        dbDelta( $sql_available_days_table );
    }

    //Create request
    public function create_request($args = array()){

        if(isset($args['branch_id']) && isset($args['name']) && isset($args['email'])
        && isset($args['phone']) && (isset($args['date']) && isset($args['time'])) || isset($args['datetime'])){

            $f_name = sanitize_text_field($args['name']);
            $email = sanitize_text_field($args['email']);
            $phone = sanitize_text_field($args['phone']);

            if(isset($args['date']) && isset($args['time'])){
                $date = $this->available_days->sanitize_date_format($args['date']);
                $time = $this->available_days->sanitize_time_format($args['time']);
                $datetime = $date.' '.$time;
            } else {
                $datetime = date ('Y-m-d H:i:s', strtotime($args['datetime']));
            }

            $lastname = (isset($args['lastname'])) ? sanitize_text_field($args['lastname']) : '';
            $message = (isset($args['message'])) ? sanitize_textarea_field($args['message']) : '';
            $message = str_replace("\\", "", $message);

            $internal_note = (isset($args['internal_note'])) ? sanitize_textarea_field($args['internal_note']) : '';
            $internal_note = str_replace("\\", "", $internal_note);

            $accepted = (isset($args['accepted'])) ? absint($args['accepted']) : $this->get_config()['status_default'];

            $location_id = absint($args['branch_id']);
            $services_from_session = $this->get_services_selected_by_user($location_id);

            //get IP
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip_address = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip_address = $_SERVER['REMOTE_ADDR'];
            }

            if(isset($args['services']) || is_array($services_from_session)){

                $services_ids = (isset($args['services'])) ? $args['services'] : $services_from_session;
                if(is_array($services_ids)){
                    $services_ids_db = maybe_serialize($services_ids);
                } else {
                    $services_ids_array[] = $services_ids;
                    $services_ids_db = maybe_serialize($services_ids_array);
                }

                //if woocommerce order related
                $wc_order = (isset($args['wc_order'])) ? intval($args['wc_order']) : 0;
                                
                $booking_data = array (
                        'branch_id'=> $location_id,
                        'staff_id'=> $this->get_session_staff_selected(),
                        'service_ids'=> $services_ids_db,
                        'request_date'=> current_time( 'mysql' ),
                        'booking_date'=> $datetime,		
                        'last_edit'=> current_time( 'mysql' ),		
                        'f_name'=> $f_name,	
                        'lastname'=> $lastname,
                        'email'=> $email,	
                        'phone'	=> $phone,	
                        'ip'=> $ip_address,	
                        'message'=> $message,	
                        'accepted'	=> $accepted,
                        'internal_note'	=> $internal_note,
                        'wc_order'	=> $wc_order,
                );
                
                global $wpdb;
                $rowResult = $wpdb->insert($this->db_table_requests, $booking_data, $format = NULL);
                $booking_id = $wpdb->insert_id;

                //remove session if exists
                $removed_session = $this->remove_session_services_selected_by_user();

                //send email
                $email_sent = $this->send_email_notification($booking_id, 'booked');
                
                return $booking_id;
            }
        }
        
        return false;
    }

    //Get booking data by id
    public function edit($booking_id = 0, $data = array()){
        $booking_id = absint($booking_id);
        
        if ( $booking_id > 0 && is_array($data)){
            $data_update  = array(
                'last_edit'=> current_time( 'mysql' ),
            );

            if(isset($data['branch_id'])){
                $data_update['branch_id'] = intval($data['branch_id']);
            }

            if(isset($data['staff_id'])){
                $data_update['staff_id'] = intval($data['staff_id']);
            }

            if(isset($data['service_ids'])){
                if(is_array($data['service_ids'])){
                    foreach($data['service_ids'] as $service_id){
                        $service_ids[] = intval($service_id);
                    }
                    $data_update['service_ids'] = maybe_serialize($service_ids);
                }                
            }
            if(isset($data['booking_date'])){
                $booking_date = $this->available_days->sanitize_date_format($data['booking_date'], true);
                if($booking_date){
                    $data_update['booking_date'] = $this->available_days->sanitize_date_format($data['booking_date'], true);
                }
            }
            if(isset($data['f_name'])){
                $data_update['f_name'] = sanitize_text_field($data['f_name']);     
            }
            if(isset($data['lastname'])){
                $data_update['lastname'] = sanitize_text_field($data['lastname']);                     
            }
            if(isset($data['email'])){
                $data_update['email'] = sanitize_email($data['email']);                                     
            }
            if(isset($data['phone'])){
                $data_update['phone'] = $this->validate_input_form('phone', $data['phone']);
            }
            if(isset($data['accepted'])){
                $data_update['accepted'] = intval($data['accepted']);
            }
            if(isset($data['internal_note'])){
                $data_update['internal_note'] = sanitize_textarea_field($data['internal_note']);
                $data_update['internal_note'] = str_replace("\\", "", $data_update['internal_note']);
            }
            if(isset($data['color'])){
                $data_update['color'] = sanitize_hex_color($data['color']);
            }

            global $wpdb;
            $where = [ 'id' => $booking_id ];
            
            $rowResult = $wpdb->update($this->db_table_requests, $data_update , $where);

            return true;      
        }
        
        return false;
    }

    //Get booking data by id
    public function get_by_id($booking_id = 0){
        $booking_id = absint($booking_id);
        
        if ( $booking_id > 0){
            
            global $wpdb;
            $query = "SELECT * FROM ".$this->db_table_requests." WHERE id = ".$booking_id;
            $booking = $wpdb->get_results($query);
    
            if (isset($booking[0]->id)){ 
                return $booking[0];
            }
        }
        
        return false;
    }

    //Get booking data by woocommerce order id
    public function get_by_wc_order_id($wc_order = 0){
        $wc_order = absint($wc_order);
        
        if ( $wc_order > 0){
            
            global $wpdb;
            $query = "SELECT * FROM ".$this->db_table_requests." WHERE wc_order = ".$wc_order;
            $booking = $wpdb->get_results($query);
    
            if (isset($booking[0]->wc_order)){ 
                return $booking[0];
            }
        }
        
        return false;
    }

    //Get booking data
    private function get_list($qty = 30){
        $qty = absint($qty);

        if (isset($_GET["list"])){
            // I check if value is integer to avoid errors
            if (strval($_GET["list"]) == strval(intval($_GET["list"])) && $_GET["list"] > 0){
                $page = intval($_GET["list"]);
            } else {
                $page = 1;
            }
        } else {
             $page = 1;
        }

        $offset = ($page - 1) * $qty;

        if (isset($_GET['search'])){
            $search = sanitize_text_field($_GET['search']);
        } else {
            $search = NULL;
        }

        if (isset($_GET['from_date'])){
            $from_date = sanitize_text_field($_GET['from_date']);
        } else {
            $from_date = '';
        }
        if (isset($_GET['to_date'])){
            $to_date = sanitize_text_field($_GET['to_date']);
        } else {
            $to_date = '';
        }

        if (isset($_GET['accepted'])){
            switch(intval($_GET['accepted'])){
                case 1:
                    $accepted = 'accepted = 1';
                    break;
                case 2:
                    $accepted = 'accepted = 2';
                    break;
                default:
                    $accepted = 'accepted = 0';
                    break;
            }
        }
        
        
        // Chechking order
        if (isset($_GET["order"]) && isset($_GET["orderdir"])){
            $orderby = sanitize_text_field($_GET["order"]);
            $orderdir = sanitize_text_field($_GET["orderdir"]);  
            if (strtoupper($orderdir) != 'ASC'){
                $orderDir= ' DESC';
                $orderClass= 'sorted desc';
            } else {
                $orderDir = ' ASC';
                $orderClass= 'sorted asc';
            }
        } else {
            $orderby = 'id';
            $orderDir = 'DESC';
            $orderClass= 'sorted desc';
        } 
        if (is_int($offset)){
            $offsetList = ' LIMIT '.$qty.' OFFSET '.$offset;
        } else {
            $offsetList = ' LIMIT '.absint($qty);
        }
        
        //Values to order filters CSS classes
        $empty = '';
        $idClass = $empty;
        $branchClass = $empty;
        $nameClass = $empty;
        $bookingDateClass = $empty;
        $phoneClass = $empty;
        $emailClass = $empty;
        $acceptedClass = $empty;
        
        switch($orderby){
            case 'name':
                $orderQuery = ' ORDER BY '.$this->db_table_requests.'.f_name '.$orderDir;
                $nameClass = $orderClass;
                break;
            case 'branch':
                $orderQuery = ' ORDER BY '.$this->db_table_requests.'.branch_id '.$orderDir;
                $branchClass = $orderClass;
                break;
            case 'booking_date':
                $orderQuery = ' ORDER BY '.$this->db_table_requests.'.booking_date '.$orderDir;
                $bookingDateClass = $orderClass;
                break;
            case 'phone':
                $orderQuery = ' ORDER BY '.$this->db_table_requests.'.phone '.$orderDir;
                $phoneClass = $orderClass;
                break;
            case 'email':
                $orderQuery = ' ORDER BY '.$this->db_table_requests.'.email '.$orderDir;
                $emailClass = $orderClass;
                break;
            case 'accepted':
                $orderQuery = ' ORDER BY '.$this->db_table_requests.'.accepted '.$orderDir;
                $acceptedClass = $orderClass;
                break;
            default:
                $orderQuery = ' ORDER BY '.$this->db_table_requests.'.id '.$orderDir;
                $idClass = $orderClass;
                break;
        }
	
        $classData = array(
            'id' => $idClass,
            'branch' => $branchClass,
            'name' => $nameClass,
            'booking_date' => $bookingDateClass,
            'phone' => $phoneClass,
            'email' => $emailClass,
            'accepted' => $acceptedClass,
        );

        if ($search != NULL){ 
            //Search by name
            $where = " WHERE ".$this->db_table_requests.".f_name LIKE '%".$search."%' OR ".$this->db_table_requests.".lastname LIKE '%".$search."%'";
            if (isset($_GET['accepted'])){
                $where .= "AND ".$accepted;
            }
        } else {
            if (isset($_GET['accepted'])){
                $where = "WHERE ".$accepted;
            } else {
                $where = "";
            }
            $search = '';
        }

        //filter by date
        if($from_date != '' || $to_date != ''){
            $from_date_db = $this->available_days->sanitize_date_format($from_date);
            $to_date_db = $this->available_days->sanitize_date_format($to_date);

            //if search is by date range
            if($from_date_db && $to_date_db){
                $date_filter = "booking_date BETWEEN '".$from_date_db." 00:00:00' AND '".$to_date_db." 23:59:59' ";
            } else if($from_date_db){// search on specif date
                $date_filter = "booking_date BETWEEN '".$from_date_db." 00:00:00' AND '".$from_date_db." 23:59:59' ";
            }

            if (str_contains($where, 'WHERE ') && isset($date_filter)) {
                $where .= " AND ".$date_filter;
            } else {
                $where .= 'WHERE '.$date_filter;
            }
        }

        $groupby = (isset($groupby)) ? $groupby : " GROUP BY ".$this->db_table_requests.".id ";

        global $wpdb;
        $query = "SELECT * FROM ".$this->db_table_requests." ".$where.$groupby.$orderQuery.$offsetList;

        $bookings = $wpdb->get_results($query);
        $bookings_data = array(
            'data' => $bookings,
            'class' => $classData,
            'search_value' => $search,    
            'from_date' => $from_date,    
            'to_date' => $to_date,    
        );

        return $bookings_data;
    }

    //Count the quantity of entries
    public function qty_records(){

        global $wpdb;
        $query = "SELECT * FROM ".$this->db_table_requests;
        $result = $wpdb->get_results($query);

        if (isset($result[0]->id)){ 
            
            $count_result = count($result);

            return $count_result;
            
        } else {
            return false;
        }
    }

    //delete bookings by ID
    public function delete($booking_id){
        $booking_id = absint($booking_id);
        global $wpdb;
        $wpdb->delete( $this->db_table_requests , array( 'id' => $booking_id ) );
        
        return true;
    }

    //select for status of booking by url or id
    public function select_status($selected, $url = false){
        $selected = sanitize_text_field($selected);

        $status_options = array(
            '-' =>  __( 'Show All', 'ik_schedule_location'),
            '0' =>  __( 'Pending', 'ik_schedule_location'),
            '1' =>  __( 'Confirmed', 'ik_schedule_location'),
            '2' =>  __( 'Rejected', 'ik_schedule_location'),
        );

        if(!$url){
            $select = '<select class="ik_sch_book_edit_field ik_sch_book_accepted" name="accepted">';
            foreach($status_options as $key_value => $status_option){
                if($key_value == $selected){
                    $select_check = 'selected';
                } else {
                    $select_check = '';
                }
                $select .= '<option '.$select_check.' value="'.$key_value.'">'.$status_option.'</option>';
            }
            $select .= '</select>';
        } else {
            $url = sanitize_url($url);
            $select = '<select class="ik-filter ik-filter-accepted" name="filter_accepted" onchange="location = this.value;">';
            foreach($status_options as $key_value => $status_option){
                if($key_value == $selected){
                    $select_check = 'selected';
                } else {
                    $select_check = '';
                }
                $select .= '<option '.$select_check.' value="'.$url.'&accepted='.$key_value.'">'.$status_option.'</option>';
            }
            $select .= '</select>';
        }
        return $select;
    }

    // method to add pagination to lists
    public function paginator($qty, $qtyToList, $page_url){
        $qty = intval($qty);
        $qtyToList = $qtyToList;
        $page_url = sanitize_url($page_url);
        $page = (isset($_GET['paged'])) ? intval($_GET['paged']) : 1;
        $page = ($page == 0) ? 1 : $page;
        $output = '';

        if ($qty > 0){
            $data_dataSubstr = $qty / $qtyToList;
            $total_pages = intval($data_dataSubstr);
                
                if (is_float($data_dataSubstr)){
                    $total_pages = $total_pages + 1;
                }
            
            if ($qty > $qtyToList){
                
                if ($total_pages > 1){
                    $output .= '<div class="ik_booking_pages">';
                    
                    //Enable certain page ids to show
                    $mitadlist = intval($total_pages/2);
                    
                    $pagesToShow[] = 1;
                    $pagesToShow[] = 2;
                    $pagesToShow[] = 3;
                    $pagesToShow[] = $total_pages;
                    $pagesToShow[] = $total_pages - 1;
                    $pagesToShow[] = $total_pages - 2;
                    $pagesToShow[] = $mitadlist - 2;
                    $pagesToShow[] = $mitadlist - 1;
                    $pagesToShow[] = $mitadlist;
                    $pagesToShow[] = $mitadlist + 1;
                    $pagesToShow[] = $mitadlist + 2;
                    $pagesToShow[] = $page+3;
                    $pagesToShow[] = $page+2;
                    $pagesToShow[] = $page+1;
                    $pagesToShow[] = $page;
                    $pagesToShow[] = $page-1;
                    $pagesToShow[] = $page-2;
                    
                    for ($i = 1; $i <= $total_pages; $i++) {
                        $show_page = false;
                        
                        //Showing enabled pages
                        if (in_array($i, $pagesToShow)) {
                            $show_page = true;
                        }
                        
                        if ($show_page == true){
                            if ($page == $i){
                                $PageNActual = 'actual_page';
                            } else {
                                $PageNActual = "";
                            }
                            $output .= '<a class="ik_list_page_data '.$PageNActual.'" href="'.$page_url.'&list='.$i.'">'.$i.'</a>';
                        }
                    }
                    $output .= '</div>';
                }
            } 	            
        }
        return $output;
    }

    //Function to return records on backend
    public function show_entries_backend($qty = NULL) {

        $qty = absint($qty);

        $bookings_data = $this->get_list($qty);
        $bookings = $bookings_data['data'];
        $search = $bookings_data['search_value'];

        //date filter data
        $from_date = $bookings_data['from_date'];
        $to_date = $bookings_data['to_date'];

        //classes for columns that are filtered
        $classData = $bookings_data['class'];

        $idClass = $classData['id'];
        $branchClass = $classData['branch'];
        $nameClass = $classData['name'];
        $booking_dateClass = $classData['booking_date'];
        $phoneClass = $classData['phone'];
        $emailClass = $classData['email'];
        $acceptedClass = $classData['accepted'];
        
        $accepted_selected = (isset($_GET['accepted'])) ? sanitize_text_field($_GET['accepted']) : '-';

        $searchBar = '<form class="search-box" action="'.$this->requests_admin_url.'" method="get">
                <label class="screen-reader-text" for="tag-search-input">Search booking:</label>
                <input type="hidden" name="page" value="'.IK_SCH_MENU_VAL_ENTRIES.'">
                <input type="search" id="tag-search-input" name="search" value="'.$search.'">
                <input type="submit" id="searchbutton" class="button" value="'.__( 'Search', 'ik_schedule_location').'">
            </form>';

        $add_new = '<div class="ik_sch_book_panel_buttons">
            <a href="#" class="button-primary" id="ik_sch_book_add_new_record">'.__( 'Add New', 'ik_schedule_location').'</a>
        </div>';

        $filterSelect = $this->select_status($accepted_selected, $this->requests_admin_url);

        $filter_date = '<style>
            #ik_sch_book_existing_records .panel_filter_date .from_date:after{
                content: "'.__( 'On/From Date', 'ik_schedule_location').'";
            }
            #ik_sch_book_existing_records .panel_filter_date .to_date:after {
                content: "'.__( 'To Date', 'ik_schedule_location').'";
            }
            </style>
            <div class="panel_filter_date">
                <form class="panel_filter_date" action="'.$this->requests_admin_url.'" method="get">
                    <input type="hidden" name="page" value="'.IK_SCH_MENU_VAL_ENTRIES.'">
                    <span class="from_date"><input required type="text" name="from_date" class="from_date_input datepicker" placeholder="'.__( 'Select Date', 'ik_schedule_location').'" value="'.$from_date.'" /></span>
                    <span class="to_date"><input type="text" name="to_date" class="to_date_input datepicker" placeholder="'.__( 'Select Date', 'ik_schedule_location').'" value="'.$to_date.'" /></span>
                    <input type="submit" class="button-primary" value="'.__( 'Filter by date', 'ik_schedule_location').'" />
                </form>
            </div>';
        $listing = '
        <div class="panel_filter_search">
            <div class="tablenav-pages">'.__( 'Total', 'ik_schedule_location').': '.$this->qty_records().' - '.__( 'Showing', 'ik_schedule_location').': '.count($bookings).'</div>
            '.$searchBar.$filterSelect.$filter_date.$add_new.'
        </div>';
        
        // If data exists
        if (isset($bookings[0]->id)){

            $columnsheading = '<tr>
                <th><input type="checkbox" class="select_all" /></th>
                <th order="id" class="worder '.$idClass.'">'.__( 'ID', 'ik_schedule_location').' <span class="sorting-indicator '.$idClass.'"></span></th>
                <th order="name" class="wide-data worder '.$nameClass.'">'.__( 'Name', 'ik_schedule_location').' <span class="sorting-indicator '.$nameClass.'"></span></th>
                <th order="branch" class="wide-data worder '.$branchClass.'">'.__( 'Branch', 'ik_schedule_location').' <span class="sorting-indicator '.$branchClass.'"></span></th>
                <th order="booking_date" class="wide-data worder '.$booking_dateClass.'">'.__( 'Booking Date', 'ik_schedule_location').' <span class="sorting-indicator '.$booking_dateClass.'"></span></th>
                <th order="phone" class="wide-data worder '.$phoneClass.'">'.__( 'Phone', 'ik_schedule_location').'<span class="sorting-indicator '.$phoneClass.'"></span></th>
                <th order="email" class="wide-data worder '.$emailClass.'">'.__( 'Email', 'ik_schedule_location').'<span class="sorting-indicator '.$emailClass.'"></span></th>
                <th order="accepted" class="wide-data worder '.$acceptedClass.'">'.__( 'Status', 'ik_schedule_location').' <span class="sorting-indicator '.$acceptedClass.'"></span></th>
                <th class="midle-actions">'.__( 'View / Edit', 'ik_schedule_location').'</th>
                <th class="wide-actions">
                    <button class="ik_sch_book_button_delete_bulk button action">'.__( 'Delete', 'ik_schedule_location').'</button></td>
                </th>
            </tr>';

            if ($search != NULL || isset($_GET['accepted']) || isset($_GET['from_date'])){
                $listing .= '<p class="show-all-button"><a href="'.$this->requests_admin_url.'" class="button button-primary">'.__( 'Show All', 'ik_schedule_location').'</a></p>';
            }

            $listing .= '<table id="ik_sch_book_existing" class="full">
                    <thead>
                        '.$columnsheading.'
                    </thead>
                    <tbody>';
                    $location = new Ik_Schedule_Locations();
                    foreach ($bookings as $booking){
                        $location_name = $location->get_location_name($booking->branch_id);

                        $accepted = $this->get_status_name_by_value($booking->accepted);

                        $listing .= '
                            <tr iddata="'.$booking->id.'">
                                <td><input type="checkbox" class="select_data" /></td>
                                <td class="ik_sch_book_id">'.$booking->id.'</td>
                                <td class="ik_sch_book_name">'.$booking->f_name.' '.$booking->lastname.'</td>
                                <td class="ik_sch_book_branch">'.$location_name.'</td>
                                <td class="ik_sch_book_booking_date">'.$booking->booking_date.'</td>
                                <td class="ik_sch_book_phone">'.$booking->phone.'</td>
                                <td class="ik_sch_book_email">'.$booking->email.'</td>
                                <td class="ik_sch_book_status">'.$accepted.'</td>
                                <td class="ik_sch_book_datamodal">
                                    <button href="#" class="ik_sch_book_button_view_modal button action">
                                        <span class="dashicons dashicons-edit"></span>
                                    </button>
                                </td>
                                <td iddata="'.$booking->id.'">
                                    <button class="ik_sch_book_button_delete button action">'.__( 'Delete', 'ik_schedule_location').'</button></td>
                            </tr>';
                        
                    }
                    $listing .= '
                    </tbody>
                    <tfoot>
                        '.$columnsheading.'
                    </tfoot>
                    <tbody>
                </table>';

                //pagination
                $listing .= $this->paginator($this->qty_records(), $qty, $this->requests_admin_url);
            
            return $listing;
            
        } else {
            if ($search != NULL || isset($_GET['accepted']) || isset($_GET['from_date'])){
                $listing .= '
                <div id="ik_sch_book_existing">
                    <p>'.__( 'Results not found', 'ik_schedule_location').'</p>
                    <p class="show-all-button"><a href="'.$this->requests_admin_url.'" class="button button-primary">'.__( 'Show All', 'ik_schedule_location').'</a></p>
                </div>';

                return $listing;
            }
        }
        
        return false;
    
    }

    public function format_date($key = 0, $format = 'javascript'){

        $format = ($format == 'javascript' || $format == 'php' || $format == 'visual' || $format == 'id') ? sanitize_text_field($format) : 'javascript';

        $format_date_key = (!is_null($key)) ? absint($key) : 0;

        // if date format ID doesn't exist
        if (!isset($this->dateFormats[$format_date_key])) {
            $format_date_key = 0;
        }

        if($format == 'id'){
            return $format_date_key;

        } else {
            return $this->dateFormats[$format_date_key][$format];
        }
    }

    //Method to list date format select options
    public function date_format_select($Selected = 0){
        $Selected = sanitize_text_field($Selected);

        $options_data_list = '<select name="format_date" class="ik_sch_book_date_format">';

        $options = $this->dateFormats;

        foreach( $options as $key => $option ) {
            $select = ($Selected == $key) ? 'selected' : '';
        
            $options_data_list .= '<option '.$select.' value="'.$key.'">'.$option['visual'].'</option>';
        }
    
        $options_data_list .= '</select>';    
        
        return $options_data_list;
    }

    //Method to list date format select options
    public function time_format_select($Selected = 0){
        $Selected = sanitize_text_field($Selected);

        $options_data_list = '<select name="format_time" class="ik_sch_book_time_format">';

        $options = array('12','24');

        foreach( $options as $option ) {
            $select = ($Selected == $option) ? 'selected' : '';
        
            $options_data_list .= '<option '.$select.' value="'.$option.'">'.$option.'</option>';
        }
    
        $options_data_list .= '</select>';    
        
        return $options_data_list;
    }

    //Method to list time frame select options
    public function time_frame_select($Selected = 0){
        $Selected = sanitize_text_field($Selected);

        $options_data_list = '<select name="time_frame" class="ik_sch_book_time_frame">';

        $options = $this->timeFrames;

        foreach( $options as $option ) {
            $select = ($Selected == $option) ? 'selected' : '';
        
            $options_data_list .= '<option '.$select.' value="'.$option.'">'.$option.'</option>';
        }
    
        $options_data_list .= '</select>';    
        
        return $options_data_list;
    }

    //Method to list booking limit time select options
    public function booking_limit_select($Selected = 180){ //limit by default to 6 months in days
        $Selected = sanitize_text_field($Selected);

        $options_data_list = '<select name="limit_booking" class="ik_sch_book_limit_booking">';

        $options = array(
            '15' => '15 '.__( 'Days', 'ik_schedule_location'),
            '30' => '1 '.__( 'Month', 'ik_schedule_location'),
            '60' => '2 '.__( 'Months', 'ik_schedule_location'),
            '90' => '3 '.__( 'Months', 'ik_schedule_location'),
            '120' => '4 '.__( 'Months', 'ik_schedule_location'),
            '150' => '5 '.__( 'Months', 'ik_schedule_location'),
            '180' => '6 '.__( 'Months', 'ik_schedule_location'),
            '210' => '7 '.__( 'Months', 'ik_schedule_location'),
            '240' => '8 '.__( 'Months', 'ik_schedule_location'),
            '270' => '9 '.__( 'Months', 'ik_schedule_location'),
            '300' => '10 '.__( 'Months', 'ik_schedule_location'),
            '330' => '11 '.__( 'Months', 'ik_schedule_location'),
            '365' => '1 '.__( 'Year', 'ik_schedule_location'),
        );

        foreach( $options as $days => $option_text ) {
            $select = ($Selected == $days) ? 'selected' : '';
        
            $options_data_list .= '<option '.$select.' value="'.$days.'">'.$option_text.'</option>';
        }
    
        $options_data_list .= '</select>';    
        
        return $options_data_list;
    }

    //Method to list booking gap before being able to book select options
    public function booking_before_limit_select($Selected = 12){ //limit by default to 12 hours
        $Selected = sanitize_text_field($Selected);

        $options_data_list = '<select name="limit_start_booking" class="ik_sch_book_before_limit_booking">';

        $options = array(
            '0' => '0 '.__( 'Hours', 'ik_schedule_location'),
            '1' => '1 '.__( 'Hour', 'ik_schedule_location'),
            '2' => '2 '.__( 'Hours', 'ik_schedule_location'),
            '3' => '3 '.__( 'Hours', 'ik_schedule_location'),
            '4' => '4 '.__( 'Hours', 'ik_schedule_location'),
            '5' => '5 '.__( 'Hours', 'ik_schedule_location'),
            '6' => '6 '.__( 'Hours', 'ik_schedule_location'),
            '7' => '7 '.__( 'Hours', 'ik_schedule_location'),
            '8' => '8 '.__( 'Hours', 'ik_schedule_location'),
            '9' => '9 '.__( 'Hours', 'ik_schedule_location'),
            '10' => '10 '.__( 'Hours', 'ik_schedule_location'),
            '11' => '11 '.__( 'Hours', 'ik_schedule_location'),
            '12' => '12 '.__( 'Hours', 'ik_schedule_location'),
            '13' => '13 '.__( 'Hours', 'ik_schedule_location'),
            '14' => '14 '.__( 'Hours', 'ik_schedule_location'),
            '15' => '15 '.__( 'Hours', 'ik_schedule_location'),
            '16' => '16 '.__( 'Hours', 'ik_schedule_location'),
            '17' => '17 '.__( 'Hours', 'ik_schedule_location'),
            '18' => '18 '.__( 'Hours', 'ik_schedule_location'),
            '19' => '19 '.__( 'Hours', 'ik_schedule_location'),
            '20' => '20 '.__( 'Hours', 'ik_schedule_location'),
            '21' => '21 '.__( 'Hours', 'ik_schedule_location'),
            '22' => '22 '.__( 'Hours', 'ik_schedule_location'),
            '23' => '23 '.__( 'Hours', 'ik_schedule_location'),
            '24' => '1 '.__( 'Day', 'ik_schedule_location'),
            '48' => '2 '.__( 'Days', 'ik_schedule_location'),
            '72' => '3 '.__( 'Days', 'ik_schedule_location'),
            '96' => '4 '.__( 'Days', 'ik_schedule_location'),
            '120' => '5 '.__( 'Days', 'ik_schedule_location'),
            '144' => '6 '.__( 'Days', 'ik_schedule_location'),
            '168' => '7 '.__( 'Days', 'ik_schedule_location'),
            '192' => '8 '.__( 'Days', 'ik_schedule_location'),
            '216' => '9 '.__( 'Days', 'ik_schedule_location'),
            '240' => '10 '.__( 'Days', 'ik_schedule_location'),
            '264' => '11 '.__( 'Days', 'ik_schedule_location'),
            '288' => '12 '.__( 'Days', 'ik_schedule_location'),
            '312' => '13 '.__( 'Days', 'ik_schedule_location'),
            '336' => '14 '.__( 'Days', 'ik_schedule_location')
        );

        foreach( $options as $days => $option_text ) {
            $select = ($Selected == $days) ? 'selected' : '';
        
            $options_data_list .= '<option '.$select.' value="'.$days.'">'.$option_text.'</option>';
        }
    
        $options_data_list .= '</select>';    
        
        return $options_data_list;
    }

    //Method to list currency select options
    public function currency_select($Selected = 0){
        $Selected = absint($Selected);

        $options_data_list = '<select name="currency" class="ik_sch_book_currency_select">';

        $options = $this->currencies;

        foreach( $options as $key => $option ) {
            $select = ($Selected === $key) ? 'selected' : '';
        
            $options_data_list .= '<option '.$select.' value="'.$key.'">'.$option.'</option>';
        }
    
        $options_data_list .= '</select>';    
        
        return $options_data_list;
    }

    //Method to list currency select options
    public function get_currency_details($currency_id){
        $currency_id = absint($currency_id);

        if(!isset($this->currencies[$currency_id])){
            $currency_id = 0;
        }

        $currency_details = new stdClass();
        $currency_details->id = $currency_id; 
        $currency_details->name = $this->currencies[$currency_id]; 
        
        if(isset($this->currencies_specs[$currency_details->name])){
            $currency_details->sign = $this->currencies_specs[$currency_details->name]['sign'];
            $currency_details->side = $this->currencies_specs[$currency_details->name]['side']; 
        } else {
            $currency_details->sign = $currency_details->name.'$';
            $currency_details->side = 'left';
        }
        
        return $currency_details;
    }

    //Method to return price with currency in the correct format
    public function get_price_currency_format($currency_id, $price){
		$price = floatval($price);

        //make sure woocommerce is not active
        $woocommerce_enabled = $this->get_config()['woocommerce'];
        if($woocommerce_enabled){
            //price format with Woocommerce
            return wc_price($price);
        } else {

            $price_details = ''; //default value

            if($currency_id !== '-1'){
                $currency_id = absint($currency_id);

                if(isset($this->currencies[$currency_id])){
                    $currency_details = $this->get_currency_details($currency_id);

                    switch($currency_details->side){
                        case 'left':
                            return $currency_details->sign.$price;
                            break;
                        case 'left-space':
                            return $currency_details->sign.' '.$price;
                            break;
                        default:
                            return $price.' '.$currency_details->sign; //right
                            break;
                    }
                }
            }            
        }
        
        return '';
    }

    //get config data
    public function get_config(){

        $config = get_option($this->config);

        $format_date = (isset($config['format_date'])) ? $this->format_date($config['format_date'], 'id') : 0;
        $format_time = (isset($config['format_time'])) ? intval($config['format_time']) : '24';
        $format_time = (intval($format_time) == 12) ? '12' : '24';
        $time_frame = (isset($config['time_frame'])) ? intval($config['time_frame']) : '5';
        $calendar_starts_monday = (isset($config['calendar_starts_monday'])) ? rest_sanitize_boolean($config['calendar_starts_monday']) : false;
        $prices_popup = (isset($config['prices_popup'])) ? rest_sanitize_boolean($config['prices_popup']) : false;
        $dates_month = (isset($config['dates_month'])) ? rest_sanitize_boolean($config['dates_month']) : false;
        $limit_start_booking = (isset($config['limit_start_booking'])) ? intval($config['limit_start_booking']) : '12';
        $block_repeat_limit = (isset($config['block_repeat_limit'])) ? intval($config['block_repeat_limit']) : '1';
        $limit_booking = (isset($config['limit_booking'])) ? intval($config['limit_booking']) : '180';
        $main_currency_id = (isset($config['currency'])) ? intval($config['currency']) : 0;
        $acceptauto_enabled = (isset($config['accept_auto'])) ? rest_sanitize_boolean($config['accept_auto']) : true;
        $woocommerce_enabled = (isset($config['woocommerce'])) ? rest_sanitize_boolean($config['woocommerce']) : false;
        $staff_enabled = (isset($config['staff_enabled'])) ? rest_sanitize_boolean($config['staff_enabled']) : false;
        $recapchaEnabled = (isset($config['recaptcha']['enabled'])) ? rest_sanitize_boolean($config['recaptcha']['enabled']) : false;
        $recaptchakey = (isset($config['recaptcha']['key'])) ? sanitize_text_field($config['recaptcha']['key']) : false;
        $recaptchasecret = (isset($config['recaptcha']['secret'])) ? sanitize_text_field($config['recaptcha']['secret']) : false;
        $recaptchoption = (isset($config['recaptcha']['option'])) ? sanitize_text_field($config['recaptcha']['option']) : 'v2';
        $email_sender = (isset($config['email_sender'])) ? sanitize_email($config['email_sender']) : get_option('admin_email');
        $status_default = (isset($config['status_default'])) ? intval($config['status_default']) : 1;

        $config = array(
            'accept_auto' => $acceptauto_enabled,
            'woocommerce' => $woocommerce_enabled,
            'staff_enabled' => $staff_enabled,
            'format_date' => $format_date,
            'format_time' => $format_time,
            'limit_booking' => $limit_booking,
            'calendar_starts_monday' => $calendar_starts_monday,
            'prices_popup' => $prices_popup,
            'dates_month' => $dates_month,
            'limit_start_booking' => $limit_start_booking,
            'block_repeat_limit' => $block_repeat_limit,
            'time_frame' => $time_frame,
            'currency' => $main_currency_id,
            'email_sender' => $email_sender,
            'status_default' => $status_default,
            'recaptcha' => array(
                'enabled' => $recapchaEnabled,
                'key'     => $recaptchakey,
                'secret'  => $recaptchasecret,
                'option'  => $recaptchoption,
            )
        );

        return $config;
    }

    // order attributes by name
    private function compareByName($a, $b) {
        return strcmp($a['name'], $b['name']);
    }

    //update config
    public function update_config(){
        
        $config = $this->get_config();

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

            $format_date = (isset($_POST['format_date'])) ? $this->format_date($_POST['format_date'], 'id') : $config['format_date'];
            $format_time = (isset($_POST['format_time'])) ? intval($_POST['format_time']) : $config['format_time'];
            $format_time = (intval($format_time) == 12) ? '12' : '24';
            $time_frame = (isset($_POST['time_frame'])) ? intval($_POST['time_frame']) : $config['time_frame'];
            $calendar_starts_monday = (isset($_POST['calendar_starts_monday'])) ? rest_sanitize_boolean($_POST['calendar_starts_monday']) : false;
            $prices_popup = (isset($_POST['prices_popup'])) ? rest_sanitize_boolean($_POST['prices_popup']) : false;
            $dates_month = (isset($_POST['dates_month'])) ? rest_sanitize_boolean($_POST['dates_month']) : false;
            $limit_booking = (isset($_POST['limit_booking'])) ? intval($_POST['limit_booking']) : $config['limit_booking'];
            $limit_start_booking = (isset($_POST['limit_start_booking'])) ? intval($_POST['limit_start_booking']) : $config['limit_start_booking'];
            $block_repeat_limit = (isset($_POST['block_repeat_limit'])) ? intval($_POST['block_repeat_limit']) : $config['block_repeat_limit'];
            $block_repeat_limit = ($block_repeat_limit < 1) ? '1' : $block_repeat_limit;
            $main_currency_id = (isset($_POST['currency'])) ? intval($_POST['currency']) : $config['currency'];
            $acceptauto_enabled = (isset($_POST['accept_auto'])) ? true : false;
            $woocommerce_enabled = (isset($_POST['woocommerce_enabled'])) ? true : false;
            $staff_enabled = (isset($_POST['staff_enabled'])) ? true : false;
            $recaptcha_k = (isset($_POST['recapkey'])) ? sanitize_text_field($_POST['recapkey']) : $config['recaptcha']['key'];
            $recaptcha_s = (isset($_POST['recapseckey'])) ? sanitize_text_field($_POST['recapseckey']) : $config['recaptcha']['secret'];    
            $recapchaEnabled = (isset($_POST['userecaptcha'])) ? rest_sanitize_boolean($_POST['userecaptcha']) : $config['recaptcha']['enabled'];
            $config_recaptcha_option = (isset($_POST['userecaptcha_option'])) ? sanitize_text_field($_POST['userecaptcha_option']) : $config['recaptcha']['option'];
            $email_sender = (isset($_POST['email_sender'])) ? sanitize_email($_POST['email_sender']) : $config['email_sender'];
            $status_default = (isset($_POST['status_default'])) ? intval($_POST['status_default']) : $config['status_default'];
            
            $configData = array(
                'accept_auto' => $acceptauto_enabled,
                'woocommerce' => $woocommerce_enabled,
                'staff_enabled' => $staff_enabled,
                'format_date' => $format_date,
                'format_time' => $format_time,
                'limit_booking' => $limit_booking,
                'calendar_starts_monday' => $calendar_starts_monday,                
                'prices_popup' => $prices_popup,                
                'dates_month' => $dates_month,                
                'limit_start_booking' => $limit_start_booking,
                'block_repeat_limit' => $block_repeat_limit,
                'time_frame' => $time_frame,
                'currency' => $main_currency_id,
                'email_sender' => $email_sender,
                'status_default' => $status_default,
                'recaptcha' => array(
                    'enabled' => $recapchaEnabled,
                    'key'     => $recaptcha_k,
                    'secret'  => $recaptcha_s,
                    'option'  => $config_recaptcha_option,
                )
            );

            update_option($this->config, $configData);
        }
    }

    //return service select options for location
    public function get_service_select_options($location_id = 0){
        $location_id = absint($location_id);

        $services = $this->services->get_services_by_location($location_id);

        if(is_array($services)){
            $service_options = '';
            
            //ordering by name
            usort($services, array($this, 'compareByName'));

            //Order by service cats
            $service_cats = $this->services->get_services_cats();

            if($service_cats == false){
                $service_cats[] = '';
            }

            // Order based on cat_name
            $order_services = array_map(function ($value) use ($service_cats) {
                $index = array_search($value, $service_cats);
                return $index !== false ? $index : count($service_cats);
            }, array_column($services, 'cat_name'));

            // Order $services based on category name related to service
            array_multisort($order_services, $services);
            
            foreach($services as $service){

                if(!isset($cats_listed[$service['cat_name']])){
                    if(array_search($service['cat_name'], $service_cats)){
                        $service_options .= '<option disabled>------ '. __( 'Price List', 'ik_schedule_location').' ( '.$service['cat_name'].' ) ------</option>';                 
                    } else {
                        $service_options .= '<option disabled>------ '. __( 'Other Services', 'ik_schedule_location').' ------</option>';                 
                    }
                    $cats_listed[$service['cat_name']] = true;
                }

                $price = ($service['custom_price'] > -1) ? number_format( floatval($service['custom_price']), 2, '.', ''  ) : number_format( floatval($service['price']), 2, '.', ''  );

                $price_details = $this->get_price_currency_format($service['currency_id'], $price);

                $service_options .= '<option value="'.$service['id'].'">'.$service['name'].' ('.$price_details.')</option>';

            }

            return $service_options;
        }

        return false;
    }

    private function get_woo_booking_services($location_id){
        $location_id = absint($location_id);
        $args = array(
            'post_type' => 'product',
    		'orderby' => 'menu_order',
    		'order' => 'ASC',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'branch_search',
                    'value' => '-location="'.$location_id.'"',
                    'compare' => 'LIKE',
                ),
            ),
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field' => 'slug',
                    'terms' => 'schedule_booking_product'
                ),
            ),
        );
        
        $products_service = get_posts($args);
        
        if ($products_service) {
            foreach ($products_service as $product_service) {
                // Get the first category name, if available
                $product_categories = wp_get_post_terms($product_service->ID, 'product_cat', array('fields' => 'names'));
                $cat_name = !empty($product_categories) ? $product_categories[0] : '';

                $product = wc_get_product($product_service->ID);
                $delivery_time = $product->get_meta('delivery_time');
                $delivery_time_full = $this->services->format_delivery_time($delivery_time);
				$product_id = $product->get_id();
				$menu_order = get_post_field('menu_order', $product_id);
                $currency = get_woocommerce_currency();

                $services[] = array(
                    'id' => $product_id,
                    'cat_name' => $cat_name,
                    'name' => $product->get_name(),
                    'price' => $product->get_price(),
                    'currency_id' => $currency,
					'menu_order' => $menu_order,
                    'custom_price' => $product->get_price(),
                    'delivery_time' => $product->get_meta('delivery_time'),
                    'delivery_time_full' => $delivery_time_full,
                );
            }
        }

        //getting product categories ordered by order given
        $terms = get_terms(array(
            'taxonomy' => 'product_cat',
            'orderby' => 'term_order',
            'order' => 'ASC',
        ));
        
        if (!empty($terms)) {
            foreach ($terms as $term) {
                $service_cats[] = $term->name;
                
                //get the icon data if exists
                $cat_icon = get_term_meta($term->term_id, 'ik_sch_book_category_icon', true);
                if($cat_icon){
                    $cat_icons[$term->name] = $cat_icon;
                }
            }
        } else {
            $service_cats[] = '';
        }

        //return of category and service data
        if(isset($services) && isset($service_cats)){
            if(!isset($cat_icons)){
                $cat_icons[] = '';
            }
            $services_data = array(
                'services' => $services,
                'cats' => $service_cats,
                'cat_icons' => $cat_icons,
            );

            return $services_data;
        }

        return false;
    }

    //method to output boxes with categories to filter services by catergory on backend
    private function get_category_filter_boxes($cat_array_filter, $cat_icons){
        $output = '';
        //if prices popup container with service list is hidden as well as the all selector
        $show_prices_aspopup = ($this->get_config()['prices_popup']) ? 'hidden' : '';

        if(is_array($cat_array_filter)){
            $output .= '<div id="ik_sch_book_service_cat_filter" data-service-id="0">
                <div cat_id="ik_sch_book_service_cat_0" class="service_cat_filter_box_selector selected" '.$show_prices_aspopup.'>
                    <div class="service_cat_filter_box">
                        <span class="service_cat_filter_box_icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><g fill="none" fill-rule="evenodd"><path d="M0 0h48v48H0z"></path><path fill="#071948" fill-rule="nonzero" d="M10 15a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM24 15a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM38 15a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM10 29a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM24 29a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM38 29a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM10 43a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM24 43a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM38 43a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path></g></svg>
                        </span>
                    </div>
                    <div class="service_cat_filter_title">
                        <span>'.__( 'All', 'ik_schedule_location').'</span>
                    </div>
                </div>'; 
            foreach($cat_array_filter as $id_cat => $cat_name){

                $cat_icon = (isset($cat_icons[$cat_name])) ? '<img src="'.esc_url($cat_icons[$cat_name]).'" alt="'.$cat_name.' icon" />' : '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><svg>';


                $output .= '<div cat_id="ik_sch_book_service_cat_'.$id_cat.'" class="service_cat_filter_box_selector">
                        <div class="service_cat_filter_box">
                            <span class="service_cat_filter_box_icon">
                                '.$cat_icon.'
                            </span>
                        </div>
                        <div class="service_cat_filter_title">
                            <span>'.$cat_name.'</span>
                        </div>
                    </div>'; 
            }
            $output .= '</div>';

        }

        return $output;
    }

    //return service list for location
    public function get_service_select_list($location_id = 0){
        $location_id = absint($location_id);

        //make sure woocommerce is not active
        $woocommerce_enabled = $this->get_config()['woocommerce'];
        if($woocommerce_enabled){
            $service_data = $this->get_woo_booking_services($location_id);
            $services = $service_data['services'];
            $service_cats = $service_data['cats'];
            $cat_icons = $service_data['cat_icons'];
        
        } else {
            $services = $this->services->get_services_by_location($location_id);

            //To order by service cats
            $service_cats = $this->services->get_services_cats();

            if($service_cats == false){
                $service_cats[] = '';
            }
        }

        //if services exist I create dynamic list
        if(isset($services)){
            if(is_array($services)){
                $service_options = '';
                
                //ordering by name
                usort($services, array($this, 'compareByName'));

                // Order based on cat_name
                $order_services = array_map(function ($value) use ($service_cats) {
                    $index = array_search($value, $service_cats);
                    return $index !== false ? $index : count($service_cats);
                }, array_column($services, 'cat_name'));

                // Order $services based on category name related to service
                array_multisort($order_services, $services);
                
                $service_list = '';
                $cat_counter = 0;
                foreach($services as $service){

                    $price = ($service['custom_price'] > -1) ? number_format( floatval($service['custom_price']), 2, '.', ''  ) : number_format( floatval($service['price']), 2, '.', ''  );
                    $price_details = $this->get_price_currency_format($service['currency_id'], $price);
                    
                    //to get later the minimum price from the list
                    $prices_from[] = $price;
                    $prices_from_price[$price] = $price_details;
					$menu_order = (isset($service['menu_order'])) ? 'menu_order='.$service['menu_order'] : '';

                    $cat_counter = $cat_counter + 1;
                    //if new category wrapper to start
                    if(!isset($cats_listed[$service['cat_name']])){
                        
                        if(isset($cats_listed)){

                            if(isset($prices_from)){
                                $service_list .= '<div class="ik_sch_book_data_services_from_value">'.__( 'From ', 'ik_schedule_location').$prices_from_price[min($prices_from)].'</div>
                                </div>';
                                unset($prices_from);
                            } else {
                                $service_list .= '</div>';
                            }
                        }

                        //to identify with html for js the category wrapper and create array to later be able to filter
                        $cat_id_html = "ik_sch_book_service_cat_".$cat_counter;
                        $cat_filter[$cat_counter] = $service['cat_name'];

                        if(array_search($service['cat_name'], $service_cats)){
                            
                            $service_list .= '
                            <div id="'.$cat_id_html.'" class="ik_sch_book_data_services_category_wrapper">
                                <div class="ik_sch_book_data_services_category_name">
                                    <h3 class="ik_sch_book_data_services_category">'.$service['cat_name'].'</h3>
                                    <i class="fas fa-chevron-up"></i>
                                </div>';                 

                        } else {
                            $service_list .= '
                            <div id="'.$cat_id_html.'" class="ik_sch_book_data_services_category_wrapper">
                                <div class="ik_sch_book_data_services_category_name">
                                    <h3 class="ik_sch_book_data_services_category">'.$service['cat_name'].'</h3>
                                    <i class="fas fa-chevron-up"></i>
                                </div>';   
                        }
                        $cats_listed[$service['cat_name']] = true;
                    }

                    $button_class_name = ($woocommerce_enabled) ? 'ik_sch_book_data_select_wc_service' : 'ik_sch_book_data_select_service';

                    $service_list .= '<div class="ik_sch_book_data_services_data" '.$menu_order.' data_id="'.$service['id'].'">
                        <div class="ik_sch_book_data_services_data_left">
                            <div class="ik_sch_book_data_services_name">'.$service['name'].'</div>
                            <div class="ik_sch_book_data_services_delivery_time">'.$service['delivery_time_full'].'</div>
                        </div>
                        <div class="ik_sch_book_data_services_data_right"><span class="ik_sch_book_data_services_price_data">'.$price_details.'</span> <button class="'.$button_class_name.'">'. __( 'Select', 'ik_schedule_location').'</button></div>
                    </div>';
                }
                if(isset($prices_from)){
                    $service_list .= '<div class="ik_sch_book_data_services_from_value">'.__( 'From ', 'ik_schedule_location').$prices_from_price[min($prices_from)].'</div>
                    </div>';
                }
                $service_list .= '</div>';

                //to avoid errors if cat_icons doesn't exist
                if(!isset($cat_icons)){
                    $cat_icons[0] = '';
                }

                $service_boxes = (isset($cat_filter)) ? $this->get_category_filter_boxes($cat_filter, $cat_icons) : '';

                //if prices popup container with service list is hidden
                $show_prices_aspopup = ($this->get_config()['prices_popup']) ? 'hidden' : '';

                $service_list = '
                        <div id="ik_sch_book_data_services_book">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>
                                            '. __( 'All Services', 'ik_schedule_location').'
                                        </h3>
                                    </div>
                                </div>
                                <div class="row">
                            '.$service_boxes.'
                                </div>
                            </div>
                            <div class="container" '.$show_prices_aspopup.'>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="ik_sch_field-wrap" data-name="all">
                                        '. __( 'Select Required Services', 'ik_schedule_location').'
                                            <span id="ik_sch_cat_filter_name"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row">
                                        '.$service_list.'
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';

                return $service_list;
            }
        }
        
        return false;
    }

    //Show recaptcha form field if enabled
    public function get_recaptcha_form($ifaccepted = false){
            
        $recapchaConfig = $this->get_config()['recaptcha'];

        if($recapchaConfig['enabled']){
            
            if ($recapchaConfig['key'] == false || $recapchaConfig['key'] == NULL || 
            $recapchaConfig['secret'] == false || $recapchaConfig['secret'] == NULL){
                //No keys
                return;
            } 
            
            //I check if it's "I'm not a robot" or "invisible" recaptcha
            if ($recapchaConfig['option'] == 'v3'){
                $recaptcha = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <div class="g-recaptcha"
                    data-sitekey="'.$recapchaConfig['key'].'"
                    data-callback="recaptchaOnSubmit"
                    data-size="invisible">
                </div>
                <input type="hidden" name="recaptcha_data_confirm" id="recaptcha_data_confirm" value="">
                <script>
                    function recaptchaOnSubmit() {
                        jQuery("#recaptcha_data_confirm").val("done");
                    }
                </script>';
            } else {
                $recaptcha = "<script src='https://www.google.com/recaptcha/api.js' async defer></script>
                <p>
                    <div class='g-recaptcha' data='robot' data-sitekey='".$recapchaConfig['key']."'></div>
                </p>";
            }

            return $recaptcha;
        } else{
            //recaptcha disabled
            return;
        }
    }

    //Returns session data about services selected for location id
    public function get_services_selected_by_user($location_id = 0){
        $location_id = absint($location_id);

        if(isset($_SESSION['ik_sch_services_added'][$location_id])){
            
            //I make sure I don't get repeated values
            if(is_array($_SESSION['ik_sch_services_added'][$location_id])){

                //delete session from other locations
                foreach($_SESSION['ik_sch_services_added'] as $key_location => $sessions){
                    if($key_location != $location_id){
                        unset($_SESSION['ik_sch_services_added'][$key_location]);
                    }
                }

                return $_SESSION['ik_sch_services_added'][$location_id];
            }
        } else if(isset($_SESSION['ik_sch_services_added'])){
            //if something's wrong with the array I delete it
            unset($_SESSION['ik_sch_services_added']);
            $this->delete_services_from_cart();
        }
        return false;    
    }

    //method to get location in session
    public function get_session_location_id(){

        //make sure there's a session
        if(isset($_SESSION['ik_sch_services_added'])){
            //I make sure I don't get repeated values
            if(is_array($_SESSION['ik_sch_services_added'])){
                //delete session from other locations
                foreach($_SESSION['ik_sch_services_added'] as $key_location => $sessions){
                    return $key_location;
                }
            }
        }
        return false;    
    }   

    //method to get data selected to book in session
    public function get_session_date_selected(){

        //make sure there's a session
        if(isset($_SESSION['ik_sch_date_selected'])){
            $date_in_session = strtotime($_SESSION['ik_sch_date_selected']);

            //make sure date is not old
            $todays_date_obj = new DateTime();
            $date_date_selected_obj = DateTime::createFromFormat("Y-m-d", date('Y-m-d', $date_in_session));
            $old_data_date = $todays_date_obj->diff($date_date_selected_obj);

            //I make sure the value is correct and not old
            if ($date_in_session && $old_data_date->invert == 0) {
                $date_selected = $date_in_session;
            }
        }
        //if date selected not defined creates a session with Todays date and returns todays value
        if(!isset($date_selected)){
            date_default_timezone_set($this->available_days->timezone);
            $today_date = date('Y-m-d');
            $_SESSION['ik_sch_date_selected'] = $today_date;
            $date_selected = strtotime($today_date);
        }

        return $date_selected;
    }

    //create session date data to when selecting a day
    public function save_session_date_selected($date_int){
        $date_int = intval($date_int);
        $date = date("Y-m-d", $date_int);

        //valid date
        if (checkdate(date("m", $date_int), date("d", $date_int), date("Y", $date_int))) {
            $_SESSION['ik_sch_date_selected'] = $date;
            
            //refresh time selection
            if(isset($_SESSION['ik_sch_time_selected'])){
                unset($_SESSION['ik_sch_time_selected']);
            }            
            return true;
        } else {
            return false;
        }

        return;
    }

    //method to get data selected to book in session
    public function get_session_time_selected(){

        //make sure there's a session
        if(isset($_SESSION['ik_sch_time_selected'])){
            $time_in_session = date('H:i:s', strtotime($_SESSION['ik_sch_time_selected']));

            return $time_in_session;
        }
        return false;
    }

    //method to get data selected to book in session
    public function save_session_time_selected($time){

        $_SESSION['ik_sch_time_selected'] = $this->available_days->sanitize_time_format($time);
        return true;
    }

    //method to get data selected to book in session
    public function get_session_staff_selected(){

        //make sure there's a session
        $staff_id_in_session = (isset($_SESSION['ik_sch_staff_selected'])) ? intval($_SESSION['ik_sch_staff_selected']) : 0;

        return $staff_id_in_session;
    }

    //method to get data selected to book in session
    public function save_session_staff_selected($staff_id){

        $_SESSION['ik_sch_staff_selected'] = intval($staff_id);
        return true;
    }

    //Remove session data about services selected
    public function remove_session_services_selected_by_user(){
        if(isset($_SESSION['ik_sch_services_added'])){
            unset($_SESSION['ik_sch_services_added']);
        }
        if(isset($_SESSION['ik_sch_date_selected'])){
            unset($_SESSION['ik_sch_date_selected']);
        }
        if(isset($_SESSION['ik_sch_time_selected'])){
            unset($_SESSION['ik_sch_time_selected']);
        }
        if(isset($_SESSION['ik_sch_url_location'])){
            unset($_SESSION['ik_sch_url_location']);
        }
        if(isset($_SESSION['ik_sch_staff_selected'])){
            unset($_SESSION['ik_sch_staff_selected']);
        }

        return true;    
    }

    //Validate phone number
    public function validate_input_form($type = "phone", $value = '%'){
        $value = sanitize_text_field($value);

        if($type == 'phone'){
            $pattern = '/^[0-9+()\-\s]{7,22}$/';
        } else if($type == 'email'){
            $pattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
        } else if($type == 'name'){
            $pattern = '/^[a-zA-Z\s\'’`]{1,60}$/';
        }

        //If it's a type valid there's a pattern
        if(isset($pattern)){
            if(preg_match($pattern, $value)){
                return $value; 
            }
        }

        return false;
    }

    //method to get status or accepted text name based on its value
    public function get_status_name_by_value($accepted_value){
        $accepted_value = intval($accepted_value);
        switch($accepted_value){
            case 1:
                $accepted = __( 'Confirmed', 'ik_schedule_location');
                break;
            case 2:
                $accepted = __( 'Rejected', 'ik_schedule_location');
                break;
            default:
                $accepted = __( 'Pending', 'ik_schedule_location');
                break;
        }

        return $accepted;
    }

    //method to return service list for modal
    public function get_services_list_for_modal($booking_id, $edit = false){
        $booking_id = intval($booking_id);
        $booking_list = $this->get_by_id($booking_id);
        $edit = rest_sanitize_boolean($edit);

        if($booking_list){

            if(is_serialized($booking_list->service_ids)){
                $service_ids = maybe_unserialize($booking_list->service_ids);
                if(is_array($service_ids)){

                    $services_list = '<div class="ik_sch_book_services_wrapper">';

                    foreach($service_ids as $service_id){
                        $service = $this->services->get_service($service_id);

                        if($service){

                            if($edit){
                                $services_list .= $this->services->get_select_services_by_location_id($booking_list->branch_id, $service_id, true);
                            } else {
                                $services_list .= '<div class="data_box">'.$service->cat_name.' > '.$service->name.'</div>';
                            }
                        }
                    }
                    if($edit){
                        $services_list .= '</div><a href="#" id="ik_sch_book_add_service_field" class="button" style="width: 100%;max-width: 100px;text-align: center;margin-left: 2px;">'.__( 'Add Service', 'ik_schedule_location').'</a>';
                    } else {
                        $services_list .= '</div> <span type_data="service_ids" class="edit_data_info dashicons dashicons-edit"></span>';
                    }                             

                    return $services_list;
                }
            }
        }
        
        return '';
    }

    //modal to create new booking
    public function show_modal_create_booking(){
        if(ik_sch_book_user_permissions()){
            $html_output = 'ik_sch_book_book_service_panel
                <div class="modal" id="ik_sch_book_modal" tabindex="-1" role="dialog" aria-labelledby="ik_sch_book_modallLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="ik_sch_book_modalLabel">'.__( 'Make an Appointment', 'ik_schedule_location').'</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-4 bg-light pt-4 pb-4 ps-4 pe-4">
                                            <h4 class="modal_data_input">'.__( 'Select Location', 'ik_schedule_location').'</h4>
                                            <div class="ik_sch_field-wrap" data-name="location">
                                            '.$this->locations->get_location_select().'
                                            </div>
                                            <h4 class="modal_data_input">'.__( 'Select Date', 'ik_schedule_location').'</h4>
                                            <div class="ik_sch_field-wrap" data-name="date">
                                                <input type="text" id="ik_sch_field_form_date" disabled name="date" value="" placeholder="'. __( 'Select Date', 'ik_schedule_location').' *" class="ik_sch_field ik_sch-text datepicker" aria-required="true">
                                            </div>
                                            <h4 class="modal_data_input">'.__( 'Select Time', 'ik_schedule_location').'</h4>
                                            <div class="ik_sch_field-wrap" data-name="time">
                                                <input type="text" disabled id="ik_sch_field_form_time" name="time" value="" placeholder="'. __( 'Select Time', 'ik_schedule_location').' *" class="ik_sch_field ik_sch-text timepicker" aria-required="true">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h4 class="modal_data_input">'.__( 'Your Name', 'ik_schedule_location').'</h4>
                                            <div class="ik_sch_field-wrap" data-name="name_customer">
                                                <input type="text" name="name_customer" id="ik_sch_field_form_name_customer" value="" placeholder="'. __( 'Enter Your Name', 'ik_schedule_location').' *" class="ik_sch_field ik_sch-text" aria-required="true">
                                            </div>
                                            <h4 class="modal_data_input">'.__( 'Email', 'ik_schedule_location').'</h4>
                                            <div class="ik_sch_field-wrap" data-name="email_address">
                                                <input type="email" name="email_address" id="ik_sch_field_form_email_address" value="" placeholder="'. __( 'Enter Your Email', 'ik_schedule_location').' *" class="ik_sch_field ik_sch-text" aria-required="true">
                                            </div>
                                            <h4 class="modal_data_input">'.__( 'Phone', 'ik_schedule_location').'</h4>
                                            <div class="ik_sch_field-wrap" data-name="phone">
                                                <input type="text" name="phone" id="ik_sch_field_form_phone" value="" placeholder="'. __( 'Enter Your Phone', 'ik_schedule_location').' *" class="ik_sch_field ik_sch-text" aria-required="true">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="ik_sch_book_modal_services">
                                                <h4 class="modal_data_input">'.__( 'Select Service', 'ik_schedule_location').'</h4>
                                                <div id="ik_sch_book_modal_services_wrapper">
                                                    <div class="ik_sch_book_services_wrapper">
                                                        <div class="ik_sch_book_services_select_wrapper">
                                                            <select name="service_ids[]" class="ik_sch_book_edit_field ik_sch_book_services_select"></select>
                                                            <a class="ik_sch_book_delete_field" href="#"><span class="dashicons dashicons-trash"></span></a>
                                                        </div>
                                                    </div>
                                                    <a href="#" disabled id="ik_sch_book_add_service_field" class="button" style="width: 100%;max-width: 100px;text-align: center;margin-left: 2px;">'.__( 'Add Service', 'ik_schedule_location').'</a>
                                                </div>
                                            </div>
                                            <h4 class="modal_data_input">'.__( 'Internal Note', 'ik_schedule_location').'</h4>
                                            <div class="ik_sch_field-wrap">
                                                <textarea name="internal_note" id="ik_sch_book_modal_interal_note"></textarea>                                            
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="ik_sch_field_form_modal_submit" class="button button-primary">'. __( 'Book', 'ik_schedule_location').'</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            return $html_output;
        }
        return false;
    }

    //method to show data about booking ID
    public function show_modal_by_booking_id($booking_id){
        $booking_id = intval($booking_id);
        $booking_list = $this->get_by_id($booking_id);
        $booking_config = $this->get_config();


        if($booking_list){

            $services_list = $this->get_services_list_for_modal($booking_id);
            $booking_color = (sanitize_hex_color($booking_list->color) != '') ? sanitize_hex_color($booking_list->color) : '#3498db';
            $accepted = $this->get_status_name_by_value($booking_list->accepted);
            $branch = $this->locations->get_location_name($booking_list->branch_id);
            $message = ($booking_list->message != '') ? $booking_list->message : '-';

            $booking_time = substr($booking_list->booking_date, -8); //00:00:00
            $booking_datetime = $this->available_days->convert_date_format($booking_list->booking_date).' '.$this->available_days->convert_time_format($booking_time);
            $request_time = substr($booking_list->request_date, -8); //00:00:00
            $request_datetime = $this->available_days->convert_date_format($booking_list->request_date).' '.$this->available_days->convert_time_format($request_time);


            //if staff data to show
            if($booking_config['staff_enabled']) {
                $employee = $this->staff->get_name($booking_list->staff_id, true);

                $staff_data = '
                    <div class="col-md-4">
                        <h4>'.__( 'Staff Member', 'ik_schedule_location').'</h4>
                        <div class="data_info">'.$employee.'<span type_data="staff_id" class="edit_data_info dashicons dashicons-edit"></span></div>

                    </div>';
            } else {
                $staff_data = '';
            }

            //if Woocommerce enabled and order > 0
            $woocommerce_order = ($booking_list->wc_order > 0) ? '<div class="col-md-4">
                <h4>'.__( 'Woocomerce Order', 'ik_schedule_location').'</h4>
                <div class="data_info">#'.$booking_list->wc_order.'</div>
            </div>' : '';

            $output = '<div class="modal" id="ik_sch_book_modal" iddata="'.$booking_list->id.'" tabindex="-1" role="dialog" aria-labelledby="ik_booking_modallLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="ik_booking_modalLabel">'.__( 'Booking Info', 'ik_schedule_location').' #'.$booking_list->id.'</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <div class="right-header-panel">
                                <div class="remove-element">
                                    <a href="#" class="button-primary ik_sch_book_red_color" id="ik_sch_book_remove_entry">'.__( 'Remove Reservation', 'ik_schedule_location').'</a>
                                </div>
                                <div id="colorPreview">
                                    <input type="color" id="ik_booking_modal_colorPicker" value="'.$booking_color.'">                            
                                    <a id="turn-back-color" href="#"><span class="dashicons dashicons-backup"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h4>'.__( 'Name', 'ik_schedule_location').'</h4>
                                        <div>
                                            <div class="data_info">'.$booking_list->f_name.'<span type_data="f_name" class="edit_data_info dashicons dashicons-edit"></span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>'.__( 'Last Name', 'ik_schedule_location').'</h4>
                                        <div>
                                            <div class="data_info">'.$booking_list->lastname.'<span type_data="lastname" class="edit_data_info dashicons dashicons-edit"></span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>'.__( 'Branch', 'ik_schedule_location').'</h4>
                                        <div>
                                            <div class="data_info">#'.$booking_list->branch_id.' '.$branch.'<span type_data="branch_id" class="edit_data_info dashicons dashicons-edit"></span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>'.__( 'Booking Date', 'ik_schedule_location').'</h4>
                                        <div>    
                                            <div class="data_info">'.$booking_datetime.'<span type_data="booking_date" class="edit_data_info dashicons dashicons-edit"></span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>'.__( 'Email', 'ik_schedule_location').'</h4>
                                        <div>
                                            <div class="data_info">'.$booking_list->email.'<span type_data="email" class="edit_data_info dashicons dashicons-edit"></span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>'.__( 'Phone', 'ik_schedule_location').'</h4>
                                        <div>
                                            <div class="data_info">'.$booking_list->phone.'<span type_data="phone" class="edit_data_info dashicons dashicons-edit"></span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>'.__( 'Request Date', 'ik_schedule_location').'</h4>
                                        <div class="data_info">'.$request_datetime.'</div>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>'.__( 'Last Edit', 'ik_schedule_location').'</h4>
                                        <div class="data_info">'.$booking_list->last_edit.'</div>
                                    </div>
                                    '.$staff_data.'
                                    '.$woocommerce_order.'
                                    <div class="col-md-4">
                                        <h4>'.__( 'IP', 'ik_schedule_location').'</h4>
                                        <div class="data_info">'.$booking_list->ip.'</div>
                                    </div>
                                    <div class="col-md-4">
                                        <h4>'.__( 'Status', 'ik_schedule_location').'</h4>
                                        <div>
                                            <div class="data_info">'.$accepted.' <span type_data="accepted" class="edit_data_info dashicons dashicons-edit"></span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h4>'.__( 'Internal Note', 'ik_schedule_location').'</h4>
                                        <div>
                                            <div class="data_info">'.esc_html($booking_list->internal_note).' <span type_data="internal_note" class="edit_data_info dashicons dashicons-edit"></span></div>
                                        </div>
                                    </div>
                                    <div class="crossed-line"></div>
                                    <div class="col-md-6">
                                        <h4>'.__( 'Services', 'ik_schedule_location').'</h4>
                                        <div>
                                        '.$services_list.'
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h4>'.__( 'Message', 'ik_schedule_location').'</h4>
                                        <div class="data_info">'.$message.'</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';

            return $output;

        }

        return false;
    }

    //send email notification by booking request id and data related for email
    public function send_email_notification($booking_id, $data_message){
        $booking_id = intval($booking_id);
        $booking_data = $this->get_by_id($booking_id);

        if($booking_data){
            switch($data_message){
                case 'booked':
                    $subject = __('Thank you for booking with us', 'ik_schedule_location');
                    $main_message = __('Thank you for booking with us. We have received your reservation request and are currently processing it. Your reservation is pending confirmation. We will notify you as soon as we have an update.', 'ik_schedule_location');
                    break;
                case 'accepted':
                    $subject = __('Reservation Status Update', 'ik_schedule_location');
                    $main_message = __('We hope this message finds you well. We want to inform you that there has been an update in the status of your reservation. Below, we provide you with the updated details:', 'ik_schedule_location');
                    break;
                case 'booking_date':
                    $subject = __('Reservation Date/Time Updated', 'ik_schedule_location');
                    $main_message = __('We hope this message finds you well. We want to inform you that there has been an update in your reservation. Below, we provide you with the updated details:', 'ik_schedule_location');
                    break;
                case 'branch_id':
                    $subject = __('Reservation Updated: Place changed', 'ik_schedule_location');
                    $main_message = __('We hope this message finds you well. We want to inform you that there has been an update in your reservation. Below, we provide you with the updated details:', 'ik_schedule_location');
                    break;
                default:
                    $subject = __('Thank you for booking with us', 'ik_schedule_location');
                    $main_message = __('Thank you for booking with us. We have received your reservation request and are currently processing it. Your reservation is pending confirmation. We will notify you as soon as we have an update.', 'ik_schedule_location');
                    break;
            }
    
            $booking_config = $this->get_config();
    
            // Sender email address
            $sender_email = $booking_config['email_sender'];
    
            // email header
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                "From: ".get_option('blogname')." <".$sender_email.">"
            );

            $services_list = '';
            if(is_serialized($booking_data->service_ids)){
                $service_ids = maybe_unserialize($booking_data->service_ids);
                if(is_array($service_ids)){
                    foreach($service_ids as $service_id){
                        $service = $this->services->get_service($service_id);
                        if($service){
                            $services_list .= $service->cat_name.' - '.$service->name. ' | ';
                        }
                    }
                    if(strlen($services_list) > 0){
                        $services_list = substr($services_list, 0, -2);
                        $services_list = __('Reserved Service:', 'ik_schedule_location') . ' '.$services_list.'<br>';
                    }
                }
            }

            $employee_data = ($booking_data->staff_id > 0) ? __('Employee:', 'ik_schedule_location') . ' '.$this->staff->get_name($booking_data->staff_id).'<br>' : '';
    
            $message = sprintf(__('Dear %s,', 'ik_schedule_location'), $booking_data->f_name.' '.$booking_data->lastname);
            $message .= '<br><br>';
            $message .= $main_message;
            $message .= '<br><br>';
            $message .= __('Reservation Date:', 'ik_schedule_location').' '.$this->available_days->convert_datetime_format($booking_data->booking_date).'<br>';
            $message .= $services_list;
            $message .= __('Branch:', 'ik_schedule_location') . ' '.$this->locations->get_location_name($booking_data->branch_id).'<br>';
            $message .= $employee_data;
            $message .= __('New Status:', 'ik_schedule_location') . ' '.$this->get_status_name_by_value($booking_data->accepted).'<br>';
            $message .= '<br>';
            $message .= __('We want to make sure you are aware of any changes to your reservation. If you have any questions or need more details about this update, please feel free to get in touch with our customer support team.', 'ik_schedule_location');
            $message .= '<br><br>';
            $message .= __('We appreciate your trust in our services and look forward to providing you with the best possible experience. We are always here to assist you with anything you need.', 'ik_schedule_location');
            $message .= '<br><br>';
            $message .= sprintf(__('Appointment cancellations must be reported at least %s hours in advance. Otherwise the costs must be offset.', 'ik_schedule_location'), $this->get_config()['limit_start_booking']);
            $message .= '<br><br>';
            $message .= __('Thank you for choosing our services!', 'ik_schedule_location');
            $message .= '<br><br>';
            $message .= __('Sincerely,', 'ik_schedule_location') . '<br>'.get_option('blogname');
            
            // Send email
            $sent = wp_mail($booking_data->email, $subject, $message, $headers);
    
            return $sent;
        }

        return false;
    }

    //method to return month quantity of days, name and number
    public function get_month_data($args = array()){
        date_default_timezone_set($this->available_days->timezone);

        $location_id = (isset($args['location_id'])) ? intval($args['location_id']) : 0;
        $month = (isset($args['month'])) ? intval($args['month']) : date('n');
        $month = ($month > 0 && $month < 13) ? $month : date('n');
        $year = (isset($args['year'])) ? intval($args['year']) : date('Y');
        $year = ($year > 0) ? $year : date('Y');
        $days_of_the_week = array(__('Sunday'),__('Monday'),__('Tuesday'),__('Wednesday'),__('Thursday'),__('Friday'),__('Saturday'));

        switch ($month) {
            case 1:
                $month_name = __('January');
                break;
            case 2:
                $month_name = __('February');
                break;
            case 3:
                $month_name = __('March');
                break;
            case 4:
                $month_name = __('April');
                break;
            case 5:
                $month_name = __('May');
                break;
            case 6:
                $month_name = __('June');
                break;
            case 7:
                $month_name = __('July');
                break;
            case 8:
                $month_name = __('August');
                break;
            case 9:
                $month_name = __('September');
                break;
            case 10:
                $month_name = __('October');
                break;
            case 11:
                $month_name = __('November');
                break;
            case 12:
                $month_name = __('December');
                break;
        }

        $location_id_where = ($location_id > 0) ? " AND branch_id = ".intval($_GET['location_id']) : '';

        //calculate number of days of the month
        $n_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        //calculate first day of the month
        $firstDayOfMonth = $year.'-'.$month.'-01';
        $lastDayOfMonth = $year.'-'.$month.'-'.$n_days;
        $dateTimeFirstDay = new DateTime($firstDayOfMonth);
        $dateTimeLastDay = new DateTime($lastDayOfMonth);


        // I get 1 for monday and 7 for sunday (1 para lunes, 7 para domingo)
        $dayOfWeekFirst = $dateTimeFirstDay->format('N');
        $dayOfWeekLast = $dateTimeLastDay->format('N');

        //if first day of the month is not Sunday
        if($dayOfWeekFirst !== 7){
            $daysFromLastMonth = $dayOfWeekFirst;
            $lastmonth = (($month - 1) == 0) ? 12 : $month - 1;
            $year_last = (($month - 1) == 0) ? $year - 1 : $year;
            $last_m_n_days = cal_days_in_month(CAL_GREGORIAN, $lastmonth, $year_last);
            $fromlastmonthDay = $last_m_n_days - $daysFromLastMonth + 1;

            global $wpdb;
            $queryFirstW = "SELECT * FROM ".$this->db_table_requests." WHERE `booking_date` BETWEEN '".$year_last."-".$lastmonth."-".$fromlastmonthDay." 00:00:00' AND '".$year_last."-".$lastmonth."-".$last_m_n_days." 23:59:59'".$location_id_where." ORDER BY UNIX_TIMESTAMP(booking_date) ASC";
            $bookings_of_last_month = $wpdb->get_results($queryFirstW);
    
            if (isset($bookings_of_last_month[0]->id)){ 
                $last_m_bookings = $bookings_of_last_month;
            } else {
                $last_m_bookings = false;
            }
        } else {
            $last_m_bookings = false;
            $last_m_n_days = 0;
            $fromlastmonthDay = 7;
        }

        //if last day of the month is not Saturday
        if($dayOfWeekLast !== 6){
            switch ($dayOfWeekLast) {
                case 1:
                    $next_m_n_days = 5;
                    break;
                case 2:
                    $next_m_n_days = 4;
                    break;
                case 3:
                    $next_m_n_days = 3;
                    break;
                case 4:
                    $next_m_n_days = 2;
                    break;
                case 5:
                    $next_m_n_days = 1;
                    break;
                case 7:
                    $next_m_n_days = 6;
                    break;
                }
            $nextmonth = (($month + 1) > 12) ? 1 : $month + 1;
            $year_next = (($month + 1) > 12) ? $year + 1 : $year;

            global $wpdb;
            $queryLastW = "SELECT * FROM ".$this->db_table_requests." WHERE `booking_date` BETWEEN '".$year_next."-".$nextmonth."-01 00:00:00' AND '".$year_next."-".$nextmonth."-".$next_m_n_days." 23:59:59'".$location_id_where." ORDER BY UNIX_TIMESTAMP(booking_date) ASC";;
            $bookings_of_next_month = $wpdb->get_results($queryLastW);
    
            if (isset($bookings_of_next_month[0]->id)){ 
                $next_m_bookings = $bookings_of_next_month;
            } else {
                $next_m_bookings = false;
            }
        } else {
            $next_m_bookings = false;
            $next_m_n_days = 0;
        }

        global $wpdb;
        $query = "SELECT * FROM ".$this->db_table_requests." WHERE `booking_date` BETWEEN '".$year."-".$month."-01 00:00:00' AND '".$year."-".$month."-".$n_days." 23:59:59'".$location_id_where." ORDER BY UNIX_TIMESTAMP(booking_date) ASC";;
        $bookings_of_the_month = $wpdb->get_results($query);

        if (isset($bookings_of_the_month[0]->id)){ 
            $bookings = $bookings_of_the_month;
        } else {
            $bookings = false;
        }

        $month_data = array(
            'name' => $month_name,
            'number' => $month,
            'year' => $year,
            'weekday_lastmonth' => $fromlastmonthDay,
            'last_m_n_days' => $last_m_n_days,
            'n_days' => $n_days,
            'next_m_n_days' => $next_m_n_days,
            'days' => $days_of_the_week,
            'last_m_bookings' => $last_m_bookings,
            'bookings' => $bookings,
            'next_m_bookings' => $next_m_bookings,
        );
        
        return $month_data;
    }

    //method to delete booking service products from Woocommerce cart
    public function delete_services_from_cart(){
        if (class_exists('WC_Product')) {
            $product_type = 'schedule_booking_product';

            $cart = WC()->cart;
            foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                // Obtiene el objeto del producto
                $product = $cart_item['data'];

                // Comprueba si el tipo de producto coincide
                if ($product->get_type() == $product_type) {
                    // Elimina el elemento del carrito
                    $cart->remove_cart_item($cart_item_key);
                }
            }
        }
        return;
    }

    //method to return available times on Woocommerce cart calendar
    public function get_available_wc_calendar_time_blocks($date, $branch_id){

        $config_data = $this->get_config();
        //default time gap
        $TimeNowGap = '00:00:00';

        //check if time gap to book
        $time_gap = $config_data['limit_start_booking'];

        //Check the available booking time gap if there's any time gap defined
        if($time_gap > 0){
            // get now day time
            $dateTimeNow = new DateTime();

            // add the time gap to now date time
            $dateTimeNowGap = $dateTimeNow->modify("+$time_gap hours");
            $dateTimeNowGap_date = $dateTimeNowGap->format('Y-m-d');
            $dateTimeNowGap_time = $dateTimeNowGap->format('H:i:s');
            $dateSelectedObj = new DateTime($date);
            $dateSelectedObj_date = $dateSelectedObj->format('Y-m-d');
            $dateSelectedObj_time = $dateSelectedObj->format('H:i:s');

            // Compare dates to make sure the date is available
            if ($dateTimeNowGap_date >= $dateSelectedObj_date) {

                if ($dateTimeNowGap_time > $dateSelectedObj_time && $dateTimeNowGap_date == $dateSelectedObj_date){
                    //define the end of the end of the limit
                    $TimeNowGap = $dateTimeNowGap_time;
                } else {
                    //no time available to show
                    return false;
                }
            }
            
        }

        $selectedDate = $this->available_days->convert_date_format($date);
        $branch_id = absint($branch_id);

        //If there're services on session i add minutes to discount
        $time_to_discount = 0;
        $service_ids_selected = $this->get_services_selected_by_user($branch_id);
        if($service_ids_selected == true){
            $service_id_listed = array();
            foreach ($service_ids_selected as $service_id){
                //to avoid add a service id data twice
                if (!in_array($service_id, $service_id_listed)) {

                    $service_id_listed[] = $service_id;                        

                    $delivery_time = get_post_meta($service_id, 'delivery_time', true);

                    if($delivery_time){
                        $time_to_discount = $time_to_discount + intval($delivery_time);
                    }
                }
            }
        }

        $format_date_id = $config_data['format_date'];
        $formatTime = $config_data['format_time'];
        $format_date = $this->format_date($format_date_id, 'php');
        $dateTime = DateTime::createFromFormat($format_date, $selectedDate);

        //validate date
        if ($dateTime && $dateTime->format($format_date) === $selectedDate) {
            $available = $this->available_days->get_available_times_js($branch_id, $dateTime, $time_to_discount, $formatTime);

            if($available){
                $available_times_data = json_decode($available, true);

                //if location is not closed
                if(!$available_times_data['times_data']['alwaysclose']){
        
                    // get closing and opening times
                    $opentime = $available_times_data['times_data']['opentime'];
                    $closetime = $available_times_data['times_data']['closetime'];
        
                    // array to save possible times for appointments
                    $available_times_array = array();
        
                    //get window time frame between appointments
                    $time_frame = $this->get_config()['time_frame'];
        
                    // get available blocks
                    for ($i = 0; $i < count($opentime); $i++) {
                        $time_start = strtotime($opentime[$i]);
                        $time_end = strtotime($closetime[$i]);
                        
                        while ($time_start <= $time_end) {
                            $available_times_array[] = date('H:i:s', $time_start);
                            $time_start += 60 * $time_frame;
                        }
                    }

                    $available_times = '<div class="available_time_blocks"><ul>';
                    
                    //to make sure available hours are added
                    $hours_available_added = false;

                    //to mark if time selected before
                    $time_selected = $this->get_session_time_selected();

                    foreach($available_times_array as $available_time){
                        
                        // I check if there's a time gap to respect
                        $TimeNowGapObj = DateTime::createFromFormat('H:i:s', $TimeNowGap);
                        $available_timeObj = DateTime::createFromFormat('H:i:s', $available_time);

                        $interval_gap = $TimeNowGapObj->diff($available_timeObj);

                        // Verify is this time is available considering the gap of time to book
                        if ($interval_gap->invert == 0) {
                            $time_selected_class = ($time_selected == $available_time) ? 'class="selected"' : '';
                            $timeblock = $this->available_days->convert_time_format($available_time);

                            //get the total price if woocommerce is enabled and has a price
                            if (class_exists('WooCommerce')) {
                                $total_price = WC()->cart->get_cart_total();
                            } else {
                                $total_price = '';
                            }

                            $available_times .= '<li '.$time_selected_class.'><a href="#" data_id="'.$timeblock.'"><div class="available_time_blocks_col align-left">'.$timeblock.'</div><div class="available_time_blocks_col align-right">'.$total_price.'</div></a></li>';
                            $hours_available_added = true;
                        }

                    }
                    $available_times .= '</ul></div>';

                    if($hours_available_added){
                        return $available_times;
                    } else {
                        return false;
                    }
                }
            }

        }

        return false;  
    }

    //get the day of the week corresponding to Today and the week of the day selected
    public function get_booking_wc_weekday_today($date_selected){
        date_default_timezone_set($this->available_days->timezone);
        $date_selected = intval($date_selected);
        $todayDayNumber = date('w');
        $selectedDayNumber = date('w', $date_selected);

        if($todayDayNumber !== $selectedDayNumber){
            $startofweek_n = $selectedDayNumber;
            $startofweek = date('Y-m-d', $date_selected);
    
            //I get the week day date to start like the same day of today
            for ($i = 0; $i <= 6; $i++) {
    
                $startofweek = date('Y-m-d', strtotime("-1 days", strtotime($startofweek)));
                $startofweek_n = date('w', strtotime($startofweek));
    
                if($startofweek_n == $todayDayNumber){
                    break;
                }
            }
        } else {
            $startofweek = date('Y-m-d', $date_selected);
        }
        return $startofweek;
    }

    //method to get calendar on Woocommerce cart
    private function get_booking_wc_calendar_data(){
        date_default_timezone_set($this->available_days->timezone);

        $location_id = $this->get_session_location_id();
        $date_selected = $this->get_session_date_selected();
        $location_name = $this->locations->get_location_name($location_id);

        // Get the day of the week number (0 for Sunday, 1 for Monday, etc.)
        $todayDayNumber = date('w');

        //I make sure the date is Today and not later of the limit
        $todays_date = date('Y-m-d');
        $todays_date_int = strtotime($todays_date);
        $todays_date_obj = new DateTime();


        //make sure date is not old
        $todays_date_obj = new DateTime();
        $date_date_selected_obj = DateTime::createFromFormat("Y-m-d", date('Y-m-d', $date_selected));
        $old_data_date = $todays_date_obj->diff($date_date_selected_obj);

        //date is old so redefine selected date to Today
        if($old_data_date->invert == 1){
            $date_selected = $todays_date_int;
            $date_date_selected_obj = DateTime::createFromFormat("Y-m-d", date('Y-m-d', $date_selected));
        }

        $config_data = $this->get_config();
        $limit_days = intval($config_data['limit_booking']);
        $over_limit_days = $limit_days + 7; //over the limit
        $limit_date_today_week = date('Y-m-d', strtotime("+6 days", $date_selected));
        $limit_date = date('Y-m-d', strtotime("+$limit_days days", $todays_date_int));
        $date_limit_obj = DateTime::createFromFormat("Y-m-d", $limit_date);
        $date_date_selected_obj = DateTime::createFromFormat("Y-m-d", date('Y-m-d', $date_selected));
        $date_this_week_obj = DateTime::createFromFormat("Y-m-d", $limit_date_today_week);

        //making sure if first week or the limit to book
        $diference_date_limit = $date_date_selected_obj->diff($date_limit_obj);
        $diference_this_week_limit = $date_this_week_obj->diff($todays_date_obj);

        //check if arrow to go to last week enabled
        if ($todays_date === date('Y-m-d', $date_selected) || $diference_this_week_limit->days < 7) {
            $arrow_back = true;
        } else {
            $arrow_back = false;
        }

        //check if arrow to go to next week enabled
        if ($diference_date_limit->days > $limit_days) {
            $arrow_forward = true;
        } else {
            $arrow_forward = false;
        }

        //if today's date is correct
        if ($diference_date_limit->days <= $over_limit_days) {
            $todaysDate = date('Y-m-d H:i:s', $date_selected);
        } else {
            $todaysDate = date('Y-m-d H:i:s');
            $arrow_forward = false;
            $arrow_back = true;
        }
        
        // Create an array to store the information
        $daysData = array();

        $month_n = date('n', strtotime($todaysDate)) - 1;
        $month_name = $this->monthNames[$month_n];

        //check how many days back until matching the day of the week that corresponds to Today
        $selectedDayNumber = date('w', $date_selected);

        //if day n is not the same for example thursday is no thursday
        if($selectedDayNumber != $todayDayNumber){

            $startofweek = $this->get_booking_wc_weekday_today($date_selected);

        } else {
            $startofweek = $todaysDate;
        }    

        // Get the values for the next 6 days
        for ($i = 0; $i <= 6; $i++) {
            $nextDayNumber = ($todayDayNumber + $i) % 7; // Ensure it doesn't go beyond the last day
            $nextDayName = $this->dayNamesAbv[$nextDayNumber];
            $nextDayName_full = $this->dayNames[$nextDayNumber];
            
            $date_addDay = date('Y-m-d', strtotime("+$i days", strtotime($startofweek)));
            $dayint = strtotime($date_addDay);
            $dayNumber = date('d', $dayint);
            $next_month_n = date('n', $dayint) - 1;
            $next_month_name = $this->monthNames[$month_n];

            //to know the selected day
            if($dayint == $date_selected){
                $index_selected = $i;
            }
            
            // Add the day to the array
            $daysData[] = array(
                'number' => $dayNumber,
                'name' => $nextDayName,
                'name_full' => $nextDayName_full,
                'month' => $next_month_name,
                'date_number' => $dayint,
            );
        }

        $index_selected = (isset($index_selected)) ? $index_selected : 0;

        //check if number selected in Woocommerce section an it's part of the array
        $selected_date_n = (isset($_SESSION['ik_sch_services_date_n_cart'])) ? intval($_SESSION['ik_sch_services_date_n_cart']) : $daysData[$index_selected]['number'];

        //check valid number of days
        $existing_day_n = array_column($daysData, 'number');

        //validate day selected
        $selected_date_n = ($todayDayNumber != $selected_date_n && !(in_array($selected_date_n, $existing_day_n))) ? $daysData[0]['number'] : $selected_date_n;
        
        //get days in order for table
        $td_days_h = '';
        $td_days_n = '';
        foreach ($daysData as $dayData){
            //select first day or session selected day
            $selected = ($selected_date_n == $dayData['number']) ? true : false;
            $selected_class = ($selected_date_n == $dayData['number']) ? 'class="active_date"' : '';
            if($selected){
                $selected_day = $dayData['name_full'];
                $selected_day_n = $dayData['number'];
                $selected_month = $dayData['month'];
            }

            $td_days_h .= '<td>'.$dayData['name'].'</td>';
            $td_days_n .= '<td><a '.$selected_class.' href="#" data_id="'.$dayData['date_number'].'">'.$dayData['number'].'</a></td>';
        }

        //get available times
        $available = $this->get_available_wc_calendar_time_blocks($todaysDate, $location_id);

        if($available){
            $available_times = $available;

        } else {
            //not available times
            $available_times = sprintf(
                __("Unfortunately, there are no appointments available on the %s, %s %s. Please choose another day.", 'ik_schedule_location'),
                $selected_day,
                $selected_day_n,
                $selected_month
            );
        }

        //get staff selection if enabled
        $args_staff_selector = array(
            'location_id' => $location_id,
            'staff_id_selected' => $this->get_session_staff_selected(),
            'date_selected' => $date_selected,
            'same_block_count' => $config_data['block_repeat_limit'], // to check booking limit per staff member (usually should be one)
        );
        $staff_select = ($config_data['staff_enabled']) ? $this->staff->get_select($args_staff_selector) : '';

        $calendar_data = array(
            'month_name'        => $month_name,
            'td_days_h'         => $td_days_h,
            'td_days_n'         => $td_days_n,
            'available_times'   => $available_times,
            'location_name'     => $location_name,
            'arrow_back'        => $arrow_back,
            'arrow_forward'     => $arrow_forward,
            'staff_select'      => $staff_select,
        );

        return $calendar_data;
    }

    private function get_booking_wc_month_calendar_data(){
        date_default_timezone_set($this->available_days->timezone);

        // Get the day of the week number (0 for Sunday, 1 for Monday, etc.)
        $todayDayNumber = date('w');

        $location_id = $this->get_session_location_id();
        $date_selected = $this->get_session_date_selected();
        $location_name = $this->locations->get_location_name($location_id);

        //I make sure the date is Today and not later of the limit
        $todays_date = date('Y-m-d');
        $todays_date_int = strtotime($todays_date);
        $todays_date_obj = new DateTime();

        $config_data = $this->get_config();
        $limit_days = intval($config_data['limit_booking']);
    
        // Get the first day of the month for the selected date
        $first_day_of_month = date('Y-m-01', $date_selected);
        $first_day_of_month_obj = new DateTime($first_day_of_month);
        $over_limit_days = $limit_days + 31; //over the limit
        $limit_date = date('Y-m-d', strtotime("+$limit_days days", $todays_date_int));
        $date_limit_obj = DateTime::createFromFormat("Y-m-d", $limit_date);
        $date_date_selected_obj = DateTime::createFromFormat("Y-m-d", date('Y-m-d', $date_selected));
        $old_data_date = $todays_date_obj->diff($date_date_selected_obj);

        //date is old so redefine selected date to Today
        if($old_data_date->invert == 1){
            $date_selected = $todays_date_int;
            $date_date_selected_obj = DateTime::createFromFormat("Y-m-d", date('Y-m-d', $date_selected));
        }

        // Determine the start day of the week
        $week_start = ($config_data['calendar_starts_monday']) ? 1 : 0; // 0 for Sunday, 1 for Monday
    
        // Get the number of days in the month
        $num_days_in_month = $first_day_of_month_obj->format('t');
    
        // Get the day of the week for the first day of the month
        $first_day_of_week = $first_day_of_month_obj->format('w');

        //making sure if first week or the limit to book
        $diference_date_limit = $date_date_selected_obj->diff($date_limit_obj);

        // Determine the offset for the first day of the month in the calendar
        $offset = ($first_day_of_week < $week_start) ? 7 - ($week_start - $first_day_of_week) : $first_day_of_week - $week_start;
    
        // Create an array to store the information
        $daysData = array();
    
        // Generate the days of the month
        for ($i = 1 - $offset; $i <= $num_days_in_month; $i++) {
            // Calculate the day number and date
            $day_number = ($i > 0) ? $i : '';
            $count_days = $i -1;
            $day_date = ($i > 0) ? date('Y-m-d', strtotime("+$count_days days", strtotime($first_day_of_month))) : '';


            if ($i > 0) {
                $next_day_date = date('Y-m-d', strtotime("+$i days", strtotime($day_date)));
                $next_day_name = date('D', strtotime($next_day_date));
                $next_day_name_full = date('l', strtotime($next_day_date));
                $next_month_name = date('F', strtotime($next_day_date));
            } else {
                $next_day_name = '';
                $next_day_name_full = '';
                $next_month_name = '';
            }

            // Add the day to the array
            $daysData[] = array(
                'number' => $day_number,
                'name' => $next_day_name,
                'name_full' => $next_day_name_full,
                'month' => $next_month_name,
                'date_number' => strtotime($day_date),
            );
        }

        //check if arrow to go to last month enabled
        if ($todays_date >= date('Y-m-d', $date_selected)) {
            $arrow_back = true;
        } else {
            $arrow_back = false;
        }

        //check if arrow to go to next week enabled
        if ($diference_date_limit->days > $limit_days) {
            $arrow_forward = true;
        } else {
            $arrow_forward = false;
        }

        //if today's date is correct
        if ($diference_date_limit->days <= $over_limit_days) {
            $todaysDate = date('Y-m-d H:i:s', $date_selected);
        } else {
            $todaysDate = date('Y-m-d H:i:s');
            $arrow_forward = false;
            $arrow_back = true;
        }

        //check valid number of days
        $existing_day_n = array_column($daysData, 'number');

        // Check if the month starts on the first day of the week
        $month_starts_on_first_day_of_week = ($offset == 0);
    
        // Check if the month ends on the last day of the week
        $last_day_of_week = ($first_day_of_week + $num_days_in_month) % 7;

        $last_day_of_month = $first_day_of_month_obj->format('Y-m-t'); // last day of the month
        $last_day_obj = new DateTime($last_day_of_month);
        $last_day_of_week = $last_day_obj->format('w'); // name of last day of the month     
        if($week_start){
            $month_ends_on_last_day_of_week = ($last_day_of_week == ($last_day_of_week == 1 ? 6 : 0));
        } else {
            $month_ends_on_last_day_of_week = ($last_day_of_week == ($last_day_of_week == 0 ? 6 : 0));
        }        
    
        // If the month doesn't start on the first day of the week, add days from the previous month
        if (!$month_starts_on_first_day_of_week) {
            // Generate the days from the previous month
            $prev_month_last_day = date('Y-m-d', strtotime('-1 day', strtotime($first_day_of_month)));
            $prev_month_last_day_obj = new DateTime($prev_month_last_day);
            $prev_month_last_day_number = $prev_month_last_day_obj->format('d');

            $prev_month_first_day = date('Y-m-01', strtotime('-1 month', strtotime($first_day_of_month)));
            $prev_month_first_day_obj = new DateTime($prev_month_first_day);
            $num_days_in_prev_month = $prev_month_first_day_obj->format('t');

            $prev_month_days = array();
            for ($i = $num_days_in_prev_month - $offset + 1; $i <= $num_days_in_prev_month; $i++) {
                $day_number = $i;
                $day_date = date('Y-m-d', strtotime("$i days", strtotime($prev_month_first_day)));
                $day_name = date('D', strtotime($day_date));
                // Add the day to the array
                $prev_month_days[] = array(
                    'number' => $day_number,
                    'name' => $day_name,
                    'date' => $day_date,
                );
            }

            // Reverse the order of the days
            $prev_month_days = array_reverse($prev_month_days);

            // Add the days to the beginning of the array
            foreach ($prev_month_days as $day) {
                array_unshift($daysData, $day);
            }
        }

        // If the month doesn't end on the last day of the week, add days from the next month
        if (!$month_ends_on_last_day_of_week) {
            // Get the first day of the next month
            $next_month_date = date('Y-m-d', strtotime('+1 month', strtotime($first_day_of_month)));
    
            // Generate the days from the next month
            $day_to_next_month = ($week_start) ? 7 : 6;
            for ($i = 1; $i <= ($day_to_next_month - $last_day_of_week); $i++) {
                // Calculate the day number and date
                $day_number = $i;
                $day_date = date('Y-m-d', strtotime("+$i days", strtotime($next_month_date)));
    
                // Add the day to the end of the array
                $daysData[] = array(
                    'number' => $day_number,
                    'date' => $day_date,
                );
            }
        }
    
        // Generate the HTML for the days
        // Add the day names
        $day_names_order = [];

        // Include Sunday only if the week starts on Sunday
        if ($week_start === 0) {
          $day_names_order[] = 0;
        } else {
            $day_names_order[] = 1;
        }
        
        // Add remaining days based on week start
        for ($i = $week_start + 1; $i <= 6; $i++) {
          $day_names_order[] = $i;
        }
        
        // If not Sunday start, add days from 0 to week_start - 1
        if ($week_start > 0) {
          for ($i = 0; $i < $week_start; $i++) {
            $day_names_order[] = $i;
          }
        }
        
        // Use the corrected $day_names_order for displaying the days of the week
        $td_days_h = '<tr>';
        foreach ($day_names_order as $day_number) {
          $td_days_h .= '<td>'.__( $this->dayNamesAbv[$day_number], 'ik_schedule_location').'</td>';
        }
        $td_days_h .= '</tr>';        
        
        $index_selected = (isset($index_selected)) ? $index_selected : 0;

        //check if number selected in Woocommerce section an it's part of the array
        $selected_date_n = (isset($_SESSION['ik_sch_services_date_n_cart'])) ? intval($_SESSION['ik_sch_services_date_n_cart']) : $daysData[$index_selected]['number'];

        //check valid number of days
        $existing_day_n = array_column($daysData, 'number');

        //validate day selected
        $selected_date_n = ($todayDayNumber != $selected_date_n && !(in_array($selected_date_n, $existing_day_n))) ? $daysData[0]['number'] : $selected_date_n;

        $td_days_n = '';
        $counterDays = 0; // to separate a row per every week
        foreach ($daysData as $dayData) {
            if($counterDays > 6){
                $td_days_n .= '</tr><tr>'; 
                $counterDays = 0;
            }
            $selected = (strtotime($todaysDate) == $dayData['date_number']) ? true : false;
            $selected_class = (strtotime($todaysDate) == $dayData['date_number'] && isset($dayData['month'])) ? 'class="active_date"' : '';
            if($selected){
                $selected_day = $dayData['name_full'];
                $selected_day_n = $dayData['number'];
                $selected_month = $dayData['month'];
            }
            if(($dayData['number']) !== ''){
                if(isset($dayData['date_number'])){
                    $td_days_n .= '<td><a '.$selected_class.' href="#" data_id="'.$dayData['date_number'].'">'.$dayData['number'].'</a></td>';
                } else {
                    $td_days_n .= '<td>'.$dayData['number'].'</td>';
                }
                $counterDays = $counterDays + 1;   
            }  
        }
        $td_days_n .= '</tr>';      
    
        //get available times
        $available = $this->get_available_wc_calendar_time_blocks($todaysDate, $location_id);
    
        if($available){
            $available_times = $available;
    
        } else {
            //not available times
            $available_times = sprintf(
                __("Unfortunately, there are no appointments available on the %s, %s %s. Please choose another day.", 'ik_schedule_location'),
                $selected_day,
                $selected_day_n,
                $selected_month
            );
        }

        //get staff selection if enabled
        $args_staff_selector = array(
            'location_id' => $location_id,
            'staff_id_selected' => $this->get_session_staff_selected(),
            'date_selected' => $date_selected,
            'same_block_count' => $config_data['block_repeat_limit'], // to check booking limit per staff member (usually should be one)
        );
        $staff_select = ($config_data['staff_enabled']) ? $this->staff->get_select($args_staff_selector) : '';
        
        $month_n = date('n', strtotime($todaysDate)) - 1;
        $month_name = $this->monthNames[$month_n];
    
        $calendar_data = array(
            'month_name'        => $month_name,
            'day_names'         => $td_days_h,
            'td_days_n'         => $td_days_n,
            'available_times'   => $available_times,
            'location_name'     => $location_name,
            'arrow_back'        => $arrow_back,
            'arrow_forward'     => $arrow_forward,
            'staff_select'      => $staff_select,
        );
    
        return $calendar_data;
                
    }
           
    //method to get calendar on Woocommerce cart
    public function get_booking_wc_calendar(){

        //config to check if dates are shown through a button dynamically
        $config_data = $this->get_config();
        $dates_month = $config_data['dates_month'];

        //if its show dates per month and not per week
        if($dates_month){
            
            $calendar_data = $this->get_booking_wc_month_calendar_data();
            
            $arrow_back = ($calendar_data['arrow_back']) ? '' : '<button class="ik_sch_book_cart_calendar_select_month_arrow go-back"><</button>
            ';
            $arrow_forward = ($calendar_data['arrow_forward']) ? '' : '<button class="ik_sch_book_cart_calendar_select_month_arrow go-forward">></button>';

            $calendar = '<div id="ik_sch_book_cart_calendar_select">
            <div class="ik_sch_book_cart_calendar_select_month">
                '.$arrow_back.'
                <div class="ik_sch_book_cart_calendar_select_month_name">'.__( $calendar_data['month_name'], 'ik_schedule_location').'</div>
                '.$arrow_forward.'
            </div>
            <table class="ik_sch_book_cart_calendar_select_schedule">
                <thead>
                <tr>
                '.$calendar_data['day_names'].'
                </tr>
                </thead>
                <tbody>
                    '.$calendar_data['td_days_n'].'
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="available_times">'.$calendar_data['available_times'].'</td>
                    </tr>
                <tfoot>
            </table>
            <div class="ik_sch_book_cart_calendar_location">'.__( 'Location: ', 'ik_schedule_location').' <b>'.$calendar_data['location_name'].'</b></div>
            </div>';
            
            //calendar wrapper and calendar
            $calendar_table = '<div id="ik_sch_book_cart_calendar_module">
                '.$calendar_data['staff_select'].'
                <h4>'.__( 'Select the date and time for the appointment:', 'ik_schedule_location').'</h4>
                <table class="ik_sch_book_cart_calendar_module_content">
                    <tbody>
                        <tr>
                            <td>'.$calendar.'</td>
                        </tr>
                    </tbody>
                </table>
            </div>';
    
            return $calendar_table;

        } else {
            $calendar_data = $this->get_booking_wc_calendar_data();

            $arrow_back = ($calendar_data['arrow_back']) ? '' : '<button class="ik_sch_book_cart_calendar_select_month_arrow go-back"><</button>
            ';
            $arrow_forward = ($calendar_data['arrow_forward']) ? '' : '<button class="ik_sch_book_cart_calendar_select_month_arrow go-forward">></button>';

            if($calendar_data['location_name']){
                $calendar = '<div id="ik_sch_book_cart_calendar_select">
                <div class="ik_sch_book_cart_calendar_select_month">
                    '.$arrow_back.'
                    <div class="ik_sch_book_cart_calendar_select_month_name">'.__( $calendar_data['month_name'], 'ik_schedule_location').'</div>
                    '.$arrow_forward.'
                </div>
                <table class="ik_sch_book_cart_calendar_select_schedule">
                    <thead>
                    <tr>
                    '.$calendar_data['td_days_h'].'
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                        '.$calendar_data['td_days_n'].'
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="available_times">'.$calendar_data['available_times'].'</td>
                        </tr>
                    <tfoot>
                </table>
                <div class="ik_sch_book_cart_calendar_location">'.__( 'Location: ', 'ik_schedule_location').' <b>'.$calendar_data['location_name'].'</b></div>
                </div>';
                
                //calendar wrapper and calendar
                $calendar_table = '<div id="ik_sch_book_cart_calendar_module">
                    '.$calendar_data['staff_select'].'
                    <h4>'.__( 'Select the date and time for the appointment:', 'ik_schedule_location').'</h4>
                    <table class="ik_sch_book_cart_calendar_module_content">
                        <tbody>
                            <tr>
                                <td>'.$calendar.'</td>
                            </tr>
                        </tbody>
                    </table>
                </div>';
        
                return $calendar_table;
        
            } else {
                //wrong location name I delete booking product types from cart
                $this->delete_services_from_cart();

            }
        }

        return '';
 
    }
  
    //method to get url on Woocommerce cart to go back to location page
    public function get_booking_wc_button_to_location_page(){
        $location_url = (isset($_SESSION['ik_sch_url_location'])) ? esc_url($_SESSION['ik_sch_url_location']) : '#';
        $button_to_back_to_location = '<tr id="add-more-modal_content">
                <td colspan="4">
                    <a href="'.$location_url.'" class="btn btn-secondary add-more-location-services"><i class="fas fa-plus-circle"></i> '. __( 'Add another service', 'ik_schedule_location').'</a>
                </td>
            </tr>';
        return $button_to_back_to_location;
    }

    //return next/last week woocommerce cart calendar
    public function get_more_week_calendar_data($move_dir){
        $move_dir = (sanitize_text_field($move_dir) == 'next') ? true : false;
        $config_data = $this->get_config();
        $dates_month = $config_data['dates_month'];
        
        //get selected date in session
        $date_selected = $this->get_session_date_selected();

        //add 7 days/1 month or remove 7 days/1 month
        if($dates_month){
            if($move_dir){
                $date_week = date('Y-m-d', strtotime("+1 month", $date_selected));
            } else {
                $date_week = date('Y-m-d', strtotime("-1 month", $date_selected));
            }
        } else {
            if($move_dir){
                $date_week = date('Y-m-d', strtotime("+7 days", $date_selected));
            } else {
                $date_week = date('Y-m-d', strtotime("-7 days", $date_selected));
            }
        }
        $new_date = strtotime($date_week);

        //get the week day corresponding to the week day today but relative to the session date
        $new_week_day = strtotime($this->get_booking_wc_weekday_today($new_date));
        
        //update session data
        $newdate_saved = $this->save_session_date_selected($new_week_day);

        //return new calendar content
        $booking_wc_calendar_data = $this->get_booking_wc_calendar();

        return $booking_wc_calendar_data;
    }

}
?>