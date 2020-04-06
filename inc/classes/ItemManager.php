<?php
    class ItemManager {

        /* A general function to create items
         * 0 - Nominal execution, if there's a duplicate then the ID is passed in second slot of index
         * 1 - Invalid type supplied, null
         * 2 - Server error, Location of error
         * 3 - Reached current item limit, null
         * 4 - Data corruption, can't json_decode the data, null
         */
        public static function create_item() {
            // Validate insert type
            $type = $_POST["form_type"];
            switch($type) {
                case "armour":
                    $table_name = "Armours";
                    $id_column_name = "Armour_IDs";
                    $limit_column_name = "Armours_Limit";
                    $valid_column_names = VALID_ARMOUR_COLUMNS;
                    break;
                case "weapon":
                    $table_name = "Weapons";
                    $id_column_name = "Weapon_IDs";
                    $limit_column_name = "Weapons_Limit";
                    $valid_column_names = VALID_WEAPON_COLUMNS;
                    break;
                case "spell":
                    $table_name = "Spells";
                    $id_column_name = "Spell_IDs";
                    $limit_column_name = "Spells_Limit";
                    $valid_column_names = VALID_SPELL_COLUMNS;
                    break;
                case "stat_block":
                    $table_name = "NPC_Stat_Blocks";
                    $id_column_name = "NPC_Stat_Block_IDs";
                    $limit_column_name = "NPC_Stat_Blocks_Limit";
                    $valid_column_names = VALID_STAT_BLOCK_COLUMNS;
                    break;
                default:
                    return array(1, null);
            }

            // Check if the user has the capacity to create the item
            $sql = "SELECT `".$id_column_name."`, `".$limit_column_name."` FROM `User_Item_IDs` INNER JOIN `User_Limitations` ON User_Item_IDs.User_ID=User_Limitations.User_ID WHERE User_Item_IDs.User_ID=:uid;";
            try {
                $request = DB::query($sql, array(":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return array(2, "capacity");
            }
            $current_item_ids = json_decode($request[0][$id_column_name]);
            $current_item_limit = $request[0][$limit_column_name];
            if (sizeof($current_item_ids) >= $current_item_limit) {
                return array(3, null);
            }
            
            // Structure data
            $sanitised_data = array();
            foreach($_POST as $key => $value) {
                // Check if the $key is a valid column name
                if (in_array($key, $valid_column_names)) {
                    // Put into sanitised data 
                    if (!empty($value)) {
                        $sanitised_data[$key] = $value;
                    }
                }
            }

            // Gather exceptions foreach type
            switch($type) {
                case "armour":
                    $sanitised_data = self::manage_armour_creation_exceptions($sanitised_data);
                    break;
                case "spell":
                    break;
                case "stat_block":
                    break;
                case "weapon":
                    break;
            }

            // Check a duplicate doesn't exist
            $sql = "SELECT `ID` FROM `".$table_name."` WHERE `Name` LIKE :name";
            $sql_prepared_variables = array(":name" => $_POST["name"]);
            foreach ($sanitised_data as $column_name => $column_value) {
                $sql .= " && `".$column_name."`=:".$column_name;
                $sql_prepared_variables[":".$column_name] = $column_value;
            }
            $sql .= ";";
            try {
                $duplicate_id = DB::query($sql, $sql_prepared_variables);
            } catch (PDOException $e) {
                return array(2, "duplicate_check");
            }

            // Insert into a database
            $column_names_sql = "INSERT INTO `".$table_name."` (`Name`";
            $values_sql = ") VALUES (:name";
            foreach ($sanitised_data as $column_name => $column_value) {
                $column_names_sql .= ", `".$column_name."`";
                $values_sql .= ", :".$column_name;
            }
            $insert_sql = $column_names_sql . $values_sql . ");";
            try {
                DB::query($insert_sql, $sql_prepared_variables);
                // Fetch the item ID
                $sql = "SELECT `ID` FROM `".$table_name."` WHERE `Name`=:name ORDER BY `ID` DESC;";
                $item_id = DB::query($sql, array(":name" => $_POST["name"]))[0]["ID"];
            } catch (PDOException $e) {
                return array(2, "insert");
            }

            // Update the users item data
            // Fetch the initial state of the data
            $item_ids_fetch_sql = "SELECT `".$id_column_name."` FROM `User_Item_IDs` WHERE `User_ID`=:uid;";
            try {
                $item_ids = DB::query($item_ids_fetch_sql, array(":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return array(2, "fetch_original_ids");
            }
            try {
                $item_ids_arr = json_decode($item_ids[0][$id_column_name]);
            } catch (Exception $e) {
                return array(4, null);
            }

            // Insert the new ID into this array
            array_push($item_ids_arr, $item_id);

            // Push to the database
            $item_ids_push_sql = "UPDATE `User_Item_IDs` SET `".$id_column_name."`=:item_ids WHERE `User_ID`=:uid";
            try {
                DB::query($item_ids_push_sql, array(":item_ids" => json_encode($item_ids_arr), ":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return array(2, "push_new_ids");
            }

            return array(0, $duplicate_id);
        }

        public static function clean_item_type_data() {
            $type = $_POST["form_type"];
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

            $sql = "UPDATE `User_Item_IDs` SET `".$column_name."`='[]' WHERE `User_ID`=:uid";
            try {
                DB::query($sql, array(":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return FALSE;
            }

            return TRUE;
        }

        private static function manage_armour_creation_exceptions($data) {
            // Gather additional modifiers for the armour
            $additional_modifiers = array();
            for ($i = 0; $i < sizeof(ABILITIES); $i++) {
                if (isset($_POST[ABILITIES[$i] . "_modifier"]) && $_POST[ABILITIES[$i] . "_modifier"] == "1") {
                    array_push($additional_modifiers, $i + 1);
                }
            }
            $data["Additional_Modifiers"] = json_encode($additional_modifiers);
            return $data;
        }

        /* Status Codes
         * 0 - Successful return of last item ID - ID that should be returned
         * 1 - Server error - null
         * 2 - No type defined - null
         */
        public static function get_last_inserted_of_type($type) {
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
            $sql = "SELECT `".$column_name."` FROM `User_Item_IDs` WHERE `User_ID`=:uid;";
            try {
                $old_ids = json_decode($sql, array(":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return false;
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
            // Switch type and determine base sql
            switch($type) {
                case "armour":
                    $sql = "SELECT `Name`, `Base_AC`, `Additional_Modifiers`, `Strength_Required`, `Stealth_Disadvantage`, `Weight`, `Value` FROM `Armours`";
                    break;
                default:
                    return FALSE;
            }

            // Add conditional for $ids array 
            $sql .= " WHERE";
            $variables = array();
            for ($i = 1; $i <= sizeof($ids); $i++) {
                $sql .= " `ID`=:id" . $i . " OR";
                $variables[":id" . $i] = $ids[$i - 1];
            }
            $sql = substr($sql, 0, -3) . ";";

            // Make request
            try {
                $data = DB::query($sql, $variables);
            } catch (PDOException $e) {
                return FALSE;
            }
            return $data;
        }
    }
?>