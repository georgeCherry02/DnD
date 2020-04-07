<?php
    final class Coins extends TypedEnum {
        public static function Platinum() { return self::_create(1); }
        public static function Gold() { return self::_create(2); }
        public static function Electrum() { return self::_create(3); }
        public static function Silver() { return self::_create(4); }
        public static function Copper() { return self::_create(5); }

        public function getAbbreviation() {
            switch($this->_value) {
                case 1:
                    return "pp";
                    break;
                case 2:
                    return "gp";
                    break;
                case 3:
                    return "ep";
                    break;
                case 4:
                    return "sp";
                    break;
                case 5:
                    return "cp";
                    break;
            }
        }
    }
?>