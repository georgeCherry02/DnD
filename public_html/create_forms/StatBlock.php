<?php
    $owned_item_ids = ItemManager::get_owned_items();
    if (!$owned_item_ids) {
        header("Location: create.php");
        exit;
    }
    $items = array();
    foreach ($owned_item_ids as $column_name => $ids) {
        $item_type_name = substr($column_name, 0, strpos($column_name, "_"));
        try {
            $item_type = ItemTypes::fromName($item_type_name);
        } catch (OutOfRangeException $e) {
            header("Location: create.php");
            exit;
        }
        $items[$item_type_name] = ItemManager::get_all_item_data(json_decode($ids), $item_type);
    }
?>
<form action="" method="POST" onsubmit="return validate_stat_block_creation();">
    <input type='hidden' name='form_type' value='StatBlock'/>
    <div>
        <div class="col-4 labels_container highlight_text">
            <label for="name" class="required">Name:</label>
            <label class="required">Hit Points:</label>
            <label class="required">Speed:</label>
            <label class="required">Abilities:</label>
            <label>Skill Proficiencies</label>
            <label>Expertise:</label>
            <label>Experience Reward:</label>
            <label>Armour:</label>
            <label>Spells:</label>
            <label>Spell Slots:</label>
            <label>Weapons:</label>
            <label>Features:</label>
            <label>Description:</label>
        </div>
        <div class="col-6 inputs_container">
            <input type="text" id="npc_name" name="name" class="grey_text grey_border white_background" required/>
            <input type="number" name="Hit_Points" class="grey_text grey_border white_background" required/>
            <input type="number" name="Speed" class="grey_text grey_border white_background" required/>
            <?php
                $enums_to_use = Abilities::ALL();
                $unique_descriptor = "modifier";
                include $form_component_dir."MultiNumber.php";

                $enums_to_use = Skills::ALL();
                $unique_descriptor = "proficiency";
                include $form_component_dir."MultiSelect.php";

                // Uses same enum as above
                $unique_descriptor = "expertise";
                include $form_component_dir."MultiSelect.php";
            ?>
            <input type="number" name="Experience_Reward" class="grey_text grey_border white_background"/>
            <?php
                $item_type = ItemTypes::Armour();
                include $form_component_dir."ItemRadio.php";

                $item_type = ItemTypes::Spell();
                include $form_component_dir."ItemSelect.php";

                include $form_component_dir."SpellSlot.php";

                $item_type = ItemTypes::Weapon();
                include $form_component_dir."ItemSelect.php";

                include $form_component_dir."Features.php";
            ?>
            <textarea name="description" class="grey_text grey_border white_background description"></textarea>
        </div>
        <input type="submit" value="Create" class="highlight_text grey_border white_background"/>
    </div>
</form>
<script src="<?php echo $file_root; ?>scripts/form_verification.js"></script>