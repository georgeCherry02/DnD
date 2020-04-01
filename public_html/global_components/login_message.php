<div class='dark_green_border temp_green message_container'>
    <h4 class='dark_green_text'>Login to access these features!</h4>
    <form action='actions/login.php' method='POST'>
        <input type='hidden' value='<?php echo $page_title; ?>' name='location'/>
        <input type='text' name='user' placeholder='Username' class='light_green dark_green_text dark_green_border'/>
        <input type='text' name='pass' placeholder='Password' class='light_green dark_green_text dark_green_border'/>
        <input type='submit' value='Login' class='light_green dark_green_text dark_green_border'/>
    </form>
</div>
<link rel='stylesheet' href='css/login_message.css' type='text/css'/>