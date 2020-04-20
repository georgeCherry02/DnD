<?php
    final class WeaponProperties extends TypedEnum {
        public static function Ammunition() { return self::_create("Weapons with the <i>Ammunition</i> property requires ammunition to make a ranged attack."); }
        public static function Finesse() { return self::_create("Weapons with the <i>Finesse</i> property can use either the Strength or Dexterity modifier for &#34Attack&#34 and &#34Damage&#34 rolls."); }
        public static function Heavy() { return self::_create("Small creatures have disadvantage on attack rolls with <i>Heavy</i> weapons."); }
        public static function Light() { return self::_create("Weapons with the <i>Light</i> property are easy to handle. This makes them ideal for two-weapon fighting."); }
        public static function Loading() { return self::_create("Weapons with the <i>Loading</i> property can only fire one piece of ammunition when you use an <i>Action</i>, <i>Bonus Action</i> or <i>Reaction</i> to fire it."); }
        public static function Range() { return self::_create("Weapons with the <i>Range</i> property can be used to make ranged attacks. Ranged weapons have an effective and maximum range."); }
        public static function Reach() { return self::_create("Weapons with the <i>Reach</i> property can make melee attacks on targets up to an additional 5ft away."); }
        public static function Special() { return self::_create("Weapons with the <i>Special</i> property have unusual rules about their use. Find them in the weapons description."); }
        public static function Thrown() { return self::_create("Weapons with the <i>Thrown</i> property are typically melee weapons that can also be thrown to make a ranged attack. You use the same modifier as you would normally use to make an attack with that weapon."); }
        public static function TwoHanded() { return self::_create("Weapons with the <i>Two-handed</i> property require two hands to use."); }
        public static function Versatile() { return self::_create("Weapons with the <i>Versatile</i> property can be used with one or two hands, the default damage is one-handed, the <i>Versatile</i> damage is two-handed"); }
    }
?>