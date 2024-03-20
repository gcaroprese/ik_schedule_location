<?php
/*

Class Ik_Schedule_Locations
Created: 20/11/2022
Update: 05/01/2023
Author: Gabriel Caroprese

*/

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

class Ik_Schedule_Locations{

    private $db_table_locations;
    public $location_admin_url;

    public function __construct() {

        global $wpdb;
        $this->db_table_locations = $wpdb->prefix . "ik_sch_booking_locations";
        $this->location_admin_url = get_admin_url().'admin.php?page='.IK_SCH_MENU_VAL_LOCATIONS;
    }

    //Get Locations table name
    public function get_table_name(){
        return $this->db_table_locations;
    }
    
    //Create request
    public function create_request($args = array()){

        if(isset($args['branches']) && isset($args['name']) && isset($args['lastname']) && isset($args['email'])
        && isset($args['phone']) && isset($args['services']) && isset($args['datetime'])){

            $location_name = sanitize_text_field($location_name);
            $customer_id = floatval($customer_id);
        
            if ($customer_id != 0 && $location_name != NULL && $location_name != ''){
                
                $location  = array (
                                'name'=> $location_name,	
                                'conversion'=> $customer_id,	
                                'main_location'=> 0,
                );
                
                global $wpdb;
                $rowResult = $wpdb->insert($this->db_table_location, $location);   
                $location_id = $wpdb->insert_id;
                
                return $location_id;
          
            }

        }
        
        return false;
    }

    //function to add new location
    public function add_location($location_name = ''){
        $location_name = sanitize_text_field($location_name);
        $location_name = str_replace('\\', '', $location_name);

        if($location_name != ''){

            //first I make sure is not repetead
            global $wpdb;
            $query_location_repeated = "SELECT * FROM ".$this->db_table_locations." WHERE name LIKE '".$location_name."'";
            $repeated_location = $wpdb->get_results($query_location_repeated);

            // if repeated is false location is created
            if (!isset($repeated_location[0]->id)){

                    global $wpdb;
                    $data_location  = array (
                        'name' => $location_name,
                    );

                    $rowResult = $wpdb->insert($this->db_table_locations, $data_location , $format = NULL);
                    $new_location_id = $wpdb->insert_id;

                    return $new_location_id;
            }
        }
        
        return false;
    }

    //Get location data by ID
    public function get_location($location_id = 0){
        $location_id = absint($location_id);
        
        if ( $location_id > 0){
            
            global $wpdb;
            $location_query = "SELECT * FROM ".$this->db_table_locations." WHERE id = ".$location_id;
            $location = $wpdb->get_results($location_query);
    
            if (isset($location[0]->id)){ 
                return $location[0];
            }
        }
        
        return false;
        
    }

    //Get location name by ID
    public function get_location_name($location_id = 0){
        $location_id = absint($location_id);
        
        if ( $location_id > 0){
            
            global $wpdb;
            $location_query = "SELECT name FROM ".$this->db_table_locations." WHERE id = ".$location_id;
            $location = $wpdb->get_results($location_query);
    
            if (isset($location[0]->name)){ 
                return $location[0]->name;
            }
        }
        
        return false;
        
    }

    //Get all locations data
    public function get_locations(){
                    
        global $wpdb;
        $locations_query = "SELECT * FROM ".$this->db_table_locations." ORDER BY name ASC";
        $locations = $wpdb->get_results($locations_query);

        if (isset($locations[0]->id)){ 
            return $locations;
        }
        
        return false;
        
    }

    //Count the quantity of location records
    public function qty_location_records(){
    
        global $wpdb;
        $querylocation = "SELECT * FROM ".$this->db_table_locations;
        $location_records = $wpdb->get_results($querylocation);

        if (isset($location_records[0]->id)){ 
            
            $location_count = count($location_records);
            return $location_count;
            
        } else {
            return false;
        }
    }

