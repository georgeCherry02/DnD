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
<html class='white_background'>
    <head>
        <title><?php echo "DnD | " . $page_title; ?></title>
        <link rel='stylesheet' href='<?php echo $file_root; ?>css/main.css' type='text/css'/> 
        <script src="https://kit.fontawesome.com/fd361f6e33.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="<?php echo $file_root; ?>scripts/constants.js"></script>
        <?php
            switch ($page_title) {
                case "Welcome":
                    echo "<link rel='stylesheet' href='" . $file_root . "css/landing.css' type='text/css'/>";
                    break;
                case "Create":
                    echo "<link rel='stylesheet' href='" . $file_root . "css/create.css' type='text/css'/>";
                    break;
                case "Duplicate Choice":
                    echo "<link rel='stylesheet' href='" . $file_root . "css/duplicate.css' type='text/css'/>";
                    break;
            }
            if (isset($game_id)) {
?>
                <script src="https://unpkg.com/paper@0.11.5/dist/paper-full.min.js"></script>
                <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
                <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
                <link rel="stylesheet" href="<?php echo $file_root; ?>css/game.css" type="text/css"/>
<?php
            }
        ?>
    </head>
    <body>