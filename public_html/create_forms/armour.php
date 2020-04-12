<form action="" method="POST" onsubmit="return validate_armour_creation();">
    <input type='hidden' name='form_type' value='Armour'/>
    <div>
        <div class="labels_container">
            <label for="name" class="required">Name:</label>
            <label for="base_ac" class="required">Base AC:</label>
            <label for="armour_add_modifiers">Additional Modifiers:</label>
            <label for="str_required">Strength Required:</label>
            <label for="stealth_disadvantage" class="required">Stealth Disadvantage:</label>
            <label for="armour_weight">Armour Weight:</label>
            <label for="armour_value">Armour Value:</label>
            <label for="description">Description:</label>
        </div>
        <div class="col-6 inputs_container">
            <input type="text" id="armour_name" name="name" class="dark_green_text dark_green_border light_green" required/><br/>
            <input type="number" id="base_ac" name="base_ac" min="1" class="dark_green_text dark_green_border light_green" required/>
            <?php
                $enums_to_use = Abilities::ALL();
                $unique_descriptor = "modifier";
                include $form_component_dir."MultiSelect.php";
            ?>
            <input type="number" id="str_required" name="strength_required" min="1" class="dark_green_text dark_green_border light_green"/>
            <?php
                $column_name = "stealth_disadvantage";
                include $form_component_dir."Checkbox.php";
            ?>
            <input type="number" id="armour_weight" name="weight" min="1" class="dark_green_text dark_green_border light_green"/><br/>
            <?php
                $enums_to_use = Coins::ALL();
                $unique_descriptor = "pieces";
                include $form_component_dir."MultiNumber.php";
            ?>
            <textarea name="description" class="dark_green_text dark_green_border light_green description"></textarea>
        </div>
    </div>
    <input type="submit" value="Create" class="dark_green_text dark_green_border light_green"/>
</form>
<script src="<?php echo $file_root; ?>scripts/form_verification.js"></script>