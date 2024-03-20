<?php
/*

Class Ik_Schedule_Staff
Created: 02/11/2023
Update: 10/11/2023
Author: Gabriel Caroprese

*/

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

class Ik_Schedule_Staff{

    private $db_table_staff;
    public $staff_admin_url;

    public function __construct() {

        global $wpdb;
        $this->db_table_staff = $wpdb->prefix . "ik_sch_booking_staff";
        $this->staff_admin_url = get_admin_url().'admin.php?page='.IK_SCH_MENU_VAL_STAFF;
    }

    //Get Staff table name
    public function get_table_name(){
        return $this->db_table_staff;
    }

    //Get Staff admin URL
    public function get_admin_url(){
        return $this->staff_admin_url;
    }
    
    //Create staff
    public function create($args = array()){

        $staff_member_name = isset($args['display_name']) ? sanitize_text_field($_POST['new_display_name']) : '';
        $staff_member_name = str_replace('\\', '', $staff_member_name);

        //make sure name's not empty
        if ((trim($staff_member_name)) !== '') {     

            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $location_id = isset($_POST['branch']) ? intval($_POST['branch']) : 0;

            $staff = array(
                'display_name'  => $staff_member_name,
                'user_id'       => $user_id,
                'location_id'   => $location_id,
                'added_datetime'=> current_time( 'mysql' ),
                'edit_datetime' => current_time( 'mysql' ),
            );
                
            global $wpdb;
            $rowResult = $wpdb->insert($this->db_table_staff, $staff);   
            $staff_id = $wpdb->insert_id;
            
            return $staff_id;

        }
        
        return false;
    }

    //function to edit staff
    public function edit($args){

        $staff = isset($args['id']) ? get_by_id($args['id']) : false;
        $staff_member_name = isset($args['display_name']) ? sanitize_text_field($_POST['new_display_name']) : '';
        $staff_member_name = str_replace('\\', '', $staff_member_name);

        //make sure name's not empty
        if ((trim($staff_member_name)) !== '' && $staff) {     

            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $location_id = isset($_POST['branch']) ? intval($_POST['branch']) : 0;

            $staff = array(
                'display_name'  => $staff_member_name,
                'user_id'       => $user_id,
                'location_id'   => $location_id,
                'edit_datetime' => current_time( 'mysql' ),
            );

            global $wpdb;
            $where = [ 'id' => $staff->id ];
            $rowResult = $wpdb->update($this->db_table_staff, $staff , $where); 
            
            return $staff->id;

        }
        
        return false;
    }

    //Get staff data by ID
    public function get_by_id($staff_id = 0){
        $staff_id = absint($staff_id);
        
        if ( $staff_id > 0){
            
            global $wpdb;
            $staff_query = "SELECT * FROM ".$this->db_table_staff." WHERE id = ".$staff_id;
            $staff = $wpdb->get_results($staff_query);
    
            if (isset($staff[0]->id)){ 
                return $staff[0];
            }
        }
        
        return false; 
    }

    //Get staff by args such as location id
    public function get($args = array()){
        $where = '';
        foreach ($args as $key => $arg){
            if ((trim($where)) !== '') {
                $where .= ' AND ';
            }
            $where .= (isset($args[$key])) ? $key.' ='.absint($args[$key]) : '';
        }
        
        //if where is not empty
        if ((trim($where)) !== '') {     
            
            global $wpdb;
            $staff_query = "SELECT * FROM ".$this->db_table_staff." WHERE ".$where;
            $staff = $wpdb->get_results($staff_query);
    
            if (isset($staff[0]->id)){ 
                return $staff;
            }
        }
        
        return false; 
    }

    //Get staff name by ID
    public function get_name($staff_id = 0, $show_false = false){
        $staff_id = absint($staff_id);
        
        if ( $staff_id > 0){
            
            global $wpdb;
            $staff_query = "SELECT display_name FROM ".$this->db_table_staff." WHERE id = ".$staff_id;
            $staff = $wpdb->get_results($staff_query);
    
            if (isset($staff[0]->display_name)){ 
                return $staff[0]->display_name;
            }
        }
        
        //if show false is true I return at least a name
        if($show_false){
            return __( 'Any Staff Member', 'ik_schedule_location');
        } else {
            return false;
        }
        
    }

    //Get all staffs data
    public function get_all(){
                    
        global $wpdb;
        $staffs_query = "SELECT * FROM ".$this->db_table_staff." ORDER BY name ASC";
        $staffs = $wpdb->get_results($staffs_query);

        if (isset($staffs[0]->id)){ 
            return $staffs;
        }
        
        return false;
        
    }

