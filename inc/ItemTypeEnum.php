<?php
    abstract class ItemTypeEnum {
        private static $_instanced_values;

        private $_name;
        private $_pretty_name;

        private function __construct($pretty_name, $name) {
            $this->_name = $name;

            $this->_pretty_name = $pretty_name;
        }

        protected static function _create($pretty_name) {
            if (self::$_instanced_values === NULL) {
                self::$_instanced_values = array();
            }

            $class_name = get_called_class();

            if (!isset(self::$_instanced_values[$class_name])) {
                self::$_instanced_values[$class_name] = array();
            }

            if (!isset(self::$_instanced_values[$class_name][$pretty_name])) {
                $debug_trace = debug_backtrace();
                $last_caller = array_shift($debug_trace);

                while ($last_caller["class"] !== $class_name && count($debug_trace) > 0) {
                    $last_caller = array_shift($debug_trace);
                }

                self::$_instanced_values[$class_name][$pretty_name] = new static($pretty_name, $last_caller["function"]);
            }

            return self::$_instanced_values[$class_name][$pretty_name];
        }

        public static function fromName($name) {
            $reflection_class = new ReflectionClass(get_called_class());
            $methods = $reflection_class->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC);
            $class_name = get_called_class();

            foreach ($methods as $method) {
                if ($method->class === $class_name) {
                    $enum_item = $method->invoke(NULL);
                    if ($enum_item instanceof $class_name && $enum_item->getName() === $name) {
                        return $enum_item;
                    }
                }
            }

            throw new OutOfRangeException();
        }

        private function getFunctionalName() {
            return str_replace(" ", "_", $this->_pretty_name);
        }

        public function getPrettyName()  {
            return $this->_pretty_name;
        }

        public function getTableName() {
            return self::getFunctionalName()."s";
        }

        public function getItemLimitColumn() {
            return self::getFunctionalName()."s_Limit";
        }

        public function getItemListColumn() {
            return self::getFunctionalName()."_IDs";
        }

        public function getValidTableColumns() {
            switch ($this->_name) {
                case "Armour":
                    return array("base_ac", "strength_required", "stealth_disadvantage", "weight", "value");
                    break;
                case "Spell":
                    return array("level", "school", "casting_time", "range_type", "range_distance", "shape", "shape_size", "vocal", "somatic", "material", "concentration", "effect");
                    break;
                case "StatBlock":
                    return array("armour_class", "hit_points", "speed", "skill_proficiencies", "expertise", "experience_reward", "weapon_id_list", "spell_id_list");
                    break;
                case "Weapon":
                    return array("properties", "damage_type", "effective_range", "maximum_range");
                    break;
                default:
                    throw new OutOfRangeException();
            }
        }

        public function getName() {
            return $this->_name;
        }
    }
?>