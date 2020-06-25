<?php
    $number_of_levels = 9;
    $max_height_on_hover = "calc(" . (($number_of_levels + 1 /* Because of the label */) * 2 /* 2em per label */) . "em + " . (($number_of_levels + 1 /* Due to label */) * 3 /* Amount of padding per label */) . "px)";
    $z_index = 30 - $dropdown_count;
    $dropdown_count++;
?>
<div id="create_form_dropdown_spell_slots" class="create_form_dropdown spell_slots white_background grey_border" onmouseover="this.style.maxHeight='<?php echo $max_height_on_hover; ?>';" onmouseout="this.style.maxHeight='calc(2em - 4px)';" style="z-index: <?php echo $z_index; ?>;">
    <div class="label_container">
        <label class="grey_text">Spell Slots:</label>
    </div>
    <?php
        for ($i = 1; $i <= 9; $i++) {
            echo "<label class='grey_text'>Level ".$i.":</label>";
            echo "<input type='number' name='spell_slot_level_".$i."' value='0' min='0' class='grey_text grey_border white_background'/>";
            echo "<br/>";
        }
    ?>
</div>
<div id='create_form_dropdown_spell_slots_placeholder' class='create_form_placeholder'></div>