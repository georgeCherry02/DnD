<?php
    session_start();
    $_SESSION["Logged_in"] = null;
    $_SESSION["Logged_in_id"] = null;
    header("Location: ../default.php");
    exit;
?>