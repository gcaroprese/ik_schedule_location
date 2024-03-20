<?php
/* 
Book - Schedule Locatons Init Functions
Created: 10/08/2022
Last Update: 09/11/2023
Author: Gabriel Caroprese
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// function to start session to save data about booking data
add_action('init', 'ik_sch_book_location_session');
function ik_sch_book_location_session() {
    if (!session_id()) {
        session_start();
    }
}

//I add style and scripts from plugin backend
function ik_sch_book_location_add_css_js() {
	wp_register_style( 'ik_sch_book_location_css', IK_SCH_BOOK_LOCATION_PUBLIC . 'css/stylesheet.css', false, '1.1.5', 'all' );
	wp_enqueue_style('ik_sch_book_location_css');
	if ( ! wp_script_is( 'jquery-ui-datepicker', 'enqueued' )) {
        wp_enqueue_script( 'jquery-ui-datepicker' );
    }
	if ( ! wp_script_is( 'timepicker', 'enqueued' )) {
		wp_enqueue_script('timepicker', IK_SCH_BOOK_LOCATION_PUBLIC . 'js/jquery.timepicker.min.js', array(), '1.1.1', true );
	}
	if( ( ! wp_style_is( 'jquery-ui-css', 'queue' ) ) && ( ! wp_style_is( 'jquery-ui-css', 'done' ) ) ) {
		wp_enqueue_style( 'jquery-ui-css', IK_SCH_BOOK_LOCATION_PUBLIC . 'css/jquery-ui.css' );
	}
	if ( ! wp_script_is( 'bootstrap', 'enqueued' )) {
		wp_enqueue_script('bootstrap-js', IK_SCH_BOOK_LOCATION_PUBLIC . 'js/bootstrap.min.js', array(), '3.3.5', true );
	}
}
add_action( 'admin_enqueue_scripts', 'ik_sch_book_location_add_css_js' );

// Add scripts and make sure boostrap, jQuery, timepicker and datepicker is added
function ik_sch_book_location_enqueue_scripts() {
    if ( ! wp_script_is( 'jquery', 'enqueued' )) {
        wp_enqueue_script( 'jquery' );	
    }
    if ( ! wp_script_is( 'jquery-ui-datepicker', 'enqueued' )) {
        wp_enqueue_script( 'jquery-ui-datepicker' );
    }
    if ( ! wp_script_is( 'timepicker', 'enqueued' )) {
		wp_enqueue_script('timepicker', IK_SCH_BOOK_LOCATION_PUBLIC . 'js/jquery.timepicker.min.js', array(), '1.1.1', true );
	}
	if( ( ! wp_style_is( 'jquery-ui-css', 'queue' ) ) && ( ! wp_style_is( 'jquery-ui-css', 'done' ) ) ) {
		wp_enqueue_style( 'jquery-ui-css', IK_SCH_BOOK_LOCATION_PUBLIC . 'css/jquery-ui.css' );
	}
	if( ( ! wp_style_is( 'bootstrap', 'queue' ) ) && ( ! wp_style_is( 'bootstrap', 'done' ) ) ) {
		wp_enqueue_style( 'bootstrap.min.css-css', IK_SCH_BOOK_LOCATION_PUBLIC . 'css/bootstrap.css' );
	}
    if ( ! wp_script_is( 'bootstrap', 'enqueued' )) {
		wp_enqueue_script('bootstrap-js', IK_SCH_BOOK_LOCATION_PUBLIC . 'js/bootstrap.min.js', array(), '3.3.5', true );
	}
	if( ( ! wp_style_is( 'fontawesome', 'queue' ) ) && ( ! wp_style_is( 'fontawesome', 'done' ) ) ) {
		wp_enqueue_style( 'fontawesome-css', IK_SCH_BOOK_LOCATION_PUBLIC . 'css/fontawesome/css/all.css' );
	}
	if( ( ! wp_style_is( 'timepicker', 'queue' ) ) && ( ! wp_style_is( 'timepicker', 'done' ) ) ) {
		wp_enqueue_style( 'timepicker-css', IK_SCH_BOOK_LOCATION_PUBLIC . 'css/jquery.timepicker.min.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'ik_sch_book_location_enqueue_scripts' );

// Add a custom user role to manage bookings
function ik_sch_book_add_custom_user_role() {
    add_role('ik_sch_bookings_admin', __( 'Bookings Admin', 'ik_schedule_location'), array(
        'read_ik_sch_book_main_menu' => true,
        'read_user_profile' => true,
		'level_0' => true,
		'edit_user' => get_current_user_id(), // Allow editing own user profile
		'read' => true, // Allow access to the dashboard
    ));
}
add_action('init', 'ik_sch_book_add_custom_user_role');

//Hide Woocommerce menus for user role ik_sch_bookings_admin
function ik_sch_book_hide_menus_custom_user_role() {
    if (current_user_can('ik_sch_bookings_admin') && class_exists('WooCommerce')) {
        remove_menu_page('woocommerce');
        remove_submenu_page('woocommerce', 'wc-settings');
        remove_submenu_page('woocommerce', 'wc-addons');
        remove_submenu_page('woocommerce', 'wc-status');
        remove_submenu_page('woocommerce', 'wc-reports');
        remove_submenu_page('woocommerce', 'wc-status');
        remove_submenu_page('woocommerce', 'wc-support');
        remove_submenu_page('woocommerce', 'wc-addons');
		remove_menu_page('wc-admin', 'analytics-overview');
		remove_menu_page('wc-admin', 'marketing');
        remove_menu_page('edit.php?post_type=product');
        remove_submenu_page('woocommerce', 'wc-reports');
    }
}
function ik_sch_book_hide_menus_custom_user_role_script() {
    if (current_user_can('ik_sch_bookings_admin') && class_exists('WooCommerce')) {
        ?>
		<style>
			#toplevel_page_woocommerce-marketing, #toplevel_page_wc-admin-path--analytics-overview,
			#toplevel_page_wc-reports, #wp-admin-bar-view-store{
				display: none! important;
			}
		</style>	
        <script>
            jQuery(document).ready(function($) {
                jQuery('#toplevel_page_woocommerce-marketing').remove();
                jQuery('#toplevel_page_wc-admin-path--analytics-overview').remove();
                jQuery('#toplevel_page_wc-reports').remove();
            });
        </script>
        <?php
    }
}
add_action('admin_menu', 'ik_sch_book_hide_menus_custom_user_role');
add_action('admin_head', 'ik_sch_book_hide_menus_custom_user_role_script');


/*
//permissions for Woocommerce for booking admin
function ik_sch_book_woocommerce_permissions_to_role() {
    if (class_exists('WooCommerce')) {
        $ik_sch_bookings_admin = get_role('ik_sch_bookings_admin');

        if ($ik_sch_bookings_admin) {
            $ik_sch_bookings_admin->add_cap('manage_woocommerce');
            $ik_sch_bookings_admin->add_cap('view_woocommerce_reports');
            $ik_sch_bookings_admin->add_cap('edit_products');
            $ik_sch_bookings_admin->add_cap('edit_shop_order');
        }
    }
}
add_action('admin_init', 'ik_sch_book_woocommerce_permissions_to_role');
*/

