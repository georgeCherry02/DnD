<?php
    $file_location = "actions";
    $page_name = "Verification";

    include_once "../../inc/base.php";

    include_once "../global_components/header.php";
    include_once "../global_components/navbar.php";

    if (isset($_POST["email"])) {
        // Insert basic account into database
        // Send verification email
        // Display verification message
    } else if (isset($_GET["ver"])) {
        // Verify account
        // Display password submission form
        // Submit hashed password
        // Login account
        // Redirect to home page
    } else {
        // Redirect to home page
    }

    include_once "../global_components/footer.php";
?>