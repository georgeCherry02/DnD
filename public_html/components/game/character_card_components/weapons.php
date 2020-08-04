<?php
    $weapon_ids = json_decode($character_info["Weapon_ID_List"], $assoc=true);
    $weapons_data = ItemManager::get_all_item_data(array_keys($weapon_ids), ItemTypes::Weapon());
    $formatted_weapons_data = array();
    foreach ($weapons_data as $weapon_data) {
        $formatted_weapons_data[$weapon_data["ID"]] = $weapon_data;
    }
?>
<h5>Weapons <i class="fas fa-chevron-down" onclick="game.player_cards.toggleWeapons(<?php echo $player_id; ?>, <?php echo sizeof(array_keys($weapon_ids)); ?>); game.swivelChevron(this);"></i></h5>
<div class="collapsing_section weapons" id="weapons_<?php echo $player_id; ?>" style="max-height: 0px;">
<?php
    foreach($formatted_weapons_data as $weapon_id => $weapon_data) {
        // Gather weapon damage
        $weapon_damage_output_string = "";
        $weapon_damage_distributions = ItemManager::get_damage_distributions(json_decode($weapon_data["Damage_Distribution_IDs"]));
        foreach ($weapon_damage_distributions as $damage_dist) {
            foreach (EffectDice::ALL() as $die) {
                $value = filter_var($damage_dist[$die->getName()], FILTER_VALIDATE_INT);
                if ($value > 0) {
                    $weapon_damage_output_string .= $value . $die->getName() . ", ";
                }
            }
            $weapon_damage_output_string = substr($weapon_damage_output_string, 0, -2);
            $weapon_damage_output_string .= " ".htmlspecialchars($damage_dist["Type"]).", ";
        }
        $weapon_damage_output_string = substr($weapon_damage_output_string, 0, -2);
        // Fetch all weapon properties
        $weapon_properties = json_decode($weapon_data["Properties"]);
        // Gather versatile damage 
        $weapon_is_versatile = in_array("Versatile", $weapon_properties);
        $versatile_damage_output_string = "";
        if ($weapon_is_versatile) {
            $versatile_damage_distribution = ItemManager::get_damage_distributions(array($weapon_data["Versatile_Damage_ID"]))[0];
            foreach (EffectDice::ALL() as $die) {
                $value = filter_var($versatile_damage_distribution[$die->getName()], FILTER_VALIDATE_INT);
                if ($value > 0) {
                    $versatile_damage_output_string .= $value . $die->getName() . ", ";
                }
            }
            $versatile_damage_output_string = substr($versatile_damage_output_string, 0, -2);
        }
        // Sort out ranged information
        $weapon_is_ranged = in_array("Range", $weapon_properties);
        if ($weapon_is_ranged) {
            $weapon_effective_range = filter_var($weapon_data["Effective_Range"], FILTER_VALIDATE_INT);
            $weapon_maximum_range = filter_var($weapon_data["Maximum_Range"], FILTER_VALIDATE_INT);
        }
        // Gather weapon properties
        $properties_output_string = "";
        for ($i = 0; $i < sizeof($weapon_properties); $i++) {
            try {
                $weapon_property = WeaponProperties::fromName($weapon_properties[$i]);
            } catch (OutOfRangeException $e) {
                // Ignore
                // ##########################################################################################
                // # Consider removing clearly corrupted data
                // ##########################################################################################
                continue;
            }
            $properties_output_string .= $weapon_property->getPrettyName();
            if ($weapon_property == WeaponProperties::Versatile()) {
                $properties_output_string .= " (".$versatile_damage_output_string.")";
            } else if ($weapon_property == WeaponProperties::Range()) {
                $properties_output_string .= " (".$weapon_effective_range."/".$weapon_maximum_range.")";
            }
            $properties_output_string .= ", ";
        }
        $properties_output_string = substr($properties_output_string, 0, -2);
        $html = "<p>"
              . htmlspecialchars($weapon_data["Name"])." x".$weapon_ids[$weapon_id]
              . " <i class=\"fas fa-chevron-down\" onclick=\"game.player_cards.toggleWeaponDetails(".$player_id.", ".$weapon_id."); game.swivelChevron(this);\"></i>"
              . "</p>"
              . "<div class=\"collapsing_section weapon_details\" id=\"weapon_details_".$player_id."_".$weapon_id."\" style=\"max-height: 0px;\">";
        $html.=     "<p>".$weapon_damage_output_string."</p>";
        $html.=     "<p>".$properties_output_string."</p>";
        $html.= "</div>";
        echo $html;
    }
?>
    <script>
        game.player_cards.card_heights["<?php echo $player_id; ?>"] = {};
        game.player_cards.card_heights["<?php echo $player_id; ?>"].weapons = <?php echo sizeof(array_keys($weapon_ids)); ?>;
    </script>
</div>