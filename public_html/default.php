<?php
    $page_title =       "Welcome";
    $file_location =    "root";

    include_once "../inc/base.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";

    if (isset($_GET["login"])) {
        if ($_GET["login"] == 0) {
            $popup_message_name = "login_failed";
            include_once "./global_components/popup_message.php";
        }
    } else if (isset($_GET["acc"])) {
        if ($_GET["acc"] == 1) {
            $popup_message_name = "account_created";
            include_once "./global_components/popup_message.php";
        } else if ($_GET["acc"] == 0) {
            if (isset($_GET["res"])) {
                if ($_GET["res"] == "email") {
                    $popup_message_name = "invalid_email_address";
                } else if ($_GET["res"] == "username") {
                    $popup_message_name = "invalid_username";
                } else {
                    header("Location: ./default.php");
                    exit;
                }
                include_once "./global_components/popup_message.php";
            }
        }
    } else if (isset($_GET["err"])) {
        if ($_GET["err"] == "server") {
            $popup_message_name = "server_error";
            include_once "./global_components/popup_message.php";
        }
    }
?>
<h1 class="hero black_text">Play DnD remote with friends!</h1>
<div class='detail_text'>
    <p class='black_text'>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque enim metus, dictum non mollis ut, viverra sollicitudin leo. Aliquam pretium consequat venenatis. Fusce sollicitudin, ipsum eu efficitur tincidunt, erat libero fringilla mauris, ullamcorper vehicula ante lorem vel justo. Etiam volutpat porta vulputate. Nam euismod dapibus auctor. Nullam dignissim in tortor at malesuada. Ut id augue pharetra, euismod tortor sed, iaculis mi. Donec tincidunt nisi eu orci tempor convallis. Maecenas augue elit, tempus tristique elementum posuere, maximus eget diam. Nulla tempor viverra gravida. Praesent bibendum sit amet augue a suscipit.</p>
    <div class='text_divider black_background'></div>
    <p class='black_text'>Nam consequat malesuada eros, vel facilisis lorem rutrum a. Quisque semper, lectus vel scelerisque sollicitudin, nibh augue sollicitudin diam, et dapibus velit ipsum vitae tellus. Pellentesque quam quam, fermentum sit amet dignissim id, luctus non mauris. Nam hendrerit arcu elementum tellus finibus, in hendrerit elit dictum. Pellentesque fringilla massa non lectus fermentum consequat. Nunc dapibus, ipsum a commodo scelerisque, augue turpis gravida justo, ac posuere neque nisl at neque. Fusce finibus rhoncus arcu, sed ullamcorper mauris dictum non. Pellentesque nec egestas nulla. Cras in volutpat diam. Nullam et dolor vehicula, lobortis elit eu, posuere elit. Donec pretium sollicitudin metus vitae placerat.</p>
</div>
<?php
    include_once "./global_components/footer.php";
?>