//Redirect users with role ik_sch_bookings_admin to booking admin panel
function ik_sch_book_admin_redirect( $user_login, $user ) {
    // Check if the user has the 'ik_sch_bookings_admin' role
    if (in_array('ik_sch_bookings_admin', $user->roles)) {
        // Redirect to the desired page
        wp_safe_redirect(admin_url('admin.php?page=ik_sch_book_main'));
        exit;
    }
}
add_action('wp_login', 'ik_sch_book_admin_redirect', 10, 2);


//to validate booking admin access
function ik_sch_book_user_permissions() {

	$user = wp_get_current_user();
	$has_access = (in_array('ik_sch_bookings_admin', $user->roles) || is_admin()) ? true : false;

    return $has_access;
}

//Required includes
require_once(IK_SCH_BOOK_LOCATION_DIR . '/include/menus.php');
require_once(IK_SCH_BOOK_LOCATION_DIR . '/include/classes/class.schedulelocations.php');
require_once(IK_SCH_BOOK_LOCATION_DIR . '/include/classes/class.bookingstaff.php');
require_once(IK_SCH_BOOK_LOCATION_DIR . '/include/classes/class.scheduleavailabledays.php');
require_once(IK_SCH_BOOK_LOCATION_DIR . '/include/classes/class.scheduleservices.php');
require_once(IK_SCH_BOOK_LOCATION_DIR . '/include/classes/class.schedulebooking.php');
require_once(IK_SCH_BOOK_LOCATION_DIR . '/include/ajax_functions.php');
require_once(IK_SCH_BOOK_LOCATION_DIR . '/include/woocommerce.php');
require_once(IK_SCH_BOOK_LOCATION_DIR . '/include/shortcodes/form_shortcode.php');
require_once(IK_SCH_BOOK_LOCATION_DIR . '/include/shortcodes/book_location_shortcode.php');
require_once(IK_SCH_BOOK_LOCATION_DIR . '/include/shortcodes/shortcodes_locations_details.php');

//function to create tables in DB
function ik_sch_book_location_create_tables() {
	$bookings = new Ik_Schedule_Booking();
	$bookings->create_db_tables();
}

?>