<?php
    abstract class TypedEnum {
        private static $_instanced_values;

        protected $_value;
        private $_name;

        private function __construct($value, $name) {
            $this->_value = $value;
            $this->_name = $name;
        }

        private static function _fromGetter($getter, $value) {
            $reflection_class = new ReflectionClass(get_called_class());
            $methods = $reflection_class->getMethods(ReflectionMethod::IS_STATIC && ReflectionMethod::IS_PUBLIC);
            $class_name = get_called_class();

            foreach ($methods as $method) {
                if ($method-> class === $class_name) {
                    $enum_item = $method->invoke(NULL);
                    if ($enum_item instanceof $class_name && $enum_item->$getter() === $value) {
                        return $enum_item;
                    }
                }
            }

            throw new OutOfRangeException();
        }

        protected static function _create($value) {
            if (self::$_instanced_values === NULL) {
                self::$_instanced_values = array();
            }

            $class_name = get_called_class();

            if (!isset(self::$_instanced_values[$class_name])) {
                self::$_instanced_values[$class_name] = array();
            }

            if (!isset(self::$_instanced_values[$class_name][$value])) {
                $debug_trace = debug_backtrace();
                $last_caller = array_shift($debug_trace);

                while ($last_caller["class"] !== $class_name && count($debug_trace) > 0) {
                    $last_caller = array_shift($debug_trace);
                }

                self::$_instanced_values[$class_name][$value] = new static($value, $last_caller["function"]);
            }

            return self::$_instanced_values[$class_name][$value];
        }

        public static function ALL() {
            $reflection_class = new ReflectionClass(get_called_class());
            $methods = $reflection_class->getMethods(ReflectionMethod::IS_STATIC && ReflectionMethod::IS_PUBLIC);
            $class_name = get_called_class();

            $all_types = array();

            foreach ($methods as $method) {
                if ($method-> class === $class_name) {
                    $enum_item = $method->invoke(NULL);
                    if ($enum_item instanceof $class_name) {
                        array_push($all_types, $enum_item);
                    }
                }
            }

            return $all_types;
        }

        public static function fromValue($value) {
            return self::_fromGetter('getValue', $value);
        }
        
        public static function fromName($name) {
            return self::_fromGetter('getName', $name);
        }

        public function getValue() {
            return $this->_value;
        }

        public function getName() {
            return $this->_name;
        }

        public function getPrettyName() {
            $name = $this->_name;
            preg_match_all('/[A-Z][^A-Z]*/', $name, $matches);
            return implode(" ", $matches[0]);
        }

        public function getClassDisplayName() {
            return get_called_class();
        }
    }
?>