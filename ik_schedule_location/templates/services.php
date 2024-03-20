<?php
/*

Services Template
Created: 20/11/2022
Update: 12/01/2023
Author: Gabriel Caroprese

*/

$booking = new Ik_Schedule_Booking();
$services = new Ik_Schedule_Services();
$config = $booking->get_config();
$update_data = $booking->services->update();

wp_enqueue_script('jquery-min-ik-sch-book', IK_SCH_BOOK_LOCATION_PUBLIC . '/js/jquery.min.1-11-2.js', array(), '1.11.2', true );
wp_enqueue_script('jquery-ui-ik-sch-book', IK_SCH_BOOK_LOCATION_PUBLIC . '/js/jquery-ui.min.1-11-4.js', array(), '1.11.4', true );
wp_enqueue_script('jquery-ui-tabs', '', array('jquery-ui-core'));

$edit_service = (isset($_GET['edit_service'])) ? $booking->services->get_service(absint($_GET['edit_service'])) : false;

?>
<div id="ik_sch_book_content" class="wrap">
    <h1><?php echo __( 'Services', 'ik_schedule_location') ?></h1>
 
    <h2 class="nav-tab-wrapper">
        <a href="#tab-1" class="nav-tab nav-tab-active"><?php echo __( 'Edit Services', 'ik_schedule_location') ?></a>
        <a href="#tab-2" class="nav-tab"><?php echo __( 'Service Categories', 'ik_schedule_location') ?></a>
    </h2>
 
    <div id="tab-1" class="tab-content">
        <div id="ik_sch_book_add_records">
            <h2><?php echo __( 'Services', 'ik_schedule_location'); ?></h2>
            <?php if($edit_service == true && $update_data != $edit_service->id){ ?>
            <form action="" method="post" name="update_service" enctype="multipart/form-data" autocomplete="no">
                <div class="ik_sch_book_fields">
                    <h3><?php echo __( 'Edit Service', 'ik_schedule_location'); ?></h3>
                    <input type="hidden" name="service_id" value="<?php echo $edit_service->id; ?>" />
                    <label>
                        <h4><?php echo __( 'Service', 'ik_schedule_location') ?></h4>
                        <input type="text" required name="edit_service" placeholder="<?php echo __( 'Service', 'ik_schedule_location') ?>" value="<?php echo $edit_service->name; ?>">
                    </label>
                    <label>
                        <h4><?php echo __( 'Service Category', 'ik_schedule_location') ?></h4>
                        <?php echo $booking->services->select_service_cats($edit_service->cat_name); ?>
                    </label>
                    <label>
                        <h4><?php echo __( 'General Price (value only)', 'ik_schedule_location') ?></h4>
                        <input type="number" required name="general_price" placeholder="<?php echo __( 'Example: 9', 'ik_schedule_location') ?>" value="<?php echo $edit_service->price; ?>">
                    </label>
                    <label>
                        <h4><?php echo __( 'Currency', 'ik_schedule_location'); ?></h4>
                        <?php echo $booking->currency_select($edit_service->currency_id); ?>
                    </label>
                    <label>
                        <h4><?php echo __( 'Estimated Delivery Time (in minutes)', 'ik_schedule_location'); ?></h4>
                        <input type="number" required name="delivery_time" placeholder="<?php echo __( 'Example: 45', 'ik_schedule_location') ?>" value="<?php echo $edit_service->delivery_time; ?>">
                    </label>
                </div>
                <input type="submit" class="button button-primary" value="<?php echo __( 'Update Service', 'ik_schedule_location') ?>">
                <br /><br />
                <div>
                    <a href="<?php echo get_site_url().'/wp-admin/admin.php?page='.IK_SCH_MENU_VAL_SERVICES;  ?>" class="button"><?php echo __( 'Add New Service', 'ik_schedule_location') ?></a>
                </div>
            </form>
            <?php } else { ?>
            <form action="" method="post" name="new_service" enctype="multipart/form-data" autocomplete="no">
                <div class="ik_sch_book_fields">
                    <h3><?php echo __( 'Add New Service', 'ik_schedule_location'); ?></h3>
                    <label>
                        <h4><?php echo __( 'Service', 'ik_schedule_location') ?></h4>
                        <input type="text" required name="new_service" placeholder="<?php echo __( 'Service', 'ik_schedule_location') ?>">
                    </label>
                    <label>
                        <h4><?php echo __( 'Service Category', 'ik_schedule_location') ?></h4>
                        <?php echo $booking->services->select_service_cats(); ?>
                    </label>
                    <label>
                        <h4><?php echo __( 'General Price (value only)', 'ik_schedule_location') ?></h4>
                        <input type="number" required name="general_price" placeholder="<?php echo __( 'Example: 9', 'ik_schedule_location') ?>">
                    </label>
                    <label>
                        <h4><?php echo __( 'Currency', 'ik_schedule_location'); ?></h4>
                        <?php echo $booking->currency_select($config['currency']); ?>
                    </label>
                    <label>
                        <h4><?php echo __( 'Estimated Delivery Time (in minutes)', 'ik_schedule_location'); ?></h4>
                        <input type="number" required name="delivery_time" placeholder="<?php echo __( 'Example: 45', 'ik_schedule_location') ?>">
                    </label>
                </div>
                <input type="submit" class="button button-primary" value="<?php echo __( 'Add Service', 'ik_schedule_location') ?>">
            </form>
            <?php } ?>
        </div>
        <div id ="ik_sch_book_existing">
            <?php echo $services->show_services_backend(); ?>
        </div>
    </div>

    <div id="tab-2" class="tab-content hide">
        <form action="" id="ik_sch_book_fields_draggable" method="post" enctype="multipart/form-data" autocomplete="no">
            <div class="ik_sch_book_fields">
                <ul>
                    <?php
                    //Existing on demand products
                    $list_cats = $services->get_services_cats();
                    if(is_array($list_cats)){
                        foreach ($list_cats as $cat){
                            ?>
                            <li class="draggable-item">
                                <input type="text" required name="new_service_cat[]" placeholder="Service Category" value="<?php echo $cat; ?>" />
                                <a href="#" class="ik_sch_book_delete_field button"><?php echo __( 'Delete', 'ik_schedule_location') ?></a>
                            </li>
                            <?php
                        }
                    } else {
                        ?>
                        <li class="draggable-item">
                            <input type="text" required name="new_service_cat[]" placeholder="Service Category" />
                            <a href="#" class="ik_sch_book_delete_field button"><?php echo __( 'Delete', 'ik_schedule_location') ?></a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <a href="#" class="button button-primary" id="ik_sch_book_add_fields"><?php echo __( 'Add Categories', 'ik_schedule_location') ?></a>
            </div>
            <input type="submit" class="button button-primary" value="<?php echo __( 'Save', 'ik_schedule_location') ?>" />
        </form>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
        jQuery('a.nav-tab').on('click', function(){
            let tabID = jQuery(this).attr('href');
            jQuery('a.nav-tab').removeClass('nav-tab-active');
            jQuery(this).addClass('nav-tab-active');
            jQuery('.tab-content').addClass('hide');
            jQuery(tabID).removeClass('hide');

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
            jQuery(this).removeAttr('selected');
        });

        jQuery('#ik_sch_book_fields_draggable').on("click",".ik_sch_book_delete_field", function() {
            jQuery(this).parent().remove();
            return false;
        });

        jQuery("#ik_sch_book_fields_draggable #ik_sch_book_add_fields").on( "click", function() {
            jQuery('#ik_sch_book_fields_draggable .ik_sch_book_fields ul').append('<li class="draggable-item"> <input type="text" required="" name="new_service_cat[]" placeholder="Service Category"> <a href="#" class="ik_sch_book_delete_field button">Delete</a></li>');
            return false;
        });

        jQuery('#ik_sch_book_existing').on('click','th.orderitem', function(e){
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
                } else if (order == 'name'){
                    var orderby = '&orderby=name&orderdir='+direc;
                    window.location.href = urlnow+orderby;
                } else if (order == 'cat_name'){
                    var orderby = '&orderby=cat_name&orderdir='+direc;
                    window.location.href = urlnow+orderby;
                } else if (order == 'price'){
                    var orderby = '&orderby=price&orderdir='+direc;
                    window.location.href = urlnow+orderby;
                } else {
                    var orderby = '&orderby=id&orderdir='+direc;
                    window.location.href = urlnow+orderby;
                }
            }

        });
        
        init_draggable(jQuery('#ik_sch_book_fields_draggable .draggable-item'));

        jQuery('#ik_sch_book_fields_draggable ul').sortable({
            items: '.draggable-item',
            start: function(event, ui) {
                jQuery('#ik_sch_book_fields_draggable ul').sortable('enable');
            },
        });

        function init_draggable(widget) {
            widget.draggable({
                connectToSortable: '#ik_sch_book_fields_draggable ul',
                stack: '.draggable-item',
                revert: true,
                revertDuration: 200,
                start: function(event, ui) {
                    jQuery('#ik_sch_book_fields_draggable ul').sortable('disable');
                }
            });
        }

        jQuery("#ik_sch_book_existing .ik_sch_book_button_delete_bulk").on( "click", function() {
            var confirmar = confirm('<?php echo __( 'Are you sure to delete?', 'ik_schedule_location'); ?>');
            if (confirmar == true) {
                jQuery('#ik_sch_book_existing tbody tr').each(function() {
                var elemento_borrar = jQuery(this).parent();
                    if (jQuery(this).find('.select_data').prop('checked') == true){
                        
                        var registro_tr = jQuery(this);
                        var iddata = registro_tr.attr('iddata');
                        
                        var data = {
                            action: "ik_sch_book_ajax_delete_service",
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
                    action: "ik_sch_book_ajax_delete_service",
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