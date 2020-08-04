<?php
    class Game {
        public static function fetch_display_information($id) {
            $game_info_sql = "SELECT `Name`, `Owner_ID`, `Player_IDs`, `Player_Colours`, `Player_Character_IDs` FROM `Games` WHERE `ID`=:gid";
            $game_info_var = array(":gid" => $id);
            try {
                $game_info = DB::query($game_info_sql, $game_info_var)[0];
            } catch (PDOException $e) {
                return false;
            }
            return $game_info;
        }
        private static function fetch_state($game_id) {
            $state_sql = "SELECT `State` FROM `Games` WHERE `ID`=:id";
            try {
                $state_req = DB::query($state_sql, array(":id" => $game_id));
                $state = json_decode($state_req[0]["State"], $assoc=TRUE);
            } catch (PDOException $e) {
                return false;
            }
            return $state;
        }
        private static function set_state($game_id, $state) {
            $set_state_sql = "UPDATE `Games` SET `State`=:state WHERE `ID`=:id";
            try {
                $res = DB::query($set_state_sql, array(":state" => json_encode($state), ":id" => $game_id));
            } catch (PDOException $e) {
                return false;
            }
            return true;
        }
        public static function add_puddle($game_id, $player_id, $x, $y) {
            $state = self::fetch_state($game_id);
            if ($state) {
                $new_puddle = array("x" => $x, "y" => $y, "seen_by" => array(), "created_by" => $player_id);
            } else {
                return false;
            }
            array_push($state["puddles"], $new_puddle);
            return self::set_state($game_id, $state);
        }
        public static function fetch_puddles($game_id, $player_id) {
            $state = self::fetch_state($game_id);
            $total_number_of_players = json_decode(self::fetch_display_information($game_id)["Player_IDs"]);
            $unseen_puddles = array();
            $keys_to_remove = array();
            foreach ($state["puddles"] as $key => $current_puddle) {
                if (!in_array($player_id, $current_puddle["seen_by"])) {
                    $tidy_puddle = array("x" => $current_puddle["x"], "y" => $current_puddle["y"], "created_by" => $current_puddle["created_by"]);
                    array_push($unseen_puddles, $tidy_puddle);
                    array_push($state["puddles"][$key]["seen_by"], $player_id);
                    // Check if everyone's seen puddles
                    $seen_by = $state["puddles"][$key]["seen_by"];
                    $connected = self::fetch_connections($game_id);
                    if (sizeof($seen_by) >= sizeof($connected) || sizeof($seen_by) == sizeof($total_number_of_players)) {
                        // Remove later
                        array_push($keys_to_remove, $key);
                    }
                }
            }
            for ($i = 0; $i < sizeof($keys_to_remove); $i++) {
                $index = $keys_to_remove[$i];
                unset($state["puddles"][$index]);
            }
            // Update who has seen the puddles
            self::set_state($game_id, $state);
            return $unseen_puddles;
        }
        public static function fetch_connections($game_id) {
            $conn_sql = "SELECT `Connections` FROM `Games` WHERE `ID`=:id";
            try {
                $connections = json_decode(DB::query($conn_sql, array(":id" => $game_id))[0]["Connections"], $assoc=TRUE);
            } catch (PDOException $e) {
                return false;
            }
            return $connections;
        }
        public static function add_connection($game_id, $player_id) {
            $connections = self::fetch_connections($game_id);
            if ($connections === false) {
                return false;
            }
            if (!in_array($player_id, $connections)) {
                array_push($connections, $player_id);
            }
            $conn_update_sql = "UPDATE `Games` SET `Connections`=:conns WHERE `ID`=:id";
            try {
                DB::query($conn_update_sql, array(":conns" => json_encode($connections), ":id" => $game_id));
            } catch (PDOException $e) {
                return false;
            }
            return true;
        }
        public static function remove_connection($game_id, $player_id) {
            $connections = self::fetch_connections($game_id);
            if (!$connections) {
                return false;
            }
            if (($key = array_search($player_id, $connections)) !== false) {
                unset($connections[$key]);
            }
            $conn_update_sql = "UPDATE `Games` SET `Connections`=:conns WHERE `ID`=:id";
            try {
                DB::query($conn_update_sql, array(":conns" => json_encode($connections), ":id" => $game_id));
            } catch (PDOException $e) {
                return false;
            }
            return true;
        }
        public static function fetch_rooms($game_id) {
            $room_sql = "SELECT `ID`, `Name` FROM `Rooms` WHERE `Game_ID`=:gid";
            $room_var = array(":gid" => $game_id);
            try {
                $rooms = DB::query($room_sql, $room_var);
            } catch (PDOException $e) {
                return false;
            }
            return $rooms;
        }
        public static function fetch_board($game_id) {
            $state = self::fetch_state($game_id);
            $res["grid"] = $state["grid"];
            $res["markers"] = array();
            if (isset($state["markers"])) {
                $res["markers"] = $state["markers"];
            }
            return $res;
        }
        // Game owner operations
        private static function verify_owner($game_id) {
            $game_owner = self::fetch_display_information($game_id)["Owner_ID"];
            return $game_owner == $_SESSION["Logged_in_id"];
        }
        public static function set_grid_state($game_id, $grid_state) {
            // Final verification
            if (!self::verify_owner($game_id)) {
                return false;
            }
            // Fetch initial state
            $state = self::fetch_state($game_id);
            // Modify state
            $state["grid"] = $grid_state;
            // Set final state
            return self::set_state($game_id, $state);
        }
        public static function add_marker($game_id, $marker) {
            // Final verification
            if (!self::verify_owner($game_id)) {
                return false;
            }
            // Fetch initial state
            $state = self::fetch_state($game_id);
            // Check if markers key is set
            if (!isset($state["markers"])) {
                $state["markers"] = array();
            }
            // Add marker to markers
            array_push($state["markers"], $marker);
            // Set final state
            return self::set_state($game_id, $state);
        }
        public static function remove_marker($game_id, $marker_x, $marker_y) {
            // Final verification
            if (!self::verify_owner($game_id)) {
                return false;
            }
            // Fetch initial state
            $state = self::fetch_state($game_id);
            // Check if markers key is set
            if (!isset($state["markers"])) {
                return false;
            }
            // Remove marker from markers
            for ($i = 0; $i < sizeof($state["markers"]); $i++) {
                $marker = $state["markers"][$i];
                if ($marker["x"] == $marker_x && $marker["y"] == $marker_y) {
                    $index = $i;
                }
            }
            if (isset($index)) {
                array_splice($state["markers"], $index, 1);
            }
            // Set final state
            return self::set_state($game_id, $state);
        }
        public static function verify_player($game_id) {
            // Check if player is in allowed players
            $sql = "SELECT `Player_IDs` FROM `Games` WHERE `ID`=:id";
            $variables = array(":id" => $game_id);
            try {
                $allowed_players = json_decode(DB::query($sql, $variables)[0]["Player_IDs"], $assoc=true);
            } catch (PDOException $e) {
                return false;
            }
            return in_array($_SESSION["Logged_in_id"], $allowed_players);
        }
        public static function add_player_character($game_id, $character_id) {
            // Verify user owns character
            if (User::verify_character_ownership($character_id) && self::verify_player($game_id)) {
                $init_char_sql = "SELECT `Player_Character_IDs` FROM `Games` WHERE `ID`=:id";
                $init_char_var = array(":id" => $game_id);
                try {
                    $init_char = json_decode(DB::query($init_char_sql, $init_char_var)[0]["Player_Character_IDs"], $assoc=true);
                } catch (PDOException $e) {
                    return false;
                }
                if (!array_key_exists($_SESSION["Logged_in_id"], $init_char)) {
                    $fin_char_sql = "UPDATE `Games` SET `Player_Character_IDs`=:pcids WHERE `ID`=:id";
                    $init_char[$_SESSION["Logged_in_id"]] = $character_id;
                    $fin_char_var = array(":pcids" => json_encode($init_char), ":id" => $game_id);
                    try {
                        DB::query($fin_char_sql, $fin_char_var);
                    } catch (PDOException $e) {
                        return false;
                    }
                    return true;
                }
                return false;
            }
            return false;
        }
        public static function fetch_characters($character_ids) {
            $character_info = array();
            foreach ($character_ids as $player_id => $character_id) {
                $character_info[$player_id] = Character::fetch_character_info($character_id);
            }
            return $character_info;
        }
        public static function fetch_character_healths($game_id) {
            $character_ids = json_decode(self::fetch_display_information($game_id)["Player_Character_IDs"], $assoc=true);
            $characters_health_summary = array();
            foreach ($character_ids as $player_id => $character_id) {
                $character_info = Character::fetch_character_info($character_id);
                $characters_health_summary[$player_id] = array("Max" => $character_info["Hit_Point_Maximum"], "Current" => $character_info["Current_Hit_Points"], "Temp" => $character_info["Temporary_Hit_Points"]);
            }
            return $characters_health_summary;
        }
        public static function fetch_character_spell_slots($game_id) {
            $character_ids = json_decode(self::fetch_display_information($game_id)["Player_Character_IDs"], $assoc=true);
            $characters_spell_slot_summary = array();
            foreach ($character_ids as $player_id => $character_id) {
                $character_info = Character::fetch_character_info($character_id);
                $characters_spell_slot_summary[$player_id] = ItemManager::get_spell_slot_distribution($character_info["Current_Spell_Slot_Distribution_ID"]);
            }
            return $characters_spell_slot_summary;
        }
    }
?>