<?php 
    $amount_of_enums = sizeof($enums_to_use); 
    $max_height_on_hover = "calc(". (($amount_of_enums + 1/* represents label */)  * 2) . "em + " . (($amount_of_enums + 1/* represents label */) * 3) . "px)";
    $visibility_style = "inline-block";
    $placeholder_visibility_style = "block";
    if (!$dropdown_visible) {
        $visibility_style = "none";
        $placeholder_visibility_style = "none";
    }
    $z_index = 30 - $dropdown_count;
    $dropdown_count++;
?>
<div id='create_form_dropdown_<?php echo $unique_descriptor; ?>' class="create_form_dropdown <?php echo get_class($enums_to_use[0]); ?> white_background grey_border" onmouseover="this.style.maxHeight='<?php echo $max_height_on_hover; ?>'" onmouseout="this.style.maxHeight= 'calc(2em - 4px)'" style="z-index: <?php echo $z_index; ?>; display: <?php echo $visibility_style; ?>;">
    <div class="label_container">
        <label class="grey_text"><?php echo $enums_to_use[0]->getClassDisplayName(); ?>:</label><br/>
    </div>
    <?php
        foreach ($enums_to_use as $enum) {
            echo "<label class='grey_text'>" . $enum->getPrettyName() . ":</label>";
            echo "<input type='number' name='" . $enum->getName() . "_" . $unique_descriptor . "' value='0' min='0' class='grey_text grey_border white_background'/>";
            echo "<br/>";
        }
    ?>
</div>
<div id='create_form_dropdown_<?php echo $unique_descriptor; ?>_placeholder' class='create_form_placeholder' style="display: <?php echo $placeholder_visibility_style; ?>"></div>
<?php
    $dropdown_visible = TRUE;
?>