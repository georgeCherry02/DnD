<form action="" method="POST" onsubmit="return validate_spell_creation();">
    <input type="hidden" name="form_type" value="Spell"/>
    <div>
        <div class='labels_container'>
            <label for='name' class='required'>Name:</label><br/>
            <label for='level' class='required'>Level:</label><br/>
            <label for='school' class='required'>School of Magic:</label><br/>
            <label for='casting_time' class='required'>Casting Time:</label><br/>
            <label for='range_type' class='required'>Range Type:</label><br/>
            <!-- Only if range_type === "ranged" set range_distance input visible -->
            <label for='range_distance' id='range_distance_label'>Distance:</label><br/>
            <label for='shape_type' class='required'>Shape:</label><br/>
            <!-- Only if a shape other than beam is selected allow shape_size input visible -->
            <label for='shape_size' id='shape_size_label'>Shape Size:</label><br/>
            <label for='vocal'>Vocal:</label><br/>
            <label for='somatic'>Somatic:</label><br/>
            <!-- Figure out how to do material input -->
            <label for='concentration'>Concentration:</label><br/>
            <label for='effect'>Effect:</label><br/>
            <!-- Only allow damage/healing magnitude visible if effect === "damage" or "healing" -->
            <label for='effect_magnitude'>Effect Magnitude:</label>
        </div>
        <div class='col-6 inputs_container'>
            <input type='text' name='name' id='name' class="dark_green_text dark_green_border light_green" required/><br/>
            <input type='number' name='level' id='level' class="dark_green_text dark_green_border light_green" required/><br/>
            <?php
                $enum_class_to_use = MagicSchools::ALL();
                $column_name = "school";
                include $file_root."create_forms/components/Radio.php";
            ?>
        </div>
    </div>
    <input type="submit" value="Create" class="dark_green_text dark_green_border light_green"/>
</form>
<script src="<?php echo $file_root; ?>scripts/form_verification.js"></script>