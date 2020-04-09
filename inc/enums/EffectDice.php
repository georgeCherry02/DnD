<?php
    final class EffectDice extends TypedEnum {
        public static function d4() { return self::_create(4); }
        public static function d6() { return self::_create(6); }
        public static function d8() { return self::_create(8); }
        public static function d10() { return self::_create(10); }
        public static function d12() { return self::_create(12); }

        public function getClassDisplayName() {
            return "Dice";
        }
    }
?>