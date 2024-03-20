<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/* 
Book - Schedule Locatons Menus
Created: 01/08/2022
Last Update: 05/11/2023
Author: Gabriel Caroprese
*/

//Menu constants
define('IK_SCH_MENU_VAL_CONFIG', "ik_sch_bookconfig_page");
define('IK_SCH_MENU_VAL_ENTRIES', "ik_sch_book_main");
define('IK_SCH_MENU_VAL_LOCATIONS', "ik_sch_book_locations");
define('IK_SCH_MENU_VAL_SERVICES', "ik_sch_book_services");
define('IK_SCH_MENU_VAL_CALENDAR', "ik_sch_book_calendar");
define('IK_SCH_MENU_VAL_STAFF', "ik_sch_book_staff");

// I add menus on WP-admin considering user roles
function ik_sch_book_wpmenu(){
    $user = wp_get_current_user();

    // Check if the user has the ik_sch_bookings_admin role
    if (in_array('ik_sch_bookings_admin', $user->roles)) {
        $user_role_access = 'read_ik_sch_book_main_menu';
  
    } else {
        $user_role_access = 'manage_options';
    }

    $user = wp_get_current_user();

    // Add main menu item with submenus
    add_menu_page(__( 'Booking', 'ik_schedule_location'), __( 'Booking', 'ik_schedule_location'), $user_role_access, 'ik_sch_book_main', false, plugin_dir_url( __DIR__ ) . 'img/plugin-icon.png' );
    add_submenu_page('ik_sch_book_main', __( 'Requests', 'ik_schedule_location'), __( 'Requests', 'ik_schedule_location'), $user_role_access, IK_SCH_MENU_VAL_ENTRIES, 'ik_sch_bookentries_page', 2 );
    add_submenu_page('ik_sch_book_main', __( 'Locations', 'ik_schedule_location'), __( 'Locations', 'ik_schedule_location'), $user_role_access, IK_SCH_MENU_VAL_LOCATIONS, 'ik_sch_book_locations', 3 );
    add_submenu_page('ik_sch_book_main', __( 'Services', 'ik_schedule_location'), __( 'Services', 'ik_schedule_location'), $user_role_access, IK_SCH_MENU_VAL_SERVICES, 'ik_sch_book_services', 4 );
    add_submenu_page('ik_sch_book_main', __( 'Calendar', 'ik_schedule_location'), __( 'Calendar', 'ik_schedule_location'), $user_role_access, IK_SCH_MENU_VAL_CALENDAR, 'ik_sch_book_calendar', 5 );
    add_submenu_page('ik_sch_book_main', __( 'Staff', 'ik_schedule_location'), __( 'Staff', 'ik_schedule_location'), $user_role_access, IK_SCH_MENU_VAL_STAFF, 'ik_sch_book_staff', 6 );
    
    // Check if the user has the ik_sch_bookings_admin role
    if (!in_array('ik_sch_bookings_admin', $user->roles)) {
        add_submenu_page('ik_sch_book_main', __( 'Config', 'ik_schedule_location'), __( 'Config', 'ik_schedule_location'), $user_role_access, IK_SCH_MENU_VAL_CONFIG, 'ik_sch_bookconfig_page', 6 );
    }
}
add_action('admin_menu', 'ik_sch_book_wpmenu', 999);


//Function to add menu content
function ik_sch_bookconfig_page(){
    include (IK_SCH_BOOK_LOCATION_DIR.'/templates/config.php');
}
function ik_sch_bookentries_page(){
    include (IK_SCH_BOOK_LOCATION_DIR.'/templates/entries.php');
}
function ik_sch_book_locations(){
    include (IK_SCH_BOOK_LOCATION_DIR.'/templates/locations.php');
}
function ik_sch_book_services(){
    include (IK_SCH_BOOK_LOCATION_DIR.'/templates/services.php');
}
function ik_sch_book_calendar(){
    include (IK_SCH_BOOK_LOCATION_DIR.'/templates/calendar.php');
}
function ik_sch_book_staff(){
    include (IK_SCH_BOOK_LOCATION_DIR.'/templates/staff.php');
}

?>