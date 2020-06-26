<?php
    class Game {
        public static function fetch_display_information($id) {
            $game_info_sql = "SELECT `Name`, `Owner_ID`, `Player_IDs`, `Player_Colours` FROM `Games` WHERE `ID`=:gid";
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
                    if (sizeof($seen_by) == sizeof($connected)) {
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
    }
?>