<?php
/* 
Book - Schedule Locatons Woocommerce Functions
Created: 20/09/202
Last Update: 12/03/2024
Author: Gabriel Caroprese
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Add WooCommerce option to upload services
function ik_sch_book_plugin_product_type(){
	if (class_exists('WC_Product')) {
		require_once(IK_SCH_BOOK_LOCATION_DIR . '/include/classes/class.booking_product.php');
	}
}
add_action( 'plugins_loaded', 'ik_sch_book_plugin_product_type' );
function ik_sch_book_register_custom_product_type($types) {
	$types['schedule_booking_product'] = __('Schedule Booking Product', 'ik_schedule_location');
	return $types;
}
add_filter('product_type_selector', 'ik_sch_book_register_custom_product_type');

add_filter( 'woocommerce_product_class', 'ik_sch_book_woocommerce_product_class', 10, 2 );
function ik_sch_book_woocommerce_product_class( $classname, $product_type ) {
    if ( $product_type == 'schedule_booking_product' ) {
        $classname = 'WC_Schedule_Booking_Product';
    }
    return $classname;
}

// Add extra fields to category edit form hook
add_action('product_cat_edit_form_fields', 'ik_sch_book_locations_extra_category_fields');
// Save extra fields when edited
add_action('edited_product_cat', 'ik_sch_book_locations_save_extra_category_fields');

// Add extra fields to category edit form callback function
function ik_sch_book_locations_extra_category_fields($term) {
    // Check for existing category meta data
    $cat_meta = get_term_meta($term->term_id, 'ik_sch_book_category_icon', true);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="cat-icon-url"><?php echo __( 'Category Icon URL', 'ik_schedule_location'); ?></label>
        </th>
        <td>
            <input type="text" name="Cat_meta[icon]" id="Cat_meta[icon]" size="3" style="width:60%;" value="<?php echo $cat_meta ? esc_url($cat_meta) : ''; ?>"><br />
            <span class="description"><?php echo __( 'URL of the category icon image.', 'ik_schedule_location'); ?></span>
        </td>
    </tr>
    <?php
}

// Save extra fields when edited
function ik_sch_book_locations_save_extra_category_fields($term_id) {
    if (isset($_POST['Cat_meta']['icon'])) {
        $cat_icon = sanitize_text_field($_POST['Cat_meta']['icon']);
        update_term_meta($term_id, 'ik_sch_book_category_icon', $cat_icon);
    }
}


// locations tab for Woo Service
function ik_sch_book_locations_product_data_tab($tabs)
{
	global $post;

    // only for "Schedule Booking Product"
    if (isset($post->ID)) {
        $product = wc_get_product($post->ID);
        if ($product->is_type('schedule_booking_product')) {
    		$tabs['ik_sch_book_locations'] = array(
				'label' => __('Locations', 'ik_schedule_location'),
				'target' => 'ik_sch_book_locations',
				'class' => array('show_if_schedule_booking_product'),
			);
			unset($tabs['shipping']);
		}
	}
    return $tabs;
}
add_filter('woocommerce_product_data_tabs', 'ik_sch_book_locations_product_data_tab');
function ik_sch_book_locations_product_data_tab_content(){
	global $post;

    // only for "Schedule Booking Product"
    if (isset($post->ID)) {
        $product = wc_get_product($post->ID);
        if ($product->is_type('schedule_booking_product')) {
			$booking = new Ik_Schedule_Booking();
			$locations_selected = $product->get_meta('branch');
			
			if(is_array($locations_selected)){
				$select = '';
				foreach($locations_selected as $location_selected){
					$select .= '<div class="ik_sch_book_locations_product_edit_container">'.$booking->locations->get_location_select($location_selected, true).'</div>';
				}
			} else {
				$select = '<div class="ik_sch_book_locations_product_edit_container">'.$booking->locations->get_location_select(0, true).'</div>';
			}
			?>
			<script>
			jQuery(document).ready(function ($) {
				jQuery('.product_data_tabs .general_tab').addClass('show_if_schedule_booking_product').show();
				jQuery('.product_data_tabs .linked_product_options').removeClass('active');
				jQuery('#linked_product_data').removeAttr('style');
				jQuery('#general_product_data').attr('style','display: block');
				jQuery('.product_data_tabs .general_tab').addClass('active'); 
				jQuery('#general_product_data .pricing').addClass('show_if_schedule_booking_product').show();
				jQuery('#ik_sch_book_add_location_field').on('click', function(){
					var select = '<div class="ik_sch_book_locations_product_edit_container"><select name="branch[]">'+jQuery('#ik_sch_book_locations_product_edit_wrapper select:first-child').html()+'</select>'+' <a class="ik_sch_book_delete_field" href="#"><span class="dashicons dashicons-trash"></span></a></div>';
					jQuery('#ik_sch_book_locations_product_edit_wrapper').append(select);
					jQuery('#ik_sch_book_locations_product_edit_wrapper .ik_sch_book_locations_product_edit_container:last-child select').val('');
					return false;
				});
				jQuery('#ik_sch_book_locations_product_edit_wrapper').on('click', '.ik_sch_book_delete_field', function(){
					jQuery(this).parent().remove();
					return false;
				});
			});
			</script>
			<div id="ik_sch_book_locations" class="panel woocommerce_options_panel">
				<h3 style=" margin-left: 10px;"><?php echo  __( 'Select Location', 'ik_schedule_location'); ?></h3>
				<p><div id="ik_sch_book_locations_product_edit_wrapper"><?php echo $select; ?></div></p>
				<p><?php echo '<a href="#" id="ik_sch_book_add_location_field" class="button">'.__( 'Add Location', 'ik_schedule_location').'</a>'; ?></p>
			</div>
			<?php
		}
	}
}
add_action('woocommerce_product_data_panels', 'ik_sch_book_locations_product_data_tab_content');

// Define Woocommerce service product as virtual
function ik_sch_book_product_is_virtual($is_virtual, $product_id)
{
    $product = wc_get_product($product_id);
    if ($product->is_type('schedule_booking_product')) {
        $is_virtual = true;
    }
    return $is_virtual;
}
add_filter('woocommerce_product_is_virtual', 'ik_sch_book_product_is_virtual', 10, 2);

// disable shipping for Woo service
function ik_sch_book_product_no_shipping($needs_shipping, $product)
{
    if ($product->is_type('schedule_booking_product')) {
        $needs_shipping = false;
    }
    return $needs_shipping;
}
add_filter('woocommerce_product_needs_shipping', 'ik_sch_book_product_no_shipping', 10, 2);

// Field to add delivery time to Woocommerce product Booking service
function ik_sch_book_delivery_time_wc_field() {
    global $woocommerce, $post;
    
    echo '<div class="options_group">';
    
    // Campo de entrada HTML
    woocommerce_wp_text_input(
        array(
            'id' => 'ik_sch_book_delivery_time_wc_field',
            'label' => __( 'Estimated Delivery Time (in minutes)', 'ik_schedule_location'),
            'placeholder' => __( 'Estimated Delivery Time (in minutes)', 'ik_schedule_location'),
            'desc_tip' => 'true',
            'description' => __( 'Estimated Delivery Time (in minutes)', 'ik_schedule_location'),
			'value' => get_post_meta($post->ID, 'delivery_time', true),
        )
    );
    
    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'ik_sch_book_delivery_time_wc_field');

//Save custom data fields of Woocommerce product edit page
function ik_sch_book_save_wc_location_field( $post_id ) {
	$post = get_post($post_id);
	
	// Return if the user doesn't have edit permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}
	// Don't store custom data twice
	if ( 'revision' === $post->post_type ) {
		return;
	}
	if(isset( $_POST[ 'branch' ] )){
		if(is_array($_POST[ 'branch' ])){
			$branch_search = '';
			foreach ($_POST[ 'branch' ] as $value) {
				$branch_search .= '-location="'.intval($value).'"';
				$branch[] = intval($value);
			}
		} else {
			$branch_search = '-location="'.intval($_POST[ 'branch' ]).'"';
			$branch[] = intval($_POST[ 'branch' ]);
		}
	}
	$delivery_time = isset($_POST['ik_sch_book_delivery_time_wc_field']) ? intval($_POST['ik_sch_book_delivery_time_wc_field']) : '';

	$product = wc_get_product( $post_id );
	$product->update_meta_data( 'delivery_time', $delivery_time );
	$product->update_meta_data( 'branch_search', $branch_search );
	$product->update_meta_data( 'branch', $branch );
	$product->save();
}
add_action( 'woocommerce_process_product_meta', 'ik_sch_book_save_wc_location_field' );

//to enable add to cart for woocommerce booking product
add_action( 'woocommerce_single_product_summary', 'ik_sch_book_schedule_booking_product_add_to_cart', 60 );
function ik_sch_book_schedule_booking_product_add_to_cart () {
    global $product;

    // Make sure it's our custom product type
    if ( 'schedule_booking_product' == $product->get_type() ) {
        do_action( 'woocommerce_before_add_to_cart_button' ); ?>

        <p class="cart">
            <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" rel="nofollow" class="single_add_to_cart_button button alt">
                <?php echo __( 'Add to cart', 'woocommerce' ); ?>
            </a>
        </p>

        <?php do_action( 'woocommerce_after_add_to_cart_button' );
    }
}

//limit add to cart to just one unit for the booking service product
function ik_sch_book_limit_product_quantity_in_cart($cart_item_data, $product_id) {
    $product = wc_get_product($product_id);
    if ($product->is_type('schedule_booking_product')) {
        $cart_item_data['quantity'] = 1;
    }
    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'ik_sch_book_limit_product_quantity_in_cart', 10, 2);
function ik_sch_book_limit_schedule_booking_product_quantity() {
	$cart = WC()->cart;
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        if ($product->is_type('schedule_booking_product')) {
            // if the quantity is more than 1
            if ($cart_item['quantity'] > 1) {
                $cart->set_quantity($cart_item_key, 1);
            }
        }
    }
}
add_action('woocommerce_before_cart', 'ik_sch_book_limit_schedule_booking_product_quantity');

//config to not show quantity, thumbnail and link to booking service product on cart
add_filter('woocommerce_cart_item_name', 'ik_sch_book_not_link_cart_item_name', 10, 3);
function ik_sch_book_not_link_cart_item_name($product_name, $cart_item, $cart_item_key) {
    $product = $cart_item['data'];

    if ($product && $product->is_type('schedule_booking_product')) {
		$product_categories = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'names'));
		$cat_name = !empty($product_categories) ? $product_categories[0] : '';
		$delivery_time = $product->get_meta('delivery_time');
		$services_data = new Ik_Schedule_Services();
		$delivery_time_text = $services_data->format_delivery_time($delivery_time);
		$title_Service = $product->get_title().' | '.$cat_name;
        return esc_html($title_Service).'</ br><span class="delivery_time_text">' . esc_html($delivery_time_text).'</span>';
    }

    return apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', $product->get_permalink($cart_item), $product_name), $cart_item, $cart_item_key);
}
add_filter('woocommerce_cart_item_thumbnail', 'ik_sch_book_hide_cart_item_thumbnail', 10, 3);
function ik_sch_book_hide_cart_item_thumbnail($product_thumbnail, $cart_item, $cart_item_key) {
    $product = $cart_item['data'];
    if ($product && $product->is_type('schedule_booking_product')) {
        return '';
    }

    return $product_thumbnail;
}
add_filter('woocommerce_cart_item_quantity', 'ik_sch_book_hide_cart_item_quantity', 10, 3);
function ik_sch_book_hide_cart_item_quantity($product_quantity, $cart_item_key, $cart_item) {
    $product = $cart_item['data'];

    if ($product && $product->is_type('schedule_booking_product')) {
        return '';
    }

    return $product_quantity;
}

//if booking product type deleted from cart
add_action('woocommerce_removed_from_cart', 'ik_sch_book_remove_product_action', 10, 2);
function ik_sch_book_remove_product_action($cart_item_key, $cart) {

    $cart_item = $cart->get_cart()[$cart_item_key];
    $product_id = $cart_item['product_id'];
    $product_type = get_post_type($product_id);
    $specific_product_type = 'schedule_booking_product';

    // I check if its the product type to check and delete session data about the product booking service id
    if ($product_type === $specific_product_type) {

        //delete service from session
        if(isset($_SESSION['ik_sch_services_added'])){

            if(is_array($_SESSION['ik_sch_services_added'])){
                foreach($_SESSION['ik_sch_services_added'] as $branch_id){
                    // found the value index in array
                    $valueindex = array_search($product_id, $branch_id);
                    $branch_id_assoc = $branch_id;
                    break;
                }
                // delete array value
                if ($valueindex !== false && isset($_SESSION['ik_sch_services_added'][$branch_id_assoc][$valueindex])) {
                    unset($_SESSION['ik_sch_services_added'][$branch_id_assoc][$valueindex]);
                }
            }
        }
    }
}

//validate booking product data was completed
add_action('woocommerce_checkout_process', 'ik_sch_book_validate_order_fields');
function ik_sch_book_validate_order_fields() {
    $specific_product_type = 'schedule_booking_product';
    $product_in_cart = false;

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        if ($product->get_type() === $specific_product_type) {
            $product_in_cart = true;
            break;
        }
    }

    // Si el producto está en el carrito pero faltan datos
    if ($product_in_cart && (
        !isset($_SESSION['ik_sch_services_added']) ||
        !isset($_SESSION['ik_sch_date_selected']) ||
        !isset($_SESSION['ik_sch_time_selected']) ||
        !isset($_SESSION['ik_sch_url_location'])
    )) {
        wc_add_notice(__(__('Please, go to the cart and select a date before completing this order.'), 'ik_schedule_location'), 'error');
    }
}


//when order a service create a booking
add_action('woocommerce_thankyou', 'ik_sch_book_service_order_action', 99, 1);
function ik_sch_book_service_order_action($order_id) {
    $order = wc_get_order($order_id);
    $create_booking = false;

    // check if booking service products in order
    if ($order->get_item_count() > 0) {
        foreach ($order->get_items() as $item_id => $item_data) {
            $product = $item_data->get_product();
            $product_type = $product->get_type();
            $specific_product_type = 'schedule_booking_product';
            if ($product_type === $specific_product_type) {
                $create_booking = true;
                break;
            }
        }

        //if booking products were added on checkout
        $booking_data = new Ik_Schedule_Booking();
        $location_id = $booking_data->get_session_location_id();
        if($create_booking && isset($_SESSION['ik_sch_services_added']) && isset($_SESSION['ik_sch_date_selected']) && isset($_SESSION['ik_sch_time_selected']) && isset($location_id) ){

            // Get customer data
            $customer_email = $order->get_billing_email();
            $customer_phone = $order->get_billing_phone();

            $args = array(
                'branch_id'     => $location_id,
                'name'          => $order->get_billing_first_name(),
                'lastname'      => $order->get_billing_last_name(),
                'email'         => $customer_email,
                'phone'         => $customer_phone,
                'datetime'      => $_SESSION['ik_sch_date_selected'] . ' '.$_SESSION['ik_sch_time_selected'],
                'wc_order'      => $order_id,
            );

            //create booking order
            $booking_data->create_request($args);
        }

        //show order data from booking products if exist
        $booking = $booking_data->get_by_wc_order_id($order_id);
        $employee_data = ($booking->staff_id > 0) ? '<br />'.__('Employee:', 'ik_schedule_location') . ' '.$booking_data->staff->get_name($booking->staff_id) : '';

        if($booking){
            $time_booked = date('H:i:s', strtotime($booking->booking_date));
            echo '<h3>'.__( 'Booking Confirmation', 'ik_schedule_location').'</h3>';
            echo '<p>'.__( 'Date', 'ik_schedule_location').': '.$booking_data->available_days->convert_date_format($booking->booking_date).'<br />';
            echo __( 'Time', 'ik_schedule_location').': '.$booking_data->available_days->convert_time_format($time_booked).'<br />';
            echo __( 'Location', 'ik_schedule_location').': '.$booking_data->locations->get_location_name($booking->branch_id).$employee_data.'</p>';
        }
    }
}

//script to add calendar to select booking service schedule
add_action('wp_footer', 'ik_sch_book_cart_script_footer');
function ik_sch_book_cart_script_footer(){
	if (function_exists('is_cart')) {
		if(is_cart()){
            $booking_data = new Ik_Schedule_Booking();

            //if there's session data
            if(isset($_SESSION['ik_sch_services_added'])){
                //show calendar and Woocommerce booking service products

                // if there's a product booking service
                $has_schedule_booking_product = false;
                $cart = WC()->cart;
                foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                    $product = $cart_item['data'];
            
                    if ($product->is_type('schedule_booking_product')) {
                        $has_schedule_booking_product = true;
                        break;
                    }
                }
            
                // there are booking service products
                if ($has_schedule_booking_product) {
                    $booking_wc_calendar = $booking_data->get_booking_wc_calendar();
                    $booking_wc_button_to_location_url = $booking_data->get_booking_wc_button_to_location_page();
                ?>
                <style>
                .hide_arrow{
                    display: none;
                }
                .woocommerce-cart .et_pb_row {
                    width: 96%;
                }
                .woocommerce-cart-form{
                    background: #f7f7f7;
                    margin-bottom: 50px;
                }
                .woocommerce table.shop_table th, .woocommerce-cart-form .product-quantity, .woocommerce-cart-form .product-subtotal{
                    display: none! important;
                }
                #ik_sch_book_cart_calendar_select{
                    background: #fff;
                    padding: 14px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }
                .ik_sch_book_cart_calendar_select_month{
                    position: relative;
                }
                .ik_sch_book_cart_calendar_select_month_name{
                    text-align: center;
                    margin: 0 auto;			
                }
                .ik_sch_book_cart_calendar_select_month_arrow{
                    border: 0;
                    position: absolute;
                    top: -2px;
                }
                .ik_sch_book_cart_calendar_select_month_arrow.go-back{
                    left: 0;
                }
                .ik_sch_book_cart_calendar_select_month_arrow.go-forward{
                    right: 0;
                }
                .ik_sch_book_cart_calendar_select_schedule thead{
                    font-size: 13px;
                }
                .ik_sch_book_cart_calendar_select_schedule td{
                    text-align: center;
                    padding: 5px! important;;
                }
                .ik_sch_book_cart_calendar_select_schedule a {
                    color: #945d1a;
                    padding: 3px 6px;
                }
                .ik_sch_book_cart_calendar_select_schedule a.active_date{
                    background: #945d1a;
                    color: #fff! important;
                    padding: 6px;
                    border-radius: 50px;
                }
                .shop_table .delivery_time_text{
                    font-size: 14px;
                    font-style: italic;
                }
                .ik_sch_book_cart_calendar_select_schedule tfoot td{
                    padding: 24px 14px! important;
                }
                .ik_sch_book_dynamic_dates .ik_sch_book_cart_calendar_location{
                    padding: 0 27px;
                }
                .ik_sch_book_dynamic_dates button.btn{
                    background: #945d1a;
                    color: #fff;
                    padding: 9px;
                    margin: 27px 27px 0;
                }
                .ik_sch_book_dynamic_dates button.btn:hover{
                    opacity: 0.8;
                }
                .woocommerce-cart-form .add-more-location-services {
                    text-align: left;
                    background: transparent! important;
                    color: #945d1a! important;;
                    border: none;
                    padding: 7px;
                    width: 100%;
                }
                .available_times{
                    max-width: 300px;
                }
                .available_time_blocks ul{
                    padding: 0;
                    margin: 0;
                    max-height: 280px;
                    overflow: auto;
                }
                .available_time_blocks ul li{
                    margin: 3px 2px;
                    padding: 0px;
                    display: block;
                }
                .available_time_blocks ul li a{
                    display: flow-root;
                    color: #945d1a;
                    margin: 3px 2px;
                    padding: 3px;
                    text-align: center;
                    background: #f1f1f1;
                    color: #945d1a;
                    border-radius: 2px;
                }
                .available_time_blocks ul li.selected a{
                    color: #fff;
                    background: #945d1a;
                }
                .available_time_blocks ul li.selecting a {
                    animation: fade-in-out 3s infinite linear;
                }
                #ik_sch_book_cart_calendar_select .available_time_blocks_col{
                    padding: 0px 5%;
                }
                #ik_sch_book_cart_calendar_select .available_time_blocks_col.align-left{
                    float: left;
                }
                #ik_sch_book_cart_calendar_select .available_time_blocks_col.align-right{
                    float: right;
                }
                #staff_member_select{
                    width: 100%;
                    margin: 0 auto 25px;
                    display: block;
                    min-width: 300px;
                    padding: 9px;
                    max-width: 315px;
                    border-radius: 5px;
                }
                #main-content table.cart #add-more-modal_content td {
                    padding-top: 3px!important;
                    padding-bottom: 3px!important;
                }
                @keyframes fade-in-out {
                    0% {
                        opacity: 1;
                    }
                    50% {
                        opacity: 0.8;
                    }
                    100% {
                        opacity: 0.4;
                    }
                }
                #ik_sch_book_modal_dates .ik_sch_book_data_services_data {
                    display: inline-block;
                }
                #ik_sch_book_modal_dates .ik_sch_book_data_services_category_name, #ik_sch_book_modal_dates .ik_sch_book_data_services_from_value {
                    display: none;
                }
                #ik_sch_book_modal_dates .modal-header{
                    padding: 0 8px;
                }
                #ik_sch_book_modal_dates .ik_sch_field-wrap input {
                    width: 90%;
                    padding: 4px 12px;
                    font-size: 16px;
                    line-height: 1;
                }
                #ik_sch_book_modal_dates h5.modal_data_input {
                    font-size: 16px;
                    padding-bottom: 4px;
                }
                #ik_sch_book_modal_dates .ik_sch_field-wrap {
                    margin-bottom: 5px;
                }
                #ik_sch_book_modal_dates{
                    z-index: 999999999999999;
                }
                #ik_sch_book_modal_dates .modal-footer .btn-primary, #ik_sch_modalConfirmation button{
                    background: #945d1a;
                    border: 1px solid #945d1a;
                }  
                #ik_sch_book_modal_dates .modal-content {
                    transform: translateY(20%);
                    max-width: 1200px;
                    margin: 0 auto;
                }  
                #ik_sch_book_modal_dates .modal-body .container .row:first-child {
                    max-height: 370px;
                    overflow: auto;
                }
                #ik_sch_book_modal_dates .modal-header h4 {
                    font-size: 18px;
                    padding-top: 10px;
                    padding-left: 10px;
                    color: #945d1a;
                    text-align: center;
                    margin: 0 auto;
                }
                #ik_sch_book_modal_dates .modal-header .close {
                    border: 0 solid transparent;
                    border-radius: 50px;
                    line-height: 20px;
                    font-size: 25px;
                    padding: 6px 8px;
                }
                #ik_sch_book_modal_dates .ik_sch_book_modal_dates_services_modal_item{
                    box-shadow: inset 0 -1px 0 0 #f1f1f1;
                    padding: 7px 0px 7px 3px;
                }
                #ik_sch_book_modal_dates .ik_sch_book_modal_dates_services_name {
                    font-weight: 600;
                    color: #000;
                }
                #ik_sch_book_modal_dates .ik_sch_book_modal_dates_remove_service {
                    border: 0 solid transparent;
                    background: transparent;
                    background-color: transparent;
                    font-size: 20px;
                }
                #ik_sch_book_modal_dates .modal-footer {
                    justify-content: flex-start;
                }
                #ik_sch_book_modal_dates ::-webkit-scrollbar {
                    background-color: #f1f1f1;
                    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
                }
                #ik_sch_book_modal_dates ::-webkit-scrollbar-thumb {
                    background-color: #888;
                    border-radius: 5px;
                    transition: background-color 0.2s ease;
                    background-color: #888;
                    width: 10px;
                }
                #ik_sch_book_modal_dates ::-webkit-scrollbar-thumb:hover {
                    background-color: #555;
                }
                #ik_sch_book_modal_dates input.invalid_data {
                    border-color: red;
                    margin: 0;
                }
                #ik_sch_book_modal_dates {
                    display: block;
                    z-index: 9999999999;
                    width: 86%;
                    margin: 0 auto;
                    position: fixed;
                    top: 20px;
                    left: 7%;
                    height: 100%;
                }
                #ik_sch_book_overlay{
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.7);
                    z-index: 1;
                }
                #ik_sch_book_modal_dates .modal-body-section {
                    text-align: center;
                    max-height: 260px;
                    overflow-y: auto;
                }
                #ik_sch_book_modal_dates .modal-content {
                    transform: translateY(20%);
                    max-width: 1200px;
                    margin: 0 auto;
                    background-color: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.5);
                    overflow-y: auto;
                }
                #ik_sch_book_modal_dates .modal-body-section button{
                    background: #f1f1f1;
                    border-radius: 12px;
                    border: 1px solid #945d1a;
                    color: #945d1a;
                    padding: 3px 12px;
                    margin: 12px;
                }
                #ik_sch_book_cart_calendar_module .loader {
                    display: none;
                    border: 2px solid #f3f3f3;
                    border-top: 2px solid #3498db;
                    border-radius: 50%;
                    width: 15px;
                    height: 15px;
                    animation: lll_loading 1s linear infinite;
                    position: relative;
                    top: 3px;
                    left: 12px;
                }
                #ik_sch_book_modal_dates .modal-body-section:last-child {
                    margin-top: 42px;
                }
                #ik_sch_book_modal_dates .modal-body-section-times-btn{
                    padding-bottom: 30px;
                }
                @keyframes ik_sch_book_loading {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                @media(min-width: 980px){
                    .woocommerce-cart-form{
                        display: flex;
                        padding: 20px;
                    }
                    #staff_member_select{
                        margin: 0 auto 25px;
                    }
                    .ik_sch_book_cart_calendar_module_content table:first-child td{
                        padding: 12px! important;
                    }
                    #ik_sch_book_cart_calendar_module table:first-child td {
                        border-top: 1px solid #eee;
                        padding: 15px;
                    }
                    .woocommerce-cart-form #ik_sch_book_cart_calendar_module {
                        margin-right: 12px;
                        float: left;
                        width: 25%;
                        min-width: 365px;
                    }
                    .woocommerce-cart-form #ik_sch_book_cart_calendar_module h4{
                        margin: 0 auto;
                        display: block;
                        max-width: 314px;
                        font-size: 16px;
                    }
                    .woocommerce-cart-form .ik_sch_book_cart_calendar_module_content{
                        width: 25%! important;
                        min-width: 365px;
                    }
                    .woocommerce-cart-form .shop_table {
                        width: 70%! important;
                        float: left;
                        min-width: 300px;
                    }
                }
                @media(max-width: 980px){
                    .woocommerce-cart-form {
                        display: table;
                        padding: 12px! important;
                        margin: 0 auto;
                    }
                    #staff_member_select {
                        margin: 0 0 25px;
                    }
                    #ik_sch_book_cart_calendar_module td{
                        padding: 5px 0! important;
                    }
                    .woocommerce-cart-form #ik_sch_book_cart_calendar_module {
                        padding: 12px 7px;
                    }
                    .woocommerce table.shop_table_responsive tr td, .woocommerce-page table.shop_table_responsive tr td {
                        text-align: left!important;
                    }
                    #ik_sch_book_modal_dates {
                        margin-top: -90px !important;
                    }
                    #ik_sch_book_modal_dates .modal-body-section button {
                        font-size: 15px;
                        margin: 6px !important;
                    }
                }
                </style>
                <script>
                jQuery(document).ready(function($) {
                    var booking_wc_calendar = <?php echo json_encode($booking_wc_calendar); ?>;
                    var booking_wc_date = '';
                    var button_add_more = <?php echo json_encode($booking_wc_button_to_location_url); ?>;

                    function ik_sch_book_js_cancel_modal(){
                        const existing_ik_sch_book_overlay = document.querySelector("#ik_sch_book_overlay");
                        if (existing_ik_sch_book_overlay != null) {
                            existing_ik_sch_book_overlay.remove();
                        }    
                    }
                    
                    function ik_sch_book_show_calendar(){
                        jQuery('#ik_sch_book_cart_calendar_module').remove();
                        jQuery('#add-more-modal_content').remove();
                        jQuery('.woocommerce-cart-form').prepend(booking_wc_calendar);
                        jQuery('.shop_table .actions').parent().before(button_add_more);
                        booking_wc_date = jQuery('#ik_sch_book_cart_calendar_select tbody a.active_date').attr('data_id');
                        var ik_sch_book_select_datetime = document.querySelector('#ik_sch_book_cart_calendar_select_datetime');
                        var ik_sch_book_selected_datetime = document.querySelector('#ik_sch_book_cart_calendar_selected_datetime');
                        var ik_sch_book_cart_calendar_module = document.querySelector('#ik_sch_book_cart_calendar_module');
                        if(ik_sch_book_select_datetime != null){
                            ik_sch_book_select_datetime.addEventListener('click', function(event) {
                                event.preventDefault();
                                ik_sch_book_loader = ik_sch_book_cart_calendar_module.querySelector('.loader');
                                if(ik_sch_book_loader != null){
                                    ik_sch_book_loader.style = 'display:inline-block';
                                    ik_sch_book_select_datetime.style = 'padding-right:27px';
                                }
                                jQuery.ajax({
                                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                                    type: "POST",
                                    data: {
                                        action: "ik_sch_book_ajax_return_popup_datetimes",
                                    },
                                    success: function(response) {
                                        if(ik_sch_book_select_datetime != null){
                                            ik_sch_book_loader.style = 'display:none';
                                            ik_sch_book_select_datetime.style = '';
                                        }
                                        const existing_ik_sch_book_modal_dates = document.querySelector("#ik_sch_book_modal_dates");
                                        if (existing_ik_sch_book_modal_dates != null) {
                                            existing_ik_sch_book_modal_dates.remove();
                                        }                  
                                        ik_sch_book_js_cancel_modal();
                                        var ik_sch_book_screenHeight = parseInt(window.innerHeight)-162;
                                        var ik_sch_book_screen_top = (window.innerWidth > 750) ? 60 : 0;
                                        var ik_sch_book_calendar_popup_element = document.createElement("div");
                                        ik_sch_book_calendar_popup_element.id = "ik_sch_book_modal_dates";
                                        ik_sch_book_calendar_popup_element.innerHTML = response;               
                                        var ik_sch_book_overlay = document.createElement('div');
                                        ik_sch_book_overlay.id = 'ik_sch_book_overlay';
                                        document.body.appendChild(ik_sch_book_overlay);                         
                                        document.body.appendChild(ik_sch_book_calendar_popup_element);
                                        var ik_sch_book_calendar_close_popup = ik_sch_book_calendar_popup_element.querySelector(".close");
                                        ik_sch_book_calendar_close_popup.onclick = function() {
                                            ik_sch_book_calendar_popup_element.style.display = "none";
                                            ik_sch_book_js_cancel_modal();
                                        };
                                    }
                                });
                            });
                        }
                    }
                    ik_sch_book_show_calendar();
                    jQuery(document.body).trigger('wc_fragment_refresh');

                    jQuery(document.body).on('updated_cart_totals', function() {
                        ik_sch_book_show_calendar();
                    });
                    jQuery('.woocommerce-cart-form').on('click', '.available_time_blocks li a', function(e) {
                        e.preventDefault();
                        let time_block = jQuery(this);
                        let time_data = time_block.attr('data_id');
                        jQuery('.available_time_blocks li').each(function() {
                            jQuery(this).removeClass('selected');
                        });
                        time_block.parent().addClass('selecting');
                        
                        jQuery.ajax({
                            url: '<?php echo admin_url("admin-ajax.php"); ?>',
                            type: "POST",
                            data: {
                                action: "ik_sch_book_ajax_wc_select_time",
                                time_booking: time_data
                            },
                            success: function(response) {
                                time_block.parent().removeClass('selecting');
                                time_block.parent().addClass('selected');
                            }
                        });     
                    });
                    jQuery('.woocommerce-cart-form').on('click', '#ik_sch_book_cart_calendar_select tbody a', function(e) {
                        e.preventDefault();
                        let day_selected_element = jQuery(this);
                        let day_selected = day_selected_element.attr('data_id');;
                        jQuery('#ik_sch_book_cart_calendar_select tbody a').each(function() {
                            jQuery(this).removeClass('active_date');
                        });
                        day_selected_element.addClass('active_date');
                        jQuery.ajax({
                            url: '<?php echo admin_url("admin-ajax.php"); ?>',
                            type: "POST",
                            data: {
                                action: "ik_sch_book_ajax_wc_select_day",
                                date_booking: day_selected,
                            },
                            success: function(response) {
                                booking_wc_calendar = response;
                                ik_sch_book_show_calendar();
                            }
                        });   
                    });
                    jQuery('.woocommerce-cart-form').on('click', '.ik_sch_book_cart_calendar_select_month_arrow', function(e) {
                        e.preventDefault();
                        let week_selected_element = jQuery(this);
                        let move_dir = 'next';
                        if (week_selected_element.hasClass('go-back')) {
                            move_dir = 'prev';
                        }

                        jQuery.ajax({
                            url: '<?php echo admin_url("admin-ajax.php"); ?>',
                            type: "POST",
                            data: {
                                action: "ik_sch_book_ajax_wc_select_week",
                                move_dir: move_dir,
                            },
                            success: function(response) {
                                booking_wc_calendar = response;
                                ik_sch_book_show_calendar();
                            }
                        });   
                    }); 
                    jQuery('.woocommerce-cart-form').on('change', '#staff_member_select', function(e) {
                        e.preventDefault();
                        let staff_member_id = parseInt(jQuery(this).val());

                        jQuery.ajax({
                            url: '<?php echo admin_url("admin-ajax.php"); ?>',
                            type: "POST",
                            data: {
                                action: "ik_sch_book_ajax_update_staff_member",
                                staff_member_id: staff_member_id,
                            },
                            success: function(response) {
                                booking_wc_calendar = response;
                                ik_sch_book_show_calendar();
                            }
                        });   
                    }); 
                });
                </script>
                <?php
                }                
            } else {
                //delete booking service products
                $booking_data->delete_services_from_cart();
            }

		}
	}
    //if checkout and booking products I change the booking product text to Make an Appointment
    if (function_exists('is_checkout')) {
        if(is_checkout()){
            if(isset($_SESSION['ik_sch_services_added'])){
            ?>
            <script>
				setInterval(function(){
                	jQuery('#place_order').text('<?php echo __( 'Make an Appointment', 'ik_schedule_location') ?>');
				}, 100);           
            </script>
            <?php
            }
        }
    }
}

?>