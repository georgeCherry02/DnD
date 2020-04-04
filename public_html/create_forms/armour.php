<form action="" method="POST" onsubmit="return validate_armour_creation();">
    <input type='hidden' name='form_type' value='armour'/>
    <div>
        <div class="labels_container">
            <label for="name" class="required">Name:</label><br/>
            <label for="base_ac" class="required">Base AC:</label><br/>
            <label for="armour_add_modifiers">Additional Modifiers:</label><br/>
            <label for="str_required">Strength Required:</label><br/>
            <label for="stealth_disadvantage" class="required">Stealth Disadvantage:</label><br/>
            <label for="armour_weight">Armour Weight:</label><br/>
            <label for="armour_value">Armour Value:</label><br/>
        </div>
        <div class="col-6 inputs_container">
            <input type="text" id="armour_name" name="name" class="dark_green_text dark_green_border light_green"/><br/>
            <input type="number" id="base_ac" name="base" min="1" class="dark_green_text dark_green_border light_green"/>
            <div class='create_form_abilities_dropdown light_green dark_green_border'>
                <div class='label_container'>
                    <label class="medium_green_text">Abilities:</label><br/>
                </div>
                <?php
                    for ($i = 0; $i < sizeof(ABILITIES); $i++) {
                        $value = $i + 1;
                        echo "<label class='medium_green_text'>" . ABILITIES[$i] . ":</label>";
                        echo "<input type='hidden' name='" . ABILITIES[$i] . "_modifier' id='" . ABILITIES[$i] . "_modifier' value='0'/>";
                        echo "<div class=\"checkbox dark_green_border\" onclick=\"toggle_checkbox('" . ABILITIES[$i] . "_modifier')\" id=\"" . ABILITIES[$i] . "_modifier_checkbox\"></div>";
                        echo "<br/>";
                    }
                ?>
            </div>
            <div class='create_form_placeholder'></div>
            <input type="number" id="str_required" name="str_required" min="1" class="dark_green_text dark_green_border light_green"/>
            <input type='hidden' name='stealth_disadvantage' id='stealth_disadvantage' value="0"/>
            <div class='checkbox dark_green_border' onclick="toggle_checkbox('stealth_disadvantage')" id="stealth_disadvantage_checkbox"></div>
            <div class='create_form_placeholder'></div>
            <input type="number" id="armour_weight" name="weight" min="1" class="dark_green_text dark_green_border light_green"/><br/>
            <input type="number" id="armour_value" name="value" min="1" class="dark_green_text dark_green_border light_green"/><br/>
        </div>
    </div>
    <input type="submit" value="Create" class="dark_green_text dark_green_border light_green"/>
</form>
<script src="<?php echo $file_root; ?>scripts/form_verification.js"></script>