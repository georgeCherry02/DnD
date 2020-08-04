<?php
    $abilities = $character_info["Abilities"];
?>
<h5>Abilities <i class="fas fa-chevron-down" onclick="game.player_cards.toggleAbilities(<?php echo $player_id; ?>); game.swivelChevron(this);"></i></h5>
<div class="collapsing_section ability_description" id="ability_description_<?php echo $player_id; ?>" style="max-height: 0px;">
    <?php
        foreach (Abilities::ALL() as $ability) {
            $abbreviation = strtoupper(substr($ability->getName(), 0, 3));
            echo "<p>".$abbreviation.": ".filter_var($abilities[$ability->getName()], FILTER_VALIDATE_INT)."</p>";
        }
    ?>
</div>