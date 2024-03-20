<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

//edit locations
$availability = new Ik_Schedule_Available_Days();
$format_datetime = $availability->get_datetime_format();
$format_date = $format_datetime['format_date'];
$format_time = $format_datetime['format_time'];
$showampm = ($format_time == '24') ? '' : ' p';
$show24hs = ($format_time == '24') ? 'true' : 'false';
$update = $availability->update_availability($location_id);

$location_name = (isset($_POST['location_name'])) ? sanitize_text_field($_POST['location_name']) : $location->name;
$location_address = (isset($_POST['location_address'])) ? sanitize_textarea_field($_POST['location_address']) : $location->address;
$location_link = (isset($_POST['location_link'])) ? sanitize_url($_POST['location_link']) : $location->map_link;
$location_embed_src = (isset($_POST['location_embed_src'])) ? sanitize_url($_POST['location_embed_src']) : $location->map_embed_src;

ik_sch_book_location_enqueue_scripts();

?>
<link href="<?php echo IK_SCH_BOOK_LOCATION_PUBLIC; ?>/css/fontawesome/css/all.css" rel="stylesheet">
<div id="ik_sch_book_edit_section">
    <h1><?php echo __( 'Edit Location: ', 'ik_schedule_location'); ?></h1>
	<div id="ik_sch_book_edit_data_fields">
    	<h2><?php echo $location_name; ?> <span class="ik_sch_book_edit_name ik_sch_book_edit_data dashicons dashicons-edit"></span></h2>
    	<div class="ik_sch_book_edit_location_address"><i class="fas fa-map-marker-alt"></i><?php echo $location_address; ?> <span class="ik_sch_book_edit_location_address ik_sch_book_edit_data dashicons dashicons-edit"></span></div>
	</div>
	<div id="ik_sch_book_edit_location_times">
		<div id="ik_sch_book_edit_location_times_main">
            <h3><?php echo __( 'Available Hours', 'ik_schedule_location'); ?></h3>
			<form action="" method="post" enctype="multipart/form-data" autocomplete="no" id="ik_sch_book_css_openinghours_form">
				<div id="ik_sch_book_css_openinghours">
					<div class="ik_sch_book_css_openingtimes">
                        <input type="hidden" name="availability_form" value="1">
						<?php echo $availability->list_available_times($location_id ); ?>
                        <input type="submit" class="button button-primary" value="<?php echo __( 'Save', 'ik_schedule_location'); ?>" />
					</div>
				</div>
			</form>
		</div>
		<div id="ik_sch_book_block_datetimes">
			<h3><?php echo __( 'Block Dates', 'ik_schedule_location'); ?></h3>
			<form action="" method="post" enctype="multipart/form-data" autocomplete="no">
				<div class="ik_sch_book_blocked_datetimes">
					<div class="ik_sch_book_blocked_fields">
						<ul>
							<?php

							$blocked_datetimes = $availability->get_blocked_datetimes($location_id);

							$countDateblock = 0;

							if($blocked_datetimes){
								foreach($blocked_datetimes as $key => $blocked_datetime){

									$checkedAllDay = ($blocked_datetime['allday'] == true) ? 'checked enabled="yes"' : '';
									$disable_inputs = ($checkedAllDay == 'checked enabled="yes"') ? 'disabled' : '';

									echo '
									<li>
										<input type="text" name="dateblock['.$countDateblock.']" value="'.$blocked_datetime['date'].'" placeholder="'. __( 'Date', 'ik_schedule_location').' *" class="ik_sch_field ik_sch_field_dateblock ik_sch-text datepicker">
										<input type="text" name="timeblock_start['.$countDateblock.']" value="'.$blocked_datetime['fromtime'].'" placeholder="'. __( 'Time Start', 'ik_schedule_location').'" '.$disable_inputs.' class="ik_sch_field ik_sch_field_timeblock_start ik_sch-text timepicker"> 
										<input type="text" name="timeblock_end['.$countDateblock.']" value="'.$blocked_datetime['totime'].'" placeholder="'. __( 'Time End', 'ik_schedule_location').'" '.$disable_inputs.' class="ik_sch_field ik_sch_field_timeblock_end ik_sch-text timepicker"> 
										<label class="ik_sch_book_fullday_label">
											<span>'.__( 'Full Day', 'ik_schedule_location').'</span>
											<input type="checkbox" '.$checkedAllDay.' name="block_all['.$countDateblock.']" value="1" class="ik_sch_field_block_fullday">
										</label>
										<a href="#" class="ik_sch_book_delete_field button">'. __( 'Delete', 'ik_schedule_location').'</a>
									</li>';
	
									$countDateblock = $countDateblock + 1;
	
								}								
							} else {
							?>
							<li>
								<input type="text" name="dateblock[<?php echo $countDateblock; ?>]" value="" placeholder="Date *" class="ik_sch_field ik_sch_field_dateblock ik_sch-text datepicker">
								<input type="text" name="timeblock_start[<?php echo $countDateblock; ?>]" value="" placeholder="Time Start" class="ik_sch_field ik_sch_field_timeblock_start ik_sch-text timepicker"> 
								<input type="text" name="timeblock_end[<?php echo $countDateblock; ?>]" value="" placeholder="Time End" class="ik_sch_field ik_sch_field_timeblock_end ik_sch-text timepicker"> 
								<label class="ik_sch_book_fullday_label">
									<span><?php echo __( 'Full Day', 'ik_schedule_location'); ?></span>
									<input type="checkbox" name="block_all[<?php echo $countDateblock; ?>]" value="1" class="ik_sch_field_block_fullday">
								</label>
								<a href="#" class="ik_sch_book_delete_field button"><?php echo __( 'Delete', 'ik_schedule_location'); ?></a>
							</li>
							<?php
							}
							?>
						</ul>
						<a href="#" class="button button-primary" id="ik_sch_book_add_fields"><?php echo __( 'Add Blocked dates', 'ik_schedule_location'); ?></a>
					</div>
					<input type="submit" class="button button-primary" value="<?php echo __( 'Save', 'ik_schedule_location'); ?>" />
				</div>
			</form>
		</div>
		<div id="ik_sch_book_location_services_wrapper">
			<div class="ik_sch_book_location_services">
				<h3><?php echo __( 'Services Delivered', 'ik_schedule_location'); ?></h3>
				<form action="" method="post" enctype="multipart/form-data" autocomplete="no">
					<div class="ik_sch_book_services_delivered">
						<div class="ik_sch_book_services_delivered_fields">
							<ul>
								<?php
								$location_services = $availability->get_location_services($location_id);

								if($location_services){
									foreach($location_services as $location_service){
										$custom_price = number_format( floatval($location_service['custom_price']), 2, '.', ''  );

										if($custom_price == '-1.00'){
											$custom_price_value = '';
											$disabled_checkbox = 'disabled';
											$check_checkbox = 'checked';
										} else {
											$custom_price_value = 'value="'.$custom_price.'"';
											$disabled_checkbox = '';
											$check_checkbox = '';
										}

										echo '
										<li>
											'.$availability->get_location_services_select($location_service['id']).'
											<label class="ik_sch_book_price ik_sch_field_sub_input_label">
												<input type="number" name="price" '.$disabled_checkbox.' '.$custom_price_value.' step="0.01" min="0" placeholder="'.__( 'Price', 'ik_schedule_location').'" class="ik_sch_field ik_sch_field_price"> 
												<span class="ik_sch_field_sub_input">'.__( 'Value Only', 'ik_schedule_location').'</span>
											</label>
											<label class="ik_sch_book_price">
												<span>'.__( 'Custom Price', 'ik_schedule_location').'</span>
												<input type="hidden" name="custom_price[]" class="ik_sch_field_custom_price" value="'.$custom_price.'"> 
												<input type="checkbox" '.$check_checkbox.' value="1" class="ik_sch_field_check_custom_price">
											</label>
											<a href="#" class="ik_sch_book_delete_field button">'. __( 'Delete', 'ik_schedule_location').'</a>
										</li>';
									}								
								} else {
								?>
								<li>
									<?php echo $availability->get_location_services_select(); ?>
									<label class="ik_sch_book_price ik_sch_field_sub_input_label">
										<input type="number" name="price" disabled step="0.01" min="0" placeholder="<?php echo __( 'Price', 'ik_schedule_location'); ?>" class="ik_sch_field ik_sch_field_price"> 
										<span class="ik_sch_field_sub_input"><?php echo __( 'Value Only', 'ik_schedule_location'); ?></span>
									</label>
									<label class="ik_sch_book_price">
										<span><?php echo __( 'Custom Price', 'ik_schedule_location'); ?></span>
										<input type="hidden" name="custom_price[]" class="ik_sch_field_custom_price" value="-1"> 
										<input type="checkbox" checked value="1" class="ik_sch_field_check_custom_price">
									</label>
									<a href="#" class="ik_sch_book_delete_field button"><?php echo __( 'Delete', 'ik_schedule_location'); ?></a>								
								</li>
								<?php
								}
								?>
							</ul>
							<a href="#" class="button button-primary" id="ik_sch_book_add_service_fields"><?php echo __( 'Add Service', 'ik_schedule_location'); ?></a>
						</div>
						<input type="submit" class="button button-primary" value="<?php echo __( 'Save', 'ik_schedule_location'); ?>" />
					</div>
				</form>
			</div>
		</div>
	</div>
    <a class="button" href="<?php echo $locations_data->location_admin_url; ?>"><?php echo __( 'Return', 'ik_schedule_location'); ?></a>
