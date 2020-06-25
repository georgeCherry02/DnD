<?php
    final class SpellEffects extends TypedEnum {
        public static function Damage() { return self::_create(1); } 
        public static function Healing() { return self::_create(2); } 
        public static function Rollplay() { return self::_create(3); } 
        
        public function getClassDisplayName() {
            return "Spell Effects";
        }
    }
?>