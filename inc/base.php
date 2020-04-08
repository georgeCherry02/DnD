<?php
    // Determine error reporting
    error_reporting(E_ALL);
    session_start();

    // Determine root for constants depending on initial location
    switch($file_location) {
        case "root": 
            $inc_file_root = "../inc/";
            break;
        case "actions":
            $inc_file_root = "../../inc/";
            break;
        default:
            $inc_file_root = "./";
    }

    include_once $inc_file_root."constants.php";
    include_once $inc_file_root."classes/Database.php";
    include_once $inc_file_root."TypedEnum.php";
    foreach(glob($inc_file_root."enums/*.php") as $file) {
        include_once $file;
    }
?>