    //Count the quantity of staff records
    public function qty_records(){
    
        global $wpdb;
        $querystaff = "SELECT * FROM ".$this->db_table_staff;
        $staff_records = $wpdb->get_results($querystaff);

        if (isset($staff_records[0]->id)){ 
            
            $staff_count = count($staff_records);
            return $staff_count;
            
        } else {
            return false;
        }
    }

    //List staffs
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
        $display_nameClass = $empty;
        $location_idClass = $empty;
    
        
        if ($orderby != 'id'){	
            if ($orderby == 'display_name'){
                $orderQuery = ' ORDER BY '.$this->db_table_staff.'.display_name '.$orderDir;
                $display_nameClass = $orderClass;
            } else if ($orderby == 'location_id'){
                $orderQuery = ' ORDER BY '.$this->db_table_staff.'.location_id '.$orderDir;
                $location_idClass = $orderClass;
            } else {
                $orderQuery = ' ORDER BY '.$this->db_table_staff.'.id '.$orderDir;
                $idClass = $orderClass;
            }
        } else {
            $orderQuery = ' ORDER BY '.$this->db_table_staff.'.id '.$orderDir;
            $idClass = $orderClass;
        }

        $classData = array(
            'id' => $idClass,
            'display_name' => $display_nameClass,
            'location_id' => $location_idClass,
        );

        if ($search != NULL){ 
            //Search by lot number, product name, botanical name and country of origin
            $where = " WHERE ".$this->db_table_staff.".display_name LIKE '%".$search."%'";
        } else {
            $where = '';
            $search = '';
        }

        $groupby = (isset($groupby)) ? $groupby : " GROUP BY ".$this->db_table_staff.".id ";

        global $wpdb;

        $query = "SELECT * FROM ".$this->db_table_staff." ".$where.$groupby.$orderQuery.$offsetList;

        $staffs = $wpdb->get_results($query);
        $staffs_data = array(
            'data' => $staffs,
            'class' => $classData,
            'search_value' => $search,        
        );

