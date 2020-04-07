<div class='create_form_abilities_dropdown light_green dark_green_border'>
    <div class='label_container'>
        <label class="medium_green_text">Abilities:</label><br/>
    </div>
    <?php
        for ($i = 1; $i <= 6; $i++) {
            $ability = Abilities::fromValue($i);
            echo "<label class='medium_green_text'>" . $ability->getName() . ":</label>";
            echo "<input type='hidden' name='" . $ability->getName() . "_modifier' id='" . $ability->getName() . "_modifier' value='0'/>";
            echo "<div class=\"checkbox dark_green_border\" onclick=\"toggle_checkbox('" . $ability->getName() . "_modifier')\" id=\"" . $ability->getName() . "_modifier_checkbox\"></div>";
            echo "<br/>";
        }
    ?>
</div>
<div class='create_form_placeholder'></div>