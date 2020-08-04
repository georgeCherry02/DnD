<?php
    $number_of_items = sizeof($items[$item_type->getName()]);
    $max_height_on_hover = "calc(" . (($number_of_items + 1 /* Because of the label */) * 2) . "em + " . (($number_of_items + 1) * 3) . "px)";
    $z_index = 30 - $dropdown_count;
    $dropdown_count++;
?>
<div id="create_form_dropdown_<?php echo $item_type->getName(); ?>" class="create_form_dropdown white_background grey_border" onmouseover="this.style.maxHeight='<?php echo $max_height_on_hover; ?>'" onmouseout="this.style.maxHeight='calc(2em - 4px)'" style="z-index: <?php echo $z_index; ?>">
    <div class="label_container">
        <label class="grey_text"><?php echo $item_type->getName(); ?>s:</label>
    </div>
    <?php
        foreach ($items[$item_type->getName()] as $item) {
            echo "<label class='grey_text'>".htmlspecialchars($item["Name"])."</label>";
            echo "<input type='number' name='".$item_type->getName()."_".$item["ID"]."' id='".$item_type->getName()."_".$item["ID"]."' value='0' min='0'/>";
            echo "<br/>";
        }
    ?>
</div>
<div id="create_form_dropdown_<?php echo $item_type->getName(); ?>_placeholder" class="create_form_placeholder"></div>