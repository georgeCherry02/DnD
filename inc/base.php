<?php
    // Determine error reporting
    error_reporting(E_ALL);
    session_start();
    $_SESSION["Logged_in"] = true;

    // Determine root for constants depending on initial location
    switch($file_location) {
        case "root": 
            include_once "./inc/constants.php";
            break;
        default:
            include_once "./contants.php";
    }

    // Connect to the database
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
    $db = new PDO($dsn, DB_USER, DB_PASS);
?>