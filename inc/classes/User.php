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
    }
?>