<?php
/*

Book - Schedule Locatons - Ajax Functions
Created: 06/21/2022
Last Update: 10/01/2023
Author: Gabriel Caroprese

*/

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

//Ajax to delete a location
add_action( 'wp_ajax_ik_sch_book_ajax_delete_location', 'ik_sch_book_ajax_delete_location');
function ik_sch_book_ajax_delete_location(){
    if(isset($_POST['iddata'])){
        $location_id = absint($_POST['iddata']);

        $location = new Ik_Schedule_Locations();

        $delete_query = $location->delete_location($location_id);

        echo json_encode( $delete_query );
    }
    wp_die();         
}

//Ajax to delete a service
add_action( 'wp_ajax_ik_sch_book_ajax_delete_service', 'ik_sch_book_ajax_delete_service');
function ik_sch_book_ajax_delete_service(){
    if(isset($_POST['iddata'])){
        $service_id = absint($_POST['iddata']);

        $services = new Ik_Schedule_Services();

        $delete_query = $services->delete_service($service_id);

        echo json_encode( $delete_query );
    }
    wp_die();         
}

//Ajax to delete a booking request
add_action( 'wp_ajax_ik_sch_book_ajax_delete_booking', 'ik_sch_book_ajax_delete_booking');
function ik_sch_book_ajax_delete_booking(){
    if(isset($_POST['iddata'])){
        $booking_id = absint($_POST['iddata']);

        $booking = new Ik_Schedule_Booking();

        $delete_query = $booking->delete($booking_id);

        echo json_encode( $delete_query );
    }
    wp_die();         
}

//Ajax to delete a booking request
add_action( 'wp_ajax_ik_sch_book_ajax_delete_staff', 'ik_sch_book_ajax_delete_staff');
function ik_sch_book_ajax_delete_staff(){
    if(isset($_POST['iddata'])){
        $staff_id = absint($_POST['iddata']);

        $staff = new Ik_Schedule_Staff();

        $delete_query = $staff->delete($staff_id);

        echo json_encode( $delete_query );
    }
    wp_die();         
}

//update form based on location
add_action('wp_ajax_nopriv_ik_sch_book_ajax_update_form', 'ik_sch_book_ajax_update_form');
add_action('wp_ajax_ik_sch_book_ajax_update_form', 'ik_sch_book_ajax_update_form');
function ik_sch_book_ajax_update_form() {
    if (isset($_POST['branch_id'])) {
        $booking_data = new Ik_Schedule_Booking();
        $location_id = absint($_POST['branch_id']);

        $location_data['service_options'] = $booking_data->get_service_select_options($location_id);
        $location_data['enabled_days'] = $booking_data->available_days->get_enabled_days($location_id);
        $location_data['disabled_dates'] = $booking_data->available_days->get_blocked_dates_js($location_id);

        wp_send_json($location_data);
    }
    wp_die();
}

//update timepicker based on date and location
add_action('wp_ajax_nopriv_ik_sch_book_ajax_update_time', 'ik_sch_book_ajax_update_time');
add_action('wp_ajax_ik_sch_book_ajax_update_time', 'ik_sch_book_ajax_update_time');
function ik_sch_book_ajax_update_time() {
    if (isset($_POST['selectedDate']) && isset($_POST['branch_id'])) {
        $booking_data = new Ik_Schedule_Booking();
        $selectedDate = esc_sql($_POST['selectedDate']);
        $branch_id = absint($_POST['branch_id']);

        //If there're services on session i add minutes to discount
        $time_to_discount = 0;
        if(isset($_POST['session_data'])){
            $service_ids_selected = $booking_data->get_services_selected_by_user($branch_id);
            if($service_ids_selected == true){
                $service_id_listed = array();
                foreach ($service_ids_selected as $service_id){
                    //to avoid add a service id data twice
                    if (!in_array($service_id, $service_id_listed)) {

                        $service_id_listed[] = $service_id;

                        $service_to_book = $booking_data->services->get_service_id_by_location_id($branch_id, $service_id);

                        if($service_to_book){
                            $time_to_discount = $time_to_discount + $service_to_book['delivery_time'];
                        }
                    }
                }
            }
        }

        $format_date_id = $booking_data->get_config()['format_date'];
        $formatTime = $booking_data->get_config()['format_time'];
        $format_date = $booking_data->format_date($format_date_id, 'php');
        $dateTime = DateTime::createFromFormat($format_date, $selectedDate);

        //validate date
        if ($dateTime && $dateTime->format($format_date) === $selectedDate) {
            $times_data = $booking_data->available_days->get_available_times_js($branch_id, $dateTime, $time_to_discount, $formatTime);
        } else {
            $times_data = 'error';
        }

        wp_send_json($times_data);

    }
    wp_die();
}

