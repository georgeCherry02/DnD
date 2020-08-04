<?php
    // Provide list of item data and item type
    $number_of_items = sizeof($item_data);
    $max_height_on_hover = "calc(" . (($number_of_items + 1 /* Because of the label */) * 2) . "em + " . (($number_of_items + 1) * 3) . "px)";
?>
<div id="create_form_dropdown_<?php echo $item_type->getName(); ?>s" class="create_form_dropdown <?php echo $item_type->getName(); ?> white_background grey_border" onmouseover="this.style.maxHeight='<?php echo $max_height_on_hover; ?>'" onmouseout="this.style.maxHeight='calc(2em - 4px)'">
    <div class="label_container">
        <label class="grey_text"><?php echo $item_type->getName(); ?>s:</label>
    </div>
    <?php
        foreach ($item_data as $item) {
            echo "<label class='grey_text'>".htmlspecialchars($item["Name"]).":</label>";
            echo "<input type='number' name='".$item_type->getName()."_".$item["ID"]."' id='".$item_type->getName()."_".$item["ID"]."' value='0' min='0' max='10'/>";
            echo "<br/>";
        }
    ?>
</div>
<div id='create_form_dropdown_<?php echo $item_type->getName(); ?>s_placeholder' class="create_form_placeholder"></div>