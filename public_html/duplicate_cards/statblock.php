<?php
    // Manage armour
    $armour_id = filter_var($item_info["Armour_ID"], FILTER_VALIDATE_INT);
    $armour_info = ItemManager::get_all_item_data(array($armour_id), ItemTypes::Armour())[0];
    $armour_output = $armour_info["Name"];
    $prepared_info["Armour"] = $armour_output;

    // Hit points
    $prepared_info["HP"] = filter_var($item_info["Hit_Points"], FILTER_VALIDATE_INT);
    // Speed
    $prepared_info["Speed"] = filter_var($item_info["Speed"], FILTER_VALIDATE_INT);
    
    // Manage ability distribution
    $ability_id = filter_var($item_info["Ability_Scores_ID"], FILTER_VALIDATE_INT);
    $ability_distribution = ItemManager::get_ability_distribution($ability_id);
    // Check that the ability distribution is successfully fetched
    if ($ability_distribution) {
        $ability_output = "";
        // Need to manage ability info output
        // Perhaps this...? 
        foreach (Abilities::ALL() as $ability) {
            $ability_output .= $ability->getName() . ": " . $ability_distribution[$ability->getName()] . "<br/>";
        }
        $prepared_info["Abilities"] = $ability_output;
    }

    // Manage saving throws
    $proficient_saving_throws = json_decode($item_info["Proficient_Saving_Throws"]);
    $prepared_info["Saving Throws"] = htmlspecialchars(implode(", ", $proficient_saving_throws));

    // Manage skills and expertise
    $skills = json_decode($item_info["Skill_Proficiencies"]);
    $expertise_skills = json_decode($item_info["Expertise"]);
    if (sizeof($skills) > 0) {
        $skill_output = "";
        foreach (Skills::ALL() as $skill) {
            if (in_array($skill->getName(), $skills)) {
                $skill_output .= $skill->getPrettyName().", ";
            }
            if (in_array($skill->getName(), $expertise_skills)) {
                $skill_output = substr($skill_output, 0, -2)." (E), ";
            }
        }
        $prepared_info["Skills"] = substr($skill_output, 0, -2);
    }

    // XP Reward
    if (!empty($item_info["Experience_Reward"])) {
        $prepared_info["XP Reward"] = filter_var($item_info["Experience_Reward"], FILTER_VALIDATE_INT);
    }

    // Manage Weapons
    $weapon_ids = json_decode($item_info["Weapon_ID_List"]);
    $weapons_summary = ItemManager::get_all_item_data($weapon_ids, ItemTypes::Weapon());
    $weapon_output = "";
    foreach ($weapons_summary as $weapon) {
        $weapon_output .= $weapon["Name"].", ";
    }
    if (sizeof($weapon_ids) > 0) {
        $prepared_info["Weapons"] = substr($weapon_output, 0, -2);
    }
    // Manage Spells
    $spell_ids = json_decode($item_info["Spell_ID_List"]);
    $spells_summary = ItemManager::get_all_item_data($spell_ids, ItemTypes::Spell());
    $spells_summary_remapped = array();
    foreach ($spells_summary as $spell) {
        if (!isset($spells_summary_remapped[$spell["Level"]])) {
            $spells_summary_remapped[$spell["Level"]] = array();
        }
        array_push($spells_summary_remapped[$spell["Level"]], $spell["Name"]);
    }
    $spell_output = "<div class='sub_features_container'>";
    for ($i = 0; $i <= 9; $i++) {
        if (isset($spells_summary_remapped[$i])) {
            $spell_output .= "<p class='grey_text sub_feature_name'>Level ".$i.":</p>";
            $spell_output .= "<p class='grey_text sub_feature_description'>";
            for ($j = 0; $j < sizeof($spells_summary_remapped[$i]); $j++) {
                $spell_output .= $spells_summary_remapped[$i][$j].", ";
            }
            $spell_output = substr($spell_output, 0, -2)."</p>";
        }
    }
    $spell_output .= "</div>";
    if (sizeof($spell_ids) > 0) {
        $prepared_info["Spells"] = $spell_output;
    }

    // Fetch Spell Slot Distribution
    $spell_slot_id = filter_var($item_info["Spell_Slot_Distribution_ID"], FILTER_VALIDATE_INT);
    $spell_slot_distribution = ItemManager::get_spell_slot_distribution($spell_slot_id);
    $spell_slot_distribution_output = "";
    $amount = 0;
    for ($i = 1; $i <= 9; $i++) {
        $amount_of_level = $spell_slot_distribution["Level_".$i];
        $amount += $amount_of_level;
        if ($amount_of_level > 0) {
            $spell_slot_distribution_output .= "Level ".$i.": ".$amount_of_level."<br/>";
        } else {
            break;
        }
    }
    if ($amount > 0) {
        $prepared_info["Spell Slots"] = $spell_slot_distribution_output;
    }

    // Manage Features
    $feature_output = "<div class='sub_features_container'>";
    $feature_ids = json_decode($item_info["Features_ID_List"]);
    $feature_summary = ItemManager::get_features($feature_ids);
    if ($feature_summary) {
        foreach ($feature_summary as $feature) {
            $feature_output .= "<div class='npc_feature_container'>";
            $feature_output .= "<p class='sub_feature_name grey_text'>".htmlspecialchars($feature["Name"]).":</p>";
            $feature_output .= "<p class='sub_feature_description grey_text'>".htmlspecialchars($feature["Description"])."</p>";
            $feature_output .= "</div>";
        }
        $feature_output .= "</div>";
        if (sizeof($feature_ids) > 0) {
            $prepared_info["Features"] = $feature_output;
        }
    }
?>