    //List locations
    private function get_location_list($qty = 30){
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
        
        
        // Chechking order
        if (isset($_GET["orderby"]) && isset($_GET["orderdir"])){
            $orderby = sanitize_text_field($_GET["orderby"]);
            $orderdir = sanitize_text_field($_GET["orderdir"]);  
            if (strtoupper($orderdir) != 'DESC'){
                $orderDir= ' ASC';
                $orderClass= 'sorted';
            } else {
                $orderDir = ' DESC';
                $orderClass= 'sorted desc';
            }
        } else {
            $orderby = 'id';
            $orderDir = 'ASC';
            $orderClass= 'sorted';
        } 
        if (is_int($offset)){
            $offsetList = ' LIMIT '.$qty.' OFFSET '.$offset;
        } else {
            $offsetList = ' LIMIT '.absint($qty);
        }
        
        //Values to order filters CSS classes
        $empty = '';
        $idClass = $empty;
        $nameClass = $empty;
    
        
        if ($orderby != 'id'){	
            if ($orderby == 'name'){
                $orderQuery = ' ORDER BY '.$this->db_table_locations.'.name '.$orderDir;
                $nameClass = $orderClass;
            } else {
                $orderQuery = ' ORDER BY '.$this->db_table_locations.'.id '.$orderDir;
                $idClass = $orderClass;
            }
        } else {
            $orderQuery = ' ORDER BY '.$this->db_table_locations.'.id '.$orderDir;
            $idClass = $orderClass;
        }

        $classData = array(
            'id' => $idClass,
            'name' => $nameClass,
        );

        if ($search != NULL){ 
            //Search by lot number, product name, botanical name and country of origin
            $where = " WHERE ".$this->db_table_locations.".name LIKE '%".$search."%'";
        } else {
            $where = '';
            $search = '';
        }

        $groupby = (isset($groupby)) ? $groupby : " GROUP BY ".$this->db_table_locations.".id ";

        global $wpdb;

        $query = "SELECT * FROM ".$this->db_table_locations." ".$where.$groupby.$orderQuery.$offsetList;

        $locations = $wpdb->get_results($query);
        $locations_data = array(
            'data' => $locations,
            'class' => $classData,
            'search_value' => $search,        
        );

        return $locations_data;

    }   
    
