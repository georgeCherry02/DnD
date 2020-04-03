<?php
    switch ($file_location) {
        case "root":
            $file_root = "./";
            break;
        case "actions":
            $file_root = "../";
            break;
        default:
            $file_root = "";
    }
?>
<!DOCTYPE html>
<html class='light_brown'>
    <head>
        <title><?php echo "DnD | " . $page_title; ?></title>
        <link rel='stylesheet' href='<?php echo $file_root; ?>css/main.css' type='text/css'/> 
        <script src="https://kit.fontawesome.com/fd361f6e33.js"></script>
        <link   rel="stylesheet"
                href="https://fonts.googleapis.com/css?family=Gotu">
        <?php
            switch ($page_title) {
                case "Welcome":
                    echo "<link rel='stylesheet' href='" . $file_root . "css/landing.css' type='text/css'/>";
                    break;
                case "Create":
                    echo "<link rel='stylesheet' href='" . $file_root . "css/create.css' type='text/css'/>";
                    break;
            }
        ?>
    </head>
    <body>