</div>
<script>
	jQuery(document).ready(function ($) {
		
		var format_date = '<?php echo $format_date; ?>';
		var format_timepmam = '<?php echo $showampm; ?>';
		var show24hs = '<?php echo $show24hs; ?>';
		
		jQuery("#ik_sch_book_block_datetimes .datepicker").datepicker({
			minDate: 0, // ban dates in the past
			format: format_date,
			dateFormat: format_date,
		});
		jQuery('#ik_sch_book_block_datetimes .timepicker').timepicker({
			'interval': 5,
			'lang': 'decimal',
			'show2400': show24hs,
			'timeFormat': 'HH:mm'+format_timepmam,
			'showDuration': true
		});

		jQuery('#ik_sch_book_css_openinghours .timepicker').timepicker({
			'interval': 5,
			'lang': 'decimal',
			'show2400': show24hs,
			'timeFormat': 'HH:mm'+format_timepmam,
			'showDuration': true
		});
		jQuery('#ik_sch_book_css_openinghours .ik_sch_book_additionalhours input').timepicker({
				'interval': 5,
				'lang': 'decimal',
				'show2400': show24hs,
				'timeFormat': 'HH:mm'+format_timepmam,
				'showDuration': true
			});

		function validateTimeBlock() {
			const startInputs = document.querySelectorAll('.ik_sch_field_timeblock_start');
			const endInputs = document.querySelectorAll('.ik_sch_field_timeblock_end');
			const n = startInputs.length;
			for (let i = 0; i < n; i++) {
				const startInput = startInputs[i];
				const endInput = endInputs[i];
				if (startInput.value && endInput.value) {
					const startTime = new Date('1970-01-01T' + startInput.value + ':00');
					const endTime = new Date('1970-01-01T' + endInput.value + ':00');
					if (startTime >= endTime) {
						alert('The start time must be earlier than the end time.');
						endInput.value = '';
					}
				}
			}
		}

		// Get all end time input fields with class "ik_sch_field_timeblock_end"
		const endTimeInputs = document.querySelectorAll('.ik_sch_field_timeblock_end');

		// Attach a change event listener to each end time input field
		endTimeInputs.forEach(endTimeInput => {
			endTimeInput.addEventListener('change', function() {
				validateTimeBlock();
			});
		});

		function ik_sch_book_js_timeFormat(inputval1,inputval2) {
			inputval1 = inputval1.replace(/(AM|PM)/, '').trim(); // elimina 'AM' o 'PM' y elimina espacios en blanco adicionales
			inputval2 = inputval2.replace(/(AM|PM)/, '').trim(); // elimina 'AM' o 'PM' y elimina espacios en blanco adicionales

			inputval1 = new Date(`2000-01-01T${inputval1}:00Z`);
			inputval2 = new Date(`2000-01-01T${inputval2}:00Z`);

			if (inputval1 >= inputval2) {
				return true;
			} else {
				return false;
			}
		
		}
		var decodeEntities = (function() {
			// this prevents any overhead from creating the object each time
			var element = document.createElement('div');

			function decodeHTMLEntities (str) {
				if(str && typeof str === 'string') {
				// strip script/html tags
				str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
				str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
				element.innerHTML = str;
				str = element.textContent;
				element.textContent = '';
				}

				return str;
			}

			return decodeHTMLEntities;
		});
		
		jQuery('#ik_sch_book_block_datetimes').on('blur', 'input.ik_sch_field_timeblock_end', function(){
			let CloseTime = jQuery(this);
			let OpenTime = jQuery(this).parent().find('input.ik_sch_field_timeblock_start');
			
			setTimeout(function(){
				let checkTimeIsLess = ik_sch_book_js_timeFormat(OpenTime.val(), CloseTime.val());

				if(checkTimeIsLess){
					CloseTime.val('');
				}

			}, 1500);
		});

		jQuery('.ik_sch_book_css_closeallday').on('click', 'input[type=checkbox]', function(){
			ik_sch_book_mark_close_allday(this);
		});

		jQuery('#ik_sch_book_edit_location_times').on('click', '.ik_sch_book_blocked_datetimes input[type=checkbox].ik_sch_field_block_fullday', function(){
			if (jQuery(this).attr('enabled') != 'yes'){
				jQuery(this).attr('enabled', 'yes');
				jQuery(this).parent().parent().find('.timepicker').prop('disabled', true);
			} else {
				jQuery(this).attr('enabled', 'no');
				jQuery(this).parent().parent().find('.timepicker').prop('disabled', false);
			}
		});

					
		// Delete fields to create
		jQuery(document).on('click', '#ik_sch_book_edit_location_times .ik_sch_book_delete_field', function(){
			jQuery(this).parent().remove();
			return false;
		});

		function ik_sch_book_mark_close_allday(element){
			if (jQuery(element).attr('enabled') != 'yes'){
				jQuery(element).attr('enabled', 'yes');
				jQuery(element).parent().parent().parent().parent().addClass('ik_sch_book_disable_fields');
				jQuery(element).parent().parent().parent().parent().find('input[type=text]').prop('disabled', true);
			} else {
				jQuery(element).attr('enabled', 'no');
				jQuery(element).parent().parent().parent().parent().removeClass('ik_sch_book_disable_fields');
				jQuery(element).parent().parent().parent().parent().find('input[type=text]').prop('disabled', false);
			}
		}
			
		jQuery('.ik_sch_book_css_columnhours').on('click', '.ik_sch_book_css_addhours', function(){
			ik_sch_book_add_day_row(this);
		});

		jQuery('#ik_sch_book_css_openinghours .ik_sch_book_css_closeallday input').each(function() {
			if(jQuery(this).is(':checked') || jQuery(this).prop('checked') || jQuery(this).attr('checked') == 'checked'){
				ik_sch_book_mark_close_allday(this);
			}
		});


		jQuery('#ik_sch_book_edit_location_times').on('click', '#ik_sch_book_add_fields',function(){
			let counter_blockdates = 0;
			jQuery('#ik_sch_book_edit_location_times .ik_sch_book_blocked_fields li').each(function() {
				jQuery(this).find('.ik_sch_field_dateblock').attr('name', 'dateblock['+counter_blockdates+']');
				jQuery(this).find('.ik_sch_field_timeblock_start').attr('name', 'timeblock_start['+counter_blockdates+']');
				jQuery(this).find('.ik_sch_field_timeblock_end').attr('name', 'timeblock_end['+counter_blockdates+']');
				jQuery(this).find('.ik_sch_field_block_fullday').attr('name', 'block_all['+counter_blockdates+']');	
				counter_blockdates = counter_blockdates + 1;			
			});
			jQuery('#ik_sch_book_edit_location_times .ik_sch_book_blocked_fields ul').append('<li><input type="text" name="dateblock['+counter_blockdates+']" value="" placeholder="Date *" class="ik_sch_field ik_sch_field_dateblock ik_sch-text datepicker"> <input type="text" name="timeblock_start['+counter_blockdates+']" value="" placeholder="Time Start" class="ik_sch_field ik_sch_field_timeblock_start ik_sch-text timepicker"> <input type="text" name="timeblock_end['+counter_blockdates+']" value="" placeholder="Time End" class="ik_sch_field ik_sch_field_timeblock_end ik_sch-text timepicker"> <label><span>Full Day</span> <input type="checkbox" name="block_all['+counter_blockdates+']" value="1" class="ik_sch_field_block_fullday"></label> <a href="#" class="ik_sch_book_delete_field button">Delete</a></li>');
			jQuery("#ik_sch_book_block_datetimes .datepicker").datepicker({
				minDate: 0, // ban dates in the past
				dateFormat: format_date,
				format: format_date,
			});
			jQuery('#ik_sch_book_block_datetimes .timepicker').timepicker({
				'interval': 5,
				'lang': 'decimal',
				'show2400': show24hs,
				'timeFormat': 'HH:mm'+format_timepmam,
				'showDuration': true
			});
			return false;
		});

		jQuery('#ik_sch_book_edit_location_times').on('click', '#ik_sch_book_add_service_fields',function(){
			jQuery('#ik_sch_book_edit_location_times .ik_sch_book_services_delivered_fields ul').append('<li><?php echo $availability->get_location_services_select(); ?> <label class="ik_sch_book_price ik_sch_field_sub_input_label"> <input type="number" name="price" disabled step="0.01" min="0" placeholder="<?php echo __( 'Price', 'ik_schedule_location'); ?>" class="ik_sch_field ik_sch_field_price"> <span class="ik_sch_field_sub_input"><?php echo __( 'Value Only', 'ik_schedule_location'); ?></span></label> <label class="ik_sch_book_price"><span><?php echo __( 'Custom Price', 'ik_schedule_location'); ?></span><input type="hidden" name="custom_price[]" class="ik_sch_field_custom_price" value="-1"> <input type="checkbox" checked value="1" class="ik_sch_field_check_custom_price"></label> <a href="#" class="ik_sch_book_delete_field button">Delete</a></li>');

			return false;
		});

		function ik_sch_book_add_day_row(element){
			var day_n = jQuery(element).parent().parent().attr('day_n');
			var data_type1 = jQuery(element).parent().parent().attr('data_type1');
			var data_type2 = jQuery(element).parent().parent().attr('data_type2');
			
			var row_input_count = (jQuery(element).parent().parent().find('.ik_sch_book_css_columnhours').length);
			jQuery(element).parent().parent().append('<div class="ik_sch_book_css_columnday"><span></span></div><div class="ik_sch_book_css_columnhours ik_sch_book_additionalhours"><div class="ik_sch_book_css_inputopen inputopen'+row_input_count+'"><input type="text" autocomplete="off" name="day'+day_n+data_type1+'[]" placeholder="00:00"><span class="time_separator">-</span> </div><div class="ik_sch_book_css_inputclose inputclose'+row_input_count+'"><input type="text" autocomplete="off" name="day'+day_n+data_type2+'[]" placeholder="00:00"></div><div class="ik_sch_book_css_deletehours"><span class="dashicons dashicons-trash"></span></div></div>');
			jQuery('#ik_sch_book_css_openinghours .ik_sch_book_additionalhours input').timepicker({
				'interval': 5,
				'lang': 'decimal',
				'show2400': show24hs,
				'timeFormat': 'HH:mm'+format_timepmam,
				'showDuration': true
			});
		}

		jQuery('.ik_sch_book_css_rowday').on('click', '.ik_sch_book_css_deletehours', function(){
			jQuery(this).parent().remove();
		});
		jQuery('#ik_sch_book_edit_section').on('click', '.ik_sch_book_edit_name', function(){
			let location_name = '<?php echo $location_name; ?>';
			jQuery('#ik_sch_book_edit_section h2').replaceWith('<form action="" method="post" enctype="multipart/form-data" autocomplete="no" id="ik_sch_book_field_edit"><input type="text" name="location_name" value="'+location_name+'" placeholder="Enter Name"><button type="submit" class="button button-primary"><?php echo __( 'Save', 'ik_schedule_location'); ?></button><button class="ik_sch_book_cancel_edit button"><?php echo __( 'Cancel', 'ik_schedule_location'); ?></button></form>');
			return false;
		});
		jQuery('#ik_sch_book_edit_section').on('click', '.ik_sch_book_edit_location_address', function(){
			let location_address = <?php echo json_encode($location_address); ?>;
			let location_link = '<?php echo $location_link; ?>';
			let location_embed_src = '<?php echo $location_embed_src; ?>';
			jQuery('#ik_sch_book_edit_section .ik_sch_book_edit_location_address').replaceWith('<form action="" method="post" enctype="multipart/form-data" autocomplete="no" id="ik_sch_book_field_edit"><label style=" display: block; margin: 15px 0; "><?php echo __( 'Address', 'ik_schedule_location'); ?><textarea style="display: block;width: 330px;max-width: 90%;" name="location_address">'+location_address+'</textarea></label><label style=" display: block; margin: 15px 0; "><?php echo __( 'Map Link', 'ik_schedule_location'); ?><input style="display: block;width: 330px;max-width: 90%;" type="url" name="location_link" value="'+location_link+'" /></label><label style=" display: block; margin: 15px 0; "><?php echo __( 'Map Embed SRC', 'ik_schedule_location'); ?><input style="display: block;width: 330px;max-width: 90%;" type="url" name="location_embed_src" value="'+location_embed_src+'" /></label><button type="submit" class="button button-primary"><?php echo __( 'Save', 'ik_schedule_location'); ?></button><button class="ik_sch_book_cancel_edit button"><?php echo __( 'Cancel', 'ik_schedule_location'); ?></button></form>');
			return false;
		}); 
		jQuery('#ik_sch_book_edit_section').on('click', '.ik_sch_book_cancel_edit', function(){
			jQuery('#ik_sch_book_edit_data_fields').replaceWith('<div id="ik_sch_book_edit_data_fields"><h2><?php echo $location_name; ?> <span class="ik_sch_book_edit_name dashicons dashicons-edit"></span></h2><div class="ik_sch_book_edit_location_address"><i class="fas fa-map-marker-alt"></i>'+location_address+' <span class="ik_sch_book_edit_location_address ik_sch_book_edit_data dashicons dashicons-edit"></span></div></div>');
			return false;
		});

		jQuery('.ik_sch_book_css_deliverytimes .ik_sch_book_css_closeallday input').each(function() {
			if (jQuery(this).prop("checked")) {
				ik_sch_book_mark_close_allday(this);
			}
		});

		jQuery('#ik_sch_book_location_services_wrapper').on('change', '.ik_sch_field_check_custom_price', function () {
			var priceField = jQuery(this).closest('li').find('.ik_sch_field_price');
			var priceHidden = jQuery(this).closest('li').find('.ik_sch_field_custom_price');

			if (jQuery(this).is(':checked')) {
				priceField.prop('disabled', true);
				priceHidden.val('-1');
			} else {
				priceField.prop('disabled', false);
				priceHidden.val(priceField.val());
			}
		});

		jQuery('#ik_sch_book_location_services_wrapper').on('change', '.ik_sch_field_price', function () {
			var priceField = jQuery(this);
			var priceHidden = jQuery(this).closest('li').find('.ik_sch_field_custom_price');

			priceHidden.val(priceField.val());
		});
	});	
 </script>