<?php
    $file_location = "root";
    $page_title =    "Create";

    include_once "../inc/base.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";

    if (isset($_SESSION["Logged_in"]) && $_SESSION["Logged_in"]) {
?>
<h4>What would you like to create?</h4>
<div>
    <div class='col-3'>
        <div class='option_container'>
            <img src='./resources/icons/helmet.svg'/>
        </div>
    </div>
    <div class='col-3'>
        <div class='option_container'>
            <img src='./resources/icons/swords.svg'/>
        </div>
    </div>
    <div class='col-3'>
        <div class='option_container'>
            <img src='./resources/icons/book.svg'/>
        </div>
    </div>
    <div class='col-3'>
        <div class='option_container'>
            <img src='./resources/icons/scroll.svg'/>
        </div>
    </div>
</div>
<?php
    } else {
        include_once "./global_components/login_message.php";
    }

    include_once "./global_components/footer.php";
?>