//Refresh booking session data after selecting a service
add_action('wp_ajax_nopriv_ik_sch_book_ajax_add_service_for_location', 'ik_sch_book_ajax_add_service_for_location');
add_action('wp_ajax_ik_sch_book_ajax_add_service_for_location', 'ik_sch_book_ajax_add_service_for_location');
function ik_sch_book_ajax_add_service_for_location() {
    if (isset($_POST['service_id']) && isset($_POST['branch_id']) && isset($_POST['action_btn_select'])) {
        $service_id = absint($_POST['service_id']);
        $branch_id = absint($_POST['branch_id']);
        $action_to_do = ($_POST['action_btn_select'] == 'add') ? 'add' : 'remove';

        $service_data = new Ik_Schedule_Services();
        //I check if service is valid and get its info
        $service_valid_data = $service_data->get_service_id_by_location_id($branch_id, $service_id);
    
        // update session data if valid service
        if($service_valid_data !== false){
            //add or remove service_id on session data
            if($action_to_do == 'add'){
                $_SESSION['ik_sch_services_added'][$branch_id][] = $service_id;
            } else {
                // found the value index in array
                $valueindex = array_search($service_id, $_SESSION['ik_sch_services_added'][$branch_id]);

                // delete array value
                if ($valueindex !== false) {
                    unset($_SESSION['ik_sch_services_added'][$branch_id][$valueindex]);
                }
            }
        }

        wp_send_json(true);
    }
    wp_die();
}

//Add woocommerce service to cart
add_action('wp_ajax_nopriv_ik_sch_book_ajax_add_wc_service_for_location', 'ik_sch_book_ajax_add_wc_service_for_location');
add_action('wp_ajax_ik_sch_book_ajax_add_wc_service_for_location', 'ik_sch_book_ajax_add_wc_service_for_location');
function ik_sch_book_ajax_add_wc_service_for_location() {
    if (isset($_POST['service_id']) && isset($_POST['branch_id']) && isset($_POST['action_btn_select'])) {
        $service_id = absint($_POST['service_id']);
        $branch_id = absint($_POST['branch_id']);
        $action_to_do = ($_POST['action_btn_select'] == 'add') ? 'add' : 'remove';

        $service_valid_data = false; 

        //validate it's a product
        $product = wc_get_product($service_id);
        if($product){
            //make sure location is correct
            $locations = $product->get_meta('branch_search');
            $location_value = '-location="'.$branch_id.'"';

            if (strpos($locations, $location_value) !== false) {
                $service_valid_data = true;
            }
        }

        // update session data if valid service
        if ($service_valid_data) {
            //add or remove service_id on session data
            if($action_to_do == 'add'){

                //add product to cart
                $cart_item_key = WC()->cart->add_to_cart($service_id);

                //if added
                if ($cart_item_key) {
                    //update session data
                    $_SESSION['ik_sch_services_added'][$branch_id][] = $service_id;

                    //url where the service was added from
                    $_SESSION['ik_sch_url_location'] = (isset($_POST['url_location'])) ? esc_url($_POST['url_location']) : '#';
                }

            } else {
                // found the value index in array
                $valueindex = array_search($service_id, $_SESSION['ik_sch_services_added'][$branch_id]);

                // delete array value
                if ($valueindex !== false) {
                    unset($_SESSION['ik_sch_services_added'][$branch_id][$valueindex]);
                }

                // if product in cart delete from cart
                $cart_item_key = WC()->cart->find_product_in_cart($service_id);

                if ($cart_item_key) {
                    WC()->cart->remove_cart_item($cart_item_key);

                }
            }

            wp_send_json($_SESSION['ik_sch_services_added'][$branch_id]);
        }
    }
    wp_die();
}


