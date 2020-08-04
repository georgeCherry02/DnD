<h5>Key Stats <i class="fas fa-chevron-down" onclick="game.player_cards.toggleKeyStats(<?php echo $player_id; ?>); game.swivelChevron(this);"></i></h5>
<div class="collapsing_section key_stats" id="key_stats_<?php echo $player_id; ?>" style="max-height: 0px;">
    <p class="col-3 text-center"><i class="fas fa-running"></i> <?php echo filter_var($character_info["Speed"], FILTER_VALIDATE_INT); ?></p>
    <p class="col-3 text-center"><i class="fas fa-shield-alt"></i> <?php echo filter_var($character_info["AC"], FILTER_VALIDATE_INT); ?></p>
    <p class="col-3 text-center"><i class="fas fa-eye"></i> <?php echo filter_var($character_info["PP"], FILTER_VALIDATE_INT); ?></p>
    <p class="col-3 text-center"><i class="fas fa-heart"></i> <span id="current_health_text_<?php echo $player_id ?>"><?php echo filter_var($character_info["Current_Hit_Points"], FILTER_VALIDATE_INT); ?></span>/<?php echo filter_var($character_info["Hit_Point_Maximum"], FILTER_VALIDATE_INT); ?></p>
</div>