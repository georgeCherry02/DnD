<div class='dark_green_border temp_green message_container'>
    <div id='login_container' style='display: inline-block;'>
        <h4 class='dark_green_text'>Login to access these features!</h4>
        <form action='actions/login.php' method='POST'>
            <input type='hidden' value='<?php echo $page_title; ?>' name='location'/>
            <input type='text' name='user' placeholder='Username' class='light_green dark_green_text dark_green_border'/>
            <input type='password' name='pass' placeholder='Password' class='light_green dark_green_text dark_green_border'/>
            <input type='submit' value='Login' class='light_green dark_green_text dark_green_border'/>
        </form>
        <h5 class='dark_green_text'>Don't have an account? Sign up <a onclick='toggle_login_form()'>here</a></h5>
    </div>
    <div id='signup_container' style='display: none;'>
        <h4 class='dark_green_text'>Login to access these features!</h4>
        <form action='actions/signup.php' method='POST'>
            <input type='text' name='email' placeholder='Email' class='light_green dark_green_text dark_green_border'/>
            <input type='text' name='user' placeholder='Username' class='light_green dark_green_text dark_green_border'/>
            <input type='submit' value='Register' class='light_green dark_green_text dark_green_border'/>
        </form>
        <h5 class='dark_green_text'>Already have an account? Login <a onclick='toggle_login_form()'>here</a></h5>
    </div>
</div>
<script src='./scripts/menus.js'></script>
<link rel='stylesheet' href='css/login_message.css' type='text/css'/>