//Update booking footer panel based on session data
add_action('wp_ajax_nopriv_ik_sch_book_ajax_service_selected_session', 'ik_sch_book_ajax_service_selected_session');
add_action('wp_ajax_ik_sch_book_ajax_service_selected_session', 'ik_sch_book_ajax_service_selected_session');
function ik_sch_book_ajax_service_selected_session() {
    //default output
    $html_output = '<div></div>';
    $service_id_listed[] = 0;
    $count_services = 0;

    if (isset($_POST['branch_id'])) {
        $branch_id = absint($_POST['branch_id']);
        
        //I validate the service is still listed for the location and I get the details
        $booking_data = new Ik_Schedule_Booking();

        //make sure woocommerce is not active
        $woocommerce_enabled = $booking_data->get_config()['woocommerce'];
        $service_ids = $booking_data->get_services_selected_by_user($branch_id);

        //if woocommerce is enabled 
        if($woocommerce_enabled){
            $footer_bar = '';
            if(is_array($service_ids)){
                $totalSum = 0;

                $service_existing_ids[] = 0;
                foreach ($service_ids as $service_id){
                    $product = wc_get_product($service_id);
                    if($product){
                        $price = $product->get_price();
                        $count_services += 1;
                        $totalSum += $price;
                        $service_existing_ids[] = $service_id;
                    }
                }

                //in case is 1 service or more
                if($count_services > 1){
                    $services_text = __( 'Services', 'ik_schedule_location');
                } else {
                    $services_text = __( 'Service', 'ik_schedule_location');                        
                }

                $footer_bar = '<div class="ik_sch_book_book_service_panel_content hidden">
                    <div class="ik_sch_book_book_service_panel_services_data">
                        <div class="ik_sch_book_book_service_panel_services_count">'.$count_services.' '.$services_text.'</div>
                        <div class="ik_sch_book_book_service_panel_services_total_price">'.wc_price($totalSum).'</div>
                    </div>
                    <a id="ik_sch_book_book_wc_service_btn" class="ik_sch_field_form_modal_btn_act" href="'.wc_get_cart_url().'">'.__( 'Book', 'ik_schedule_location').'</a>
                </div>';
            }
            

            $data_services_session['services'] = (isset($service_existing_ids)) ? $service_existing_ids : array(0);
            $data_services_session['panel_html'] = ($count_services > 0) ? $footer_bar : '';

        } else {
            //if regular services from plugin
            if(is_array($service_ids)){
    
                $html_output = '';
    
                $count_services = 0;
                $price_services = 0;
                $services_list = '';
                foreach ($service_ids as $service_id){
    
                    //to avoid add a service id data twice
                    if (!in_array($service_id, $service_id_listed)) {
    
                        $service_id_listed[] = $service_id;
    
                        $service_to_book = $booking_data->services->get_service_id_by_location_id($branch_id, $service_id);
    
                        if($service_to_book){
                            if(isset($currency_id)){
                                if($service_to_book['currency_id'] != $currency_id){
                                    $currency_id = '-1';
                                }
                            } else {
                                $currency_id = $service_to_book['currency_id'];
                            }
    
                            //if the service price is custom or not
                            if($service_to_book['custom_price'] == '-1.00'){
                                $price_service = $service_to_book['price'];
                            } else {
                                $price_service = $service_to_book['custom_price'];
                            }
                            
                            //I accumulate the price
                            $price_services = $price_service + $price_services;
                            $price_services = number_format( floatval($price_services), 2, '.', ''  );
    
                            $services_list .= '<div class="row ik_sch_book_modal_services_modal_item">
                                <div class="col-md-10">
                                    <div class="ik_sch_book_modal_services_modal_data" data_id="'.$service_to_book['id'].'">
                                        <div class="ik_sch_book_modal_services_cat_name">'.$service_to_book['cat_name'].'</div>
                                        <div class="ik_sch_book_modal_services_name">'.$service_to_book['name'].'</div>
                                        <div class="ik_sch_book_modal_services_data">
                                            <span class="ik_sch_book_modal_services_time">'.$service_to_book['delivery_time_full'].'</span>
                                            <span class="ik_sch_book_modal_services_price">'.$booking_data->get_price_currency_format($service_to_book['currency_id'], $price_service).'</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" data_id="'.$service_to_book['id'].'" class="ik_sch_book_modal_remove_service float-end"><span aria-hidden="true">×</span></button>
                                </div>
                            </div>';
    
                            //I accumulate the number of services intended to book
                            $count_services = $count_services + 1;
                        }
                    }
                }
    
                if($count_services > 0){
    
                    //to format price
                    $price_details = $booking_data->get_price_currency_format($currency_id, $price_services);
    
                    //in case is 1 service or more
                    if($count_services > 1){
                        $services_text = __( 'Services', 'ik_schedule_location');
                    } else {
                        $services_text = __( 'Service', 'ik_schedule_location');                        
                    }
    
                    $name_customer = (isset($_SESSION['ik_sch_field_name_customer'])) ? sanitize_text_field($_SESSION['ik_sch_field_name_customer']) : '';
                    $email_address = (isset($_SESSION['ik_sch_field_email_address'])) ? sanitize_text_field($_SESSION['ik_sch_field_email_address']) : '';
                    $phone_field = (isset($_SESSION['ik_sch_field_phone'])) ? sanitize_text_field($_SESSION['ik_sch_field_phone']) : '';
    
                    $html_output = '<div class="ik_sch_book_book_service_panel_content hidden">
                            <div class="ik_sch_book_book_service_panel_services_data">
                                <div class="ik_sch_book_book_service_panel_services_count">'.$count_services.' '.$services_text.'</div>
                                <div class="ik_sch_book_book_service_panel_services_total_price">'.$price_details.'</div>
                            </div>
                            <button id="ik_sch_book_book_service_panel_book_btn" class="ik_sch_field_form_modal_btn_act" data-toggle="modal" data-target="#ik_sch_book_modal">'.__( 'Book', 'ik_schedule_location').'</button>
                        </div>
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
                                                    <form id="ik_sch_book_modal_booking_form">
                                                        <h5 class="modal_data_input">'.__( 'Select Date', 'ik_schedule_location').'</h5>
                                                        <div class="ik_sch_field-wrap" data-name="date">
                                                            <input type="text" id="ik_sch_field_form_date" disabled name="date" value="" placeholder="'. __( 'Select Date', 'ik_schedule_location').' *" class="ik_sch_field ik_sch-text datepicker" aria-required="true">
                                                        </div>
                                                        <h5 class="modal_data_input">'.__( 'Select Time', 'ik_schedule_location').'</h5>
                                                        <div class="ik_sch_field-wrap" data-name="time">
                                                            <input type="text" disabled id="ik_sch_field_form_time" name="time" value="" placeholder="'. __( 'Select Time', 'ik_schedule_location').' *" class="ik_sch_field ik_sch-text timepicker" aria-required="true">
                                                        </div>
                                                        <h5 class="modal_data_input">'.__( 'Your Name', 'ik_schedule_location').'</h5>
                                                        <div class="ik_sch_field-wrap" data-name="name_customer">
                                                            <input type="text" name="name_customer" id="ik_sch_field_form_name_customer" value="'.$name_customer.'" placeholder="'. __( 'Enter Your Name', 'ik_schedule_location').' *" class="ik_sch_field ik_sch-text" aria-required="true">
                                                        </div>
                                                        <h5 class="modal_data_input">'.__( 'Email', 'ik_schedule_location').'</h5>
                                                        <div class="ik_sch_field-wrap" data-name="email_address">
                                                            <input type="email" name="email_address" id="ik_sch_field_form_email_address" value="'.$email_address.'" placeholder="'. __( 'Enter Your Email', 'ik_schedule_location').' *" class="ik_sch_field ik_sch-text" aria-required="true">
                                                        </div>
                                                        <h5 class="modal_data_input">'.__( 'Phone', 'ik_schedule_location').'</h5>
                                                        <div class="ik_sch_field-wrap" data-name="phone">
                                                            <input type="text" name="phone" id="ik_sch_field_form_phone" value="'.$phone_field.'" placeholder="'. __( 'Enter Your Phone', 'ik_schedule_location').' *" class="ik_sch_field ik_sch-text" aria-required="true">
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="container">
                                                        <div class="row">
                                                        '.$services_list.'
                                                        </div>
                                                        <div class="row">
                                                            <button type="button" class="btn btn-secondary add-more-modal" data-dismiss="modal"><i class="fas fa-plus-circle"></i> '. __( 'Add another service', 'ik_schedule_location').'</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">'. __( 'Close', 'ik_schedule_location').'</button>
                                        <button type="button" id="ik_sch_field_form_modal_submit" class="btn btn-primary">'. __( 'Book', 'ik_schedule_location').'</button>
                                    </div>
                                </div>
                            </div>
                        </div>';
                }
            }
            $data_services_session['services'] = $service_id_listed;
            $data_services_session['panel_html'] = $html_output;
        }


    }

    wp_send_json($data_services_session);
    wp_die();
}

