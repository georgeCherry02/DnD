<?php
    final class SpellRangeTypes extends TypedEnum {
        public static function Range() { return self::_create(1); }
        public static function Touch() { return self::_create(2); }
        public static function Self() { return self::_create(3); }

        public function getClassDisplayName() {
            return "Range Types";
        }
    }
?>