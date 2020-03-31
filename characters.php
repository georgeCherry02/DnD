<?php
    $page_title =       "Characters";
    $file_location =    "root";

    include_once "./inc/base.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";
?>
<div class='main_content_container'>
<?php
    if (isset($_SESSION["Logged_in"]) && $_SESSION["Logged_in"]) {
?>
    <p>Test</p>
<?php
    } else {
        include_once "./global_components/login_message.php";
    }
?>
</div>
<?php
    include_once "./global_components/footer.php";
?>