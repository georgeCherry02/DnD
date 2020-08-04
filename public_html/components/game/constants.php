<script>
    const PLAYER_ID = <?php echo $_SESSION["Logged_in_id"]; ?>;
    const GAME_OWNER = <?php echo $game_info["Owner_ID"]; ?>;
    const GAME_ID = <?php echo $game_id; ?>;
    const ROOM_ID = <?php echo $room_id; ?>;
    const BACKGROUND_HEIGHT = <?php echo $end_height; ?>;
    const BACKGROUND_GRID_WIDTH = <?php echo $room_grid_dim["width"]; ?>;
    const BACKGROUND_GRID_HEIGHT = <?php echo $room_grid_dim["height"]; ?>;
    const PLAYER_COLOURS = <?php echo $game_info["Player_Colours"] ?>;
    const PLAYER_IDS =
<?php
    $players_excluding_gm = array_splice($allowed_players, array_search($game_info["Owner_ID"], $allowed_players), 1);
    $html = "[";
    for ($i = 0; $i < sizeof($players_excluding_gm); $i++) {
        $html .= $players_excluding_gm[$i].", ";
    }
    echo substr($html, 0, -2)."]";
?>
</script>