<?php
    class Character {
        public static function fetch_character_name($character_id) {
            $sql = "SELECT `Name` FROM `Characters` WHERE `ID`=:id";
            $sql_var = array(":id" => $character_id);
            try {
                $name = DB::query($sql, $sql_var)[0]["Name"];
            } catch (PDOException $e) {
                return false;
            }
            return $name;
        }
        public static function fetch_character_ability_distribution($character_id) {
            $sql = "SELECT `Ability_Scores_ID` FROM `Characters` WHERE `ID`=:cid";
            $sql_var = array(":cid" => $character_id);
            try {
                $ability_id = DB::query($sql, $sql_var)[0]["Ability_Scores_ID"];
            } catch (PDOException $e) {
                return false;
            }
            $sql = "SELECT * FROM `Ability_Distributions` WHERE `ID`=:aid";
            $sql_var = array(":aid" => $ability_id);
            try {
                $ability_distribution = DB::query($sql, $sql_var)[0];
            } catch (PDOException $e) {
                return false;
            }
            return $ability_distribution;
        }
        public static function fetch_character_info($character_id) {
            $sql = "SELECT `Name`, `Class`, `Level`, `Speed`, `Hit_Point_Maximum`, `Current_Hit_Points`, `Temporary_Hit_Points`, `Racial_Spell_Casting_Ability`, `Class_Spell_Casting_Ability`, `Total_Spell_Slot_Distribution_ID`, `Current_Spell_Slot_Distribution_ID`, `Weapon_ID_List`, `Spell_ID_List` FROM `Characters` WHERE `ID`=:id";
            $sql_var = array(":id" => $character_id);
            try {
                $character_info = DB::query($sql, $sql_var)[0];
            } catch (PDOException $e) {
                return false;
            }
            $character_abilities = self::fetch_character_ability_distribution($character_id);
            if ($character_abilities) {
                $character_info["Abilities"] = $character_abilities;
            }
            $armour_class = self::fetch_character_armour_class($character_id);
            if ($armour_class) {
                $character_info["AC"] = $armour_class;
            }
            $character_info["PP"] = 10 + self::get_ability_modifier($character_abilities["Wisdom"]);
            return $character_info;
        }
        private static function fetch_character_armour_class($character_id) {
            $aid_sql = "SELECT `Armour_ID` FROM `Characters` WHERE `ID`=:id";
            $aid_sql_var = array(":id" => $character_id);
            try {
                $armour_id = DB::query($aid_sql, $aid_sql_var)[0]["Armour_ID"];
            } catch (PDOException $e) {
                return false;
            }
            $ability_info = self::fetch_character_ability_distribution($character_id);
            $armour_info = ItemManager::get_all_item_data(array($armour_id), ItemTypes::Armour())[0];
            if ($armour_info) {
                $armour_class = $armour_info["Base_AC"];
                $additional_modifiers = json_decode($armour_info["Additional_Modifiers"]);
                foreach ($additional_modifiers as $add_mod) {
                    $ability_type = Abilities::fromValue($add_mod);
                    $modifier_value = self::get_ability_modifier($ability_info[$ability_type->getName()]);
                    $armour_class += $modifier_value;
                }
                return $armour_class;
            } else {
                return false;
            }
            return false;
        }
        public static function add_temporary_hit_points($temp_hitpoints, $character_id) {
            $sql = "UPDATE `Characters` SET `Temporary_Hit_Points`=:temp WHERE `ID`=:cid";
            $sql_var = array(":temp" => $temp_hitpoints, ":cid" => $character_id);
            try {
                DB::query($sql, $sql_var);
            } catch (PDOException $e) {
                return false;
            }
            return true;
        }
        public static function modify_health($amount, $damaging, $character_id) {
            $character_info = self::fetch_character_info($character_id);
            $init_health = $character_info["Current_Hit_Points"];
            $max_health = $character_info["Hit_Point_Maximum"];
            $temp_health = $character_info["Temporary_Hit_Points"];
            if ($damaging) {
                // Manage temporary health
                if ($amount <= $temp_health) {
                    $temp_health = $temp_health - $amount;
                    return self::set_health($init_health, $temp_health, $character_id);
                }
                $amount = $amount - $temp_health;
                $temp_health = 0;
                // Manage current health
                $init_health = $init_health - $amount;
                if ($init_health < 0) {
                    $init_health = 0;
                }
                return self::set_health($init_health, $temp_health, $character_id);
            }
            // Manage currrent health
            $init_health = $init_health + $amount;
            if ($init_health > $max_health) {
                $init_health = $max_health;
            }
            return self::set_health($init_health, $temp_health, $character_id);
        }
        private static function set_health($health, $temp_health, $character_id) {
            $sql = "UPDATE `Characters` SET `Current_Hit_Points`=:health, `Temporary_Hit_Points`=:temp WHERE `ID`=:cid";
            $sql_var = array(":health" => $health, ":temp" => $temp_health, ":cid" => $character_id);
            try {
                DB::query($sql, $sql_var);
            } catch (PDOException $e) {
                return false;
            }
            return true;
        }
        public static function modify_spell_slots($level, $addition, $character_id) {
            $character_info = self::fetch_character_info($character_id);
            $initial_spell_slots = ItemManager::get_spell_slot_distribution($character_info["Current_Spell_Slot_Distribution_ID"]);
            $max_spell_slots = ItemManager::get_spell_slot_distribution($character_info["Total_Spell_Slot_Distribution_ID"]);
            $initial_amount_at_level = $initial_spell_slots["Level_".$level];
            $max_amount_for_level = $max_spell_slots["Level_".$level];
            if ($addition) {
                if ($initial_amount_at_level == $max_amount_for_level) {
                    return false;
                }
                $final_amount_for_level = $initial_amount_at_level + 1;
            } else { 
                if ($initial_amount_at_level == 0) {
                    return false;
                }
                $final_amount_for_level = $initial_amount_at_level - 1;
            }
            return ItemManager::update_spell_slot_distribution($level, $final_amount_for_level, $character_info["Current_Spell_Slot_Distribution_ID"]);
        }
        private static function get_ability_modifier($ability_score) {
            $shift = $ability_score - 10;
            $modifier = floor($shift / 2);
            return $modifier;
        }
    }
?>