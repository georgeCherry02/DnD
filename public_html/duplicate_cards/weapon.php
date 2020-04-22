<div class='duplicate_card weapon_card light_background grey_border'>
    <?php
        if ($card_count == 1) {
    ?>
    <?php
        } else {
    ?>
    <?php
        }
        $card_count++;

        // Gather and parse weapon information
        // Fetch and parse weapon properties
        $weapon_properties_output_string = "";
        $weapon_properties = json_decode($item_info["Properties"]);
        for ($i = 0; $i < sizeof($weapon_properties); $i++) {
            $weapon_property = WeaponProperties::fromName($weapon_properties[$i]);
            $weapon_properties_output_string .= $weapon_property->getPrettyName() . ", ";
        }
        $weapon_properties_output_string = substr($weapon_properties_output_string, 0, -2);
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
                $value = $damage_dist[$die->getName()];
                if ($value > 0) {
                    $addition = $value . $die->getName() . ", ";
                    if ($damage_dist["Type"] == "Versatile") {
                        $versatile_damage_output_string .= $addition;  
                    } else {
                        $weapon_damage_output_string .= $addition;
                    }
                }
            }
            $weapon_damage_output_string = substr($weapon_damage_output_string, 0, -2);
            $weapon_damage_output_string .= " ".$damage_dist["Type"].", ";
        }
        $weapon_damage_output_string = substr($weapon_damage_output_string, 0, -2);
        $versatile_damage_output_string = substr($versatile_damage_output_string, 0, -2);
        // Check if weapon is ranged
        $weapon_is_ranged = in_array("Range", $weapon_properties);
        // Parse weapon value
        $value_output_string = "";
        $coins_arr = json_decode($item_info["Value"]);
        $coin_count = 0;
        foreach (Coins::ALL() as $coin) {
            if ($coins_arr[$coin_count] > 0) {
                $value_output_string .= $coins_arr[$coin_count] . $coin->getValue() . ", ";
            }
            $coin_count++;
        }
        $value_output_string = substr($value_output_string, 0, -2);
        $weapon_has_defined_value = array_sum($coins_arr) > 0;
        // Santise description
        $description = "";
        $description_lines = explode("\n", $item_info["Description"]);
        for ($i = 0; $i < sizeof($description_lines); $i++) {
            $description .= filter_var($description_lines[$i], FILTER_SANITIZE_SPECIAL_CHARS) . "<br/>";
        }
    ?>
    <h3 class="grey_text"><?php echo htmlspecialchars($item_info["Name"]); ?></h3><br/>
    <h4 class="grey_text">Properties: <?php echo $weapon_properties_output_string; ?></h4>
    <h4 class="grey_text">Damage: <?php echo $weapon_damage_output_string ?></h4>
    <?php
        if (in_array("Versatile", $weapon_properties)) {
    ?>
    <h4 class="grey_text">Versatile Damage: <?php echo $versatile_damage_output_string; ?></h4>
    <?php
        }
    ?>
    <?php 
        if ($weapon_is_ranged) {
    ?>
    <h4 class="grey_text">Effective Range: <?php echo $item_info["Effective_Range"]; ?></h4>
    <h4 class="grey_text">Maximum Range: <?php echo $item_info["Maximum_Range"]; ?></h4>
    <?php
        }
    ?>
    <?php
        if (!empty($item_info["Weight"])) {
    ?>
    <h4 class="grey_text">Weight: <?php echo $item_info["Weight"]; ?>lb</h4>
    <?php
        }
    ?>
    <?php
        if ($weapon_has_defined_value) {
    ?>
    <h4 class="grey_text">Value: <?php echo $value_output_string; ?></h4>
    <?php
        }
    ?>
    <p><?php echo $description; ?></p>
</div>