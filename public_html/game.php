<?php
    $file_location = "root";
    include_once "../inc/base.php";
    include_once "../inc/classes/Game.php";
    if (!isset($_SESSION["Logged_in_id"]) || !$_SESSION["Logged_in"]) {
        header("Location: default.php?err=not_logged_in");
        exit;
    }
    if (isset($_GET["id"])) {
        $game_id = $_GET["id"];
        $game_room = true;
        $game_info = Game::fetch_display_information($game_id);
        $is_game_owner = $game_info["Owner_ID"] == $_SESSION["Logged_in_id"];
        $page_title = $game_info["Name"];
        $allowed_players = json_decode($game_info["Player_IDs"]);
        if (Game::verify_player($game_id)) {
            include_once "./global_components/header.php";
            include_once "./global_components/navbar.php";
            if (isset($_GET["room"])) {
                $room_id = $_GET["room"];
                $player_character_ids = json_decode($game_info["Player_Character_IDs"], $assoc=true);
                if (!$is_game_owner && array_key_exists($_SESSION["Logged_in_id"], $player_character_ids)) {
                    $character_id = $player_character_ids[$_SESSION["Logged_in_id"]];
                } else if (!$is_game_owner) {
                    header("Location: create_character.php?gid=".urlencode($game_id)."&rid=".urlencode($room_id));
                    exit;
                }
                $room_id = $_GET["room"];
                include_once "../inc/classes/Character.php";
                include_Once "../inc/classes/ItemManager.php";
                include_once "../inc/classes/Room.php";
                $room_grid_dim = Room::fetch_dim($room_id);
                $background_size = getimagesize("./resources/rooms/background".$room_id.".jpg");
                $width_ratio = 1024 / $background_size[0];
                $end_height = floor($width_ratio * $background_size[1]);
                include_once "./global_components/ajax.php";
                include_once "./components/game/constants.php";
                $characters_info = Game::fetch_characters($player_character_ids);
                $player_colours = json_decode($game_info["Player_Colours"], $assoc=true);
                foreach ($characters_info as $player_id => $char_info) {
                    $player_colour = $player_colours[$player_id];
                    $characters_info[$player_id]["Colour"] = $player_colour;
                }
?>
<div class="gm_control_panel">
    <div class="tool_button" id="comm_tool" onclick="game.use_point_tool();">
        <i class="fas fa-satellite-dish"></i>
    </div>
    <hr/>
    <label for="radius_tool_value" class="mt-1" style="margin-bottom: 0.2rem;">Radius (ft):</label>
    <input type="number" id="radius_tool_value" class="form-control" min="0"/>
    <label for="radius_tool_colour" class="mt-1" style="margin-bottom: 0.2rem;">Radius colour:</label>
    <select id="radius_tool_colour" class="form-control mb-1">
        <option value="#70b52b">Green</option>
        <option value="#e0d648">Yellow</option>
        <option value="#4e61ed">Blue</option>
        <option value="#e02424">Red</option>
    </select>
    <div class="row">
        <div class="col-2">
            <div class="tool_button" id="radius_tool" onclick="game.use_radius_tool();">
                <i class="fas fa-circle"></i>
            </div>
        </div>
        <div class="col-2">
            <div class="tool_button" id="clear_radius_tool" onclick="game.clear_radius_markers();">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
        <div class="col-2">
            <div class="tool_button" id="measure_tool" onclick="game.use_measure_tool();">
                <i class="fas fa-ruler"></i>
            </div>
        </div>
    </div>
<?php
                if ($game_info["Owner_ID"] == $_SESSION["Logged_in_id"]) {
?>  
    <hr/>
    <div class="tool_button" id="fog_tool" onclick="game.use_fog_tool();">
        <i class="fas fa-cloud"></i>
    </div>
    <hr/>
    <div class="row mt-1 mb-1">
        <div class="col-2">
            <div class="tool_button" id="marker_tool" onclick="game.use_marker_tool('npc');">
                <i class="fas fa-running"></i>
            </div>
        </div>
        <div class="col-10">
            <input type="text" id="marker_colour" class="form-control"/>
        </div>
    </div>
    <div class="row mt-1">
    <?php 
        foreach($characters_info as $id => $char_info) {
            echo    "<div class=\"col-2\">"
               .        "<div class=\"tool_button\" style=\"background-color: ".$char_info["Colour"].";\" onclick=\"game.use_marker_tool('".$player_id."')\">"
               .            "<i class=\"fas fa-running\"></i>"
               .        "</div>"
               .    "</div>";
        }
    ?>
    </div>
    <hr/>
    <label for="health_tool_player" class="mt-1" style="margin-bottom: 0.2rem;">Player to modify health:</label>
    <select id="health_tool_player" class="form-control">
        <?php
            foreach($characters_info as $player_id => $char_info) {
                echo "<option value=\"".$player_id."\">".$char_info["Name"]."</option>";
            }
        ?>
    </select>
    <label for="health_tool_amount" class="mt-1" style="margin-bottom: 0.2rem;">Amount of health:</label>
    <input type="number" id="health_tool_amount" class="form-control mb-1" min="0"/>
    <div class="row">
        <div class="col-2">
            <div class="tool_button" id="health_tool_plus" onclick="game.owner.modifyHealth(add=true);">
                <i class="fas fa-plus"></i>
            </div>
        </div> 
        <div class="col-2">
            <div class="tool_button" id="health_tool_minus" onclick="game.owner.modifyHealth(add=false);">
                <i class="fas fa-minus"></i>
            </div>
        </div>
        <div class="col-2">
            <div class="tool_button" id="temp_health_tool" onclick="game.owner.setTempHealth();">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <hr/>
    <label for="spell_slot_level">Slot Level:</label>
    <input type="number" class="form-control" id="spell_slot_level" min="1" max="9"/>
    <label for="spell_slot_player">Player to modify spell slots of:</label>
    <select id="spell_slot_player" class="form-control">
        <?php
            foreach($characters_info as $player_id => $char_info) {
                echo "<option value=\"".$player_id."\">".$char_info["Name"]."</option>";
            }
        ?>
    </select>
    <div class="row">
        <div class="col-2">
            <div class="tool_button" id="spell_slot_plus" onclick="game.owner.modifySpellSlots(add=true);">
                <i class="fas fa-plus"></i>
            </div>
        </div>
        <div class="col-2">
            <div class="tool_button" id="spell_slot_minus" onclick="game.owner.modifySpellSlots(add=false);">
                <i class="fas fa-minus"></i>
            </div>
        </div>
    </div>
<?php
                }
?>
</div>
<div class="canvas_container">
    <canvas id="game_canvas" width="1024" height="<?php echo $end_height; ?>" style="-webkit-user-drag: none; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); width: 1024px; height: <?php echo $end_height; ?>px; border: 1px solid black; cursor: none;"></canvas>
</div>
<script src="./scripts/game.js" type="application/javascript"></script>
<div class="player_panel">
<?php
    foreach ($characters_info as $player_id => $character_info) {
        include "./components/game/character_card.php";
    }
?>
</div>
<?php
            } else {
                if ($is_game_owner) {
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
            header("Location: default.php?err=not_allowed");
            exit;
        }
    } else {
        header("Location: default.php?err=no_game_id");
        exit;
    }
?>