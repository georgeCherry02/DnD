<?php
    $page_title =       "Games";
    $file_location =    "root";

    include_once "../inc/base.php";
    include_once "../inc/classes/User.php";
    include_once "../inc/classes/Game.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";

    if (isset($_SESSION["Logged_in"]) && $_SESSION["Logged_in"]) {
        $games = User::fetch_game_ids();
        for ($i = 0; $i < 3; $i++) {
            if ($i == sizeof($games)) {
                $game = false;
                include "./components/game_select/game_card.php";
                break;
            } else if (filter_var($games[$i]["ID"], FILTER_VALIDATE_INT)) {
                $game = Game::fetch_display_information($games[$i]["ID"]);
                include "./components/game_select/game_card.php";
            }
        }
    } else {
        include_once "./global_components/login_message.php";
    }

    include_once "./global_components/footer.php";
?>