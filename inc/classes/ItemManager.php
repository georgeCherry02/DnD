<?php
    class ItemManager {

        /* Status codes
         * 0 - Successful execution - null OR if there's a similar/duplicate item return that one's ID
         * 1 - Server error - Where the error occured
         * 2 - Reached current armour limit - null
         */
        public static function create_armour() {
            $name = $_POST["name"];
            $base_ac = $_POST["base"];
            $modifiers = array();
            for ($i = 0; $i < sizeof(ABILITIES); $i++) {
                if (isset($_POST[ABILITIES[$i] . "_modifier"]) && $_POST[ABILITIES[$i] . "_modifier"] == "1") {
                    array_push($modifiers, $i + 1);
                }
            }
            $modifiers = json_encode($modifiers);
            $str_required = $_POST["str_required"];
            if (isset($_POST["stealth_disadvantage"]) && $_POST["stealth_disadvantage"] == "1") {
                $stealth_disadvantage = 1;
            } else {
                $stealth_disadvantage = 0;
            }
            $armour_weight = $_POST["weight"];
            $armour_value = $_POST["value"];
            
            // Check if user has capacity to create more armour
            $sql = "SELECT `Armour_IDs`, `Armours_Limit` FROM `User_Item_IDs` INNER JOIN `User_Limitations` ON User_Item_IDs.User_ID=User_Limitations.User_ID WHERE User_Item_IDs.User_ID=:uid;";
            try {
                $request = DB::query($sql, array(":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return array(1, 'capacity');
            }
            $current_armours = json_decode($request[0]["Armour_IDs"]);
            $current_limit = $request[0]["Armours_Limit"];
            if (sizeof($current_armours) >= $current_limit) {
                return array(2, null);
            }

            // Check a duplicate doesn't exist
            $sql = "SELECT `ID` FROM `Armours` WHERE `Name` LIKE :name && `Base_AC`=:b_ac && `Additional_Modifiers`=:add_mod";
            $variables = array(":name" => $name, ":b_ac" => $base_ac, ":add_mod" => $modifiers);
            if (!empty($str_required)) {
                $sql .= " && `Strength_Required`=:str_req";
                $variables[":str_req"] = $str_required;
            }
            $sql .= " && `Stealth_Disadvantage`=:stealth_disadvantage";
            $variables[":stealth_disadvantage"] = $stealth_disadvantage;
            if (!empty($armour_weight)) {
                $sql .= " && `Weight`=:weight";
                $variables[":weight"] = $armour_weight;
            }
            if (!empty($armour_value)) {
                $sql .= " && `Value`=:value";
                $variables[":value"] = $armour_value;
            }
            try {
                $duplicate_id = DB::query($sql, $variables);
            } catch (PDOException $e) {
                return array(1, "duplicate_check");
            }

            // Otherwise insert the armour into the database
            $column_names_sql = "INSERT INTO `Armours` (`Name`, `Base_AC`, `Additional_Modifiers`, `Stealth_Disadvantage`";
            $values_sql = ") VALUES (:name, :base_ac, :add_mod, :stealth";
            $variables = array(":name" => $name, "base_ac" => $base_ac, ":add_mod" => $modifiers, ":stealth" => $stealth_disadvantage);
            if (!empty($str_required)) {
                $column_names_sql .= ", `Strength_Required`";
                $values_sql .= ", :str_req";
                $variables[":str_req"] = $str_required;
            }
            if (!empty($armour_weight)) {
                $column_names_sql .= ", `Weight`";
                $values_sql .= ", :weight";
                $variables[":weight"] = $armour_weight;
            }
            if (!empty($armour_value)) {
                $column_names_sql .= ", `Value`";
                $values_sql .= ", :value";
                $variables[":value"] = $armour_value;
            }
            $sql = $column_names_sql . $values_sql . ");";
            try {
                DB::query($sql, $variables);
                $armour_id = DB::query("SELECT `ID` FROM `Armours` WHERE `Name`=:name ORDER BY `ID` DESC;", array(":name" => $name))[0]["ID"];
            } catch (PDOException $e) {
                return array(1, "insert");
            }

            // Now update the users item data
            // Fetch initial data
            try {
                $armour_ids = json_decode(DB::query("SELECT `Armour_IDs` FROM `User_Item_IDs` WHERE `User_ID`=:uid;", array(":uid" => $_SESSION["Logged_in_id"]))[0]["Armour_IDs"]);
            } catch (PDOException $e) {
                return array(1, "fetch_original_ids");
            }
            array_push($armour_ids, $armour_id);
            try {
                DB::query("UPDATE `User_Item_IDs` SET `Armour_IDs`=:armour_ids WHERE `User_ID`=:uid;", array(":armour_ids" => json_encode($armour_ids), ":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return array(1, "final_insert");
            }

            return array(0, $duplicate_id);
        }

        /* Status Codes
         * 0 - Successful return of last item ID - ID that should be returned
         * 1 - Server error - null
         * 2 - No type defined - null
         */
        public static function get_last_inserted_of_type($type) {
            switch($type) {
                case "armour":
                    try {
                        $old_ids = json_decode(DB::query("SELECT `Armour_IDs` FROM `User_Item_IDs` WHERE `User_ID`=:uid;", array(":uid" => $_SESSION["Logged_in_id"]))[0]["Armour_IDs"]);
                    } catch (PDOException $e) {
                        return FALSE;
                    }
                    break;
                default: 
                    return FALSE;
            }
            return $old_ids[sizeof($old_ids) - 1];
        }
        /* Essentially if this function breaks it's likely a server error or malicious
         * Therefore returns false if it fails, true if successful
         */
        public static function replace_last_inserted_of_type() {
            // Fetch old item_id array from database
            $type = $_POST["type"];
            $old_id = $_POST["old_id"];
            switch($type) {
                case "armour":
                    $column_name = "Armour_IDs";
                    break;
                case "spell":
                    $column_name = "Spell_IDs";
                    break;
                case "stat_block":
                    $column_name = "NPC_Stat_Block_IDs";
                    break;
                case "weapon":
                    $column_name = "Weapon_IDs";
                    break;
                default:
                    return FALSE;
            }
            $sql = "SELECT `".$column_name."` FROM `User_Item_IDs` WHERE `User_ID`=:uid";
            try {
                $item_ids = DB::query($sql, array(":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return FALSE;
            }
            // Replace old item
            if ($item_ids) {
                $item_ids_arr = json_decode($item_ids[0][$column_name]);
                $item_ids_arr[sizeof($item_ids_arr) - 1] = $old_id;
            } else {
                return FALSE;
            }
            // Update the database
            $sql = "UPDATE `User_Item_IDs` SET `".$column_name."`=:new_item_ids WHERE `User_ID`=:uid";
            try {
                DB::query($sql, array(":new_item_ids" => json_encode($item_ids_arr), ":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return FALSE;
            }
            return TRUE;
        }

        /* Status codes
         * 0 - Successful return of data - data
         * 1 - Server error - null
         * 2 - No type defined - null
         */
        public static function get_all_item_data($ids, $type) {
            switch($type) {
                case "armour":
                    $sql = "SELECT `Name`, `Base_AC`, `Additional_Modifiers`, `Strength_Required`, `Stealth_Disadvantage`, `Weight`, `Value` FROM `Armours` WHERE";
                    $variables = array();
                    for ($i = 1; $i <= sizeof($ids); $i++) {
                        $sql .= " `ID`=:id" . $i . " OR";
                        $variables[":id" . $i] = $ids[$i - 1];
                    }
                    $sql = substr($sql, 0, -3) . ";";
                    try {
                        $data = DB::query($sql, $variables);
                    } catch (PDOException $e) {
                        return FALSE;
                    }
                    break;
                default:
                    return FALSE;
            }
            return $data;
        }
    }
?>