<?php
    $number_of_items = sizeof($items[$item_type->getName()]);
    $max_height_on_hover = "calc(" . (($number_of_items + 1) * 2) . "em + " . (($number_of_items + 1) * 3) . "px)";
    $z_index = 30 - $dropdown_count;
    $dropdown_count++;
?>
<div id="create_form_dropdown_<?php echo $item_type->getName() ?>" class="create_form_dropdown light_green dark_green_border" onmouseover="this.style.maxHeight='<?php echo $max_height_on_hover; ?>'" onmouseout="this.style.maxHeight= 'calc(2em - 4px)'" style="z-index: <?php echo $z_index; ?>; display: <?php echo $visibility_style; ?>;">
    <div class="label_container">
        <label class="medium_green_text"><?php echo $item_type->getName(); ?>s:</label>
    </div>
    <input type="hidden" name="<?php echo $item_type->getName(); ?>_ID" id="<?php echo $item_type->getName(); ?>_radio_input">
    <?php
        foreach ($items[$item_type->getName()] as $item) {
            echo "<label class='medium_green_text'>".htmlspecialchars($item["Name"]).":</label>";
            echo "<div class=\"".$item_type->getName()."_radio dark_green_border\" onclick=\"toggle_radio(".$item["ID"].", '".$item_type->getName()."', this)\" id=\"".$item_type->getName()."_radio\"></div>";
            echo "<br/>";
        }
    ?>
</div>
<div id='create_form_dropdown_<?php echo $item_type->getName(); ?>_placeholder' class='create_form_placeholder'></div>