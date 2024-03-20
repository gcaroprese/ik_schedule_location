<?php
/*

Class Ik_Schedule_Services
Created: 20/11/2022
Update: 05/01/2023
Author: Gabriel Caroprese

*/

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

class Ik_Schedule_Services{

    private $db_table_services;
    public $qtyListing_services; 

    public function __construct() {

        global $wpdb;
        $this->db_table_services = $wpdb->prefix . "ik_sch_booking_services";
        $this->qtyListing_services = 30;

    }

    //Get Services table name
    public function get_table_name(){
        return $this->db_table_services;
    }

    //Get service by ID
    public function get_service($service_id = 0){
        $service_id = absint($service_id);
        
        if ( $service_id > 0){
            
            global $wpdb;
            $service_query = "SELECT * FROM ".$this->db_table_services." WHERE id = ".$service_id;
            $service = $wpdb->get_results($service_query);
    
            if (isset($service[0]->id)){ 
                return $service[0];
            }
        }
        
        return false;
        
    }

    //Get service by ID
    public function get_services_list(){
            
        global $wpdb;
        $services_query = "SELECT * FROM ".$this->db_table_services." ORDER BY name ASC";
        $services = $wpdb->get_results($services_query);

        if (isset($services[0]->id)){ 

            foreach( $services as $service ) {

                $service_list[] = array(
                    'id' => $service->id,
                    'cat_name' => $this->get_services_cat($service->cat_name),
                    'name' => $service->name,
                    'price' => $service->price,
                    'currency_id' => $service->currency_id
                );
            }

            return $service_list;

        } else {
            return false;
        }
            
    }

    //Get service for Location
    public function get_services_by_location($location_id = 0){
        $location_id = absint($location_id);
        
        if ( $location_id > 0){

            $location_data = new Ik_Schedule_Locations();
            
            $location = $location_data->get_location($location_id);

            if($location){

                $services_locations = $location->service_ids;

                if(is_serialized($services_locations)){

                        $services_locations = maybe_unserialize($services_locations);

                    if(is_array($services_locations)){

                        $counterService = 0;

                        foreach($services_locations as $service_location){

                            $service = $this->get_service(absint($service_location['service_id']));
                    
                            if ($service){ 
                                $service_available[$counterService]['id'] = $service->id;
                                $service_available[$counterService]['cat_name'] = $service->cat_name;
                                $service_available[$counterService]['name'] = $service->name;
                                $service_available[$counterService]['price'] = $service->price;
                                $service_available[$counterService]['currency_id'] = $service->currency_id;
                                $service_available[$counterService]['custom_price'] = $service_location['custom_price'];
                                $service_available[$counterService]['delivery_time'] = $service->delivery_time;
                                $service_available[$counterService]['delivery_time_full'] = $this->format_delivery_time($service->delivery_time);

                            }

                            $counterService = $counterService + 1;
                        }

                        if(isset($service_available)){
                            return $service_available;
                        }
                    }
                }
            }
        }
        
        return false;
        
    }

    //method to format delivery time text
    public function format_delivery_time($delivery_time){
        $delivery_time = intval($delivery_time);

        if($delivery_time > 0){
            if($delivery_time >= 60 && $delivery_time <= 120 && is_int($delivery_time/60)){
                $time_hours = $delivery_time/60;
                $delivery_time_full = $time_hours.__( ' hour', 'ik_schedule_location');
            } else if($delivery_time > 120 && is_int($delivery_time/60)){
                $time_hours = $delivery_time/60;
                $delivery_time_full = $time_hours.__( ' hours', 'ik_schedule_location');
            } else if($delivery_time < 2){
                $delivery_time_full = $delivery_time.__( ' minute', 'ik_schedule_location');
            } else {
                $delivery_time_full = $delivery_time.__( ' minutes', 'ik_schedule_location');
            }
        }

        return $delivery_time_full;
    }

    //method to get valid service for location by id
    public function get_service_id_by_location_id($location_id = 0, $service_id = 0){
        $location_id = absint($location_id);
        $service_id = absint($service_id);

        //check if service exists for location
        $location_services = $this->get_services_by_location($location_id);

        foreach($location_services as $location_service){
            if($location_service['id'] == $service_id){
                return $location_service;
            }
        }

        return false;
    }

