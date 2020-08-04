<?php
    $file_location = "root";
    include_once "../inc/base.php";
    include_once "../inc/classes/Database.php";
    include_once "../inc/classes/Game.php";
    include_once "../inc/TypedEnum.php";

    $response = array();
    $response["status"] = "Failure";

    if (!isset($_POST["ajax_token"]) || $_POST["ajax_token"] !== $_SESSION["ajax_token"]) {
        $response["error_message"] = "Failed to verify";
        echo json_encode($response);
        exit;
    }

    if (!isset($_POST["process"])) {
        $response["error_message"] = "Failed to provide process";
        echo json_encode($response);
        exit;
    }

    if (!isset($_POST["data"])) {
        $response["error_message"] = "Failed to provide any data";
        echo json_encode($response);
        exit;
    }

    $request_data = json_decode($_POST["data"], $assoc=TRUE);

    if (sizeof($request_data) === 0) {
        $response["error_message"] = "Malformed data provided";
        echo json_encode($response);
        exit;
    }
    switch($_POST["process"]) {
        case "addConnection":
            if (!isset($request_data["game_id"]) || !isset($request_data["player_id"])) {
                $response["error_message"] = "Failed to provide necessary data";
                break;
            } 
            $res = Game::add_connection($request_data["game_id"], $request_data["player_id"]);
            if ($res) {
                $response["status"] = "Success";
            }
            break; 
        case "removeConnection":
            if (!isset($request_data["game_id"]) || !isset($request_data["player_id"])) {
                $response["error_message"] = "Failed to provide necessary data";
                break;
            }
            $res = Game::remove_connection($request_data["game_id"], $request_data["player_id"]);
            if ($res) {
                $response["status"] = "Success";
            }
            break;
        case "addPuddle":
            if (!isset($request_data["game_id"]) || !isset($request_data["player_id"]) || !isset($request_data["point_x"]) || !isset($request_data["point_y"])) {
                $response["error_message"] = "Failed to provide necessary data";
                break;
            }
            $res = Game::add_puddle($request_data["game_id"], $request_data["player_id"], $request_data["point_x"], $request_data["point_y"]);
            if ($res) {
                $response["status"] = "Success";
            } else {
                $response["error_message"] = "Failed to send puddle to server";
            }
            break;
        case "addMarker":
            if (!isset($request_data["game_id"]) || !isset($request_data["marker"])) {
                $response["error_message"] = "Failed to provide necessary data";
                break;
            }
            // Double check session is owner
            $game_info = Game::fetch_display_information($request_data["game_id"]);
            if ($_SESSION["Logged_in_id"] !== $game_info["Owner_ID"]) {
                $response["error_message"] = "As you're not the owner of this game you can't do this";
                break;
            }
            $res = Game::add_marker($request_data["game_id"], $request_data["marker"]);
            if ($res) {
                $response["status"] = "Success";
            } else {
                $response["error_message"] = "Failed to add marker";
            }
            break;
        case "removeMarker":
            if (!isset($request_data["game_id"]) || !isset($request_data["marker_x"]) || !isset($request_data["marker_y"])) {
                $response["error_message"] = "Failed to provide necessary data";
                break;
            }
            $game_info = Game::fetch_display_information($request_data["game_id"]);
            if ($_SESSION["Logged_in_id"] !== $game_info["Owner_ID"]) {
                $response["error_message"] = "As you're not the owner of this game you can't do this";
                break;
            }
            $res = Game::remove_marker($request_data["game_id"], $request_data["marker_x"], $request_data["marker_y"]);
            if ($res) {
                $response["status"] = "Success";
                $response["res"] = $res;
            } else {
                $response["error_message"] = "Failed to remove marker";
            }
            break;
        case "fetchPuddles":
            if (!isset($request_data["game_id"]) || !isset($request_data["player_id"])) {
                $response["error_message"] = "Failed to provide necessary data";
                break;
            }
            $res = Game::fetch_puddles($request_data["game_id"], $request_data["player_id"]);
            $response["puddles"] = $res;
            $response["status"] = "Success";
            break;
        case "fetchBoard":
            if (!isset($request_data["game_id"])) {
                $response["error_message"] = "Failed to provide necessary data";
                break;
            }
            $res = Game::fetch_board($request_data["game_id"]);
            $response["board"] = $res;
            $response["status"] = "Success";
            break;
        case "updateGrid":
            if (!isset($request_data["game_id"]) || !isset($request_data["grid_state"])) {
                $response["error_message"] = "Failed to provide necessary data";
                break;
            }
            // Double check session is owner
            $game_info = Game::fetch_display_information($request_data["game_id"]);
            if ($_SESSION["Logged_in_id"] !== $game_info["Owner_ID"]) {
                $response["error_message"] = "As you're not the owner of this game you can't do this";
                break;
            }
            $res = Game::set_grid_state($request_data["game_id"], $request_data["grid_state"]);
            if ($res) {
                $response["status"] = "Success";
            } else {
                $response["error_message"] = "Failed to set initial state";
            }
            break;
        case "fetchPlayerHealth":
            include_once "../inc/classes/ItemManager.php";
            include_once "../inc/classes/Character.php";
            if (!isset($request_data["game_id"])) {
                $response["error_message"] = "Failed to provide necessary data";
            }
            // Fetch game character's healths
            $character_health = Game::fetch_character_healths($request_data["game_id"]);
            if (!$character_health) {
                $response["error_message"] = "PDOException, failed to commit to database.";
                break;
            }
            $response["health_summary"] = $character_health;
            $response["status"] = "Success";
            break;
        case "fetchPlayerInfo":
            include_once "../inc/classes/ItemManager.php";
            include_once "../inc/classes/Character.php";
            if (!isset($request_data["game_id"])) {
                $response["error_message"] = "Failed to provide necessary data";
            }
            // Fetch game characters' healths
            $character_health = Game::fetch_character_healths($request_data["game_id"]);
            if (!$character_health) {
                $response["error_message"] = "PDOException, failed to commit to database.";
                break;
            }
            // Fetch game characters' spell slots
            $character_spell_slots = Game::fetch_character_spell_slots($request_data["game_id"]);
            if (!$character_spell_slots) {
                $response["error_message"] = "PDOException, failed to commit to database.";
                break;
            }
            $response["health_summary"] = $character_health;
            $response["spell_slot_summary"] = $character_spell_slots;
            $response["status"] = "Success";
            break;
        case "modifyHealth":
            include_once "../inc/classes/Character.php";
            if (!isset($request_data["game_id"]) || !isset($request_data["health_amount"]) || !isset($request_data["damaging"]) || !isset($request_data["player_id"])) {
                $response["error_message"] = "Failed to provide necessary data";
                break;
            }
            // Double check session is owner
            $game_info = Game::fetch_display_information($request_data["game_id"]);
            if ($_SESSION["Logged_in_id"] !== $game_info["Owner_ID"]) {
                $response["error_message"] = "As you're not the owner of this game you can't do this.";
                break;
            }
            // Modify the character's health
            $character_ids = json_decode($game_info["Player_Character_IDs"], $assoc=true);
            if (array_key_exists($request_data["player_id"], $character_ids)) {
                $result = Character::modify_health($request_data["health_amount"], $request_data["damaging"], $character_ids[$request_data["player_id"]]);
            } else {
                $response["error_message"] = "This player does not play in your game... You can't do this.";
                break;
            }
            if ($result) {
                $response["status"] = "Success";
            } else {
                $response["error_message"] = "PDOException, failed to commit to database.";
            }
            break;
        case "setTempHealth":
            include_once "../inc/classes/Character.php";
            if (!isset($request_data["game_id"]) || !isset($request_data["health_amount"]) || !isset($request_data["player_id"])) {
                $response["error_message"] = "Failed to provide necessary data";
                break;
            }
            // Double check the session is the owner
            $game_info = Game::fetch_display_information($request_data["game_id"]);
            if ($_SESSION["Logged_in_id"] !== $game_info["Owner_ID"]) {
                $response["error_message"] = "As you're not the owner of this game you can't do this.";
                break;
            }
            // Modify the character's health
            $character_ids = json_decode($game_info["Player_Character_IDs"], $assoc=true);
            if (array_key_exists($request_data["player_id"], $character_ids)) {
                $result = Character::add_temporary_hit_points($request_data["health_amount"], $character_ids[$request_data["player_id"]]);
            } else {
                $response["error_message"] = "This player does not play in your game... You can't do this.";
                break;
            }
            if ($result) {
                $response["status"] = "Success";
            } else {
                $response["error_message"] = "PDOException, failed to commit to database.";
            }
            break;
        case "modifySpellSlots":
            include_once "../inc/classes/ItemManager.php";
            include_once "../inc/classes/Character.php";
            if (!isset($request_data["game_id"]) || !isset($request_data["slot_level"]) || !isset($request_data["player_id"]) || !isset($request_data["addition"])) {
                $response["error_message"] = "Failed to provide necessary data";
                break;
            }
            // Double check the session is the owner
            $game_info = Game::fetch_display_information($request_data["game_id"]);
            if ($_SESSION["Logged_in_id"] !== $game_info["Owner_ID"]) {
                $response["error_message"] = "As you're not the owner of this game you can't do this.";
                break;
            }
            // Modify the character's spell slots
            $character_ids = json_decode($game_info["Player_Character_IDs"], $assoc=true);
            if (array_key_exists($request_data["player_id"], $character_ids)) {
                $result = Character::modify_spell_slots($request_data["slot_level"], $request_data["addition"], $character_ids[$request_data["player_id"]]);
            } else {
                $response["error_message"] = "This player does not play in your game... You can't do this.";
                break;
            }
            if ($result) {
                $response["status"] = "Success";
            } else {
                $response["error_message"] = "Modification out of bounds or PDOException, failed to commit to database.";
            }
            break;
        default:
            $response["error_message"] = "Failed to provide valid process";
    }
    echo json_encode($response);
    exit;
?>