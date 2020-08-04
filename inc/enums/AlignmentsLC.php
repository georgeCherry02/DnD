<?php
    final class AlignmentsLC extends TypedEnum {
        public static function Lawful() { return self::_create(1); }
        public static function Neutral() { return self::_create(2); }
        public static function Chaotic() { return self::_create(3); }
    }
?>