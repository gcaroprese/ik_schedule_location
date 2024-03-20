<?php
/*

Book - Schedule Locations - Config
Created: 10/07/2023
Update:  12/03/2024
Author: Gabriel Caroprese

*/
if ( ! defined('ABSPATH')) exit('restricted access');

$booking_data = new Ik_Schedule_Booking();
$booking_data->update_config();


//variables

$booking_config = $booking_data->get_config();
$date_format = $booking_config['format_date'];
$format_time = $booking_config['format_time'];
$frame_time = $booking_config['time_frame'];
$prices_list_popup = $booking_config['prices_popup'];
$dates_month = $booking_config['dates_month'];
$limit_booking = $booking_config['limit_booking'];
$calendar_starts_monday = $booking_config['calendar_starts_monday'];
$limit_start_booking = $booking_config['limit_start_booking'];
$currency_id = $booking_config['currency'];
$acceptauto_enabled = $booking_config['accept_auto'];
$woocommerce_enabled = $booking_config['woocommerce'];
$staff_enabled = $booking_config['staff_enabled'];
$block_repeat_limit = $booking_config['block_repeat_limit'];
$recaptcha_config = $booking_config['recaptcha'];
$recaptchakey = ($recaptcha_config['key'] == true) ? $recaptcha_config['key'] : '';
$recaptchasecret = ($recaptcha_config['secret'] == true) ? $recaptcha_config['secret'] : '';
$recapchaEnabled = $recaptcha_config['enabled'];
$recapchaoptionData = $recaptcha_config['option'];
$email_sender = $booking_config['email_sender'];
$status_default = $booking_config['status_default'];
$statusPendingSelect = ($status_default == 0) ? 'selected' : '';
$statusConfirmedSelect = ($status_default == 1) ? 'selected' : '';

if ($acceptauto_enabled){
    $acceptauto = 'checked';
} else {
    $acceptauto = '';
}


if ($recaptchakey == false || $recaptchakey == NULL){
    $recaptchakey = '';
}
if ($recaptchasecret == false || $recaptchasecret == NULL){
    $recaptchasecret = '';
}
$recapchacheck = ($recapchaEnabled) ? 'checked' : '';
$woocommercecheck = ($woocommerce_enabled) ? 'checked' : '';
$staffcheck = ($staff_enabled) ? 'checked' : '';
$starts_monday = ($calendar_starts_monday) ? 'checked' : '';
$prices_list_popup = ($prices_list_popup) ? 'checked' : '';
$dates_month = ($dates_month) ? 'checked' : '';


$robotchecked = 'checked';
$invisiblechecked = '';
if ($recapchaoptionData == 'v3'){
    $robotchecked = '';
    $invisiblechecked = 'checked';
}

?>

