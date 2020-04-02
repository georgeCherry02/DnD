<div id='popup_message' class='dark_green_border main_green message_container'>
    <div class='close_button'>
        <i class='fas fa-times-circle dark_green_text' onclick="close_popup_message()"></i>
    </div>
    <?php include_once $file_root . "messages/" . $popup_message_name . ".php"; ?>
</div>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>