//Update booking footer panel based on session data
add_action('wp_ajax_nopriv_ik_sch_book_ajax_get_location_dates', 'ik_sch_book_ajax_get_location_dates');
add_action('wp_ajax_ik_sch_book_ajax_get_location_dates', 'ik_sch_book_ajax_get_location_dates');
function ik_sch_book_ajax_get_location_dates() {
    if (isset($_POST['branch_id'])) {

        $booking_data = new Ik_Schedule_Booking();
        $location_id = absint($_POST['branch_id']);
        
        $location_dates['enabled_days'] = $booking_data->available_days->get_enabled_days($location_id);
        $location_dates['disabled_dates'] = $booking_data->available_days->get_blocked_dates_js($location_id);

        wp_send_json($location_dates);
    }
    wp_die();
}

//Update booking footer panel based on session data
add_action('wp_ajax_nopriv_ik_sch_book_ajax_update_book_session_data_fields', 'ik_sch_book_ajax_update_book_session_data_fields');
add_action('wp_ajax_ik_sch_book_ajax_update_book_session_data_fields', 'ik_sch_book_ajax_update_book_session_data_fields');
function ik_sch_book_ajax_update_book_session_data_fields() {
    if (isset($_POST['input_name']) && isset($_POST['input_value'])) {
        $input_name = sanitize_text_field($_POST['input_name']);
        $input_value = sanitize_text_field($_POST['input_value']);

        switch($input_name){
            case 'name_customer':
                $_SESSION['ik_sch_field_name_customer'] = $input_value;
                break;
            case 'email_address':
                $_SESSION['ik_sch_field_email_address'] = $input_value;
                break;
            case 'phone':
                $_SESSION['ik_sch_field_phone'] = $input_value;
                break;
        }

        wp_send_json(true);
    }
    wp_die();
}

