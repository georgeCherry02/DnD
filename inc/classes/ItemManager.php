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
                    $sanitised_data = self::manage_spell_creation_exceptions($sanitised_data);
                    break;
                case ItemTypes::StatBlock():
                    break;
                case ItemTypes::Weapon():
                    $sanitised_data = self::manage_weapon_creation_exceptions($sanitised_data);
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

            // Add description for data base insert
            $sql_prepared_variables[":desc"] = $_POST["description"];
            // Insert into a database
            $column_names_sql = "INSERT INTO `".$item_type->getTableName()."` (`Name`, `Description`";
            $values_sql = ") VALUES (:name, :desc";
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

        private static function manage_armour_creation_exceptions($data) {
            // Gather additional modifiers for the armour
            $data = self::gather_multi_select($data, "Additional_Modifiers", "modifier", Abilities::ALL());
            // Parse coin value and manage input
            $data = self::gather_multi_number($data, "Value", "pieces", Coins::ALL());
            return $data;
        }

        private static function manage_spell_creation_exceptions($data) {
            // Gather material value
            $data = self::gather_multi_number($data, "material_value", "pieces", Coins::ALL());
            // Gather effect magnitude
            if ($data["effect"] !== 3) {
                $data = self::gather_multi_number($data, "effect_dice", "amount", EffectDice::ALL());
            }
            return $data;
        }
        
        private static function manage_weapon_creation_exceptions($data) {
            // Gather weapon properties
            $weapon_properties = array();
            foreach (WeaponProperties::ALL() as $property) {
                if (isset($_POST[$property->getName()."_property"]) && $_POST[$property->getName()."_property"] == "1") {
                    array_push($weapon_properties, $property->getName());
                }
            }
            $data["Properties"] = json_encode($weapon_properties);

            // Declare damage id array
            $damage_ids = array();
            // Gather damage summary
            $damage_summary = self::gather_damage_summary();
            // Insert into database and put damage distribution IDs into array
            foreach ($damage_summary as $damage_type => $damage_distribution) {
                $sql = "INSERT INTO `Damage_Distributions` (d4, d6, d8, d10, d12, `Type`) VALUES (:d4, :d6, :d8, :d10, :d12, :damage_type)";
                $sql_variables = $damage_distribution;
                $sql_variables[":damage_type"] = $damage_type;
                // Add distribution to database
                try {
                    DB::query($sql, $sql_variables);
                } catch (PDOException $e) {
                    return FALSE;
                }
                // Fetch the ID of that distribution
                try {
                    $distribution_id = DB::query("SELECT `ID` FROM `Damage_Distributions` ORDER BY `ID` DESC LIMIT 1")[0]["ID"];
                } catch (PDOException $e) {
                    return FALSE;
                }
                // Add that ID to the damage_ids array
                array_push($damage_ids, $distribution_id);
            }
            // Add the encoded damage_ids to the weapon SQL variables
            $data["Damage_Distribution_IDs"] = json_encode($damage_ids);

            // If the weapons versatile, gather that damage
            // Set damage type to versatile as a flag
            if (in_array("Versatile", $weapon_properties)) {
                // Gather the versatile damage
                $versatile_damage = array();
                $running_total = 0;
                foreach (EffectDice::ALL() as $die) {
                    if (isset($_POST[$die->getName()."_versatile_damage"]) && filter_input(INPUT_POST, $die->getName()."_versatile_damage", FILTER_VALIDATE_INT)) {
                        $value = $_POST[$die->getName()."_versatile_damage"];
                        $versatile_damage[":".$die->getName()] = $value;
                        $running_total = $running_total + $value;
                    } else {
                        $versatile_damage[":".$die->getName()] = 0;
                    }
                }
                // If there's damage dealt, put this damage in database and add id to weapon SQL variables
                if ($running_total > 0) {
                    $sql = "INSERT INTO `Damage_Distributions` (d4, d6, d8, d10, d12, `Type`) VALUES (:d4, :d6, :d8, :d10, :d12, :damage_type)";
                    $sql_variables = $versatile_damage;
                    $sql_variables[":damage_type"] = "Versatile";
                    try {
                        DB::query($sql, $sql_variables);
                    } catch (PDOException $e) {
                        return FALSE;
                    }
                    try {
                        $damage_distribution_id = DB::query("SELECT `ID` FROM `Damage_Distributions` ORDER BY `ID` DESC LIMIT 1")[0]["ID"];
                    } catch (PDOException $e) {
                        return FALSE;
                    }
                    $data["Versatile_Damage_ID"] = $damage_distribution_id;
                }
            }

            // Gather weapon value information
            $data = self::gather_multi_number($data, "Value", "pieces", Coins::ALL());
            return $data;
        }

        private static function gather_damage_summary() {
            $damage_summary = array();
            foreach (DamageType::ALL() as $damage_type) {
                $damage_type_summary = array();
                $running_total = 0;
                foreach (EffectDice::ALL() as $die) {
                    if (isset($_POST[$die->getName()."_".$damage_type->getName()."_damage"]) && filter_input(INPUT_POST, $die->getName()."_".$damage_type->getName()."_damage", FILTER_VALIDATE_INT)) {
                        $value = $_POST[$die->getName()."_".$damage_type->getName()."_damage"];
                        $damage_type_summary[":".$die->getName()] = $value;
                        $running_total = $running_total + $value;
                    } else {
                        $damage_type_summary[":".$die->getName()] = 0;
                    }
                }
                if ($running_total > 0) {
                    $damage_summary[$damage_type->getName()] = $damage_type_summary;
                }
            }
            return $damage_summary;
        }

        private static function gather_multi_number($data, $column_name, $unique_descriptor, $enum_class) {
            $data_arr = array();
            foreach ($enum_class as $enum) {
                if (isset($_POST[$enum->getName()."_".$unique_descriptor]) && filter_input(INPUT_POST, $enum->getName()."_".$unique_descriptor, FILTER_VALIDATE_INT)) {
                    array_push($data_arr, $_POST[$enum->getName()."_".$unique_descriptor]);
                } else {
                    array_push($data_arr, "0");
                }
            }
            $data[$column_name] = json_encode($data_arr);
            return $data;
        }

        private static function gather_multi_select($data, $column_name, $unique_descriptor, $enum_class) {
            $data_arr = array();
            foreach ($enum_class as $enum) {
                if (isset($_POST[$enum->getName()."_".$unique_descriptor]) && $_POST[$enum->getName()."_".$unique_descriptor] == "1") {
                    array_push($data_arr, $enum->getValue());
                } 
            }
            $data[$column_name] = json_encode($data_arr);
            return $data;
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

        public static function get_last_inserted_of_type($item_type) {
            $sql = "SELECT `" . $item_type->getItemListColumn() . "` FROM `User_Item_IDs` WHERE `User_ID`=:uid;";
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
            $old_id = filter_input(INPUT_POST, "old_id", FILTER_VALIDATE_INT);
            $new_id = filter_input(INPUT_POST, "new_id", FILTER_VALIDATE_INT);

            if (!$old_id && !$new_id) {
                return FALSE;
            }

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

            // Delete the item the user uploaded to the database
            $sql = "DELETE FROM `".$item_type->getTableName()."` WHERE `ID`=:iid;";
            try {
                DB::query($sql, array(":iid" => $new_id));
            } catch (PDOException $e) {
                return FALSE;
            }

            return TRUE;
        }

        public static function get_all_item_data($ids, $item_type) {
            // Switch type and determine base sql
            switch($item_type) {
                case ItemTypes::Armour():
                    $sql = "SELECT `Name`, `Base_AC`, `Additional_Modifiers`, `Strength_Required`, `Stealth_Disadvantage`, `Weight`, `Value`, `Description`";
                    break;
                case ItemTypes::Spell():
                    $sql = "SELECT `Name`, `Level`, `School`, `Casting_Time`, `Range_Type`, `Range_Distance`, `Shape`, `Shape_Size`, `Vocal`, `Somatic`, `Material_Value`, `Concentration`, `Effect`, `Effect_Dice`, `Description`";
                    break;
                case ItemTypes::Weapon():
                    $sql = "SELECT `Name`, `Properties`, `Damage_Distribution_IDs`, `Effective_Range`, `Maximum_Range`, `Versatile_Damage_ID`, `Weight`, `Value`, `Description`";
                    break;
                default:
                    return FALSE;
            }

            // Add table to SQL
            $sql .= " FROM `".$item_type->getTableName()."`";
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

        public static function get_damage_distributions($ids) {
            $sql = "SELECT `d4`, `d6`, `d8`, `d10`, `d12`, `Type` FROM `Damage_Distributions` WHERE";
            $variables = array();
            for ($i = 1; $i <= sizeof($ids); $i++) {
                $sql .= " `ID`=:id" . $i . " OR";
                $variables[":id".$i] = $ids[$i - 1];
            }
            $sql = substr($sql, 0, -3).";";

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