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
            try {
                $item_type = ItemTypes::fromName($_POST["form_type"]);
            } catch (OutOfRangeException $e) {
                return array(1, null);
            }

            // Check if the user has the capacity to create the item
            $sql = "SELECT `".$item_type->getItemListColumn()."`, `".$item_type->getItemLimitColumn()."` FROM `User_Item_IDs` INNER JOIN `User_Limitations` ON User_Item_IDs.User_ID=User_Limitations.User_ID WHERE User_Item_IDs.User_ID=:uid;";
            try {
                $request = DB::query($sql, array(":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return array(2, "capacity");
            }
            $current_item_ids = json_decode($request[0][$item_type->getItemListColumn()]);
            $current_item_limit = $request[0][$item_type->getItemLimitColumn()];
            if (sizeof($current_item_ids) >= $current_item_limit) {
                return array(3, null);
            }
            
            // Structure data
            $sanitised_data = array();
            foreach($_POST as $key => $value) {
                // Check if the $key is a valid column name
                if (in_array($key, $item_type->getValidTableColumns())) {
                    // Put into sanitised data 
                    if (!empty($value)) {
                        $sanitised_data[$key] = $value;
                    }
                }
            }

            // Gather exceptions foreach type
            switch($item_type) {
                case ItemTypes::Armour():
                    $sanitised_data = self::manage_armour_creation_exceptions($sanitised_data);
                    break;
                case ItemTypes::Spell():
                    break;
                case ItemTypes::StatBlock():
                    break;
                case ItemTypes::Weapon():
                    break;
            }

            // Check a duplicate doesn't exist
            $sql = "SELECT `ID` FROM `".$item_type->getTableName()."` WHERE `Name` LIKE :name";
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
            $column_names_sql = "INSERT INTO `".$item_type->getTableName()."` (`Name`";
            $values_sql = ") VALUES (:name";
            foreach ($sanitised_data as $column_name => $column_value) {
                $column_names_sql .= ", `".$column_name."`";
                $values_sql .= ", :".$column_name;
            }
            $insert_sql = $column_names_sql . $values_sql . ");";
            try {
                DB::query($insert_sql, $sql_prepared_variables);
                // Fetch the item ID
                $sql = "SELECT `ID` FROM `".$item_type->getTableName()."` WHERE `Name`=:name ORDER BY `ID` DESC;";
                $item_id = DB::query($sql, array(":name" => $_POST["name"]))[0]["ID"];
            } catch (PDOException $e) {
                return array(2, "insert");
            }

            // Update the users item data
            // Fetch the initial state of the data
            $item_ids_fetch_sql = "SELECT `".$item_type->getItemListColumn()."` FROM `User_Item_IDs` WHERE `User_ID`=:uid;";
            try {
                $item_ids = DB::query($item_ids_fetch_sql, array(":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return array(2, "fetch_original_ids");
            }

            // Insert the new ID into this array
            $item_ids_arr = json_decode($item_ids[0][$item_type->getItemListColumn()]);
            try {
                array_push($item_ids_arr, $item_id);
            } catch (Exception $e) {
                return array(4, null);
            }

            // Push to the database
            $item_ids_push_sql = "UPDATE `User_Item_IDs` SET `".$item_type->getItemListColumn()."`=:item_ids WHERE `User_ID`=:uid";
            try {
                DB::query($item_ids_push_sql, array(":item_ids" => json_encode($item_ids_arr), ":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return array(2, "push_new_ids");
            }

            return array(0, $duplicate_id);
        }

        public static function clean_item_type_data() {
            $type = $_POST["form_type"];
            try {
                $item_type = ItemTypes::fromName($type);
            } catch (OutOfRangeException $e) {
                return FALSE;
            }

            $sql = "UPDATE `User_Item_IDs` SET `".$item_type->getItemListColumn()."`='[]' WHERE `User_ID`=:uid";
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
            for ($i = 1; $i <= 6; $i++) {
                $ability = Abilities::fromValue($i);
                if (isset($_POST[$ability->getName() . "_modifier"]) && $_POST[$ability->getName() . "_modifier"] == "1") {
                    array_push($additional_modifiers, $ability->getValue());
                }
            }
            $data["Additional_Modifiers"] = json_encode($additional_modifiers);
            // Parse coin value and manage input
            $coin_amounts = array();
            for ($i = 1; $i <= 5; $i++) {
                $coin = Coins::fromValue($i);
                if (isset($_POST[$coin->getName()."_amount"])) {
                    array_push($coin_amounts, filter_input(INPUT_POST, $coin->getName()."_amount", FILTER_VALIDATE_INT));
                } else {
                    array_push($coin_amounts, "0");
                }
            }
            $data["Value"] = json_encode($coin_amounts);
            return $data;
        }

        public static function get_last_inserted_of_type($item_type) {
            $sql = "SELECT `".$item_type->getItemListColumn()."` FROM `User_Item_IDs` WHERE `User_ID`=:uid;";
            try {
                $old_ids = json_decode(DB::query($sql, array(":uid" => $_SESSION["Logged_in_id"]))[0][$item_type->getItemListColumn()]);
            } catch (PDOException $e) {
                return FALSE;
            }
            return $old_ids[sizeof($old_ids) - 1];
        }

        public static function replace_last_inserted_of_type() {
            // Fetch old item_id array from database
            $type = $_POST["type"];
            $old_id = $_POST["old_id"];

            try {
                $item_type = ItemTypes::fromName($type);
            } catch (OutOfRangeException $e) {
                return FALSE;
            }

            $sql = "SELECT `".$item_type->getItemListColumn()."` FROM `User_Item_IDs` WHERE `User_ID`=:uid";
            try {
                $item_ids = DB::query($sql, array(":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return FALSE;
            }
            // Replace old item
            if ($item_ids) {
                $item_ids_arr = json_decode($item_ids[0][$item_type->getItemListColumn()]);
                $item_ids_arr[sizeof($item_ids_arr) - 1] = $old_id;
            } else {
                return FALSE;
            }
            // Update the database
            $sql = "UPDATE `User_Item_IDs` SET `".$item_type->getItemListColumn()."`=:new_item_ids WHERE `User_ID`=:uid";
            try {
                DB::query($sql, array(":new_item_ids" => json_encode($item_ids_arr), ":uid" => $_SESSION["Logged_in_id"]));
            } catch (PDOException $e) {
                return FALSE;
            }
            return TRUE;
        }

        public static function get_all_item_data($ids, $item_type) {
            // Switch type and determine base sql
            switch($item_type) {
                case ItemTypes::Armour():
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