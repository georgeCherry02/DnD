<?php
    final class ActionPhases extends TypedEnum {
        public static function Movement() { return self::_create(1); }
        public static function Action() { return self::_create(2); }
        public static function BonusAction() { return self::_create(3); }
        public static function Reaction() { return self::_create(4); }

        public function getClassDisplayName() {
            return "Action Phases";
        }
    }
?>