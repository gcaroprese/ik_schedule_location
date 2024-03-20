<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$locations_data = new Ik_Schedule_Locations();

//I check if I load add or edit location view
$location_edit = false;
if(isset($_GET['location_id'])){
	$location_id = absint($_GET['location_id']);

	$location = $locations_data->get_location($location_id);

	if($location){
		//checks update submitted from edit template to run updates
		$locations_data->update_location($location_id);
		$location_edit = true;
	}
}

if($location_edit){
	include('edit_location.php');
} else {

	//Create new locations
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		if (isset($_POST['new_location'])){
			$new_locations = $_POST['new_location'];

			if (is_array($new_locations)){
		
				foreach( $new_locations as $new_location ) {
		
					if (isset($new_location)) {
						$new_location = sanitize_text_field($new_location);
						$new_location = str_replace('\\', '', $new_location);
					
						$locations_data->add_location($new_location);
				
					}
				}
			}
		}

	}
	?>
	<div id="ik_sch_book_add_records">
		<h1><?php echo __( 'Edit Locations', 'ik_schedule_location'); ?></h1>
		<form action="" method="post" enctype="multipart/form-data" autocomplete="no">
			<div class="ik_sch_book_fields">
				<ul>
					<li>
						<input type="text" required name="new_location[]" placeholder="<?php echo __( 'Location', 'ik_schedule_location'); ?>" /> <a href="#" class="ik_sch_book_delete_field button"><?php echo __( 'Delete', 'ik_schedule_location'); ?></a>
					</li>
				</ul>
				<a href="#" class="button button-primary" id="ik_sch_book_add_fields"><?php echo __( 'Add Locations', 'ik_schedule_location'); ?></a>
			</div>
			<input type="submit" class="button button-primary" value="<?php echo __( 'Save', 'ik_schedule_location'); ?>" />
		</form>
	</div>
	<div id ="ik_sch_book_records_existing">
	<?php
		$list_locations = $locations_data->get_list_locations_wrapper_backend();
		if($list_locations){
			echo $list_locations;
		}
	?>
	</div>
	<script>

		// Add fields
		jQuery(document).on('click', '#ik_sch_book_add_fields', function(){
			jQuery('#ik_sch_book_add_data .ik_sch_book_data_fields ul').append('<li><input type="text" required name="new_location[]" placeholder="<?php echo __( 'Location', 'ik_schedule_location'); ?>" /> <a href="#" class="ik_sch_book_delete_field button"><?php echo __( 'Delete', 'ik_schedule_location'); ?></a></li>');
			return false;
		});
		
		// Delete fields to create
		jQuery(document).on('click', '#ik_sch_book_add_data .ik_sch_book_data_fields .ik_sch_book_delete_field', function(){
			jQuery(this).parent().remove();
			return false;
		});
		jQuery("#ik_sch_book_existing th .select_all").on( "click", function() {
		if (jQuery(this).attr('selected') != 'selected'){
			jQuery('#ik_sch_book_existing th .select_all').prop('checked', true);
			jQuery('#ik_sch_book_existing th .select_all').attr('checked', 'checked');
			jQuery('#ik_sch_book_existing tbody tr').each(function() {
				jQuery(this).find('.select_data').prop('checked', true);
				jQuery(this).find('.select_data').attr('checked', 'checked');
			});        
			jQuery(this).attr('selected', 'selected');
		} else {
			jQuery('#ik_sch_book_existing th .select_all').prop('checked', false);
			jQuery('#ik_sch_book_existing th .select_all').removeAttr('checked');
			jQuery('#ik_sch_book_existing tbody tr').each(function() {
				jQuery(this).find('.select_data').prop('checked', false);
				jQuery(this).find('.select_data').removeAttr('checked');
			});   
			jQuery(this).removeAttr('selected');
			
		}
	});
	jQuery("#ik_sch_book_existing td .select_data").on( "click", function() {
		jQuery('#ik_sch_book_existing th .select_all').prop('checked', false);
		jQuery('#ik_sch_book_existing th .select_all').removeAttr('checked');
		jQuery('#ik_sch_book_existing th .select_all').removeAttr('selected');
	});

	jQuery('#ik_sch_book_edit_location_times').on('click', '#ik_sch_book_add_fields',function(){
		jQuery('#ik_sch_book_edit_location_times .ik_sch_book_blocked_fields ul').append('<li><input type="text" name="dateblock[]" value="" placeholder="Date *" class="ik_sch_field ik_sch-text datepicker hasDatepicker" id="dp1673612371795"> <input type="text" name="timeblock_start[]" value="" placeholder="Time Start" class="ik_sch_field ik_sch-text timepicker"> <input type="text" name="timeblock_end[]" value="" placeholder="Time End" class="ik_sch_field ik_sch-text timepicker"> <label><span>Full Day</span> <input type="checkbox" name="block_all[]" value="1" class="ik_sch_field_block_fullday"></label> <a href="#" class="ik_sch_book_delete_field button">Delete</a></li>');

		return false;
	});

	// Delete fields to create
	jQuery(document).on('click', '#ik_sch_book_add_data .ik_sch_book_data_fields .ik_sch_book_delete_field', function(){
		jQuery(this).parent().remove();
		return false;
	});
	
	jQuery("#ik_sch_book_existing .ik_sch_book_button_delete").on( "click", function() {
		var confirmar = confirm('<?php echo __( 'Are you sure to delete?', 'ik_schedule_location'); ?>');
		if (confirmar == true) {
			jQuery('#ik_sch_book_existing tbody tr').each(function() {
			var elemento_borrar = jQuery(this).parent();
				if (jQuery(this).find('.select_data').prop('checked') == true){
					
					var registro_tr = jQuery(this);
					var iddata = registro_tr.attr('iddata');
					
					var data = {
						action: "ik_sch_book_ajax_delete_location",
						"post_type": "post",
						"iddata": iddata,
					};  
		
					jQuery.post( ajaxurl, data, function(response) {
						if (response){
							registro_tr.fadeOut(700);
							registro_tr.remove();
						}        
					});
				}
			});
		}
		jQuery('#ik_sch_book_existing th .select_all').attr('selected', 'no');
		jQuery('#ik_sch_book_existing th .select_all').prop('checked', false);
		jQuery('#ik_sch_book_existing th .select_all').removeAttr('checked');
		return false;
	});
	
	jQuery('#ik_sch_book_existing').on('click','td .ik_sch_book_button_delete_location', function(e){
		e.preventDefault();
		var confirmar =confirm('<?php echo __( 'Are you sure to delete?', 'ik_schedule_location'); ?>');
		if (confirmar == true) {
			var iddata = jQuery(this).parent().attr('iddata');
			var registro_tr = jQuery('#ik_sch_book_existing tbody').find('tr[iddata='+iddata+']');
			
			var data = {
				action: "ik_sch_book_ajax_delete_location",
				"post_type": "post",
				"iddata": iddata,
			};  
	
			jQuery.post( ajaxurl, data, function(response) {
				if (response){
					registro_tr.fadeOut(700);
					registro_tr.remove();
				}        
			});
		}
	});

	jQuery('#ik_sch_book_add_records').on('click', '#ik_sch_book_add_fields',function(){
		jQuery('#ik_sch_book_add_records .ik_sch_book_fields ul').append('<li><input type="text" required="" name="new_location[]" placeholder="Location"> <a href="#" class="ik_sch_book_delete_field button">Delete</a></li>');

		return false;
	});

	// Delete fields to create
	jQuery('#ik_sch_book_add_records').on('click', '.ik_sch_book_delete_field', function(){
		jQuery(this).parent().remove();
		return false;
	});

	jQuery('#ik_sch_book_records_existing').on('click','#searchbutton_location', function(e){
		e.preventDefault();

		var search = jQuery('#tag-search-input').val();
		var urlnow = window.location.href;
		
		window.location.href = urlnow+'&search='+search;

	});

	jQuery('#ik_sch_book_existing').on('click','th.worder', function(e){
		e.preventDefault();

		var order = jQuery(this).attr('order');
		var urlnow = window.location.href;
		
		if (order != undefined){
			if (jQuery(this).hasClass('desc')){
				var direc = 'asc';
			} else {
				var direc = 'desc';
			}
			if (order == 'name'){
				var orderby = '&orderby=name&orderdir='+direc;
				window.location.href = urlnow+orderby;
			} else {
				var orderby = '&orderby=id&orderdir='+direc;
				window.location.href = urlnow+orderby;
			}
		}

	});			
	</script>
	<?php
}

?>