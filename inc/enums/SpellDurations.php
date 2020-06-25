<?php
    final class SpellDurations extends TypedEnum {
        public static function Instantaneous() { return self::_create(0); }
        public static function Round() { return self::_create(1); }
        public static function Minute() { return self::_create(2); }
        public static function Hour() { return self::_create(3); }
        public static function Day() { return self::_create(4); }

        public function getClassDisplayName() {
            return "Spell Durations";
        }

        public function getUnits() {
            return strtolower($this->getName())."s";
        }
    }
?>