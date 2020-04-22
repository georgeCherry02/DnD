<?php
    $max_height_on_hover = "calc(" . ((sizeof(DamageType::ALL()) + 1) * 2) . "em + " . ((sizeof(DamageType::ALL())+ 1) * 3) . "px)";
    $z_index = 30 - $dropdown_count;
    $dropdown_count++;
    if ($dropdown_visible) {
        $display_style = "block";
    } else {
        $display_style = "none";
    }
    $damage_visibility = "display: ".$display_style.";";
?>
<div id='create_form_dropdown_damage' class='create_form_dropdown damage_input white_background grey_border' onmouseover="this.style.maxHeight = '<?php echo $max_height_on_hover; ?>';" onmouseout="this.style.maxHeight = 'calc(2em - 4px)'" style="z-index: <?php echo $z_index; ?>; transition: 3s max-height; <?php echo $damage_visibility; ?>">
    <div class='label_container'>
        <label class='grey_text'>Damage Types:</label><br/>
    </div>
    <?php
        foreach(DamageType::ALL() as $damage_type) {
            echo "<label class='grey_text'>".$damage_type->getName().":</label>";
            echo "<div class='checkbox white_background grey_border' onclick=\"toggle_damage_checkbox('".$damage_type->getName()."');\" id='".$damage_type->getName()."_check'></div>";
            echo "<br/>";
        }
    ?>
</div>
<div id='create_form_dropdown_<?php echo $unique_descriptor; ?>_placeholder' class='create_form_placeholder' style='<?php echo $damage_visibility; ?>'></div>
<?php
    $enums_to_use = EffectDice::ALL();
    foreach(DamageType::ALL() as $damage_type) {
        $unique_descriptor = $damage_type->getName() . "_damage";
        $dropdown_visible = FALSE;
        include $form_component_dir."MultiNumber.php";
    }
?>