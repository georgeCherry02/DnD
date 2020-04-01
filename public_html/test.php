<?php  
    $file_location = "root";
    $page_title = "Test";

    include_once "../inc/base.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";
?>
<div class='dark_green_border temp_green message_container'>
    <h4>There was a server error on the first attempt.</h4>
    <h4>Please try again!</h4>
    <form action="" method="POST" onsubmit="return validate_password_choice()">
        <input type="hidden" name="user" value="<?php echo $_POST["user"]; ?>"/>
        <input id="password_choice_field" type="password" name="pass" placeholder="Password" class='light_green dark_green_text dark_green_border'/>
        <input id="confirm_password_choice_field" type="password" name="confirm_pass" placeholder="Confirm Password" class='light_green dark_green_text dark_green_border'/>
        <input type="submit" value="Continue" class='light_green dark_green_text dark_green_border'/>
    </form>
</div>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>
<script src='<?php echo $file_root; ?>scripts/form_verification.js'></script>
<?php
    include_once "./global_components/footer.php";
?>