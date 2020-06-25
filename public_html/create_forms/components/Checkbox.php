<?php
    $visibility_style = "inline-block";
    $placeholder_visibility_style = "block";
    if (!$dropdown_visible) {
        $visibility_style = "none";
        $placeholder_visibility_style = "none";
    }
?>
<input type='hidden' name='<?php echo $column_name; ?>' id='<?php echo $column_name; ?>' value='0' style='display: <?php echo $visibility_style; ?>;'/>
<div class='checkbox grey_border white_background' onclick="toggle_checkbox('<?php echo $column_name; ?>')" id='<?php echo $column_name; ?>_checkbox'></div>
<div id='create_form_dropdown_<?php echo $column_name; ?>_placeholder' class='create_form_placeholder' style="display: <?php echo $placeholder_visibility_style; ?>"></div>
<?php
    $dropdown_visible = TRUE;
?>