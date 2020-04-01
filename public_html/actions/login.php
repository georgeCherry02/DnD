<?php
    $file_location = "actions";

    include_once "../inc/base.php";
    include_once "../inc/classes/UserAdmin.php";

    if (isset($_POST["location"])) {
        $original_location = $_POST["location"];
        $user = $_POST["user"];
        $pass = $_POST["pass"];
        $user = new UserAdmin($db);
    }
?>