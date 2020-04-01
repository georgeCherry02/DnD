<div class='dark_green_border temp_green message_container'>
    <div id='login_container' style='display: none;'>
        <h4 class='dark_green_text'>Login to access these features!</h4>
        <form action='<?php echo $file_root; ?>actions/login.php' method='POST'>
            <input id='login_username' type='text' name='user' placeholder='Username' class='light_green dark_green_text dark_green_border'/>
            <input id='login_password' type='password' name='pass' placeholder='Password' class='light_green dark_green_text dark_green_border'/>
            <input type='submit' value='Login' class='light_green dark_green_text dark_green_border'/>
        </form>
        <h5 class='dark_green_text'>Don't have an account? Sign up <a onclick='toggle_login_form()'>here</a></h5>
    </div>
    <div id='signup_container' style='display: inline-block;'>
        <h4 class='dark_green_text'>Sign up now!</h4>
        <form action='<?php echo $file_root; ?>signup.php' method='POST' onsubmit="return validate_signup()">
            <input id='signup_email' type='text' name='email' placeholder='Email' class='light_green dark_green_text dark_green_border'/>
            <input id='signup_username' type='text' name='user' placeholder='Username' class='light_green dark_green_text dark_green_border'/>
            <input type='submit' value='Register' class='light_green dark_green_text dark_green_border'/>
        </form>
        <h5 class='dark_green_text'>Already have an account? Login <a onclick='toggle_login_form()'>here</a></h5>
    </div>
</div>
<script src='<?php echo $file_root; ?>scripts/form_verification.js'></script>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>