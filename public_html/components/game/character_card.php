<div class="player_card" style="background-color: <?php echo $character_info["Colour"] ?>">
    <h3 onclick="game.player_cards.reveal(<?php echo $player_id; ?>)"><?php echo htmlspecialchars($character_info["Name"]) ?></h3>
    <div class="collapsing_section" id="class_and_level_<?php echo $player_id; ?>" style="max-height: 0px;">
        <h5><?php
            try {
                $class = Classes::fromValue(intval($character_info["Class"]));
                echo $class->getName()." - ".filter_var($character_info["Level"], FILTER_VALIDATE_INT);
            } catch (OutOfRangeException $e) {
                // Ignore
                // If class isn't provided within range don't bother with providing information
            }
        ?></h5>
    </div>
    <div class="player_card_divider"></div>
    <?php include "./components/game/character_card_components/health_bar.php"; ?>
    <div class="collapsing_section" id="player_details_<?php echo $player_id; ?>" style="max-height: 0px;">
        <div class="player_card_divider"></div>
        <?php include "./components/game/character_card_components/ability_description.php"; ?>
        <div class="player_card_divider"></div>
        <?php include "./components/game/character_card_components/key_stats.php"; ?>
        <div class="player_card_divider"></div>
        <?php include "./components/game/character_card_components/weapons.php"; ?>
        <div class="player_card_divider"></div>
        <?php include "./components/game/character_card_components/spells.php"; ?>
    </div>
</div>