//submit form for reservation or return errors
add_action('wp_ajax_nopriv_ik_sch_book_ajax_submit_form', 'ik_sch_book_ajax_submit_form');
add_action('wp_ajax_ik_sch_book_ajax_submit_form', 'ik_sch_book_ajax_submit_form');
function ik_sch_book_ajax_submit_form() {
    if (isset($_POST['selectedDate']) && isset($_POST['selectedTime']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['branch_id']) && isset($_POST['type'])) {

        $booking_data = new Ik_Schedule_Booking();

        //validate fields
        $selectedDate = sanitize_text_field($_POST['selectedDate']);
        $selectedTime = sanitize_text_field($_POST['selectedTime']);
        $name = $booking_data->validate_input_form($type = "name", sanitize_text_field($_POST['name']));
        $email = $booking_data->validate_input_form($type = "email", sanitize_text_field($_POST['email']));
        $phone = $booking_data->validate_input_form($type = "phone", sanitize_text_field($_POST['phone']));
        $branch_id = absint($_POST['branch_id']);

        $note = (isset($_POST['note'])) ? sanitize_textarea_field($_POST['note']) : NULL;
        $service_ids = (isset($_POST['service_ids'])) ? explode(',', $_POST['service_ids']) : NULL;

        //I create the array for positive or negative validations
        $result = array(
            'result' => false,
            'fields' => array(
                'selectedDate' => $selectedDate,
                'selectedTime' => $selectedTime,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
            )
        );     

        //if everything's valid
        if($selectedDate && $selectedTime && $name && $email && $phone){
            //to validate if it's from form or modal popup
            $type_submit = (sanitize_text_field($_POST['type']) == 'modal') ? 'modal' : 'form';

            $booking_data = new Ik_Schedule_Booking();

            //make sure date is available
            if($booking_data->available_days->is_date_available($branch_id, $selectedDate)){
                
                //make sure time is available
                if($booking_data->available_days->is_time_available($selectedTime, $selectedDate, $selectedTime)){
                    
                    //add book to database and send email
                    $args = array(
                        'branch_id' => $branch_id,
                        'name'      => $name,
                        'email'     => $email,
                        'phone'     => $phone,
                        'date'      => $selectedDate,
                        'time'      => $selectedTime
                    );

                    //I add note or service data if not null
                    if($note !== NULL){
                        $args['internal_note'] = $note;
                    }
                    if($service_ids !== NULL){
                        $args['services'] = $service_ids;
                    }

                    $request_booking_id = $booking_data->create_request($args);

                    //message to return
                    $result['message'] = __( 'Your reservation has been confirmed! Thank you!', 'ik_schedule_location');
                    $result['result'] = true;

                } else {
                    $result['message'] = __( 'The selected time is no longer available.', 'ik_schedule_location');
                    $result['fields']['selectedTime'] = false;             
                }

            } else {
                $result['message'] = __( 'The selected date is no longer available.', 'ik_schedule_location');
                $result['fields']['selectedDate'] = false;
                $result['fields']['selectedTime'] = false;             
            }

        } else {
            $result = array(
                'result' => false,
                'message' => __( 'Please fix the required fields.', 'ik_schedule_location'),
                'fields' => array(
                    'selectedDate' => $selectedDate,
                    'selectedTime' => $selectedTime,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                )
            );           
        }

    } else {
        $result = array(
            'result' => false,
            'message' => __( 'Something went wrong.', 'ik_schedule_location'),
            'fields' => array(
                'selectedDate' => false,
                'selectedTime' => false,
                'name' => false,
                'email' => false,
                'phone' => false,
            )
        );
    }
    wp_send_json($result);
    wp_die();
}

