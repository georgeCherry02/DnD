<!DOCTYPE html>
<html class='light_brown'>
    <head>
        <title><?php echo "DnD | " . $page_title; ?></title>
        <link rel='stylesheet' href='css/main.css' type='text/css'/> 
        <script src="https://kit.fontawesome.com/fd361f6e33.js"></script>
        <link   rel="stylesheet"
                href="https://fonts.googleapis.com/css?family=Gotu">
        <?php
            switch ($page_title) {
                case "Welcome":
                    echo "<link rel='stylesheet' href='css/landing.css' type='text/css'/>";
                    break;
            }
        ?>
    </head>
    <body>