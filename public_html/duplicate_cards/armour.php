<div class="duplicate_card armour_card main_green dark_green_border">
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
    ?>
    <h3 class="dark_green_text"><?php echo htmlspecialchars($item_info["Name"]); ?></h3><br/>
    <h4 class="dark_green_text">Base AC: <?php echo htmlspecialchars($item_info["Base_AC"]); ?></h4>
    <h4 class="dark_green_text">Additional Modifiers: <?php
        $modifiers_arr = json_decode($item_info["Additional_Modifiers"]);
        if (sizeof($modifiers_arr) == 0) {
            echo "None";
        } else {
            $modifiers = "";
            foreach (json_decode($item_info["Additional_Modifiers"]) as $modifier_id) {
                $modifiers .= Abilities::fromValue($modifier_id)->getName() . ", ";
            }
            echo substr($modifiers, 0, -2);
        }
    ?></h4>
    <?php
        if (!empty($item_info["Strength_Required"])) {
    ?>
    <h4 class="dark_green_text">Strength Required: <?php echo htmlspecialchars($item_info["Strength_Required"]); ?></h4>
    <?php
        }
    ?>
    <h4 class="dark_green_text">Stealth Disadvantage: <?php
        if ($item_info["Stealth_Disadvantage"] == 1) {
            echo "Yes";
        } else {
            echo "No";
        }
    ?></h4><br/>
    <?php
        if (!empty($item_info["Weight"])) {
    ?>
    <h4 class="dark_green_text">Weight: <?php echo htmlspecialchars($item_info["Weight"]); ?>lb</h4>
    <?php
        }
    ?>
    <?php
        if (!empty($item_info["Value"])) {
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
            if (array_sum($coins_arr) > 0) {
    ?>
    <h4 class="dark_green_text">Value: <?php echo $value_output_string; ?></h4>
    <?php
            }
        }
    ?>
</div>