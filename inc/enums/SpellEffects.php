<?php
    final class SpellEffects extends TypedEnum {
        public static function Damage() { return self::_create(1); } 
        public static function Create() { return self::_create(2); } 
        public static function RollPlay() { return self::_create(3); } 
        
        public function getClassDisplayName() {
            return "Spell Effects";
        }
    }
?>