<style>
.error, .updated, #setting-error-tgmpa{display: none! important;}
</style>
<div id="ik_sch_book_config">
    <h1>Config</h1>
    <form action="" method="post" id="ik_sch_book_config_form" enctype="multipart/form-data" autocomplete="no">
        <p>
            <label>
                <input type="checkbox" name="accept_auto" <?php echo $acceptauto; ?> value="1">
                <span><?php echo __( 'Accept bookings automatically.', 'ik_schedule_location'); ?></span>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="woocommerce_enabled" <?php echo $woocommercecheck; ?> value="1">
                <span><?php echo __( 'Services uploaded on Woocommerce.', 'ik_schedule_location'); ?></span>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="calendar_starts_monday" <?php echo $starts_monday; ?> value="1">
                <span><?php echo __( 'Week starts on Monday (leave it uncheck if starts on Sunday).', 'ik_schedule_location'); ?></span>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="prices_popup" <?php echo $prices_list_popup; ?> value="1">
                <span><?php echo __( 'Show price list as popup.', 'ik_schedule_location'); ?></span>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="dates_month" <?php echo $dates_month; ?> value="1">
                <span><?php echo __( 'Show dates as a month (for Woocommerce).', 'ik_schedule_location'); ?></span>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="staff_enabled" <?php echo $staffcheck; ?> value="1">
                <span><?php echo __( 'Staff Selection Enabled.', 'ik_schedule_location'); ?></span>
            </label>
        </p>
        <p>
            <label>
                <p><span><?php echo __( 'Booking limit per staff or time block (recommended: 1)', 'ik_schedule_location'); ?><br />
                <?php echo __( 'to prevent overbooking or allow multiple bookings for simultaneous appointments:', 'ik_schedule_location'); ?></span></p>
                <input type="number" name="block_repeat_limit" value="<?php echo $block_repeat_limit; ?>" />
            </label>
        </p>
        <p>
            <label>
                <span><?php echo __( 'Date Format:', 'ik_schedule_location'); ?></span>
                <?php echo $booking_data->date_format_select($date_format); ?>
            </label>
        </p>
        <p>
            <label>
                <span><?php echo __( 'Time Format:', 'ik_schedule_location'); ?></span>
                <?php echo $booking_data->time_format_select($format_time); ?>
            </label>
        </p>
        <p>
            <label>
                <span><?php echo __( 'Time Frame (minutes):', 'ik_schedule_location'); ?></span>
                <?php echo $booking_data->time_frame_select($frame_time); ?>
            </label>
        </p>
        <p>
            <label>
                <span><?php echo __( 'Hours gap to book:', 'ik_schedule_location'); ?></span>
                <?php echo $booking_data->booking_before_limit_select($limit_start_booking); ?>

            </label>
        </p>
        <p>
            <label>
                <span><?php echo __( 'Limit bookings to:', 'ik_schedule_location'); ?></span>
                <?php echo $booking_data->booking_limit_select($limit_booking); ?>

            </label>
        </p>
        <p>
            <label>
                <span><?php echo __( 'Email Sender:', 'ik_schedule_location'); ?></span>
                <input type="email" name="email_sender" value="<?php echo $email_sender; ?>" />
            </label>
        </p>
        <p>
            <label>
                <span><?php echo __( 'Default Status for bookings:', 'ik_schedule_location'); ?></span>
                <select name="status_default">
                    <option <?php echo $statusPendingSelect; ?> value="0"><?php echo __( 'Pending', 'ik_schedule_location'); ?></option>
                    <option <?php echo $statusConfirmedSelect; ?> value="1"><?php echo __( 'Confirmed', 'ik_schedule_location'); ?></option>
                </select>
            </label>
        </p>
        <p>
            <label>
                <span><?php echo __( 'Currency:', 'ik_schedule_location'); ?></span>
                <?php echo $booking_data->currency_select($currency_id); ?>
            </label>
        </p>
        <hr>
        <p><?php echo __( 'Create keys at', 'ik_schedule_location'); ?> <a href="https://www.google.com/recaptcha/admin" target="_blank">Google Recaptcha</a></p>
        <p>
            <label for="recaptcha-key">
                <span><?php echo __( 'Key', 'ik_schedule_location'); ?></span><br />
                <input type="text" name="recapkey" value="<?php echo $recaptchakey; ?>" />
            </label>
        </p>
        <p>
            <label for="recaptcha-secret-key">
                <span><?php echo __( 'Secret Key', 'ik_schedule_location'); ?></span><br />
                <input type="password" readonly="readonly" onfocus="this.removeAttribute('readonly');" name="recapseckey" value="<?php echo $recaptchasecret; ?>" />
            </label>
        </p>
        <p class="ik_recaptcha_radio_options">
            <label for="recaptcha-option-robot">
                <input type="radio" name="userecaptcha_option" value="v2" <?php echo $robotchecked; ?> /> <?php echo __( 'V2 - Not a Robot', 'ik_schedule_location'); ?>
            </label>
            <label for="recaptcha-option-invisible">
                <input type="radio" name="userecaptcha_option" value="v3" <?php echo $invisiblechecked; ?> /> <?php echo __( 'V3 - Invisible', 'ik_schedule_location'); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="userecaptcha" <?php echo $recapchacheck; ?> value="1">
                <span><?php echo __( 'Enable Recaptcha.', 'ik_schedule_location'); ?></span>
            </label>
        </p>
        <p>
            <input type="submit" value="<?php echo __( 'Save', 'ik_schedule_location'); ?>" class="button-primary">
        </p>
    </form>
</div>