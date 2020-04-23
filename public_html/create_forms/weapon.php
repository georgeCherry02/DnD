<form action="" method="POST" onsubmit="return validate_weapon_creation();">
    <input type="hidden" name="form_type" value="Weapon"/>
    <div>
        <div class="col-4 labels_container">
            <label for='name' class='required'>Name:</label>
            <label for='properties' class='required'>Properties:</label>
            <label for='damage_types'>Damage Types:</label>
            <?php
                foreach(DamageType::ALL() as $damage_type) {
                    echo "<label id='".$damage_type->getName()."_label' style='display: none;'>".$damage_type->getName()." Damage:</label>";
                }
            ?>
            <label id='versatile_damage_label' style='display: none;'>Versatile Damage:</label>
            <label id='Effective_Range_label' style='display: none;'>Effective Range:</label>
            <label id='Maximum_Range_label' style='display: none;'>Maximum Range:</label>
            <label>Weight:</label>
            <label>Value:</label>
            <label for='description'>Description:</label>
        </div>
        <div class='col-6 inputs_container'>
            <input type="text" id="weapon_name" name="name" class="grey_text grey_border white_background" required/><br/>
            <?php
                include $form_component_dir."WeaponProperties.php";

                include $form_component_dir."Damage.php";

                $enums_to_use = EffectDice::ALL();
                $unique_descriptor = "versatile_damage";
                $dropdown_visible = FALSE;
                include $form_component_dir."MultiNumber.php";
            ?>
            <input type="number" id="Effective_Range" name="Effective_Range" class="grey_text grey_border white_background" min="5" style="display: none;"/>
            <input type="number" id="Maximum_Range" name="Maximum_Range" class="grey_text grey_border white_background" min="5" style="display: none;"/>
            <input type="number" name="Weight" min="1" class="grey_text grey_border white_background"/><br/>
            <?php
                $enums_to_use = Coins::ALL();
                $unique_descriptor = "pieces";
                include $form_component_dir."MultiNumber.php";
            ?>
            <textarea name="description" class="grey_text grey_border white_background description"></textarea>
        </div>
    </div>
    <input type="submit" value="Create" class="highlight_text grey_border white_background"/>
</form>
<script src="<?php echo $file_root; ?>scripts/form_verification.js"></script>