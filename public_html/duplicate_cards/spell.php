<div class="duplicate_card spell_card main_green dark_green_border">
    <?php
        if ($card_count == 1) {
    ?>
    <h2 class="dark_green_text">Your item</h2>
    <?php
        } else {
    ?>
    <h2 class="dark_green_text">Pre-existing item</h2>
    <?php
        }
        $card_count++;

        // Gather and parse spell information
        // Fetch school of magic
        try {
            $magic_school = MagicSchools::fromValue($item_info["School"]);
        } catch (OutOfRangeException $e) {
            $magic_school = MagicSchools::Abjuration();
        }
        // Fetch casting time
        try {
            $casting_time = SpellCastingDurations::fromValue($item_info["Casting_Time"]);
        } catch (OutOfRangeException $e) {
            $casting_time = SpellCastingDurations::Action();
        }
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
            $range_info = $range_type->getName();
        }
        // Gather shape info
        try {
            $shape_type = SpellShapes::fromValue($item_info["Shape"]);
        } catch (OutOfRangeException $e) {
            $shape_type = SpellShapes::Beam();
        }
        if ($shape_type !== SpellShapes::Beam()) {
            $shape_size = filter_var($item_info["Shape_Size"], FILTER_VALIDATE_INT);
            $shape_info = $shape_size . "ft " . $shape_type->getName();
        } else {
            $shape_info = $shape_type->getName();
        }
        // Gather requirements
        $spell_requirements = "";
        if ($item_info["Vocal"] == "1") {
            $spell_requirements .= "V, ";
        }
        if ($item_info["Somatic"] == "1") {
            $spell_requirements .= "S, ";
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
        // Concentration
        if ($item_info["Concentration"] == "1") {
            $concentration = "Yes";
        } else {
            $concentration = "No";
        }
        // Determine effect
        try {
            $effect = SpellEffects::fromValue($item_info["Effect"]);
        } catch (OutOfRangeException $e) {
            $effect = SpellEffects::Rollplay();
        }
        if ($effect === SpellEffects::Damage() || $effect === SpellEffects::Healing()) {
            $effect_dice = json_decode($item_info["Effect_Dice"]);
            $dice_output = "";
            $die_count = 0;
            foreach (EffectDice::ALL() as $dice) {
                if ($effect_dice[$die_count] > 0) {
                    $dice_output .= $effect_dice[$die_count] . $dice->getName() . ", ";
                }
                $die_count++;
            }
            $dice_output = substr($dice_output, 0, -2);
            $spell_effect = $dice_output . " " . $effect->getName();
        } else {
            $spell_effect = $effect->getName();
        }
    ?>
    <h3 class="dark_green_text"><?php echo htmlspecialchars($item_info["Name"]); ?></h3><br/>
    <h4 class="dark_green_text">Level: <?php echo filter_var($item_info["Level"], FILTER_VALIDATE_INT); ?></h4>
    <h4 class="dark_green_text">School of Magic: <?php echo $magic_school->getName(); ?></h4>
    <h4 class="dark_green_text">Casting phase: <?php echo $casting_time->getName(); ?></h4>
    <h4 class="dark_green_text">Range: <?php echo $range_info; ?></h4>
    <h4 class="dark_green_text">Shape: <?php echo $shape_info; ?></h4>
    <h4 class="dark_green_text">Requirements: <?php echo $spell_requirements; ?></h4>
    <h4 class="dark_green_text">Concentration: <?php echo $concentration; ?></h4>
    <h4 class="dark_green_text">Effect: <?php echo $spell_effect; ?></h4>
</div>