<?php
    class User {
        public static function fetch_game_ids() {
            $games_sql = "SELECT `ID` FROM `Games` WHERE `Owner_ID`=:uid";
            $games_var = array(":uid" => $_SESSION["Logged_in_id"]);
            try {
                $games = DB::query($games_sql, $games_var);
            } catch (PDOException $e) {
                return false;
            }
            return $games;
        }
        public static function fetch_name() {
            if (isset($_SESSION["Logged_in_id"]) && $_SESSION["Logged_in"]) {
                $name_sql = "SELECT `Username` FROM `Users` WHERE `ID`=:uid";
                $name_var = array(":uid" => $_SESSION["Logged_in_id"]);
                try {
                    $user_name = DB::query($name_sql, $name_var)[0]["Username"];
                } catch (PDOException $e) {
                    return false;
                }
                return $user_name;
            }
            return false;
        }
        public static function verify_character_ownership($char_id) {
            $char_sql = "SELECT `Owner_ID` FROM `Characters` WHERE `ID`=:chid";
            $char_var = array(":chid" => $char_id);
            try {
                $owner_id = DB::query($char_sql, $char_var)[0]["Owner_ID"];
            } catch (PDOException $e) {
                return false;
            }
            return $owner_id = $_SESSION["Logged_in_id"];
        }
    }
?>