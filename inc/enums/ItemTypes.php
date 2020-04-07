<?php
    final class ItemTypes extends ItemTypeEnum {
        public static function Armour() { return self::_create("Armour"); }
        public static function Spell() { return self::_create("Spell"); }
        public static function StatBlock() { return self::_create("NPC Stat Block"); }
        public static function Weapon() { return self::_create("Weapon"); }
    }
?>