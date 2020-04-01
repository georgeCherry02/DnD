<?php
    $file_location = "actions";
    error_reporting(E_ALL);

    include_once "../../inc/base.php";
    include_once "../../inc/classes/UserAdmin.php";

    if (isset($_POST["user"])) {
        $result = UserAdmin::login_user();
        if ($result) {
            $_SESSION["Logged_in"] = 1;
            $_SESSION["Logged_in_id"] = $result;
            header("Location: ../default.php?login=1");
            exit;
        } else {
            header("Location: ../default.php?login=0");
            exit;
        }
    } else {
        header("Location: ../default.php?login=0");
        exit;
    }
?>