<?php
    $max_hp = $character_info["Hit_Point_Maximum"];
    $c_hp = $character_info["Current_Hit_Points"];
    $t_hp = $character_info["Temporary_Hit_Points"];
    $c_hp_percentile = floor($c_hp/$max_hp * 100);
    $t_hp_percentile = floor($t_hp/$max_hp * 100);
?>
<div class='health_container'>
    <div class='health_bar_background'></div>
    <div id='hb_<?php echo $player_id; ?>' class='health_bar' style='width: calc(<?php echo $c_hp_percentile; ?>% - 2px)<?php
        if ($c_hp_percentile < 100) {
            echo "; border-top-right-radius: 0; border-bottom-right-radius: 0;";
        }
    ?>'></div>
    <div id='thb_<?php echo $player_id; ?>' class='temp_health_bar' style='width: calc(<?php echo $t_hp_percentile; ?>% - 2px)<?php
        if ($c_hp_percentile < 100) {
            echo "; border-top-right-radius: 0; border-bottom-right-radius: 0;";
        }
    ?>'></div>
</div>