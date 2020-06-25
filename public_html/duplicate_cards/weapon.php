<?php
    // Gather and parse weapon information
    // Fetch and parse weapon properties
    $weapon_properties_output_string = "";
    $weapon_properties = json_decode($item_info["Properties"]);
    for ($i = 0; $i < sizeof($weapon_properties); $i++) {
        try {
            $weapon_property = WeaponProperties::fromName($weapon_properties[$i]);
        } catch (OutOfRangeException $e) {
            // Maybe remove this property and do some fix here to correct corrupted data
            continue;
        }
        $weapon_properties_output_string .= $weapon_property->getPrettyName() . ", ";
    }
    $weapon_properties_output_string = substr($weapon_properties_output_string, 0, -2);
    $prepared_info["Properties"] = $weapon_properties_output_string;
    // Fetch damage distributions
    $damage_ids = json_decode($item_info["Damage_Distribution_IDs"]);
    if (in_array("Versatile", $weapon_properties)) {
        array_push($damage_ids, $item_info["Versatile_Damage_ID"]);
    }
    $damage_distributions = ItemManager::get_damage_distributions($damage_ids);
    $weapon_damage_output_string = "";
    $versatile_damage_output_string = "";
    foreach ($damage_distributions as $damage_dist) {
        foreach (EffectDice::ALL() as $die) {
            $value = filter_var($damage_dist[$die->getName()], FILTER_VALIDATE_INT);
            if ($value > 0) {
                $addition = $value . $die->getName() . ", ";
                if ($damage_dist["Type"] == "Versatile") {
                    $versatile_damage_output_string .= $addition;  
                } else {
                    $weapon_damage_output_string .= $addition;
                }
            }
        }
        if ($damage_dist["Type"] == "Versatile") {
            $versatile_damage_output_string = substr($versatile_damage_output_string, 0, -2);
            $versatile_damage_output_string .= " ".htmlspecialchars($damage_dist["Type"]).", ";
        } else {
            $weapon_damage_output_string = substr($weapon_damage_output_string, 0, -2);
            $weapon_damage_output_string .= " ".htmlspecialchars($damage_dist["Type"]).", ";
        }
    }
    $weapon_damage_output_string = substr($weapon_damage_output_string, 0, -2);
    $versatile_damage_output_string = substr($versatile_damage_output_string, 0, -2);
    $prepared_info["Damage"] = $weapon_damage_output_string;
    $prepared_info["Versatile Damage"] = $versatile_damage_output_string;
    // Check if weapon is ranged
    $weapon_is_ranged = in_array("Range", $weapon_properties);
    if ($weapon_is_ranged) {
        $prepared_info["Effective Range"] = filter_var($item_info["Effective_Range"], FILTER_VALIDATE_INT);
        $prepared_info["Maximum Range"] = filter_var($item_info["Maximum_Range"], FILTER_VALIDATE_INT);
    }
    // Parse weapon value
    $value_output_string = "";
    $coins_arr = json_decode($item_info["Value"]);
    $coin_count = 0;
    foreach (Coins::ALL() as $coin) {
        if ($coins_arr[$coin_count] > 0) {
            $value_output_string .= filter_var($coins_arr[$coin_count], FILTER_VALIDATE_INT) . $coin->getValue() . ", ";
        }
        $coin_count++;
    }
    $value_output_string = substr($value_output_string, 0, -2);
    $weapon_has_defined_value = array_sum($coins_arr) > 0;
    if ($weapon_has_defined_value) {
        $prepared_info["Value"] = $value_output_string;
    }
    if (!empty($item_info["Weight"])) {
        $prepared_info["Weight"] = filter_var($item_info["Weight"], FILTER_VALIDATE_INT);
    }
?>