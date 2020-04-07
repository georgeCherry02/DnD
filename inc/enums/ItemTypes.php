<?php
    final class ItemTypes extends TypedEnum {
        public static function Armour() { return self::_create("Armour"); }
        public static function Spell() { return self::_create("Spell"); }
        public static function StatBlock() { return self::_create("NPC Stat Block"); }
        public static function Weapon() { return self::_create("Weapon"); }

        private function getFunctionalName() {
            return str_replace(" ", "_", $this->_value);
        }

        public function getPrettyName() {
            return $this->_value;
        }

        public function getTableName() {
            return $this->getFunctionalName()."s";
        }

        public function getItemLimitColumn() {
            return $this->getFunctionalName()."s_Limit";
        }

        public function getItemListColumn() {
            return $this->getFunctionalName()."_IDs";
        }

        public function getValidTableColumns() {
            switch ($this) {
                case ItemTypes::Armour():
                    return array("base_ac", "strength_required", "stealth_disadvantage", "weight");
                    break;
                case ItemTypes::Spell():
                    return array("level", "school", "casting_time", "range_type", "range_distance", "shape", "shape_size", "vocal", "somatic", "material", "concentration", "effect");
                    break;
                case ItemTypes::StatBlock():
                    return array("armour_class", "hit_points", "speed", "skill_proficiencies", "expertise", "experience_reward", "weapon_id_list", "spell_id_list");
                    break;
                case ItemTypes::Weapon():
                    return array("properties", "damage_type", "effective_range", "maximum_range");
                    break;
                default:
                    throw new OutOfRangeException();
            }
        }
    }
?>