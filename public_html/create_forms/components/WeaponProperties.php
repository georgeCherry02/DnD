<?php
    $max_height_on_hover = "calc(" . ((sizeof(WeaponProperties::ALL()) + 1) * 2) . "em + " . ((sizeof(WeaponProperties::ALL())+ 1) * 3) . "px)";
    $z_index = 30 - $dropdown_count;
    $dropdown_count++;
?>
<div id='create_form_dropdown_weapon_properties' class='create_form_dropdown weapon_properties light_green dark_green_border' onmouseover="this.style.maxHeight = '<?php echo $max_height_on_hover; ?>'" onmouseout="this.style.maxHeight = 'calc(2em - 4px)'" style="z-index: <?php echo $z_index; ?>;">
    <div class='label_container'>
        <label class='medium_green_text'>Weapon Properties:</label>
    </div>
    <?php
        foreach (WeaponProperties::ALL() as $weapon_prop) {
            echo "<label class='medium_green_text'>".$weapon_prop->getPrettyName().":</label>";
            echo "<input type='hidden' name='".$weapon_prop->getName()."_property' id='".$weapon_prop->getName()."_property' value='0'/>";
            echo "<div class=\"checkbox dark_green_border\" onclick=\"toggle_weapon_prop_checkbox('".$weapon_prop->getName()."');\" id=\"".$weapon_prop->getName()."_property_checkbox\"></div>";
            echo "<br/>";
        }
    ?>
</div>
<div class='create_form_placeholder'></div>