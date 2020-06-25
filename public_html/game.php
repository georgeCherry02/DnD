<?php
    $file_location = "root";
    include_once "../inc/base.php";
    include_once "../inc/classes/Game.php";
    if (!isset($_GET["id"]) || !isset($_SESSION["Logged_in_id"]) || !$_SESSION["Logged_in"]) {
        header("Location: default.php");
    }
    $game_id = $_GET["id"];
    $game_info = Game::fetch_display_information($game_id);

    $page_title = $game_info["Name"];
    $allowed_players = json_decode($game_info["Player_IDs"]);

    // Check if player connecting is allowed
    if (in_array($_SESSION["Logged_in_id"], $allowed_players)) {
        include_once "./global_components/header.php";
        include_once "./global_components/navbar.php";
        include_once "./global_components/ajax.php";
        include_once "./components/game/constants.php";
?>
<div class="canvas_container">
    <canvas id="game_canvas" width="1024" height="512" style="-webkit-user-drag: none; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); width: 100%; border: blue 1px solid;"></canvas>
</div>
<script src="./scripts/game.js" type="application/javascript"></script>
<?php
        include_once "./global_components/footer.php";
    }
?>