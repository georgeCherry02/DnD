<script>
    const PLAYER_ID = <?php echo $_SESSION["Logged_in_id"]; ?>;
    const GAME_OWNER = <?php echo $game_info["Owner_ID"]; ?>;
    const GAME_ID = <?php echo $game_id; ?>;
    const ROOM_ID = <?php echo $room_id; ?>;
    const BACKGROUND_HEIGHT = <?php echo $end_height; ?>;
    const BACKGROUND_GRID_WIDTH = <?php echo $room_grid_dim["width"]; ?>;
    const BACKGROUND_GRID_HEIGHT = <?php echo $room_grid_dim["height"]; ?>;
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