<?php
    final class Races extends TypedEnum {
        public static function Dwarf() { return self::_create(1); }
        public static function Elf() { return self::_create(2); }
        public static function Halfling() { return self::_create(3); }
        public static function Human() { return self::_create(4); }
        public static function Dragonborn() { return self::_create(5); }
        public static function Gnome() { return self::_create(6); }
        public static function HalfElf() { return self::_create(7); }
        public static function HalfOrc() { return self::_create(8); }
        public static function Tiefling() { return self::_create(8); }
    }
?>