        return $staffs_data;

    }   
    
    private function paginator($qtyToList){
        $list_datas_all = $this->qty_records();
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
                        echo '<a class="ik_listar_page_data '.$PageNActual.'" href="'.$this->staff_admin_url.'&list='.$i.'">'.$i.'</a>';
                    }
                }
                echo '</div>';
            }
        } 	        
    }

    //Method to show list of staffs for backend
    public function get_list_wrapper_backend($qty = 30){

        $qty = absint($qty);

        $staffs_data = $this->get_list($qty);
        $staffs = $staffs_data['data'];;
        $search = $staffs_data['search_value'];;

        //classes for columns that are filtered
        $classData = $staffs_data['class'];

        $idClass = $classData['id'];
        $display_nameClass = $classData['display_name'];
        $location_idClass = $classData['location_id'];

        //to get locations data
        $locations = new Ik_Schedule_Locations();
        
        $searchBar = '<p class="search-box">
            <label class="screen-reader-text" for="tag-search-input">'.__( 'Search', 'ik_schedule_location').':</label>
            <input type="search" id="tag-search-input" name="search" value="'.$search.'">
            <input type="submit" id="searchbutton" class="button" value="'.__( 'Search', 'ik_schedule_location').'">
        </p>';

        // If data exists
        if (isset($staffs[0]->id)){

            $columnsheading = '<tr>
                <th><input type="checkbox" class="select_all" /></th>
                <th order="id" class="worder '.$idClass.'">'.__( 'ID', 'ik_schedule_location').' <span class="sorting-indicator"></span></th>
                <th order="display_name" class="worder '.$display_nameClass.'">'.__( 'Staff Member', 'ik_schedule_location').' <span class="sorting-indicator"></span></th>
                <th order="location_id" class="worder '.$location_idClass.'">'.__( 'Location', 'ik_schedule_location').' <span class="sorting-indicator"></span></th>
                <th>
                    <button class="ik_sch_book_button_delete button action">'.__( 'Delete', 'ik_schedule_location').'</button></td>
                </th>
            </tr>';
            $listing = '
            <div class="tablenav-pages">'.__( 'Total', 'ik_schedule_location').': '.$this->qty_records().' - '.__( 'Showing', 'ik_schedule_location').': '.count($staffs).'</div>'.$searchBar;

            if ($search != NULL){
                $listing .= '<p class="show-all-button"><a href="'.$this->staff_admin_url.'" class="button button-primary">'.__( 'Show All', 'ik_schedule_location').'</a></p>';
            }

            $listing .= '<table id="ik_sch_book_existing">
                <thead>
                    '.$columnsheading.'
                </thead>
                <tbody>';
                foreach ($staffs as $staff){
                                        
                    $listing .= '
                        <tr iddata="'.$staff->id.'">
                            <td><input type="checkbox" class="select_data" /></td>
                            <td class="ik_sch_book_id">'.$staff->id.'</td>
                            <td class="ik_sch_book_display_name">'.$staff->display_name.'</td>
                            <td class="ik_sch_book_location_id">'.$locations->get_location_name($staff->location_id).'</td>
                            <td iddata="'.$staff->id.'">
                                <a href="'.$this->staff_admin_url.'&staff_id='.$staff->id.'" class="ik_sch_book_button_edit_staff button action">'.__( 'Edit', 'ik_schedule_location').'</a>
                                <button class="ik_sch_book_button_delete_staff button action">'.__( 'Delete', 'ik_schedule_location').'</button></td>
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
                <p class="show-all-button"><a href="'.$this->staff_admin_url.'" class="button button-primary">Show All</a></p>
            </div>';
                return $listing;
            }
        }
        
        return false;
    }    

    //delete staff by ID
    public function delete($staff_id){
        $staff_id = absint($staff_id);
        global $wpdb;
        $wpdb->delete( $this->db_table_staff , array( 'id' => $staff_id ) );
        
        return true;
    }

    //return staff selector
    public function get_select($args){
        $selected_value = (isset($args['staff_id_selected'])) ? intval($args['staff_id_selected']) : 0;
        $location_id = (isset($args['location_id'])) ? intval($args['location_id']) : 0;
        
        //if backend only select
        $head_select = (isset($args['backend'])) ? '' : '<h4>'. __( 'Choose employee:', 'ik_schedule_location').'</h4>';
        $backend_data = (isset($args['backend'])) ? 'name="staff_id" id="ik_sch_book_edit_location_field" class="ik_sch_book_edit_field"' : 'id="staff_member_select"';

        $staff_select_options = $head_select.'<select '.$backend_data.'>
            <option id="option-selected" class="nocexisting_stafflass" value="0">'. __( 'Any Staff Member', 'ik_schedule_location').' </option>';
        $existing_staffs = $this->get(array('location_id' => $location_id));
        if ($existing_staffs){
            foreach($existing_staffs as $existing_staff){
                $selected = ($selected_value == $existing_staff->id) ? 'selected' : '';
                $staff_select_options .= '<option '.$selected.' value="'.$existing_staff->id.'">'.esc_html($existing_staff->display_name).'</option>';
            }
        }
        $staff_select_options .= '</select>';

        return $staff_select_options;
    }

    //return username selector to associate to staff member
    public function select_username($selected_user_id = 0){
        $selected_user_id = intval($selected_user_id);

        $staff_select_options = '<select name="user_id" id="staff_user_select">
        <option id="option-selected" class="nocexisting_stafflass" value="0">'. __( 'Select Username', 'ik_schedule_location').' </option>';
        $users = get_users();
        if ($users){
            foreach($users as $user){
                $selected = ($selected_user_id == $user->ID) ? 'selected' : '';
                $staff_select_options .= '<option '.$selected.' value="'.$user->ID.'">'.esc_html($user->display_name).'</option>';
            }
        }
        $staff_select_options .= '</select>';

        return $staff_select_options;
    }

    //update staff data
    public function update(){

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ik_sch_book_user_permissions()){
        
            //if edit
            if(isset($_GET['staff_id']) && isset($_POST['staff_id'])){

                $staff_member_name = isset($_POST['edit_display_name']) ? sanitize_text_field($_POST['edit_display_name']) : '';
                $staff_member_name = str_replace('\\', '', $staff_member_name);
                $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
                $location_id = isset($_POST['branch']) ? intval($_POST['branch']) : 0;

                $args = array(
                    'id' => intval($_GET['staff_id']),
                    'display_name' => $staff_member_name,
                    'user_id' => $user_id,
                    'location_id' => $location_id,
                );

                return $this->edit($args);
            } else 
            //if new
            if (isset($_POST['new_display_name'])){

                $staff_member_name = sanitize_text_field($_POST['new_display_name']);
                $staff_member_name = str_replace('\\', '', $staff_member_name);
                $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
                $location_id = isset($_POST['branch']) ? intval($_POST['branch']) : 0;

                $args = array(
                    'display_name' => $staff_member_name,
                    'user_id' => $user_id,
                    'location_id' => $location_id,
                );
                    
                return $this->create($args);                            
            }

        }

		return false;
        
    }

}

?>