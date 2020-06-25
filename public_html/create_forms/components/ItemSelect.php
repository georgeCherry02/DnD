<?php
    $number_of_items = sizeof($items[$item_type->getName()]);
    $max_height_on_hover = "calc(" . (($number_of_items + 1 /* Because of the label */) * 2 /* 2em for each one */) . "em + " . (($number_of_items + 1 /* Because of the label */) * 3 /* 3px spacing for each */) . "px)";
    $z_index = 30 - $dropdown_count;
    $dropdown_count++;
?>
<div id='create_form_dropdown_<?php echo $item_type->getName(); ?>s' class='create_form_dropdown <?php echo $item_type->getName(); ?> white_background grey_border' onmouseover="this.style.maxHeight='<?php echo $max_height_on_hover; ?>'" onmouseout="this.style.maxHeight= 'calc(2em - 4px)'" style="z-index: <?php echo $z_index; ?>;">
    <div class='label_container'>
        <label class='grey_text'><?php echo $item_type->getName(); ?>s:</label>
    </div>
    <?php
        foreach ($items[$item_type->getName()] as $item) {
            echo "<label class='grey_text'>".htmlspecialchars($item["Name"]).":</label>";
            echo "<input type='hidden' name='".$item_type->getName()."_".$item["ID"]."' id='".$item_type->getName()."_".$item["ID"]."' value='0'/>";
            echo "<div class=\"checkbox grey_border white_background\" onclick=\"toggle_checkbox('".$item_type->getName()."_".$item["ID"]."');\" id=\"".$item_type->getName()."_".$item["ID"]."_checkbox\"></div>";
            echo "<br/>";
        }
    ?>
</div>
<div id='create_form_dropdown_<?php echo $item_type->getName(); ?>s_placeholder' class='create_form_placeholder'></div>