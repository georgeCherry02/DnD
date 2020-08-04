<?php
    final class Classes extends TypedEnum {
        public static function Barbarian() { return self::_create(1); }
        public static function Bard() { return self::_create(2); }
        public static function Cleric() { return self::_create(3); }
        public static function Druid() { return self::_create(4); }
        public static function Fighter() { return self::_create(5); }
        public static function Monk() { return self::_create(6); }
        public static function Paladin() { return self::_create(7); }
        public static function Ranger() { return self::_create(8); }
        public static function Rogue() { return self::_create(9); }
        public static function Sorcerer() { return self::_create(10); }
        public static function Warlock() { return self::_create(11); }
        public static function Wizard() { return self::_create(12); }
    }
?>