//ajax to show modal about specific booking id
add_action('wp_ajax_ik_sch_book_ajax_show_booking_data', 'ik_sch_book_ajax_show_booking_data');
function ik_sch_book_ajax_show_booking_data(){
    if (isset($_POST['iddata'])){
        $booking_id = intval($_POST['iddata']);
        $booking = new Ik_Schedule_Booking();
        $booking_modal = $booking->show_modal_by_booking_id($booking_id);
        wp_send_json($booking_modal);
    } else {
        wp_send_json(false);
    }
    wp_die();
}

//ajax to get edit field data to update
add_action('wp_ajax_ik_sch_book_ajax_add_edit_booking_field', 'ik_sch_book_ajax_add_edit_booking_field');
function ik_sch_book_ajax_add_edit_booking_field(){
    if (isset($_POST['iddata']) && isset($_POST['type_data'])){
        $booking_id = intval($_POST['iddata']);
        $booking = new Ik_Schedule_Booking();
        $booking_data = $booking->get_by_id($booking_id);

        if($booking_data){
            $type_data = sanitize_text_field($_POST['type_data']);

            switch($type_data){
                case 'accepted':
                    $field = $booking->select_status($booking_data->accepted);
                    $ask_email = true;
                    break;
                case 'booking_date':
                    $booking_time = substr($booking_data->booking_date, -8); //00:00:00
                    $booking_datetime = $booking->available_days->convert_date_format($booking_data->booking_date).' '.$booking->available_days->convert_time_format($booking_time);
                    $field = '<input type="text" class="ik_sch_book_edit_field" name="booking_date" value="'.$booking_datetime.'">';
                    $ask_email = true;
                    break;
                case 'branch_id':
                    $field = $booking->locations->get_locations_select(array(
                        'name'      => 'branch_id', 
                        'class'     => 'ik_sch_book_edit_field', 
                        'id_element'=> 'ik_sch_book_edit_location_field' 
                    ),$booking_data->branch_id);
                    $ask_email = true;
                    break;
                case 'internal_note':
                    $field = '<textarea name="internal_note" class="ik_sch_book_edit_field">'.esc_html($booking_data->internal_note).'</textarea>';
                    $ask_email = false;
                    break;
                case 'service_ids':
                    $services_list = $booking->get_services_list_for_modal($booking_id, true);
                    $field = $services_list;
                    $ask_email = false;
                    break;
                case 'staff_id':
                    $staff_list = $booking->staff->get_select(array('location_id'=> $booking_data->branch_id, 'backend'=> true, 'staff_id_selected' => $booking_data->staff_id));
                    $field = $staff_list;
                    $ask_email = false;
                    break;
                default:
                    $field = '<div class="data_info"><input type="text" class="ik_sch_book_edit_field" name="'.$type_data.'" value="'.$booking_data->$type_data.'"></div>';
                    $ask_email = false;
                    break;
            }

            $field .= '<button class="button button_save_edit"><span class="dashicons dashicons-yes"></span></button><button class="button button_cancel_edit"><span class="dashicons dashicons-undo"></span></button>';

            if($ask_email){
                $field .= '<div class="ask_email_field"><input type="checkbox" name="ask_email" value="1"> '.__( 'Send Email.', 'ik_schedule_location').'</div>';
            }

            wp_send_json($field);
        }

    }
    wp_die();
}

