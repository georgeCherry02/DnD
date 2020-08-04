<?php
    class MDB {
        public static function query($query, $params = array()) {
            echo "Mock request:<br/>";
            echo $query."<br/>";
            echo json_encode($params)."<br/>";
            return 0;
        }
    }
?>