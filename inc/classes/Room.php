<?php
    class Room {
        public static function fetch_dim($id) {
            $dim_sql = "SELECT `Grid_Dimensions` FROM `Rooms` WHERE `ID`=:id";
            $dim_var = array(":id" => $id);
            try {
                $dimensions = json_decode(DB::query($dim_sql, $dim_var)[0]["Grid_Dimensions"], $assoc=TRUE);
            } catch (PDOException $e) {
                return false;
            }
            return $dimensions;
        }
    }
?>