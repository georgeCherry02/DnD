<?php
    if ($room) {
        $link = "game.php?id=".$game_id."&room=".$room["ID"];
        $title = $room["Name"];
    } else {
        $link = "create_room.php?id=".$game_id;
        $title = "+";
    }
?>
<div class="room_card black_background" style="display: inline-block; padding: 2px; width: calc(20% - 10px); margin: 5px;">
    <a href="<?php echo $link; ?>">
        <h4 class="white_text" style="text-align: center; cursor: pointer;"><?php echo $title; ?></h4>
    </a>
</div>