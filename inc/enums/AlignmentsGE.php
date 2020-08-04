<?php
    final class AlignmentsGE extends TypedEnum {
        public static function Good() { return self::_create(1); }
        public static function Neutral() { return self::_create(2); }
        public static function Evil() { return self::_create(3); }
    }
?>