    private function paginator($qtyToList){
        $list_datas_all = $this->qty_location_records();
        $data_dataSubstr = $list_datas_all / $qtyToList;
        $total_pages = intval($data_dataSubstr);
            
            if (is_float($data_dataSubstr)){
                $total_pages = $total_pages + 1;
            }
        
        if ($list_datas_all > $qtyToList){
            
            if ($page <= $total_pages){
                echo '<div class="ik_sch_book_pages">';
                
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
                        if ($page== $i){
                            $PageNActual = 'actual_page';
                        } else {
                            $PageNActual = "";
                        }
                        echo '<a class="ik_listar_page_data '.$PageNActual.'" href="'.$this->location_admin_url.'&list='.$i.'">'.$i.'</a>';
                    }
                }
                echo '</div>';
            }
        } 	        
    }

    //Method to show list of locations for backend
    public function get_list_locations_wrapper_backend($qty = 30){

        $qty = absint($qty);

        $locations_data = $this->get_location_list($qty);
        $locations = $locations_data['data'];;
        $search = $locations_data['search_value'];;

        //classes for columns that are filtered
        $classData = $locations_data['class'];

        $idClass = $classData['id'];
        $nameClass = $classData['name'];

        $searchBar = '<p class="search-box">
                <label class="screen-reader-text" for="tag-search-input">'.__( 'Search', 'ik_schedule_location').':</label>
                <input type="search" id="tag-search-input" name="search" value="'.$search.'">
                <input type="submit" id="searchbutton_location" class="button" value="'.__( 'Search', 'ik_schedule_location').'">
            </p>';
            
        // If data exists
        if (isset($locations[0]->id)){

            $columnsheading = '<tr>
                <th><input type="checkbox" class="select_all" /></th>
                <th order="id" class="worder '.$idClass.'">'.__( 'ID', 'ik_schedule_location').' <span class="sorting-indicator"></span></th>
                <th order="name" class="worder '.$nameClass.'">'.__( 'Location', 'ik_schedule_location').' <span class="sorting-indicator"></span></th>
                <th>
                    <button class="ik_sch_book_button_delete button action">'.__( 'Delete', 'ik_schedule_location').'</button></td>
                </th>
            </tr>';

            $listing = '
            <div class="tablenav-pages">'.__( 'Total', 'ik_schedule_location').': '.$this->qty_location_records().' - '.__( 'Showing', 'ik_schedule_location').': '.count($locations).'</div>'.$searchBar;

            if ($search != NULL){
                $listing .= '<p class="show-all-button"><a href="'.$this->location_admin_url.'" class="button button-primary">'.__( 'Show All', 'ik_schedule_location').'</a></p>';
            }

            $listing .= '<table id="ik_sch_book_existing">
                <thead>
                    '.$columnsheading.'
                </thead>
                <tbody>';
                foreach ($locations as $location){
                                        
                    $listing .= '
                        <tr iddata="'.$location->id.'">
                            <td><input type="checkbox" class="select_data" /></td>
                            <td class="ik_sch_book_id">'.$location->id.'</td>
                            <td class="ik_sch_book_name">'.$location->name.'</td>
                            <td iddata="'.$location->id.'">
                                <a href="'.$this->location_admin_url.'&location_id='.$location->id.'" class="ik_sch_book_button_edit_location button action">'.__( 'Edit', 'ik_schedule_location').'</a>
                                <button class="ik_sch_book_button_delete_location button action">'.__( 'Delete', 'ik_schedule_location').'</button></td>
                        </tr>';
                    
                }
                $listing .= '
                </tbody>
                <tfoot>
                    '.$columnsheading.'
                </tfoot>
                <tbody>
            </table>';

            $listing .= $this->paginator($qty);
            
            return $listing;
            
        } else {
            if ($search != NULL){
                $listing = $searchBar.'
            <div id="ik_sch_book_existing">
                <p>'.__( 'Results not found', 'ik_schedule_location').'</p>
                <p class="show-all-button"><a href="'.$this->location_admin_url.'" class="button button-primary">Show All</a></p>
            </div>';
                return $listing;
            }
        }
        
        return false;
    }    

    //delete location by ID
    public function delete_location($location_id){
        $location_id = absint($location_id);
        global $wpdb;
        $wpdb->delete( $this->db_table_locations , array( 'id' => $location_id ) );
        
        return true;
    }

    //return locations selector
    public function get_location_select($selected_value = 0, $array_select = false){
        $array_select = ($array_select != false) ? '[]' : '';
        $selected_value = intval($selected_value);

        $location_select_options = '<select name="branch'.$array_select.'" id="branch_select">
        <option id="option-selected" class="nocexisting_locationlass" value="0">'. __( 'Select Location', 'ik_schedule_location').' </option>';
        $existing_locations = $this->get_locations();
        if ($existing_locations){
            foreach($existing_locations as $existing_location){
                $selected = ($selected_value == $existing_location->id) ? 'selected' : '';
                $location_select_options .= '<option '.$selected.' value="'.$existing_location->id.'">'.$existing_location->name.'</option>';
            }
        }
        $location_select_options .= '</select>';

        return $location_select_options;
    }
    
    //update location data
    public function update_location($location_id){

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ik_sch_book_user_permissions()){
        
            if (isset($_POST['location_name'])){

                $location_id = absint($location_id);
                $location_name = sanitize_text_field($_POST['location_name']);
                
                $location = $this->get_location($location_id);

                if($location){
                    
                    $location_data  = array (
                        'name'=> $location_name,	
                    );
                    
                    global $wpdb;
                    $where = [ 'id' => $location_id ];
                    $rowResult = $wpdb->update($this->db_table_locations, $location_data , $where); 
                }      
                            
            }

            if (isset($_POST['location_address'])){

                $location_id = absint($location_id);
                $location_address = sanitize_textarea_field($_POST['location_address']);
                
                $location = $this->get_location($location_id);

                if($location){
                    
                    $location_data  = array (
                        'address'=> $location_address,	
                    );
                    
                    global $wpdb;
                    $where = [ 'id' => $location_id ];
                    $rowResult = $wpdb->update($this->db_table_locations, $location_data , $where);  
                } 
            }

            if (isset($_POST['location_link'])){

                $location_id = absint($location_id);
                $location_link = sanitize_url($_POST['location_link']);
                
                $location = $this->get_location($location_id);

                if($location){
                    
                    $location_data  = array (
                        'map_link'=> $location_link,	
                    );
                    
                    global $wpdb;
                    $where = [ 'id' => $location_id ];
                    $rowResult = $wpdb->update($this->db_table_locations, $location_data , $where);  
                } 

            }

            if (isset($_POST['location_embed_src'])){

                $location_id = absint($location_id);
                $location_embed_src = sanitize_url($_POST['location_embed_src']);
                
                $location = $this->get_location($location_id);

                if($location){
                    
                    $location_data  = array (
                        'map_embed_src'=> $location_embed_src,	
                    );
                    
                    global $wpdb;
                    $where = [ 'id' => $location_id ];
                    $rowResult = $wpdb->update($this->db_table_locations, $location_data , $where);  
                } 

            }
        }

		return;
        
    }

    //update location data
    public function update_services($location_id = 0, $service_ids = NULL){
        $location_id = absint($location_id);

        if ($location_id > 0 && is_array($service_ids)){

            //Make sure location exists
            $location = $this->get_location($location_id);

            if($location){

                $service_ids_serialized = maybe_serialize($service_ids);

                $location_data  = array (
                    'service_ids'=> $service_ids_serialized,	
                );
                
                global $wpdb;
                $where = [ 'id' => $location_id ];
                $rowResult = $wpdb->update($this->db_table_locations, $location_data , $where);       
                
                return true;
        
            }
        }

        return false;
        
    }

    //method to get opentimes
    public function get_open_times($location_id = 0, $show_only_today = false){
        $location_id = absint($location_id);
        $location = $this->get_location($location_id);
        $show_only_today = ($show_only_today == true) ? true : false;

        if($location){
            if(is_serialized($location->availability)){

                $opentimes = maybe_unserialize($location->availability);
    
                if(isset($opentimes['day1'])){
    
                    foreach($opentimes as $dayKey  => $opentime){
    
                        switch ($dayKey) {
                            case 'day1':
                                $dayAbrev = __( 'Mon', 'ik_schedule_location');
                                break;
                            case 'day2':
                                $dayAbrev = __( 'Tues', 'ik_schedule_location');
                                break;
                            case 'day3':
                                $dayAbrev = __( 'Wed', 'ik_schedule_location');
                                break;
                            case 'day4':
                                $dayAbrev = __( 'Thurs', 'ik_schedule_location');
                                break;
                            case 'day5':
                                $dayAbrev = __( 'Fri', 'ik_schedule_location');
                                break;
                            case 'day6':
                                $dayAbrev = __( 'Sat', 'ik_schedule_location');
                                break;
                            case 'day7':
                                $dayAbrev = __( 'Sun', 'ik_schedule_location');
                                break;
                            default:
                                $dayAbrev = 'error';
                                break;
                        }

                        //Init class to use method to convert times
                        $availability_data = new Ik_Schedule_Available_Days();
    
                        if($dayAbrev != 'error'){
                            //I get the times for every day
                            if($opentime['alwaysclose'] || $opentime['opentime'][0] == $opentime['closetime'][0]){
                                $times = __( 'Closed', 'ik_schedule_location');
                                $openingHours[$dayKey] = array(
                                        'day_name' => $dayAbrev,
                                        'times' => $times                                                           
                                    );
                            } else {
                                $times = '';

                                foreach($opentime['opentime'] as $keyHours => $hours){
                                    $times .= '<span class="ik_sch_book_times_lines">'.$availability_data->convert_time_format($hours).' - '.$availability_data->convert_time_format($opentime['closetime'][$keyHours]).' '. __( 'Hr', 'ik_schedule_location').'</br>';
                                }
                                $times = substr($times, 0, -5).'</span>';
                                $openingHours[$dayKey] = array(
                                    'day_name' => $dayAbrev,
                                    'times' => $times
                                );
                            }
                        }
                    }

                    //if only show todays opening hours
                    if($show_only_today){

                        $day_number = 'day'.date('N');

                        $workingHours = '<div class="ik_sch_workingdays_details">
                                            <div class="ik_sch_workingdays_details_title">'.__( 'Today', 'ik_schedule_location').':  <span class="ik_sch_workinghours_details">' . $openingHours[$day_number]['times'].'</span></div>
                                        </div>';
                        return $workingHours;
                    } else {
                        //if show all week opening hours
                        if (isset($openingHours)) {

                            //I check if some days have the same opening hours so I create groups
                            $lastOpeninHours = '';
                            $groupId = 0;
                            $groupCounter = 0;
                            foreach ($openingHours as $day => $openDay){
                                if($lastOpeninHours != $openDay['times'] && $groupCounter != 0){
                                    $groupId = $groupId + 1;
                                }
                                if($openDay['times'] == $openingHours['day1']['times']){
                                    $groupDays[$groupId]['days'][] = $day;
                                    $groupDays[$groupId]['time'] = $openingHours['day1']['times'];
                                } else if($openDay['times'] == $openingHours['day2']['times']){
                                    $groupDays[$groupId]['days'][] = $day;
                                    $groupDays[$groupId]['time'] = $openingHours['day2']['times'];
                                } else if($openDay['times'] == $openingHours['day3']['times']){
                                    $groupDays[$groupId]['days'][] = $day;
                                    $groupDays[$groupId]['time'] = $openingHours['day3']['times'];
                                } else if($openDay['times'] == $openingHours['day4']['times']){
                                    $groupDays[$groupId]['days'][] = $day;
                                    $groupDays[$groupId]['time'] = $openingHours['day4']['times'];
                                } else if($openDay['times'] == $openingHours['day5']['times']){
                                    $groupDays[$groupId]['days'][] = $day;
                                    $groupDays[$groupId]['time'] = $openingHours['day5']['times'];
                                } else if($openDay['times'] == $openingHours['day6']['times']){
                                    $groupDays[$groupId]['days'][] = $day;
                                    $groupDays[$groupId]['time'] = $openingHours['day6']['times'];
                                } else if($openDay['times'] == $openingHours['day7']['times']){
                                    $groupDays[$groupId]['days'][] = $day;
                                    $groupDays[$groupId]['time'] = $openingHours['day7']['times'];
                                } else {
                                    $groupDays[$groupId]['days'][] = $day;
                                    $groupDays[$groupId]['time'] = $openingHours[$day]['times'];
                                }
                                $lastOpeninHours = $openingHours[$day]['times'];
                                $groupCounter = $groupCounter + 1;
                            }
                        
                            // return the grouped working hours
                            if(isset($groupDays)){
                                $workingHours = '<div class="ik_sch_workingdays_details">
                                    <div class="ik_sch_workingdays_details_title">'.__( 'Opening Hours', 'ik_schedule_location').'</div>';
    
                                foreach($groupDays as $groupkey => $groupDay){
                                    if($groupDay['days'] == in_array('day1', $groupDay['days']) && 
                                    $groupDay['days'] == in_array('day2', $groupDay['days']) && 
                                    $groupDay['days'] == in_array('day3', $groupDay['days']) && 
                                    $groupDay['days'] == in_array('day4', $groupDay['days']) &&
                                    $groupDay['days'] == in_array('day5', $groupDay['days']) && 
                                    $groupDay['days'] == in_array('day6', $groupDay['days']) && 
                                    $groupDay['days'] == in_array('day7', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day1']['day_name'].' - '.$openingHours['day7']['day_name'];  
                                    } else if($groupDay['days'] == in_array('day2', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day3', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day4', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day5', $groupDay['days']) &&
                                        $groupDay['days'] == in_array('day6', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day7', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day2']['day_name'].' - '.$openingHours['day7']['day_name'];  
                                    } else if($groupDay['days'] == in_array('day1', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day2', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day3', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day4', $groupDay['days']) &&
                                        $groupDay['days'] == in_array('day5', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day1']['day_name'].' - '.$openingHours['day5']['day_name'];
                                    } else if($groupDay['days'] == in_array('day1', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day2', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day3', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day4', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day1']['day_name'].' - '.$openingHours['day4']['day_name'];
                                    } else if($groupDay['days'] == in_array('day1', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day2', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day3', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day1']['day_name'].' - '.$openingHours['day3']['day_name'];
                                    }  else if($groupDay['days'] == in_array('day2', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day3', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day3', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day1']['day_name'].' - '.$openingHours['day3']['day_name'];
                                    } else if($groupDay['days'] == in_array('day1', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day2', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day1']['day_name'].' - '.$openingHours['day2']['day_name'];
                                    } else if($groupDay['days'] == in_array('day6', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day7', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day6']['day_name'].' - '.$openingHours['day7']['day_name'];
                                    }  else if($groupDay['days'] == in_array('day2', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day3', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day4', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day5', $groupDay['days']) &&
                                        $groupDay['days'] == in_array('day6', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day2']['day_name'].' - '.$openingHours['day6']['day_name'];  
                                    } else if($groupDay['days'] == in_array('day2', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day3', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day4', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day5', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day2']['day_name'].' - '.$openingHours['day5']['day_name'];  
                                    } else if($groupDay['days'] == in_array('day3', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day4', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day5', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day6', $groupDay['days']) &&
                                        $groupDay['days'] == in_array('day7', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day3']['day_name'].' - '.$openingHours['day7']['day_name'];  
                                    } else if($groupDay['days'] == in_array('day3', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day4', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day5', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day6', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day2']['day_name'].' - '.$openingHours['day6']['day_name'];  
                                    } else if($groupDay['days'] == in_array('day3', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day4', $groupDay['days']) && 
                                        $groupDay['days'] == in_array('day5', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day2']['day_name'].' - '.$openingHours['day5']['day_name'];  
                                    }  else if($groupDay['days'] == in_array('day1', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day1']['day_name'];
                                    }  else if($groupDay['days'] == in_array('day2', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day2']['day_name'];
                                    }  else if($groupDay['days'] == in_array('day3', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day3']['day_name'];
                                    }  else if($groupDay['days'] == in_array('day4', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day4']['day_name'];
                                    }  else if($groupDay['days'] == in_array('day5', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day5']['day_name'];
                                    }  else if($groupDay['days'] == in_array('day6', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day6']['day_name'];
                                    }  else if($groupDay['days'] == in_array('day7', $groupDay['days'])){
                                        $dataTimes[$groupkey]['days'] = $openingHours['day7']['day_name'];
                                    }
                                    
                                    $dataTimes[$groupkey]['hours'] = $groupDays[$groupkey]['time'];
                                }
                                
                                foreach($dataTimes as $dataTime){
                                    $workingHours .= '<div class="ik_sch_working_openingtimes_details">
                                        <span class="ik_sch_workingday_details">' . $dataTime['days'] . ': </span>&nbsp;<span class="ik_sch_workinghours_details"> ' . $dataTime['hours'] . '</span>
                                    </div>';
                                }
                                $workingHours .= '</div>';
                                return $workingHours;
                            }
                        }
                    }
                }
            }
    
        }
        return false;
    }

    //method to return a select of the existing locations
    public function get_locations_select($args = array(
        'name'      => 'location_id', 
        'class'     => 'ik_sch_book_location_select', 
        'id_element'=> 'ik_sch_book_location_select'), 
        $selected_id = 0
        ){

        $name = ($args['name'] != 'location_id') ? sanitize_text_field($args['name']) : $args['name'];
        $id_element = ($args['id_element'] != 'ik_sch_book_location_select') ? sanitize_text_field($args['id_element']) : $args['id_element'];
        $class = ($args['class'] != 'ik_sch_book_location_select') ? sanitize_text_field($args['class']) : $args['class'];

        $Selected = (isset($_GET['location_id'])) ? intval($_GET['location_id']) : intval($selected_id);

        $options_data_list = '<select name="'. $name.'" id="'.$id_element.'" class="'.$class.'">
        <option value="0">'.__( 'Show All', 'ik_schedule_location').'</option>';

        $options = $this->get_locations();

        if($options){

            foreach( $options as $option ) {
                $select = ($Selected === intval($option->id)) ? 'selected' : '';
            
                $options_data_list .= '<option '.$Selected.' '.$select.' value="'.$option->id.'">'.$option->name.'</option>';
            }
        }
        
        $options_data_list .= '</select>';    
        
        return $options_data_list;
    }

}

?>