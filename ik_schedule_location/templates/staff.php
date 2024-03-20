<?php
/*

Staff Template
Created: 02/11/2022
Update: 09/11/2023
Author: Gabriel Caroprese

*/

$booking = new Ik_Schedule_Booking();
$staff = $booking->staff;
$update_data = $staff->update();
$edit_staff = (isset($_GET['staff_id'])) ? $staff->get_by_id(absint($_GET['staff_id'])) : false;

?>
<div id="ik_sch_book_content" class="wrap">
    <h1><?php echo __( 'Staff', 'ik_schedule_location') ?></h1>

    <div id="ik_sch_book_add_records">
        <?php if($edit_staff == true && $update_data != $edit_staff->id){ ?>
        <form action="" method="post" name="update_staff" enctype="multipart/form-data" autocomplete="no">
            <div class="ik_sch_book_fields">
                <h3><?php echo __( 'Edit Staff', 'ik_schedule_location'); ?></h3>
                <input type="hidden" name="staff_id" value="<?php echo $edit_staff->id; ?>" />
                <label>
                    <h4><?php echo __( 'Staff Member Name', 'ik_schedule_location') ?></h4>
                    <input type="text" required name="edit_display_name" placeholder="<?php echo __( 'Display Name', 'ik_schedule_location') ?>" value="<?php echo $edit_staff->name; ?>">
                </label>
                <label>
                    <h4><?php echo __( 'User Associated', 'ik_schedule_location') ?></h4>
                    <?php echo $staff->select_username($edit_staff->user_id); ?>
                </label>
                <label>
                    <h4><?php echo __( 'Location', 'ik_schedule_location') ?></h4>
                    <?php echo $booking->locations->get_location_select($edit_staff->location_id); ?>
                </label>
            </div>
            <input type="submit" class="button button-primary" value="<?php echo __( 'Update Staff Member', 'ik_schedule_location') ?>">
            <br /><br />
            <div>
                <a href="<?php echo $staff->get_admin_url();  ?>" class="button"><?php echo __( 'Add New Staff', 'ik_schedule_location') ?></a>
            </div>
        </form>
        <?php } else { ?>
        <form action="" method="post" name="new_staff" enctype="multipart/form-data" autocomplete="no">
            <div class="ik_sch_book_fields">
                <h3><?php echo __( 'Add New Staff', 'ik_schedule_location'); ?></h3>
                <label>
                    <h4><?php echo __( 'Staff Member Name', 'ik_schedule_location') ?></h4>
                    <input type="text" required name="new_display_name" placeholder="<?php echo __( 'Display Name', 'ik_schedule_location') ?>">
                </label>
                <label>
                    <h4><?php echo __( 'User Associated', 'ik_schedule_location') ?></h4>
                    <?php echo $staff->select_username(); ?>
                </label>
                <label>
                    <h4><?php echo __( 'Location', 'ik_schedule_location') ?></h4>
                    <?php echo $booking->locations->get_location_select(); ?>
                </label>
            </div>
            <input type="submit" class="button button-primary" value="<?php echo __( 'Add Staff', 'ik_schedule_location') ?>">
        </form>
        <?php } ?>
    </div>
    <div id ="ik_sch_book_existing" class="staff">
        <?php echo $staff->get_list_wrapper_backend(); ?>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
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
            jQuery(this).removeAttr('selected');
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
                if (order == 'id'){
                    var orderby = '&orderby=id&orderdir='+direc;
                    window.location.href = urlnow+orderby;
                } else if (order == 'display_name'){
                    var orderby = '&orderby=display_name&orderdir='+direc;
                    window.location.href = urlnow+orderby;
                } else if (order == 'location_id'){
                    var orderby = '&orderby=location_id&orderdir='+direc;
                    window.location.href = urlnow+orderby;
                } else {
                    var orderby = '&orderby=id&orderdir='+direc;
                    window.location.href = urlnow+orderby;
                }
            }

        });

        jQuery("#ik_sch_book_existing .ik_sch_book_button_delete_bulk").on( "click", function() {
            var confirmar = confirm('<?php echo __( 'Are you sure to delete?', 'ik_schedule_location'); ?>');
            if (confirmar == true) {
                jQuery('#ik_sch_book_existing tbody tr').each(function() {
                var elemento_borrar = jQuery(this).parent();
                    if (jQuery(this).find('.select_data').prop('checked') == true){
                        
                        var registro_tr = jQuery(this);
                        var iddata = registro_tr.attr('iddata');
                        
                        var data = {
                            action: "ik_sch_book_ajax_delete_staff",
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
        jQuery('#ik_sch_book_existing').on('click','#searchbutton', function(e){
            e.preventDefault();
            
            var search_value = jQuery('#tag-search-input').val();
            var urlnow = window.location.href;
            window.location.href = urlnow+"&search="+search_value;
        });
        jQuery('#ik_sch_book_existing').on('click','td .ik_sch_book_button_delete', function(e){
            e.preventDefault();
            var confirmar =confirm('<?php echo __( 'Are you sure to delete?', 'ik_schedule_location'); ?>');
            if (confirmar == true) {
                var iddata = jQuery(this).parent().attr('iddata');
                var registro_tr = jQuery('#ik_sch_book_existing tbody').find('tr[iddata='+iddata+']');
                
                var data = {
                    action: "ik_sch_book_ajax_delete_staff",
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
    });
</script>
<?php
?>