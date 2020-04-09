<form action="" method="POST" onsubmit="return validate_spell_creation();">
    <input type="hidden" name="form_type" value="Spell"/>
    <div>
        <div class='labels_container'>
            <label for='name' class='required'>Name:</label>
            <label for='level' class='required'>Level:</label>
            <label for='school' class='required'>School of Magic:</label>
            <label for='casting_time' class='required'>Casting Time:</label>
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
            <label for='effect_magnitude'>Effect Magnitude:</label>
        </div>
        <div class='col-6 inputs_container'>
            <input type='text' name='name' id='name' class="dark_green_text dark_green_border light_green" required/><br/>
            <input type='number' name='level' id='level' class="dark_green_text dark_green_border light_green" required/><br/>
            <?php
                $enums_to_use = MagicSchools::ALL();
                $column_name = "school";
                include $form_component_dir."Radio.php";
            ?>
            <?php
                $enums_to_use = ActionPhases::ALL();
                $column_name = "casting_time";
                include $form_component_dir."Radio.php";
            ?>
            <?php 
                $enums_to_use = SpellRangeTypes::ALL();
                $column_name = "range_type";
                include $form_component_dir."Radio.php";
            ?>
            <input type='number' name='range_distance' id='range_distance' style='display: none;' min="0" class="dark_green_text dark_green_border light_green"/>
            <?php
                $enums_to_use = SpellShapes::ALL();
                $column_name = "shape_type";
                include $form_component_dir."Radio.php";
            ?>
            <input type='number' name='shape_size' id='shape_size' style='display: none;' min="0" class="dark_green_text dark_green_border light_green"/>
            <?php
                $column_name = "vocal";
                include $form_component_dir."Checkbox.php";
            ?>
            <?php
                $column_name = "somatic";
                include $form_component_dir."Checkbox.php";
            ?>
            <?php
                $enums_to_use = Coins::ALL();
                include $form_component_dir."MultiNumber.php";
            ?>
            <?php
                $column_name = "concentration";
                include $form_component_dir."Checkbox.php";
            ?>
            <?php
                $enums_to_use = SpellEffects::ALL();
                $column_name = "effect";
                include $form_component_dir."MultiNumber.php";
            ?>
            <?php
                $enums_to_use = EffectDice::ALL();
            ?>
        </div>
    </div>
    <input type="submit" value="Create" class="dark_green_text dark_green_border light_green"/>
</form>
<script src="<?php echo $file_root; ?>scripts/form_verification.js"></script>