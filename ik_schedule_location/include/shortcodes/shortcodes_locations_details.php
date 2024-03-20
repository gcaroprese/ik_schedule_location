<?php
/*

Book - Schedule Locatons - Shortcode for details about location
Created: 07/04/2023
Last Update: 07/04/2023
Author: Gabriel Caroprese

*/

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

//function shortcode to show details about address and open times
function ik_sch_book_show_location_details($atts = [], $content = null, $tag = ''){
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $data_location = shortcode_atts(
        [
            'id' => '0',
            'today' => 'false',
            'show_hours' => 'true'
        ], $atts, $tag);

    $location_id = absint($data_location['id']);
    $show_today = ($data_location['today'] == 'true') ? true : false;
    $show_hours = ($data_location['show_hours'] == 'true') ? true : false;

    $location_data = new Ik_Schedule_Locations();

    $location = $location_data->get_location($location_id);

    //if enabled to show hours and open times are available
    $opentimes = ($show_hours) ? $location_data->get_open_times($location_id, $show_today) : false;

    if($location){
        $output = '
        <style>
		@media (min-width: 767px){
			.ik_sch_book_location_details {
				padding: 26px;
			}
			.ik_sch_book_location_details {
				min-width: 410px;
			}
		}
		@media (max-width: 767px){
			.ik_sch_book_location_details {
				padding: 26px 0;
			}
			.ik_sch_book_locations_details{
				margin: 0 auto;
			}
			.ik_sch_book_locations_details .container {
				width: 90%;
			}
			.ik_sch_book_locations_details .container {
				width: 90%! important;
				padding-left: 20px;
				padding-right: 20px;
            }
            .ik_sch_book_locations_details .container .col-md-6 {
                padding: 0;
            }
		}
        .ik_sch_book_location_details_opentimes_wrapper, .ik_sch_book_location_details_address_wrapper, ik_sch_book_location_details_opentimes_wrapper{
            display: flex;
			max-width: 90%;
        }
		.ik_sch_book_location_details_address, .ik_sch_book_location_details_opentimes, .ik_sch_book_location_details{
			display: block;
			max-width: 90%;
		}
        .ik_sch_working_openingtimes_details {
            display: block;
        }
        .ik_sch_book_location_details i {
            margin-right: 5px;
            float: left;
            position: relative;
            top: 7px;
            width: 20px;
        }
        .ik_sch_book_location_details {
            margin-bottom: 35px;
            color: #333;
			max-width: 92%;
			overflow: hidden;
        }
        .ik_sch_book_location_details a{
            color: #333;
        }
        </style>
        <div class="ik_sch_book_location_details">';
    
    
        if($location->address != ''){
            $output .= '<div class="ik_sch_book_location_details_address_wrapper">
                <i class="fas fa-map-marker-alt"></i> 
                <div class="ik_sch_book_location_details_address">
                '.nl2br($location->address).'
                </div>
            </div>';
        }
    
    
        //$opentimes = 'Geöffnet Heute: 09:00 - 17:30';
        if($opentimes !== false){
    
    
            $output .= '<div class="ik_sch_book_location_details_opentimes_wrapper">
                <i class="fas fa-clock"></i>
                <div class="ik_sch_book_location_details_opentimes">'.$opentimes.'</div>
            </div>';
        }
        $output .= '</div>';
    } else {
        $output = '';
    }

    return $output;
    
}
add_shortcode('IK_SHOW_LOCATION_DETAILS', 'ik_sch_book_show_location_details');


//function shortcode to show details about address and open times for all locations
function ik_sch_book_show_all_locations_details($atts = [], $content = null, $tag = ''){
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $data_location = shortcode_atts(
        [
            'show_hours' => 'true'
        ], $atts, $tag);

    $show_hours = ($data_location['show_hours'] == 'true') ? true : false;

    $location_data = new Ik_Schedule_Locations();

    $locations = $location_data->get_locations();

    if($locations){
        wp_enqueue_style( 'bootstrap.min.css-css', IK_SCH_BOOK_LOCATION_PUBLIC . 'css/bootstrap.css' );
        $output = '
        <style>
		@media (min-width: 767px){
			.ik_sch_book_location_details {
				padding: 26px;
			}
			.ik_sch_book_location_details {
				min-width: 410px;
			}
		}
		@media (max-width: 767px){
			.ik_sch_book_location_details {
				padding: 26px 0;
			}
			.ik_sch_book_locations_details{
				margin: 0 auto;
			}
			.ik_sch_book_locations_details .container {
				width: 90%;
			}
			.ik_sch_book_locations_details .container {
				width: 90%! important;
				padding-left: 20px;
				padding-right: 20px;
            }
            .ik_sch_book_locations_details .container .col-md-6 {
                padding: 0;
            }
		}
        .ik_sch_book_location_details_opentimes_wrapper, .ik_sch_book_location_details_address_wrapper, ik_sch_book_location_details_opentimes_wrapper{
            display: flex;
			max-width: 100%;
        }
		.ik_sch_book_location_details_address, .ik_sch_book_location_details_opentimes, .ik_sch_book_location_details{
			display: block;
			max-width: 90%;
		}
        .ik_sch_working_openingtimes_details {
            display: block;
        }
        .ik_sch_book_location_details i {
            margin-right: 5px;
            float: left;
            position: relative;
            top: 7px;
            width: 20px;
        }
        .ik_sch_book_location_details {
			max-width: 92%;
            margin-bottom: 35px;
            color: #333;
			overflow: hidden;
        }
        .ik_sch_book_location_details a{
            color: #333;
        }
        </style> 
        <div class="ik_sch_book_locations_details">';
    
        foreach ($locations as $location){
            $output .= '<div class="container">
            <div class="row">
                <div class="col-md-6">   
                    <div class="ik_sch_book_location_details">
                        <h4>'.$location->name.'</h4>';
            //if enabled to show hours and open times are available
            $opentimes = ($show_hours) ? $location_data->get_open_times($location->id, $show_today) : false;

            if($location->address != ''){

                if($location->map_link != ''){
                    $location_address = '<a href="'.$location->map_link.'" target="_blank">'.nl2br($location->address).'</a>';
                } else {
                    $location_address = nl2br($location->address);
                }
                $output .= '<div class="ik_sch_book_location_details_address_wrapper">
                                <i class="fas fa-map-marker-alt"></i> 
                                <div class="ik_sch_book_location_details_address">
                                '.$location_address.'
                                </div>
                            </div>';
                    if($opentimes !== false){

                        $output .= '<div class="ik_sch_book_location_details_opentimes_wrapper">
                            <i class="fas fa-clock"></i>
                            <div class="ik_sch_book_location_details_opentimes">'.$opentimes.'</div>
                        </div>';
                    }
            }
            $output .= '</div></div>';
            $output .= '<div class="col-md-6">';
            if($location->map_embed_src != ''){

                $output .= '<iframe src="'.$location->map_embed_src.'" width="100%" height="260" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';   
            } 
            $output .= '</div>
                    </div>
            </div>';
        }
        $output .= '</div>';
    } else {
        $output = '';
    }

    return $output;
    
}
add_shortcode('IK_SHOW_ALL_LOCATIONS_DETAILS', 'ik_sch_book_show_all_locations_details');
?>