//ajax to get reset field data saving or cancel edited info
add_action('wp_ajax_ik_sch_book_ajax_add_reset_booking_field', 'ik_sch_book_ajax_add_reset_booking_field');
function ik_sch_book_ajax_add_reset_booking_field(){
    if (isset($_POST['iddata']) && isset($_POST['type_data']) && isset($_POST['save'])){
        $booking_id = intval($_POST['iddata']);
        $save = rest_sanitize_boolean($_POST['save']);
        $send_email = (isset($_POST['send_email'])) ? rest_sanitize_boolean($_POST['send_email']) : false;
        $booking = new Ik_Schedule_Booking();
        $type_data = sanitize_text_field($_POST['type_data']);

        switch($type_data){
            case 'accepted':
                $field_edit = 'accepted';
                break;
            case 'booking_date':
                $field_edit = 'booking_date';
                break;
            case 'service_ids[]':
                $field_edit = 'service_ids';
                break;
            case 'staff_id':
                $field_edit = 'staff_id';
                break;
            default:
                $field_edit = $type_data;
                break;
        }

        if($save && isset($_POST['value_edit'])){
            //in case is an array
            if(str_contains($type_data, '[')){
                $value_edit[$field_edit] = $_POST['value_edit'];
            } else {
                //simple value
                $value_edit[$field_edit] = sanitize_text_field($_POST['value_edit']);
            }

            //save data
            $update = $booking->edit($booking_id, $value_edit);

            if($send_email){
                //if email is marked to be sent
                $email_sent = $booking->send_email_notification($booking_id, $field_edit);
            }
        }

        $booking_data = $booking->get_by_id($booking_id);
        switch($type_data){
            case 'accepted':
                $field = '<div class="data_info">'.$booking->get_status_name_by_value($booking_data->accepted).' <span type_data="accepted" class="edit_data_info dashicons dashicons-edit"></span></div>';
                break;
            case 'booking_date':
                $booking_time = substr($booking_data->booking_date, -8); //00:00:00
                $booking_datetime = $booking->available_days->convert_date_format($booking_data->booking_date).' '.$booking->available_days->convert_time_format($booking_time);
                $field = '<div class="data_info">'.$booking_datetime.' <span type_data="booking_date" class="edit_data_info dashicons dashicons-edit"></span></div>';
                break;
            case 'internal_note':
                $field = '<div class="data_info">'.esc_html($booking_data->internal_note).' <span type_data="internal_note" class="edit_data_info dashicons dashicons-edit"></span></div>';
                break;
            case 'service_ids[]':
                $services_list = $booking->get_services_list_for_modal($booking_id);
                $field = '<div>'.$services_list.'</div>';
                $ask_email = false;
                break;
            case 'branch_id':
                $field = '<div class="data_info">#'.$booking_data->branch_id.' '.$booking->locations->get_location_name($booking_data->branch_id).' <span type_data="branch_id" class="edit_data_info dashicons dashicons-edit"></span></div>';
                break;
            case 'staff_id':
                $field = '<div class="data_info">'.$booking->staff->get_name($booking_data->staff_id, true).' <span type_data="staff_id" class="edit_data_info dashicons dashicons-edit"></span></div>';
                break;
            default:
                $field = '<div class="data_info">'.$booking_data->$type_data.' <span type_data="'.$type_data.'" class="edit_data_info dashicons dashicons-edit"></span></div>';
                break;
        }

        wp_send_json($field);

    }
    wp_die();
}

//ajax to update booking color
add_action('wp_ajax_ik_sch_book_ajax_update_booking_color', 'ik_sch_book_ajax_update_booking_color');
function ik_sch_book_ajax_update_booking_color(){
    if (isset($_POST['iddata']) && isset($_POST['color'])){
        $booking_id = intval($_POST['iddata']);
        $data_update['color'] = sanitize_hex_color($_POST['color']);
        $booking = new Ik_Schedule_Booking();
        $update = $booking->edit($booking_id, $data_update);
        wp_send_json($update);
    } else {
        wp_send_json(false);
    }
    wp_die();
}

//get month data values of bookings for that month
add_action('wp_ajax_ik_sch_book_ajax_get_month_data', 'ik_sch_book_ajax_get_month_data');
function ik_sch_book_ajax_get_month_data(){
    $bookings = new Ik_Schedule_Booking;

    $args['location_id'] = (isset($_POST['location_id'])) ? absint($_POST['location_id']) : 0;
    $args['month'] = (isset($_POST['month'])) ? absint($_POST['month']) : 0;
    $args['year'] = (isset($_POST['year'])) ? absint($_POST['year']) : 0;

    $month = $bookings->get_month_data($args);

    wp_send_json($month);
    wp_die();
}

//get modal to create new booking
add_action('wp_ajax_ik_sch_book_ajax_show_new_booking_modal', 'ik_sch_book_ajax_show_new_booking_modal');
function ik_sch_book_ajax_show_new_booking_modal(){
    $bookings = new Ik_Schedule_Booking;
    $new_booking_modal = $bookings->show_modal_create_booking();

    wp_send_json($new_booking_modal);
    wp_die();
}

//get service select based on location id
add_action('wp_ajax_ik_sch_book_ajax_get_service_select', 'ik_sch_book_ajax_get_service_select');
function ik_sch_book_ajax_get_service_select(){
    if (isset($_POST['branch_id'])){
        $branch_id = absint($_POST['branch_id']);
        $locations = new Ik_Schedule_Services;
        $service_select_by_location = $locations->get_select_services_by_location_id($branch_id);
        wp_send_json($service_select_by_location);
    }
    wp_die();
}

//select time on calendar from Wooomerce cart
add_action('wp_ajax_nopriv_ik_sch_book_ajax_wc_select_time', 'ik_sch_book_ajax_wc_select_time');
add_action('wp_ajax_ik_sch_book_ajax_wc_select_time', 'ik_sch_book_ajax_wc_select_time');
function ik_sch_book_ajax_wc_select_time(){
    if (isset($_POST['time_booking'])){
        $bookings = new Ik_Schedule_Booking;

        $bookings->save_session_time_selected($_POST['time_booking']);

        wp_send_json(true);
    }
    wp_die();
}