    //Get all services data
    public function get_services($paging = 1, $orderby = 'id', $orderDir = 'DESC'){

        if ($paging < 1 || !is_int($paging)){
            $paging = 1;
        }
        
        if ($orderDir != 'DESC'){
            $orderDir = 'ASC';
        }
        
        if ($orderby != 'id'){
            if ($orderby == 'name'){
                $orderBy = 'name';
            } else if ($orderby == 'price'){
                $orderBy = 'price';
            } else {
                $orderBy = 'cat_name';
            }
        } else {
            $orderBy = 'id';
        }
        
        if (isset($_GET['search'])){
            $search = sanitize_text_field($_GET['search']);
        } else {
            $search = NULL;
        }

        $offsetList = ($paging - 1) * $this->qtyListing_services;
        $order = 'ORDER BY '.$orderBy.' '.$orderDir;
        
    	$offset = ' LIMIT '.$this->qtyListing_services.' OFFSET '.$offsetList;

        global $wpdb;
        if ($search != NULL){ 
            //Search by lot number, product name, botanical name and country of origin
            $where = " WHERE name LIKE '%".$search."%' OR cat_name LIKE '%".$search."%'";
        } else {
            $where = "";
        }
        $services_query = "SELECT * FROM ".$this->db_table_services.$where." ".$order.$offset;

        $services_found = $wpdb->get_results($services_query);

        if (isset($services_found[0]->id)){ 

            $servicesAllQuery = "SELECT DISTINCT ".$this->db_table_services.".id FROM ".$this->db_table_services;       
    
            $servicesAll = $wpdb->get_results($servicesAllQuery);
            
            $countTotal = count($servicesAll);
            
            $services = new stdClass();
            $services->listing = $services_found;
            $services->total = $countTotal;


            return $services;
        }
        
        return false;
        
    }

    //Get all services categories
    public function get_services_cats(){
            
        $service_cats = get_option('ik_schedule_service_cats');

        if (isset($service_cats)){ 
            if (is_array($service_cats)){ 
                return $service_cats;
            }
        }
        
        return false;
    }

    //Get service category by id
    public function get_services_cat($cat_name){
        $cat_name = sanitize_text_field($cat_name);
        

        $cat_names = $this->get_services_cats();

        if (in_array($cat_name, $cat_names)) {
            return $cat_name;
        } else {
            return 'None';
        }
        
    }
    
    //Method to list service categories by location
    public function get_select_services_by_location_id($location_id = 0, $Selected = 'None', $is_select_array = false){
        $location_id = absint($location_id);
        $Selected = sanitize_text_field($Selected);
        $array_select_name = (rest_sanitize_boolean($is_select_array)) ? '[]' : '';
        //add delete option
        $array_select_name = (rest_sanitize_boolean($is_select_array)) ? '[]' : '';

        $options_data_list = '<div class="ik_sch_book_services_select_wrapper"><select name="service_ids'.$array_select_name.'" class="ik_sch_book_edit_field ik_sch_book_services_select">
        <option value="0">'.__( 'None', 'ik_schedule_location').'</option>';

        $options = $this->get_services_by_location($location_id);

        if($options){

            foreach( $options as $option ) {
                $select = ($Selected === $option['id']) ? 'selected' : '';
            
                $options_data_list .= '<option '.$select.' value="'.$option['id'].'">'.$option['cat_name'].' > '.$option['name'].'</option>';
            }
        }
        
        $options_data_list .= '</select><a class="ik_sch_book_delete_field" href="#"><span class="dashicons dashicons-trash"></span></a></div>';    
        
        return $options_data_list;
    }

