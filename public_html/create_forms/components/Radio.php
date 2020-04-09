<?php 
    $amount_of_enums = sizeof($enums_to_use); 
    $max_height_on_hover = "calc(". (($amount_of_enums + 1/* represents label */)  * 2) . "em + " . (($amount_of_enums + 1/* represents label */) * 3) . "px)";
    $z_index = 10 - $dropdown_count;
    $dropdown_count++;
?>
<div class='create_form_dropdown <?php echo get_class($enums_to_use[0]); ?> light_green dark_green_border' onmouseover="this.style.maxHeight='<?php echo $max_height_on_hover; ?>'" onmouseout="this.style.maxHeight= 'calc(2em - 4px)'" style="z-index: <?php echo $z_index; ?>;">
    <div class='label_container'>
        <label class='medium_green_text'><?php echo $enums_to_use[0]->getClassDisplayName(); ?>:</label>
    </div>
    <input type='hidden' name='<?php echo $column_name; ?>' id='<?php echo $column_name; ?>_radio_input'/>
    <?php
        foreach($enums_to_use as $enum) {
            echo "<label class='medium_green_text'>" . $enum->getName() . ":</label>";
            echo "<div class=\"" . $column_name . "_radio dark_green_border\" onclick=\"toggle_radio(" . $enum->getValue() . ", '" . $column_name . "', this)\" id=\"" . $enum->getName() . "_radio\"></div>";
            echo "<br/>";
        }
    ?>
</div>
<div class='create_form_placeholder'></div>