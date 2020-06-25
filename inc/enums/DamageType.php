<?php
    final class DamageType extends TypedEnum {
        public static function Acid() { return self::_create("The corrosive spray of a Black Dragon&#39s breath and the dissolving enzymes secreted by a black pudding deal <i>acid</i> damage."); }
        public static function Bludgeoning() { return self::_create("Blunt force attacks &#45 hammers, falling, constriction, and the like &#45 deal <i>bludgeoning</i> damage.");}
        public static function Cold() { return self::_create("The infernal chill radiating from an ice devil&#39s spear and the frigid blast of a white dragon&#39s breath deal <i>cold</i> damage."); }
        public static function Fire() { return self::_create("Red dragons breathe fire, and many spells conjure flames to deal <i>fire</i> damage."); }
        public static function Force() { return self::_create("Force is a pure magical energy focused into a damaging form. Most effects that deal <i>force</i> damage are spells, including magic missile and spiritual weapon."); }
        public static function Lightning() { return self::_create("A lightning bolt spell and a blue dragon&#39s breath deal <i>lightning</i> damage."); }
        public static function Necrotic() { return self::_create("<i>Necrotic</i> damage, dealt by certain undead and a spell such as chill touch, withers matter and even the soul."); }
        public static function Piercing() { return self::_create("Puncturing and impaling attacks, including spears and monster&#39s bites, deal <i>piercing</i> damage."); }
        public static function Poison() { return self::_create("Venomous stings and the toxic gas of a green dragon&#39s breath deal <i>poison</i> damage."); }
        public static function Psychic() { return self::_create("Mental abilities such as a mind flayer&#39s psionic blast deal <i>psychic</i> damage."); }
        public static function Radiant() { return self::_create("<i>Radiant</i> damage, dealt by a cleric&#39s flame strike spell or an angel&#39s smiting weapon, sears the flesh like fire and overloads the spirit with power."); }
        public static function Slashing() { return self::_create("Swords, axes, and monsters&#39 claws deal slashing damage."); }
        public static function Thunder() { return self::_create("A concussive burst of sound, such as the effect of the thunderwave spell, deals <i>thunder</i> damage."); }
    }
?>