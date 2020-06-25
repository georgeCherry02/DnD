<?php
    $prepared_info["Base Armour Class"] = filter_var($item_info["Base_AC"], FILTER_VALIDATE_INT);
    $modifiers_arr = json_decode($item_info["Additional_Modifiers"]);
    if (sizeof($modifiers_arr) > 0) {
        $modifiers = "";
        foreach ($modifiers_arr as $modifier_id) {
            try {
                $modifier = Abilities::fromValue($modifier_id);
            } catch (OutOfRangeException $e) {
                continue;
            }
            $modifiers .= $modifier->getName().", ";
        }
        $prepared_info["Additional Modifiers"] = substr($modifiers, 0, -2);
    }
    if (!empty($item_info["Strength_Required"])) {
        $prepared_info["Strength Required"] = filter_var($item_info["Strength_Required"], FILTER_VALIDATE_INT);
    }
    if (!empty($item_info["Stealth_Disadvantage"])) {
        $stealth_dis = "No";
        if ($item_info["Stealth_Disadvantage"] == 1) {
            $stealth_dis = "Yes";
        }
        $prepared_info["Stealth Disadvantage"] = $stealth_dis;
    }
    if (!empty($item_info["Weight"])) {
        $prepared_info["Weight"] = filter_var($item_info["Weight"], FILTER_VALIDATE_INT);
    }
    if (!empty($item_info["Value"])) {
        $value_output_string = "";
        $coins_arr = json_decode($item_info["Value"]);
        $coin_count = 0;
        foreach (Coins::ALL() as $coin) {
            if ($coins_arr[$coin_count] > 0) {
                $value_output_string .= $coins_arr[$coin_count].$coin->getValue().", ";
            }
            $coin_count++;
        }
        $value_output_string = substr($value_output_string, 0, -2);
        if (array_sum($coins_arr) > 0) {
            $prepared_info["Value"] = $value_output_string;
        }
    }
?>