<div class='grey_border light_background message_container'>
    <div id='login_container' style='display: none;'>
        <h4 class='grey_text'>Login to access these features!</h4>
        <form action='<?php echo $file_root; ?>actions/login.php' method='POST'>
            <input id='login_username' type='text' name='user' placeholder='Username' class='white_background grey_text grey_border'/>
            <input id='login_password' type='password' name='pass' placeholder='Password' class='white_background grey_text grey_border'/>
            <input type='submit' value='Login' class='white_background highlight_text grey_border'/>
        </form>
        <h5 class='grey_text'>Don't have an account? Sign up <a onclick='toggle_login_form()'>here</a></h5>
    </div>
    <div id='signup_container' style='display: inline-block;'>
        <h4 class='grey_text'>Sign up now!</h4>
        <form action='<?php echo $file_root; ?>signup.php' method='POST' onsubmit="return validate_signup()">
            <input id='signup_email' type='text' name='email' placeholder='Email' class='white_background grey_text grey_border'/>
            <input id='signup_username' type='text' name='user' placeholder='Username' class='white_background grey_text grey_border'/>
            <input type='submit' value='Register' class='white_background highlight_text grey_border'/>
        </form>
        <h5 class='grey_text'>Already have an account? Login <a onclick='toggle_login_form()'>here</a></h5>
    </div>
</div>
<script src='<?php echo $file_root; ?>scripts/form_verification.js'></script>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>