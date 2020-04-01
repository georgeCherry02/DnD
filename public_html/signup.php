<?php
    $file_location =    "root";
    $page_title    =    "Verification";

    include_once "../inc/base.php";
    include_once "../inc/classes/UserAdmin.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";

    if (isset($_POST["email"])) {
        // Insert new user into database and email user verification email
        $status = UserAdmin::create_account();
        // Display verification message
        switch($status[0]) {
            case 0:
?>
<div class='dark_green_border temp_green message_container'>
    <p>You should have been sent an email to verify your account.</p>
    <p>If not, click <a onclick="alert('test');">here</a> to resend.</p>
</div>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>
<?php
                break;
            case 1:
?>
<div class='dark_green_border temp_green message_container'>
    <p><?php echo $status[1]; ?></p>
</div>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>
<?php
                include_once "./global_components/login_message.php";
                break;
            case 2:
?>
<div class='dark_green_border temp_green message_container'>
    <p>There was a server error, please try again!</p>
</div>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>
<?php
                include_once "./global_components/login_message.php";
                break;
            default:
                header("Location: default.php");
                exit;

        }
    } else if (isset($_POST["pass"]) && isset($_POST["confirm_pass"])) {
        // Update password on database
        $status = UserAdmin::add_password();
        switch($status) {
            case 0:
                // Return user to main page with created account
                header("Location: default.php?acc=1");
                exit;
                break;
            case 1:
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
                break;
            default:
                header("Location: default.php");
                exit;
        }
    } else if (isset($_GET["ver"])) {
        // Verify account
        $status = UserAdmin::verify_account();

        // Determine how to proceed depending on status
        switch($status) {
            case 0:
            // Display password submission form
?>
<div class='dark_green_border temp_green message_container'>
    <h4>Please choose a password</h4>
    <form action="" method="POST" onsubmit="return validate_password_choice()">
        <input type="hidden" name="user" value="<?php echo $_GET["user"]; ?>"/>
        <input id="password_choice_field" type="password" name="pass" placeholder="Password" class='light_green dark_green_text dark_green_border'/>
        <input id="confirm_password_choice_field" type="password" name="confirm_pass" placeholder="Confirm Password" class='light_green dark_green_text dark_green_border'/>
        <input type="submit" value="Continue" class='light_green dark_green_text dark_green_border'/>
    </form>
</div>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>
<script src='<?php echo $file_root; ?>scripts/form_verification.js'></script>
<?php
                break;
            case 1:
            // State why the thing failed and tell them to go back to old link.
?>
<div class='dark_green_border temp_green message_container'>
    <p>Server error. Please try following link again.</p>
</div>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>
<?php
                break;
            case 2:
            // State why the thing failed and tell them to go back to old link.
            // *** Here I should implement reissuing of link...
?>
<div class='dark_green_border temp_green message_container'>
    <p>Verification code mismatch. Please try following the link again.</p>
</div>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>
<?php
                break;
            default:
                header("Location: default.php");
                exit;
        }
    } else {
        // Redirect to home page
        header("Location: default.php");
        exit;
    }

    include_once "./global_components/footer.php";
?>