//select day on calendar from Wooomerce cart and shows available times
add_action('wp_ajax_nopriv_ik_sch_book_ajax_wc_select_day', 'ik_sch_book_ajax_wc_select_day');
add_action('wp_ajax_ik_sch_book_ajax_wc_select_day', 'ik_sch_book_ajax_wc_select_day');
function ik_sch_book_ajax_wc_select_day(){
    if (isset($_POST['date_booking'])){
        $bookings = new Ik_Schedule_Booking;
        
        //update session data
        $bookings->save_session_date_selected($_POST['date_booking']);

        //return new calendar content
        $booking_wc_calendar = $bookings->get_booking_wc_calendar();

        wp_send_json($booking_wc_calendar);
    }
    wp_die();
}

//select day on calendar from Wooomerce cart and shows available times
add_action('wp_ajax_nopriv_ik_sch_book_ajax_wc_select_week', 'ik_sch_book_ajax_wc_select_week');
add_action('wp_ajax_ik_sch_book_ajax_wc_select_week', 'ik_sch_book_ajax_wc_select_week');
function ik_sch_book_ajax_wc_select_week(){
    if (isset($_POST['move_dir'])){
        $bookings = new Ik_Schedule_Booking;
        $move_dir = sanitize_text_field($_POST['move_dir']);

        $booking_wc_calendar_data = $bookings->get_more_week_calendar_data($move_dir);

        wp_send_json($booking_wc_calendar_data);
    }
    wp_die();
}

//update session data and calendar based on staff member selected
add_action('wp_ajax_nopriv_ik_sch_book_ajax_update_staff_member', 'ik_sch_book_ajax_update_staff_member');
add_action('wp_ajax_ik_sch_book_ajax_update_staff_member', 'ik_sch_book_ajax_update_staff_member');
function ik_sch_book_ajax_update_staff_member(){
    if (isset($_POST['staff_member_id'])){
        $bookings = new Ik_Schedule_Booking;
        
        //update session data
        $bookings->save_session_staff_selected($_POST['staff_member_id']);

        //return new calendar content
        $booking_wc_calendar = $bookings->get_booking_wc_calendar();

        wp_send_json($booking_wc_calendar);
    }
    wp_die();
}

//update session data and calendar based on staff member selected
add_action('wp_ajax_nopriv_ik_sch_book_ajax_return_popup_datetimes', 'ik_sch_book_ajax_return_popup_datetimes');
add_action('wp_ajax_ik_sch_book_ajax_return_popup_datetimes', 'ik_sch_book_ajax_return_popup_datetimes');
function ik_sch_book_ajax_return_popup_datetimes(){
    $bookings = new Ik_Schedule_Booking;

    wp_send_json('<div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="ik_sch_book_modalLabel">'.__( 'Your Reservation', 'ik_schedule_location').'</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="modal-body-section">
                    <h2>'.__( 'When do you want to come?', 'ik_schedule_location').'</h2>
                    <button id="ik_sch_book_btn_today">'.__( 'Today', 'ik_schedule_location').'</button>
                    <button id="ik_sch_book_btn_tomorrow">'.__( 'Tomorrow', 'ik_schedule_location').'</button>
                    <button id="ik_sch_book_btn_thisweek">'.__( 'This Week', 'ik_schedule_location').'</button>
                    <button id="ik_sch_book_btn_nextweek">'.__( 'Next Week', 'ik_schedule_location').'</button>
                    <button id="ik_sch_book_btn_in2weeks">'.__( 'In 2 Weeks', 'ik_schedule_location').'</button>
                    <button id="ik_sch_book_btn_in2weeks">'.__( 'In 3 Weeks', 'ik_schedule_location').'</button>
                    <button id="ik_sch_book_btn_select_date">'.__( 'Select Date', 'ik_schedule_location').'</button>
                </div>
                <div class="modal-body-section">
                    <h2 id="ik_sch_book_js_date">'.__( 'Thursday', 'ik_schedule_location').', 08.02.2024</h2>
                    <div class="ik_sch_book_not_available_dates hidden"><img src="" /></div>
                    <div class="modal-body-section-times-btn">
                        <button data_time="5565" class="ik_sch_book_btn_time">09:00</button>
                        <button data_time="5565" class="ik_sch_book_btn_time">10:00</button>
                        <button data_time="5565" class="ik_sch_book_btn_time disabled">11:00</button>
                        <button data_time="5565" class="ik_sch_book_btn_time">12:00</button>
                        <button data_time="5565" class="ik_sch_book_btn_time disabled">13:00</button>
                    </div>
                </div>
            </div>
        </div>');
    wp_die();
}

?>