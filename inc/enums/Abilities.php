<?php
    final class Abilities extends TypedEnum {
        public static function Strength() { return self::_create(1); }
        public static function Dexterity() { return self::_create(2); }
        public static function Constitution() { return self::_create(3); }
        public static function Intelligence() { return self::_create(4); }
        public static function Wisdom() { return self::_create(5); }
        public static function Charisma() { return self::_create(6); }
    }
?>