<div class="create_form_value_dropdown light_green dark_green_border">
    <div class="label_container">
        <label class="medium_green_text">Coins:</label><br/>
    </div>
    <?php
        for ($i = 1; $i <= 5; $i++) {
            $coin = Coins::fromValue($i);
            echo "<label class='medium_green_text'>".$coin->getName()." Pieces:</label>";
            echo "<input type='number' name='".$coin->getName()."_amount' value='0' min='0' class='dark_green_text dark_green_border light_green'/>";
            echo "<br/>";
        }
    ?>
</div>
<div class='create_form_placeholder'></div>