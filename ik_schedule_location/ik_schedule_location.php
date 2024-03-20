<?php
/*
Plugin Name: Book - Schedule Locations
Description: System to manage reservations for services in different locations. Use [IK_BOOKING_FORM] to show the form.
Version: 1.7.3
Author: Gabriel Caroprese
Author URI: https://mediaclick.ch/
Requires at least: 5.3
Requires PHP: 7.3
*/ 

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$ik_sch_book_locationDir = dirname( __FILE__ );
$ik_sch_book_locationPublicDir = plugin_dir_url(__FILE__ );
define( 'IK_SCH_BOOK_LOCATION_DIR', $ik_sch_book_locationDir);
define( 'IK_SCH_BOOK_LOCATION_PUBLIC', $ik_sch_book_locationPublicDir);

require_once($ik_sch_book_locationDir . '/include/init.php');
register_activation_hook( __FILE__, 'ik_sch_book_location_create_tables' );

//I add a text domain for translations
function ik_schedule_location_textdomain_init() {
    load_plugin_textdomain( 'ik_schedule_location', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'ik_schedule_location_textdomain_init' );

?>