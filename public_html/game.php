<?php
    $file_location = "root";
    include_once "../inc/base.php";
    include_once "../inc/classes/Game.php";
    if (!isset($_SESSION["Logged_in_id"]) || !$_SESSION["Logged_in"]) {
        header("Location: default.php");
    }
    if (isset($_GET["id"])) {
        $game_id = $_GET["id"];
        $game_info = Game::fetch_display_information($game_id);
        $page_title = $game_info["Name"];
        $allowed_players = json_decode($game_info["Player_IDs"]);
        if (in_array($_SESSION["Logged_in_id"], $allowed_players)) {
            include_once "./global_components/header.php";
            include_once "./global_components/navbar.php";
            if (isset($_GET["room"])) {
                $room_id = $_GET["room"];
                include_once "../inc/classes/Room.php";
                $room_grid_dim = Room::fetch_dim($room_id);
                $background_size = getimagesize("./resources/rooms/background".$room_id.".jpg");
                $width_ratio = 1024 / $background_size[0];
                $end_height = floor($width_ratio * $background_size[1]);
                include_once "./global_components/ajax.php";
                include_once "./components/game/constants.php";
                if ($game_info["Owner_ID"] == $_SESSION["Logged_in_id"]) {
?>
<div class="gm_control_panel">
    <div class="tool_button" id="comm_tool" onclick="game.use_point_tool();"></div>
    <div class="tool_button" id="fog_tool" onclick="game.use_fog_tool();"></div>
    <div class="tool_button" id="marker_tool" onclick="game.use_marker_tool();"></div>
    <input type="text" id="marker_colour"/>
</div>
<?php
                }
?>
<div class="canvas_container">
    <canvas id="game_canvas" width="1024" height="<?php echo $end_height; ?>" style="-webkit-user-drag: none; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); width: 1024px; height: <?php echo $end_height; ?>px; border: 1px solid black; cursor: none;"></canvas>
</div>
<script src="./scripts/game.js" type="application/javascript"></script>
<?php
            } else {
                if ($_SESSION["Logged_in_id"] == $game_info["Owner_ID"]) {
                    // Implement room selection screen
                    $rooms = Game::fetch_rooms($game_id);
                    if ($rooms !== false) {
                        for ($i = 0; $i < 5; $i++) {
                            if ($i == sizeof($rooms)) {
                                $room = false;
                                include "./components/game/room_card.php";
                                break;
                            } else {
                                $room = $rooms[$i];
                                include "./components/game/room_card.php";
                            }
                        }
                    } else {
                        echo "PDOExecption";
                    }
                }
            }
            include_once "./global_components/footer.php";
        } else {
            header("Location: default.php");
            exit;
        }
    } else {
        header("Location: default.php");
        exit;
    }
?>