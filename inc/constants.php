<?php
	define("DB_HOST", "localhost");
	define("DB_USER", "George");
	define("DB_PASS", "TgDITDHRcEjE83y0");
	define("DB_NAME", "dnd_proj_db");

	const ABILITIES = array("Strength", "Dexterity", "Constitution", "Intelligence", "Wisdom", "Charisma");

	const VALID_ARMOUR_COLUMNS = array("base_ac", "strength_required", "stealth_disadvantage", "weight", "value");
	const VALID_WEAPON_COLUMNS = array("properties", "damage_type", "effective_range", "maximum_range");
	const VALID_SPELL_COLUMNS = array("level", "school", "casting_time", "range_type", "range_distance", "shape", "shape_size", "vocal", "somatic", "material", "concentration", "effect");
	const VALID_STAT_BLOCK_COLUMNS = array("armour_class", "hit_points", "speed", "skill_proficiencies", "expertise", "experience_reward", "weapon_id_list", "spell_id_list");
?>