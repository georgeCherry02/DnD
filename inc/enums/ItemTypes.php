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
                    return array("level", "school", "casting_time", "duration_type", "duration", "range_type", "range_distance", "shape", "shape_size", "vocal", "somatic", "concentration", "effect");
                    break;
                case ItemTypes::StatBlock():
                    return array("Hit_Points", "Speed", "Experience_Reward", "Armour_ID");
                    break;
                case ItemTypes::Weapon():
                    return array("Effective_Range", "Maximum_Range", "Weight");
                    break;
                default:
                    throw new OutOfRangeException();
            }
        }

        public function getDuplicateCheckColumns() {
            switch($this) {
                case ItemTypes::Armour():
                    return array("Base_AC", "Additional_Modifiers", "Stealth_Disadvantage");
                    break;
                case ItemTypes::Spell():
                    return array();
                    break;
                case ItemTypes::StatBlock():
                    return array();
                    break;
                case ItemTypes::Weapon():
                    return array("Properties", "Effective_Range", "Maximum_Range");
                    break;
                default:
                    throw new OutOfRangeException();
            }
        }
    }
?>