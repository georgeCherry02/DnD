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
                    $sanitised_data = self::manage_stat_block_exceptions($sanitised_data);
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
            $effect_summary = array();
            if ($data["effect"] == 1) {
                // Already have written a method to parse damages
                $effect_summary = self::gather_damage_summary();
            } else if ($data["effect"] == 2) {
                // This will fetch a healing summary if that's how the spell works
                $healing_summary = array();
                foreach (EffectDice::ALL() as $die) {
                    $value = 0;
                    if (isset($_POST[$die->getName()."_healing"]) && filter_input(INPUT_POST, $die->getName()."_healing", FILTER_VALIDATE_INT)) {
                        $value = $_POST[$die->getName()."_healing"];
                    }
                    $healing_summary[":".$die->getName()] = $value;
                }
                $effect_summary["healing"] = $healing_summary;
            }

            // Push effects into database and fetch a list of IDs
            $effect_ids = array();
            foreach ($effect_summary as $effect => $effect_distribution) {
                $sql = "INSERT INTO `Damage_Distributions` (d4, d6, d8, d10, d12, `Type`) VALUES (:d4, :d6, :d8, :d10, :d12, :effect_type)";
                $sql_variables = $effect_distribution;
                // Note this could be "healing" hence the word effect rather than just damage_type
                $sql_variables[":effect_type"] = $effect;
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
                array_push($effect_ids, $distribution_id);
            }
            $data["Effect_IDs"] = json_encode($effect_ids);
            return $data;
        }

        private static function manage_stat_block_exceptions($data) {
            // Gather ability scores
            $ability_summary = self::gather_ability_summary();
            $beginning_of_sql = "INSERT INTO `Ability_Distributions` (";
            $end_of_sql = ") VALUES (";
            foreach (Abilities::ALL() as $ability) {
                $beginning_of_sql .= $ability->getName() . ", ";
                $end_of_sql .= ":" . $ability->getName() . ", ";
            }
            $sql = substr($beginning_of_sql, 0, -2) . substr($end_of_sql, 0, -2) . ")";
            // Commit abilities to database
            try {
                DB::query($sql, $ability_summary);
            } catch (PDOException $e) {
                return FALSE;
            }
            // Fetch the ID of that distribution
            try {
                $distribution_id = DB::query("SELECT `ID` FROM `Ability_Distributions` ORDER BY `ID` DESC LIMIT 1")[0]["ID"];
            } catch (PDOException $e) {
                return FALSE;
            }
            $data["Ability_Scores_ID"] = $distribution_id;

            // Gather skill proficiencies
            $data = self::gather_multi_select($data, "Skill_Proficiencies", "proficiency", Skills::ALL());

            // Gather expertise
            $data = self::gather_multi_select($data, "Expertise", "expertise", Skills::ALL());

            // Gather item IDs and push to lists
            $data["Spell_ID_List"] = array();
            $data["Weapon_ID_List"] = array();
            // Fetch the list of owned item IDs so that you can validate these items have been added by the user
            $owned_item_ids = self::get_owned_items();
            foreach($_POST as $key => $value) {
                // Define patterns to filter POSTed variables
                $weapon_pattern = "/^" . ItemTypes::Weapon()->getName() . "_/";
                $spell_pattern = "/^" . ItemTypes::Spell()->getName() . "_/";
                if (preg_match($weapon_pattern, $key)) {
                    $current_item_type = ItemTypes::Weapon();
                } else if (preg_match($spell_pattern, $key)) {
                    $current_item_type = ItemTypes::Spell();
                } else {
                    // Skip this POST variable if it doesn't fit one of the patterns
                    continue;
                }
                // Parse the item ID from the key, strpos returns the last occurence of the "needle" so this will always work
                $item_id = substr($key, strpos($key, "_") + 1);
                // Check if the item associated with the ID is owned by the user
                if (in_array($item_id, $owned_item_ids[$current_item_type->getItemListColumn()])) {
                    if (filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT) && $value == 1) {
                        array_push($data[$current_item_type->getName()."_ID_List"], $item_id);
                    }
                }
            }
            // Encode appropriately for upload to database
            $data["Spell_ID_List"] = json_encode($data["Spell_ID_List"]);
            $data["Weapon_ID_List"] = json_encode($data["Weapon_ID_List"]);

            // Gather spell slots
            $spell_slot_sql_variables = array();
            $beginning_of_spell_slot_sql = "INSERT INTO `Spell_Slot_Distributions` (";
            $end_of_spell_slot_sql = ") VALUES (";
            for ($i = 1; $i <= 9; $i++) {
                // Determine the number of spell slots for each level, using 0 as a default
                $value = 0;
                if (isset($_POST["spell_slot_level_".$i]) && filter_input(INPUT_POST, "spell_slot_level_".$i, FILTER_VALIDATE_INT)) {
                    $value = $_POST["spell_slot_level_".$i];
                }
                // Put these values into an array ready to create a request
                $spell_slot_sql_variables[":level".$i] = $value;
                // Assemble the SQL for the request
                $beginning_of_spell_slot_sql .= "`Level_".$i."`, ";
                $end_of_spell_slot_sql .= ":level".$i.", ";
            }
            // Trim the sections of SQL appropriately and concatenate into final SQL
            $spell_slot_sql = substr($beginning_of_spell_slot_sql, 0, -2) . substr($end_of_spell_slot_sql, 0, -2) . ")";
            try {
                DB::query($spell_slot_sql, $spell_slot_sql_variables);
            } catch (PDOException $e) {
                return FALSE;
            }
            // Go fetch the ID of the inserted variables
            try {
                $distribution_id = DB::query("SELECT `ID` FROM `Spell_Slot_Distributions` ORDER BY `ID` DESC LIMIT 1")[0]["ID"]; 
            } catch (PDOException $e) {
                return FALSE;
            }
            $data["Spell_Slot_Distribution_ID"] = $distribution_id;

            // Fetch and validate the number of features first
            $feature_ids = array();
            if (filter_input(INPUT_POST, "feature_amount", FILTER_VALIDATE_INT)) {
                $number_of_features = $_POST["feature_amount"];
                for ($i = 1; $i <= $number_of_features; $i++) {
                    $feature_sql_variables = array();
                    $feature_sql_variables[":name"] = $_POST["feature_".$i."_name"];
                    $feature_sql_variables[":desc"] = $_POST["feature_".$i."_desc"];
                    $feature_sql = "INSERT INTO `Features` (`Name`, `Description`) VALUES (:name, :desc)";
                    try {
                        DB::query($feature_sql, $feature_sql_variables);
                    } catch (PDOException $e) {
                        return FALSE;
                    }
                    try {
                        $feature_id = DB::query("SELECT `ID` FROM `Features` ORDER BY `ID` DESC LIMIT 1")[0]["ID"];
                    } catch (PDOException $e) {
                        return FALSE;
                    }
                    array_push($feature_ids, $feature_id);
                }
            }
            $data["Features_ID_List"] = json_encode($feature_ids);

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

        private static function gather_ability_summary() {
            $ability_summary = array();
            foreach (Abilities::ALL() as $ability) {
                if (isset($_POST[$ability->getName()."_modifier"]) && filter_input(INPUT_POST, $ability->getName()."_modifier", FILTER_VALIDATE_INT)) {
                    $ability_summary[":".$ability->getName()] = $_POST[$ability->getName()."_modifier"];
                } else {
                    $ability_summary[":".$ability->getName()] = 0;
                }
            }
            return $ability_summary;
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
                    if ($enum instanceof Skills) {
                        array_push($data_arr, $enum->getName());
                    } else {
                        array_push($data_arr, $enum->getValue());
                    }
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

        public static function get_owned_items() {
            $sql = "SELECT `Armour_IDs`, `Spell_IDs`, `Weapon_IDs` FROM `User_Item_IDs` WHERE `User_ID`=:uid";
            $sql_variables = array(":uid" => $_SESSION["Logged_in_id"]);
            try {
                $result = DB::query($sql, $sql_variables);
            } catch (PDOException $e) {
                return FALSE;
            }
            return $result[0];
        }

        public static function get_all_item_data($ids, $item_type) {
            // Switch type and determine base sql
            switch($item_type) {
                case ItemTypes::Armour():
                    $sql = "SELECT `ID`, `Name`, `Base_AC`, `Additional_Modifiers`, `Strength_Required`, `Stealth_Disadvantage`, `Weight`, `Value`, `Description`";
                    break;
                case ItemTypes::Spell():
                    $sql = "SELECT `ID`, `Name`, `Level`, `School`, `Casting_Time`, `Range_Type`, `Range_Distance`, `Shape`, `Shape_Size`, `Vocal`, `Somatic`, `Material_Value`, `Concentration`, `Effect`, `Effect_IDs`, `Description`";
                    break;
                case ItemTypes::Weapon():
                    $sql = "SELECT `ID`, `Name`, `Properties`, `Damage_Distribution_IDs`, `Effective_Range`, `Maximum_Range`, `Versatile_Damage_ID`, `Weight`, `Value`, `Description`";
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