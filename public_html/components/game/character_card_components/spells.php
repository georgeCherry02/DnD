<?php
    // Determine which ability is used to cast spells
    if ($racial_ability_set = $character_info["Racial_Spell_Casting_Ability"] !== 0) {
        try {
            $racial_ability = Abilities::fromValue($character_info["Racial_Spell_Casting_Ability"]);
        } catch (OutOfRangeException $e) {
            // Ignore
            // ##########################################################################################
            // # Consider removing ability and setting to 0
            // ##########################################################################################
        }
    }
    if ($class_ability_set = $character_info["Class_Spell_Casting_Ability"] !== 0) {
        try {
            $class_ability = Abilities::fromValue($character_info["Class_Spell_Casting_Ability"]);
        } catch (OutOfRangeException $e) {
            // Ignore
            // ##########################################################################################
            // # Consider removing ability and setting to 0
            // ##########################################################################################
        }
    }
    // Determine their spell slots
    $total_spell_slots = ItemManager::get_spell_slot_distribution($character_info["Total_Spell_Slot_Distribution_ID"]);
    $current_spell_slots = ItemManager::get_spell_slot_distribution($character_info["Current_Spell_Slot_Distribution_ID"]);
    $spell_slot_html = "";
    for ($i = 1; $i <= 9; $i++) {
        $total = $total_spell_slots["Level_".$i];
        if ($total == 0) {
            break;
        }
        $current = $current_spell_slots["Level_".$i];
        $spell_slot_html .= "<div class=\"spell_slot_row\">"
                          .     "<p>Level ".$i.":</p>"
                          .     "<div class=\"spell_slot_placeholder\"></div>";
        for ($j = 0; $j < $total; $j++) {
            $spell_slot_html .= "<div class=\"spell_slot\" id=\"spell_slot_".$player_id."_".$i."_".$j."\">"
                              .     "<i class=\"fa";
            if ($j < $current) {
                $spell_slot_html .= "s";
            } else {
                $spell_slot_html .= "r";
            }
            $spell_slot_html .=     " fa-circle\"></i>"
                              . "</div>";
        }
        $spell_slot_html .= "</div>";
    }
    // Gather spell list
    $spell_ids = json_decode($character_info["Spell_ID_List"]);
    $spell_info = ItemManager::get_all_item_data($spell_ids, ItemTypes::Spell());
    $formatted_spell_list = array();
    foreach ($spell_info as $spell) {
        if (!array_key_exists($spell["Level"], $formatted_spell_list)) {
            $formatted_spell_list[$spell["Level"]] = array();
        }
        array_push($formatted_spell_list[$spell["Level"]], $spell);
    }
    // Now begin to create html now spells are sorted in level order
    $spell_list_html = "";
    $number_of_spell_levels = 0;
    foreach ($formatted_spell_list as $level => $level_spell_list) {
        $number_of_spell_levels++;
        $spell_level_marker = "Cantrips:";
        if ($level > 0) {
            $spell_level_marker = "Level ".$level.":";
        }
        $spell_list_html .= "<p>".$spell_level_marker."</p>";
        foreach ($level_spell_list as $spell) {
            $spell_list_html .= "<p class=\"spell_name\">".$spell["Name"]."</p>";
        }
    }
    $spell_list_height = $number_of_spell_levels + sizeof($spell_ids);
?>
<h5>Spells <i class="fas fa-chevron-down" onclick="game.player_cards.toggleSpells(<?php echo $player_id; ?>, <?php echo $i-1; ?>, <?php echo $spell_list_height; ?>); game.swivelChevron(this);"></i></h5>
<div class="collapsing_section spells" id="spells_<?php echo $player_id; ?>" style="max-height: 0px;">
    <h6>Spell Slots <i class="fas fa-chevron-down" onclick="game.player_cards.toggleSpellSlots(<?php echo $player_id; ?>, <?php echo $i-1; ?>); game.swivelChevron(this);"></i></h6>
    <div class="collapsing_section spell_slots" id="spell_slots_<?php echo $player_id; ?>" style="max-height: 0px;"><?php echo $spell_slot_html; ?></div>
    <h6>Spell List <i class="fas fa-chevron-down" onclick="game.player_cards.toggleSpellList(<?php echo $player_id; ?>, <?php echo $spell_list_height; ?>); game.swivelChevron(this);"></i></h6>
    <div class="collapsing_section spell_list" id="spell_list_<?php echo $player_id; ?>" style="max-height: 0px;"><?php echo $spell_list_html; ?></div>
    <script>
        game.player_cards.card_heights["<?php echo $player_id; ?>"].slots = <?php echo $i-1; ?>;
        game.player_cards.card_heights["<?php echo $player_id; ?>"].list = <?php echo $spell_list_height; ?>
    </script>
</div>