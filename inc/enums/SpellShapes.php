<?php
    final class SpellShapes extends TypedEnum {
        public static function Cone() { return self::_create(1); }
        public static function Cube() { return self::_create(2); }
        public static function Cylinder() { return self::_create(3); }
        public static function Beam() { return self::_create(4); }
        public static function Sphere() { return self::_create(5); }

        public function getClassDisplayName() {
            return "Shapes";
        }
    } 
?>