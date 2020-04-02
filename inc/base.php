<?php
    // Determine error reporting
    error_reporting(E_ALL);
    session_start();

    // Determine root for constants depending on initial location
    switch($file_location) {
        case "root": 
            include_once "../inc/constants.php";
            include_once "../inc/classes/Database.php";
            break;
        case "actions":
            include_once "../../inc/constants.php";
            include_once "../../inc/classes/Database.php";
            break;
        default:
            include_once "./contants.php";
            include_once "./classes/Database.php";
    }
?>