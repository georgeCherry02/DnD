<form action="" method="POST" onsubmit="return validate_spell_creation();">
    <input type="hidden" name="form_type" value="Spell"/>
    <div>
        <div class='col-4 labels_container highlight_text'>
            <label for='name' class='required'>Name:</label>
            <label for='level' class='required'>Level:</label>
            <label for='school' class='required'>School of Magic:</label>
            <label for='casting_time' class='required'>Casting phase:</label>
            <label class='required'>Duration Type:</label>
            <label id='duration_label' style='display: none;'>Duration:</label>
            <label for='range_type' class='required'>Range Type:</label>
            <!-- Only if range_type === "ranged" set range_distance input visible -->
            <label for='range_distance' id='range_distance_label' style='display: none;'>Distance:</label>
            <label for='shape_type' class='required'>Shape:</label>
            <!-- Only if a shape other than beam is selected allow shape_size input visible -->
            <label for='shape_size' id='shape_size_label' style="display: none;">Shape Size:</label>
            <label for='vocal'>Vocal:</label>
            <label for='somatic'>Somatic:</label>
            <label for='material_value'>Materials:</label>
            <label for='concentration'>Concentration:</label>
            <label for='effect'>Effect:</label>
            <!-- Only allow damage/healing magnitude visible if effect === "damage" or "healing" -->
            <label id='damage_types_label' style='display: none;'>Damage Types:</label>
            <?php
                foreach (DamageType::ALL() as $damage_type) {
                    echo "<label id='".$damage_type->getName()."_label' style='display: none;'>".$damage_type->getName()." Damage:</label>";
                }
            ?>
            <label id='healing_amount_label' style='display: none;'>Healing Amount:</label>
            <label for='description'>Description:</label>
        </div>
        <div class='col-6 inputs_container'>
            <input type='text' name='name' id='spell_name' class="grey_text grey_border white_background" required/>
            <input type='number' name='level' id='level' class="white_background grey_text grey_border" min="0" max="9" required/>
            <?php
                $enums_to_use = MagicSchools::ALL();
                $column_name = "school";
                include $form_component_dir."Radio.php";

                $enums_to_use = SpellCastingDurations::ALL();
                $column_name = "casting_time";
                include $form_component_dir."Radio.php";

                $enums_to_use = SpellDurations::ALL();
                $column_name = "duration_type";
                include $form_component_dir."Radio.php";
            ?>
            <input type="number" name="duration" id="duration" class="white_background grey_text grey_border" min="0" style="display: none;"/>
            <?php
                $enums_to_use = SpellRangeTypes::ALL();
                $column_name = "range_type";
                include $form_component_dir."Radio.php";
            ?>
            <input type='number' name='range_distance' id='range_distance' style='display: none;' min="0" class="white_background grey_text grey_border"/>
            <?php
                $enums_to_use = SpellShapes::ALL();
                $column_name = "shape";
                include $form_component_dir."Radio.php";
            ?>
            <input type='number' name='shape_size' id='shape_size' style='display: none;' min="0" class="white_background grey_text grey_border"/>
            <?php
                $column_name = "vocal";
                include $form_component_dir."Checkbox.php";

                $column_name = "somatic";
                include $form_component_dir."Checkbox.php";

                $enums_to_use = Coins::ALL();
                $unique_descriptor = "pieces";
                include $form_component_dir."MultiNumber.php";

                $column_name = "concentration";
                include $form_component_dir."Checkbox.php";

                $enums_to_use = SpellEffects::ALL();
                $column_name = "effect";
                include $form_component_dir."Radio.php";

                $dropdown_visible = FALSE;
                $unique_descriptor = "damage_types";
                include $form_component_dir."Damage.php";

                $enums_to_use = EffectDice::ALL();
                $unique_descriptor = "healing";
                $dropdown_visible = FALSE;
                include $form_component_dir."MultiNumber.php";
            ?>
            <textarea name="description" class="white_background grey_text grey_border description"></textarea>
        </div>
    </div>
    <div>
        <input type="submit" value="Create" class="white_background highlight_text grey_border"/>
    </div>
</form>
<script src="<?php echo $file_root; ?>scripts/form_verification.js"></script>