<?php 
    $amount_of_enums = sizeof($enum_class_to_use); 
    $max_height_on_hover = "calc(". (($amount_of_enums + 1/* represents label */)  * 2) . "em + " . (($amount_of_enums + 1/* represents label */) * 3) . "px)";
?>
<div class='create_form_dropdown <?php echo get_class($enum_class_to_use[0]); ?> light_green dark_green_border' onmouseover="this.style.maxHeight='<?php echo $max_height_on_hover; ?>'" onmouseout="this.style.maxHeight= 'calc(2em - 4px)'">
    <div class='label_container'>
        <label class='medium_green_text'><?php echo $enum_class_to_use[0]->getClassDisplayName(); ?>:</label><br/>
    </div>
    <?php
        foreach($enum_class_to_use as $enum) {
            echo "<label class='medium_green_text'>" . $enum->getName() . ":</label>";
            echo "<input type='hidden' name='" . $enum->getName() . "_". $unique_descriptor ."' id='" . $enum->getName() . "_" . $unique_descriptor . "' value='0'/>";
            echo "<div class=\"checkbox dark_green_border\" onclick=\"toggle_checkbox('" . $enum->getName() . "_" . $unique_descriptor . "')\" id=\"" . $enum->getName() . "_" . $unique_descriptor . "_checkbox\"></div>";
            echo "<br/>";
        }
    ?>
</div>
<div class='create_form_placeholder'></div>