    //Method to list service categories select options
    public function select_service_cats($Selected = 'None'){
        $Selected = sanitize_text_field($Selected);

        $options_data_list = '<select name="service_cat" class="ik_sch_book_currency_select">
        <option value="0">'.__( 'None', 'ik_schedule_location').'</option>';

        $options = $this->get_services_cats();

        if($options){

            foreach( $options as $option ) {
                $select = ($Selected === $option) ? 'selected' : '';
            
                $options_data_list .= '<option '.$select.' value="'.$option.'">'.$option.'</option>';
            }
        }
        
        $options_data_list .= '</select>';    
        
        return $options_data_list;
    }

    public function update(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

            if (isset($_POST['new_service'])){

                $new_service = (isset($_POST['new_service'])) ? sanitize_text_field($_POST['new_service']) : false;

                if($new_service == true && !empty($new_service) && $new_service != ' '){

                    $booking_data = new Ik_Schedule_Booking;

                    $service_cat = (isset($_POST['service_cat'])) ? $this->get_services_cat($_POST['service_cat']) : 'None';
                    $general_price = (isset($_POST['general_price'])) ? number_format( floatval($_POST['general_price']), 2, '.', ''  ) : 0;
                    $currency = (isset($_POST['currency'])) ? $booking_data->get_currency_details(absint($_POST['currency']))->id : 0;
                    $delivery_time = (isset($_POST['delivery_time'])) ? absint($_POST['delivery_time']) : 0;

                    $service_data  = array (
                        'cat_name'=> $service_cat,	
                        'name'=> $new_service,	
                        'price'=> $general_price,
                        'currency_id'=> $currency,
                        'delivery_time'=> $delivery_time,
                    );
                 
                    global $wpdb;
                    $rowResult = $wpdb->insert($this->db_table_services, $service_data);   
                    $service_id = $wpdb->insert_id;
                    
                    return $service_id;
     
                }

            }

            if (isset($_POST['new_service_cat'])){

                if(is_array($_POST['new_service_cat'])){

                    foreach($_POST['new_service_cat'] as $service_cat){

                        $new_service_cat = (isset($service_cat)) ? sanitize_text_field($service_cat) : false;

                        if($new_service_cat == true && !empty($new_service_cat) && $new_service_cat != ' '){
                        
                            $service_cats[] = $new_service_cat;
             
                        }
                        
                    }

                    if(isset($service_cats)){
                        
                        update_option('ik_schedule_service_cats', $service_cats);
                        
                        return true;
                    }

                }

                return false;
            }

            if (isset($_POST['service_id']) && isset($_POST['edit_service'])){

                $service_id = absint($_POST['service_id']);
                $service_name = (isset($_POST['edit_service'])) ? sanitize_text_field($_POST['edit_service']) : false;

                if($service_id == true && $service_name == true && !empty($service_name) && $service_name != ' '){

                    $booking_data = new Ik_Schedule_Booking;

                    $service_cat = (isset($_POST['service_cat'])) ? sanitize_text_field($_POST['service_cat']) : 0;
                    $general_price = (isset($_POST['general_price'])) ? number_format( floatval($_POST['general_price']), 2, '.', ''  ) : 0;
                    $currency = (isset($_POST['currency'])) ? $booking_data->get_currency_details(absint($_POST['currency']))->id : 0;
                    $delivery_time = (isset($_POST['delivery_time'])) ? absint($_POST['delivery_time']) : 0;

                    $service_data  = array (
                        'cat_name'=> $service_cat,	
                        'name'=> $service_name,	
                        'price'=> $general_price,
                        'currency_id'=> $currency,
                        'delivery_time'=> $delivery_time,
                    );

                    global $wpdb;
                    $where = [ 'id' => $service_id ];
                    $rowResult = $wpdb->update($this->db_table_services, $service_data, $where);   
                    
                    return $service_id;
     
                }
            }

            return false;

        }
    }

    //Function to return records on backend
    public function show_services_backend($qty = NULL) {

        $qty = ($qty == NULL && absint($qty) > 0) ? $this->qtyListing : absint($qty);
        
        $data_list='';

        //qty data
        $qty = (absint($qty) > 0) ? absint($qty) : 50;
        
        // Page Number
        $paging = (isset($_GET["listing"])) ? intval($_GET["listing"]) : 1;
        $paging = ($paging > 0) ? $paging : 1;


    	// I get the value for order of listing
        $orderDir = (isset($_GET['orderdir'])) ? sanitize_text_field($_GET['orderdir']) : 'DESC';
        $orderDir = (strtoupper($orderDir) != 'DESC') ? 'ASC' : 'DESC';
        $orderdir = ($orderDir != 'DESC') ? 'asc' : 'desc';
        $orderClass= ($orderDir == 'DESC') ? 'sorted desc' : 'sorted';

        $empty = '';
        $idClass = $empty;
        $cat_nameClass = $empty;
        $nameClass = $empty;
        $priceClass = $empty;
        
        if (isset($_GET['orderby'])){
            $orderby = $_GET['orderby'];
        } else {
            $orderby = 'id';
        }

        if ($orderby != 'id'){
            if ($orderby == 'cat_name'){
                $cat_nameClass = $orderClass;
            } else if ($orderby == 'name'){
                $nameClass = $orderClass;
            } else if ($orderby == 'price'){
                $priceClass = $orderClass;
            } else {
                $idClass = $orderClass;
            }
        } else {
            $idClass = $orderClass;
        }

        //I get the data
    	$services = $this->get_services($paging, $orderby, $orderDir);

        if (isset($_GET['search'])){
            $search = sanitize_text_field($_GET['search']);
        } else {
            $search = NULL;
        }
    	
    	$url_unfiltered = get_site_url().'/wp-admin/admin.php?page='.IK_SCH_MENU_VAL_SERVICES;

    	if ($services !== false){
    	    
    	    $services_data =$services->listing;
            $total = $services->total;
    	   
    	    //I check the page number
    	    if ($paging > 1){
    	        $pagen = $paging - 1;
    	    } else {
    	        $pagen = 0;
    	    }
    	    
    	    $url_getted = $url_unfiltered.'&listing='.$pagen;
		    
            $table_heads = '    						
            <tr>
                <th>
                    <input type="checkbox" class="select_all" />
                </th>
                <th class="orderitem '.$idClass.'" order="id">
                    <div class="sorting"><span>'.__( 'ID', 'ik_schedule_location').'</span><span class="sorting-indicator"></span></div>
                </th>
                <th class="orderitem '.$nameClass.'" order="name">
                    <div class="sorting"><span>'.__( 'Name', 'ik_schedule_location').'</span><span class="sorting-indicator"></span></div>
                </th>
                <th class="orderitem '.$cat_nameClass.'" order="cat_name">
                    <div class="sorting"><span>'.__( 'Category', 'ik_schedule_location').'</span><span class="sorting-indicator"></span></div>
                </th>
                <th class="orderitem '.$priceClass.'" order="price">
                    <div class="sorting"><span>'.__( 'Price', 'ik_schedule_location').'</span><span class="sorting-indicator"></span></div>
                </th>
                <th>
                    <button class="ik_sch_book_button_delete_bulk button action">'.__( 'Delete', 'ik_schedule_location').'</button>
                </th>
            </tr>';
            $searchBar = '<p class="search-box">
                <label class="screen-reader-text" for="tag-search-input">'.__( 'Search:', 'ik_schedule_location').'</label>
                <input type="search" id="tag-search-input" name="search" value="'.$search.'">
                <input type="submit" id="searchbutton" class="button" value="'.__( 'Search', 'ik_schedule_location').'">
            </p>';
            if ($search != NULL){ 
                $show_all = '<p class="show-all-button"><a href="'.$url_unfiltered.'" class="button button-primary">'.__( 'Show All', 'ik_talent_admin' ).'</a></p>';
            } else {
                $show_all = '';
            }
		    $table_head = $show_all.'
		        <p class="ik_sch_book_data">
                    <span>'.__( 'Total', 'ik_schedule_location').':</span>
                    <span class="ik_sch_book_data_total"> '.$total.'</span>
    			</p>'.$searchBar.'
    			<table type="services">
    					<thead>
                        '.$table_heads.'
    					</thead>
    				<tbody>';
    				
    		$table_foot = '</tbody>
    				    <tfoot>
                        '.$table_heads.'
    					</tfoot>
    					<tbody>
    				</table>';
		    
	
			$data_list = $table_head;

            $booking_data = new Ik_Schedule_Booking;
    		
    	    foreach ($services_data as $service){
    	            
    				$data_list.= '
    				<tr iddata="'.$service->id.'">
                        <td>
                            <input type="checkbox" class="select_data" />
                        </td>
    					<td type="id">'.$service->id.'</td>
    					<td type="name">'.$service->name.'</td>
    					<td type="cat_name">'.$this->get_services_cat($service->cat_name).'</td>
    					<td type="price">
                            <span class="data_value">'.$service->price.'</span>
                            <span class="data_currency">'.$booking_data->get_currency_details($service->currency_id)->name.'</span>
                        </td>
                        <td iddata="'.$service->id.'">
                            <a href="'.$url_unfiltered.'&edit_service='.$service->id.'" class="ik_sch_book_button_edit button action">'.__( 'Edit', 'ik_schedule_location').'</a>
                            <button class="ik_sch_book_button_delete button action">'.__( 'Delete', 'ik_schedule_location').'</button>
                        </td>
                    </tr>';			
    	    }
    	    
			$data_list.= $table_foot;
				
			if ($paging > 1){
                $listcalcpages = $total / $this->qtyListing_services;
                $total_pages = intval($listcalcpages);
                
                if (is_float($listcalcpages)){
                    $total_pages = $total_pages + 1;
                }
                
                if ($total > $this->qtyListing  && $paging <= $total_pages){
                    $data_list.= '<div class="ik_sch_book_pages">';
                    
                    
                    //If there are a lot of pages
                    if ($total_pages > 11){
                        $almostlastpage1 = $total_pages - 1;
                        $almostlastpage2 = $total_pages - 2;
                        $halfpages1 = intval($total_pages/2);
                        $halfpages2 = intval($total_pages/2)-1;
                        
                        $listing_limit = array('1', '2', $paging, $halfpages2, $halfpages1, $almostlastpage2, $almostlastpage1, $total_pages);
                        
                        $pages_limited = true;
                    } else{
                        $listing_limit[0] = false;
                        $pages_limited = false;
                    }
                    $arrowprevious = $paging - 1;
                    $arrownext = $paging + 1;
                    if ($arrowprevious > 1){
                        $data_list.= '<a href="'.$url_getted.'&listing='.$arrowprevious.'"><</a>';
                    }
                    for ($i = 1; $i <= $total_pages; $i++) {
                        $showpage = true;
                        
                        if ($pages_limited == true && !in_array($i, $listing_limit)){
                            $nextpage = $paging+1;
                            $beforepage = $paging - 1;
                            if ($paging != $i && $nextpage != $i && $beforepage != $i){
                                $showpage = false;
                            }
                        }
                        
                        if ($showpage == true){
                            if ($paging == $i){
                                $selectedPageN = 'class="actual_page"';
                            } else {
                                $selectedPageN = "";
                            }
                            
                            $data_list.= '<a '.$selectedPageN.' href="'.$url_getted.'&listing='.$i.'">'.$i.'</a>';
                            
                        }
                        
                    }
                    if ($arrownext < $total_pages){
                        $data_list.= '<a href="'.$url_getted.'&listing='.$arrownext.'">></a>';
                    }
                    $data_list.= '</div>';
            	}
			}

    	    return $data_list;
    	} else {
            if ($search != NULL){
                $listing = $searchBar.'
                <div id="ik_sch_book_existing">
                    <p>'.__( 'Results not found', 'ik_schedule_location').'</p>
                    <p class="show-all-button"><a href="'.$url_unfiltered.'" class="button button-primary">'.__( 'Show All', 'ik_schedule_location').'</a></p>
                </div>';

                return $listing;
            }
        }
        
        return false;
    
    }

    //delete service by ID
    public function delete_service($service_id){
        $service_id = absint($service_id);
        global $wpdb;
        $wpdb->delete( $this->db_table_services , array( 'id' => $service_id ) );
        
        return true;
    }
}

?>