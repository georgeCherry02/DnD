<?php
    final class MagicSchools extends TypedEnum {
        public static function Abjuration() { return self::_create(1); }
        public static function Conjuration() { return self::_create(2); }
        public static function Divination() { return self::_create(3); }
        public static function Enchantment() { return self::_create(4); }
        public static function Evocation() { return self::_create(5); }
        public static function Illusion() { return self::_create(6); }
        public static function Necromancy() { return self::_create(7); }
        public static function Transmutation() { return self::_create(8); }

        public function getClassDisplayName() {
            return "Schools of Magic";
        }
    }
?>