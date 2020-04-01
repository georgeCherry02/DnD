<?php
    $file_location = "actions";

    include_once "../../inc/base.php";
    include_once "../../inc/classes/UserAdmin.php";

    if (isset($_POST["user"])) {
        $status = UserAdmin::login_user();
        $_SESSION["Logged_in"] = $status[0] == 1;
        $_SESSION["Logged_in_id"] = $status[1];
        header("Location: ../default.php?login=1");
        exit;
    } else {
        header("Location: ../default.php?login=0");
        exit;
    }
?>