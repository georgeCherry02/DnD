<script>
    const PLAYER_ID = <?php echo $game_info["Owner_ID"]; ?>;
    const GAME_ID = <?php echo $game_id; ?>;
    const PLAYER_COLOURS = {
<?php
    $colours = "";
    $player_ids = json_decode($game_info["Player_IDs"]);
    $player_colours = json_decode($game_info["Player_Colours"]);
    for ($i = 0; $i < sizeof($player_ids); $i++) {
        $colours .= $player_ids[$i].": \"".$player_colours[$i]."\", ";
    }
    $colours = substr($colours, 0, strlen($colours) - 2);
    echo $colours;
?>
    };
</script>