<?php
if ($game) {
    $link = "game.php?id=" . $games[$i]["ID"];
    $title = $game["Name"];
} else {
    $link = "create_game.php";
    $title = "+";
}
?>
<div class="game_card black_background" style="display: inline-block; padding: 2px; width: calc(33.3% - 10px); margin: 5px;">
    <a href="<?php echo $link; ?>">
        <h4 class="white_text" style="text-align: center; cursor: pointer;"><?php echo $title; ?></h4>
    </a>
</div>