<?php
    $file_location = "root";
    $page_title =    "Failed verification";

    include_once "../inc/base.php";
    include_once "../inc/classes/UserAdmin.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";

    if (isset($_POST["email"])) {
        $status = UserAdmin::reissue_verification();
        switch($status) {
            case 0:
                $popup_message_name = "reissue/link_reissued";
                include_once "./global_components/popup_message.php";
                break;
            case 1:
                header("Location: ./default.php?err=server");
                exit;
                break;
            case 2:
                $popup_message_name = "reissue/email_not_registered";
                include_once "./global_components/popup_message.php";
                break;
            case 3:
                $popup_message_name = "reissue/account_already_exists";
                include_once "./global_components/popup_message.php";
                break;
            case 4:
                $popup_message_name = "reissue/no_password";
                include_once "./global_components/popup_message.php";
                break;
        }
    } else {
?>
<div class='grey_border light_background message_container'>
    <p>If you're email validation has failed, enter your email here to re-issue a new link.</p>
    <form action='' method="POST">
        <input type='text' name='email' placeholder='Email' class='white_background grey_text grey_border'/>
        <input type='submit' value='Send link' class='white_background highlight_text grey_border'/>
    </form>
</div>
<link rel='stylesheet' href='./css/form_message.css' type='text/css'/>
<?php
    }
?>
<?php
    include_once "./global_components/footer.php";
?>