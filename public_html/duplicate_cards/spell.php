<?php
    $prepared_info["Level"] = filter_var($item_info["Level"], FILTER_VALIDATE_INT);

    // Gather and parse spell information
    // Fetch school of magic
    try {
        $magic_school = MagicSchools::fromValue($item_info["School"]);
    } catch (OutOfRangeException $e) {
        $magic_school = MagicSchools::Abjuration();
    }
    $prepared_info["School of Magic"] = $magic_school->getPrettyName();
    // Fetch casting time
    try {
        $casting_time = SpellCastingDurations::fromValue($item_info["Casting_Time"]);
    } catch (OutOfRangeException $e) {
        $casting_time = SpellCastingDurations::Action();
    }
    $prepared_info["Casting phase"] = $casting_time->getPrettyName();
    // Gather range info
    try {
        $range_type = SpellRangeTypes::fromValue($item_info["Range_Type"]);
    } catch (OutOfRangeException $e) {
        $range_type = SpellRangeTypes::Range();
    }
    if ($range_type === SpellRangeTypes::Range()) {
        $range_distance = filter_var($item_info["Range_Distance"], FILTER_VALIDATE_INT);
        $range_info = $range_distance . "ft";
    } else {
        $range_info = $range_type->getPrettyName();
    }
    $prepared_info["Range"] = $range_info;
    // Gather shape info
    try {
        $shape_type = SpellShapes::fromValue($item_info["Shape"]);
    } catch (OutOfRangeException $e) {
        $shape_type = SpellShapes::Beam();
    }
    if ($shape_type !== SpellShapes::Beam()) {
        $shape_size = filter_var($item_info["Shape_Size"], FILTER_VALIDATE_INT);
        $shape_info = $shape_size . "ft " . $shape_type->getPrettyName();
    } else {
        $shape_info = $shape_type->getPrettyName();
    }
    $prepared_info["Shape"] = $shape_info;
    // Gather requirements
    $spell_requirements = "";
    if ($item_info["Vocal"] == "1") {
        $spell_requirements .= "Vocal, ";
    }
    if ($item_info["Somatic"] == "1") {
        $spell_requirements .= "Somatic, ";
    }
    $value_output_string = "";
    $coins_arr = json_decode($item_info["Material_Value"]);
    $coin_count = 0;
    foreach (Coins::ALL() as $coin) {
        if ($coins_arr[$coin_count] > 0) {
            $value_output_string .= $coins_arr[$coin_count] . $coin->getValue() . ", ";
        }
        $coin_count++;
    }
    $value_output_string = substr($value_output_string, 0, -2);
    if (array_sum($coins_arr) > 0) {
        $spell_requirements .= $value_output_string . ", ";
    }
    if (strlen($spell_requirements) === 0) {
        $spell_requirements = "None";
    } else {
        $spell_requirements = substr($spell_requirements, 0, -2);
    }
    $prepared_info["Requirements"] = $spell_requirements;
    // Concentration
    if ($item_info["Concentration"] == "1") {
        $concentration = "Yes";
    } else {
        $concentration = "No";
    }
    $prepared_info["Concentration"] = "Yes";
    // Determine effect
    $effect_output_str = "";
    try {
        $effect = SpellEffects::fromValue(json_decode($item_info["Effect"]));
    } catch (OutOfRangeException $e) {
        $effect = SpellEffects::Rollplay();
    }
    if ($effect === SpellEffects::Damage() || $effect === SpellEffects::Healing()) {
        $effect_ids = json_decode($item_info["Effect_IDs"]);
        $effect_distributions = ItemManager::get_damage_distributions($effect_ids);
        foreach ($effect_distributions as $eff_dist) {
            foreach (EffectDice::ALL() as $die) {
                $value = $eff_dist[$die->getName()];
                if ($value > 0) {
                    $effect_output_str .= $value . $die->getName() . ", ";
                }
            }
            $effect_output_str = substr($effect_output_str, 0, -2);
            $effect_output_str .= " ".$eff_dist["Type"];
            if ($effect === SpellEffects::Damage()) {
                $effect_output_str .= " damage, ";
            } else {
                $effect_output_str .= ", ";
            }
        }
        $effect_output_str = substr($effect_output_str, 0, -2);
    } else {
        $effect_output_str = $effect->getName();
    }
    $prepared_info["Effect"] = $effect_output_str;
?>