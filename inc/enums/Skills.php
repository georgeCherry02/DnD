<?php
    final class Skills extends TypedEnum {

        private $_paired_ability;

        public static function Acrobatics() { 
            $result = self::_create("<i>Acrobatics</i> checks cover attempts to stay on your feet in tricky situations, such as when you&#39re trying to run across a sheet of ice, balance on a tightrope, or stay upright on a rocking ship&#39s deck. It also covers acrobatic stunts, including dives, rolls, somersaults, and flips.");
            $result->_paired_ability = Abilities::Dexterity();
            return $result;
        }
        public static function AnimalHandling() { 
            $result = self::_create("<i>Animal Handling</i> checks cover whether you can calm down a domesticated animal, keep a mount from getting spooked, or intuit an animal&#39s intentions.");
            $result->_paired_ability = Abilities::Wisdom();
            return $result;
        }
        public static function Arcana() { 
            $result = self::_create("<i>Arcana</i> checks measure your ability to recall lore about spells, magic items, eldritch symbols, magical traditions, the planes of existence, and the inhabitants of those planes.");
            $result->_paired_ability = Abilities::Intelligence();
            return $result;
        }
        public static function Athletics() { 
            $result = self::_create("<i>Athletics</i> checks cover difficult situations you encounter while climbing, jumping, or swimming.");
            $result->_paired_ability = Abilities::Strength();
            return $result;
        }
        public static function Deception() { 
            $result = self::_create("<i>Deception</i> checks cover whether you can convincingly hide the truth, either verbally or through your actions. This deception can encompass everything from misleading others through ambiguity to telling outright lies. Typics situations include trying to fast-talk a guard, con a merchant, earn money through gambling, pass yourself off in a disguise, dull someone&#39s suspicions with false assurances, or maintain a straight face while telling a blatant lie.");
            $result->_paired_ability = Abilities::Charisma();
            return $result;
        }
        public static function History() { 
            $result = self::_create("<i>History</i> checks cover your ability to recall lore about historical events, legendary people, ancient kingdoms, past disputes, recent wars and lost civilizations.");
            $result->_paired_ability = Abilities::Intelligence();
            return $result;
        }
        public static function Insight() { 
            $result = self::_create("<i>Insight</i> checks determine whether you can determine the true intentions of a creature, such as when searching out a lie or predicting someone&#39s next move. Doing so involves gleaning clues from body language, speech habits, and changes in mannerisms.");
            $result->_paired_ability = Abilities::Wisdom();
            return $result;
        }
        public static function Intimidation() { 
            $result = self::_create("<i>Intimidation</i> checks cover attempts to influence someone through overt threats, hostile actions, and physical violence. Examples include trying to pry information out of a prisoner, convincing street thugs to back down from a confrontation, or using the edge of a broken bottle to convince a sneering vizier to reconsider a decision.");
            $result->_paired_ability = Abilities::Charisma();
            return $result;
        }
        public static function Investigation() { 
            $result = self::_create("<i>Investigation</i> checks are for when you look around for clues and make deductions based on those clues. You might deduce the location of a hidden object, discern from the appearance of a wound what kind of weapon dealt it, or determine the weakest point in a tunnel that could cause it to collapse. Poring through ancient scrolls in search of a hidden fragment of knowledge might also call for a check.");
            $result->_paired_ability = Abilities::Intelligence();
            return $result;
        }
        public static function Medicine() { 
            $result = self::_create("<i>Medicine</i> checks let your try to stabilise a dying companion or diagnose an illness.");
            $result->_paired_ability = Abilities::Wisdom();
            return $result;
        }
        public static function Nature() { 
            $result = self::_create("<i>Nature</i> checks measure your abiliity to recall lore about terrain, plants and animals, the weather, and natural cycles.");
            $result->_paired_ability = Abilities::Wisdom();
            return $result;
        }
        public static function Perception() { 
            $result = self::_create("<i>Perception</i> checks let your spot hear, or otherwise detect the presence of something. It measures your general awareness of your surroundings and the keenness of your senses. For example you might try to hear a conversation through a closed door, eavesdrop under an open window, or hear monsters moving stealthily in the forest. Or you might try to spot things that are obscured or easy to miss, whether they are orcs lying in ambush on a orad, thugs hiding in the shadows of an alley, or candlelight under a closed secret door.");
            $result->_paired_ability = Abilities::Wisdom();
            return $result;
        }
        public static function Performance() { 
            $result = self::_create("<i>Performance</i> checks determine how well you can delight an audience with music, dance, acting, storytelling, or some other form of entertainment.");
            $result->_paired_ability = Abilities::Charisma();
            return $result;
        }
        public static function Persuasion() { 
            $result = self::_create("<i>Persuasion</i> checks cover when you attempt to influence someone or a group o fpeople with tact, social graces, or good nature. Typically, you use persuasion when acting in good faith, to foster friendships, make cordial requests, or exhibit proper etiquette. Examples of persuading others include convincing a chamberlain to let your party see the king, negotiating peace between warring tribes, or inspiring a crowd of townsfolk.");
            $result->_paired_ability = Abilities::Charisma();
            return $result;
        }
        public static function Religion() { 
            $result = self::_create("<i>Intelligence</i> checks measure your ability to recall lore about deities, rites and prayers, religious hierarchies, holy symbols, and the practices of secret cults.");
            $result->_paired_ability = Abilities::Intelligence();
            return $result;
        }
        public static function SleightOfHand() { 
            $result = self::_create("<i>Sleight of Hand</i> checks cover whenever you attempt an act of legerdemain or manual trickery, such as planting something on someone else or concealing an object on your person. Other examples include attempting to lift a coin purse off another person or slip something out of another person's pocket");
            $result->_paired_ability = Abilities::Dexterity();
            return $result;
        }
        public static function Stealth() { 
            $result = self::_create("<i>Stealth</i> checks cover when you attempt to conceal yourself from enemies, slink past guards, slip away without being noticed, or sneak up on someone without being seen or heard.");
            $result->_paired_ability = Abilities::Dexterity();
            return $result;
        }
        public static function Survival() { 
            $result = self::_create("<i>Wisdom</i> checks cover checking to follow tracks, hunt wild game, guide your group through frozen wastelands, identify signs that owlbears live nearby, predict the weather, or avoid quicksand and other natural hazards.");
            $result->_paired_ability = Abilities::Wisdom();
            return $result;
        }

        public function getAbility() {
            return $this->_paired_ability;
        }
    }
?>