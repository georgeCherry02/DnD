<?php
    final class Coins extends TypedEnum {
        public static function Platinum() { return self::_create("pp"); }
        public static function Gold() { return self::_create("gp"); }
        public static function Electrum() { return self::_create("ep"); }
        public static function Silver() { return self::_create("sp"); }
        public static function Copper() { return self::_create("cp"); }
    }
?>