<?php
    $file_location = "root";
    $page_title = "Create";

    include_once "../inc/base.php";

    if (isset($_SESSION["Logged_in_id"]) && $_SESSION["Logged_in"]) {
        if (isset($_POST["game_id"]) && isset($_POST["room_id"])) {
            $game_id = $_POST["game_id"];
            $room_id = $_POST["room_id"];
            include_once "../inc/classes/ItemManager.php";
            include_once "../inc/classes/User.php";
            include_once "../inc/classes/Game.php";
            // Need to start off by inserting all distributions that will be replaced with an ID
            // Start with abilities
            $ability_summary = ItemManager::gather_ability_summary();
            $sql_start = "INSERT INTO `Ability_Distributions` (";
            $sql_end = ") VALUES (";
            foreach (Abilities::ALL() as $ability) {
                $sql_start .= $ability->getName() . ", ";
                $sql_end .= ":" . $ability->getName() . ", ";
            }
            $sql = substr($sql_start, 0, -2) . substr($sql_end, 0, -2) . ")";
            try {
                $ability_id = DB::query($sql, $ability_summary);
            } catch (PDOException $e) {
                // Error immediately, can afford to do nothing
                header("Location: default.php?err=1");
                exit;
            }
            // Then move onto Spell Slot Distributions
            $spellslot_summary = ItemManager::gather_spell_slot_summary();
            $sql_start = "INSERT INTO `Spell_Slot_Distributions` (";
            $sql_end = ") VALUES (";
            for ($i = 1; $i <= 9; $i++) {
                $sql_start .= "`Level_".$i."`, ";
                $sql_end .= ":level".$i.", ";
            }
            $sql = substr($sql_start, 0, -2) . substr($sql_end, 0, -2) . ")";
            try {
                $total_spell_slot_id = DB::query($sql, $spellslot_summary);
            } catch (PDOException $e) {
                // Need to think about this
                header("Location: default.php?err=2");
                exit;
            }
            try {
                $current_spell_slot_id = DB::query($sql, $spellslot_summary);
            } catch (PDOException $e) {
                header("Location: default.php?err=3");
                exit;
                // Again think about this
            }
            // Then move onto features
            $feature_ids = array();
            if (filter_input(INPUT_POST, "feature_amount", FILTER_VALIDATE_INT)) {
                $number_of_features = $_POST["feature_amount"];
                for ($i = 1; $i <= $number_of_features; $i++) {
                    $feature_sql_variables = array();
                    $feature_sql_variables[":name"] = $_POST["feature_".$i."_name"];
                    $feature_sql_variables[":desc"] = $_POST["feature_".$i."_desc"];
                    $feature_sql = "INSERT INTO `Features` (`Name`, `Description`) VALUES (:name, :desc)";
                    try {
                        $feature_id = DB::query($feature_sql, $feature_sql_variables);
                    } catch (PDOException $e) {
                        // Fix this...
                        header("Location: default.php?err=4");
                        exit;
                    }
                    array_push($feature_ids, $feature_id);
                }
            }

            // Then parse any complex inputs
            $parsed_data = array();
            $parsed_data = ItemManager::gather_multi_select($parsed_data, "saving", "saving", Abilities::ALL());
            $parsed_data = ItemManager::gather_multi_select($parsed_data, "skill_prof", "proficiency", Skills::ALL());
            $parsed_data = ItemManager::gather_multi_select($parsed_data, "expertise", "expertise", Skills::ALL());
            $parsed_data = ItemManager::gather_multi_number($parsed_data, "tot_hd", "hit_dice", EffectDice::ALL());
            $parsed_data["c_hd"] = $parsed_data["tot_hd"];
            $parsed_data["spell_list"] = array();
            $parsed_data["weapons"] = array();
            $owned_item_ids = ItemManager::get_owned_items();
            $weapon_pattern = "/^".ItemTypes::Weapon()->getName()."_/";
            $spell_pattern = "/^".ItemTypes::Spell()->getName()."_/";
            foreach ($_POST as $key => $value) {
                if (preg_match($weapon_pattern, $key)) {
                    $current_item_type = ItemTypes::Weapon();
                } else if (preg_match($spell_pattern, $key)) {
                    $current_item_type = ItemTypes::Spell();
                } else {
                    continue;
                }
                // Parse the item ID from the key
                $item_id = substr($key, strpos($key, "_") + 1);
                $owned_items_of_type = json_decode($owned_item_ids[$current_item_type->getItemListColumn()]);
                if (in_array($item_id, $owned_items_of_type)) {
                    if (filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT)) {
                        if ($current_item_type == ItemTypes::Spell() && $value == 1) {
                            array_push($parsed_data["spell_list"], $item_id);
                        } else if ($current_item_type == ItemTypes::Weapon() && $value > 0) {
                            $parsed_data["weapons"][$item_id] = $value;
                        }
                    }
                }
            }
            $parsed_data["spell_list"] = json_encode($parsed_data["spell_list"]);
            $parsed_data["weapons"] = json_encode($parsed_data["weapons"]);
            
            // Now start assembling the final request...
            $sql = "INSERT INTO `Characters` (`Owner_ID`, `Name`, `Class`, `Level`, `Background`, `Player_Name`, `Race`, `Alignment_GE`, `Alignment_LC`, `Experience`, `Ability_Scores_ID`, `Saving_Throw_Proficiencies`, `Skill_Proficiencies`, `Expertise`, `Jack_of_all_trades`, `Speed`, `Hit_Point_Maximum`, `Current_Hit_Points`, `Temporary_Hit_Points`, `Total_Hit_Dice_ID`, `Current_Hit_Dice_ID`, `Racial_Spell_Casting_Ability`, `Class_Spell_Casting_Ability`, `Spell_ID_List`, `Total_Spell_Slot_Distribution_ID`, `Current_Spell_Slot_Distribution_ID`, `Weapon_ID_List`, `Armour_ID`, `Shield`, `Feature_IDs`)";
            $sql .= " VALUES (:oid, :name, :class, :level, :background, :player_name, :race, :alignment_ge, :alignment_lc, :exp, :ab_id, :saving, :skill_prof, :expertise, :jack, :speed, :hp_max, :c_hp, :t_hp, :tot_hd, :c_hd, :rac_spell_ab, :class_spell_ab, :spell_list, :total_spell_slot, :current_spell_slot, :weapons, :armour, :shield, :feature_ids)";
            $sql_var[":oid"] = $_SESSION["Logged_in_id"];
            $sql_var[":name"] = $_POST["name"];
            $sql_var[":class"] = $_POST["class"];
            $sql_var[":level"] = $_POST["level"];
            $sql_var[":background"] = $_POST["background"];
            $sql_var[":player_name"] = User::fetch_name();
            $sql_var[":race"] = $_POST["race"];
            $sql_var[":alignment_ge"] = $_POST["alignment_ge"];
            $sql_var[":alignment_lc"] = $_POST["alignment_lc"];
            $sql_var[":exp"] = $_POST["experience"];
            $sql_var[":ab_id"] = $ability_id;
            $sql_var[":jack"] = $_POST["jack"];
            $sql_var[":speed"] = $_POST["speed"];
            $sql_var[":hp_max"] = $_POST["hp_max"];
            $sql_var[":c_hp"] = $_POST["hp_max"];
            $sql_var[":t_hp"] = "0";
            $sql_var[":rac_spell_ab"] = $_POST["racial_spell_casting_ability"];
            $sql_var[":class_spell_ab"] = $_POST["class_spell_casting_ability"];
            $sql_var[":total_spell_slot"] = $total_spell_slot_id;
            $sql_var[":current_spell_slot"] = $current_spell_slot_id;
            $sql_var[":armour"] = $_POST[ItemTypes::Armour()->getName()."_ID"];
            $sql_var[":shield"] = $_POST["shield"];
            $sql_var[":feature_ids"] = json_encode($feature_ids);

            foreach ($parsed_data as $name => $value) {
                $sql_var[":".$name] = $value;
            }
            try {
                $char_id = DB::query($sql, $sql_var);
            } catch (PDOException $e) {
                header("Location: default.php?err=5");
                exit;
            }

            $res = Game::add_player_character($game_id, $char_id);
            if ($res) {
                header("Location: game.php?id=".urlencode($game_id)."&room=".urlencode($room_id));
                exit;
            } else {
                header("Location: default.php?err=6");
                exit;
            }
        } else if (isset($_GET["gid"]) && isset($_GET["rid"])) {
            $game_id = filter_input(INPUT_GET, "gid", FILTER_VALIDATE_INT);
            $room_id = filter_input(INPUT_GET, "rid", FILTER_VALIDATE_INT);
            include_once "../inc/classes/Game.php";
            // Verify that player is in game
            if (Game::verify_player($game_id)) {
                // Verify that player doesn't have a character
                $character_ids = json_decode(Game::fetch_display_information($game_id)["Player_Character_IDs"], $assoc=true);
                if (!array_key_exists($_SESSION["Logged_in_id"], $character_ids)) {
                    include_once "../inc/classes/ItemManager.php";
                    include_once "./global_components/header.php";
                    include_once "./global_components/navbar.php";

                    // Initialise form
                    $dropdown_count = 0;
                    $dropdown_visible = TRUE;
                    $form_component_dir = $file_root."create_forms/components/";

                    // Gather owned item information
                    $owned_item_ids = ItemManager::get_owned_items();
                    if (!$owned_item_ids) {
                        header("Location: default.php?err=pdo");
                        exit;
                    }
                    $items = array();
                    foreach ($owned_item_ids as $column_name => $ids) {
                        $item_type_name = substr($column_name, 0, strpos($column_name, "_"));
                        try {
                            $item_type = ItemTypes::fromName($item_type_name);
                        } catch (OutOfRangeException $e) {
                            header("Location: default.php?err=enum_err");
                            exit;
                        }
                        $items[$item_type_name] = ItemManager::get_all_item_data(json_decode($ids), $item_type);
                    }
?>
                    <div class="create_form_container light_background grey_border">
                        <form action="" method="POST">
                            <input type="hidden" name="game_id" value="<?php echo $game_id; ?>"/>
                            <input type="hidden" name="room_id" value="<?php echo $room_id; ?>"/>
                            <div class="col-4 labels_container highlight_text">
                                <label for="name" class="required">Name:</label>
                                <label for="class" class="required">Class:</label>
                                <label for="level" class="required">Level:</label>
                                <label for="experience" class="required">Experience:</label>
                                <label for="background">Background:</label>
                                <label for="race" class="required">Race:</label>
                                <label for="alignment_ge" class="required">Good/Evil Alignment:</label>
                                <label for="alignment_lc" class="required">Lawful/Chaotic Alignment:</label>
                                <label for="abilities" class="required">Abilities:</label>
                                <label for="saving_throws" class="required">Saving Throw Proficiencies:</label>
                                <label for="skill_proficiencies" class="required">Skill Proficiencies:<label>
                                <label for="skill_expertise" class="required">Skill Expertise:</label>
                                <label for="jack_of_all_trades">Jack of all trades:</label>
                                <label for="speed" class="required">Speed:</label>
                                <label for="hp_max" class="required">Hit Point Max:</label>
                                <label for="hit_dice" class="required">Hit Dice:</label>
                                <label for="class_spell_casting_ability">Class Spell Casting Ability:</label>
                                <label for="racial_spell_casting_ability">Racial Spell Casting Ability:</label>
                                <label for="spell_list">Spells:</label>
                                <label for="spell_slot_distribution">Spell Slots:</label>
                                <label for="weapons" class="required">Weapons:</label>
                                <label for="armour" class="required">Armour:</label>
                                <label for="shield" class="required">Shield:</label>
                                <label for="features">Features:</label>
                            </div>
                            <div class="col-6 inputs_container">
                                <input type="text" id="name" name="name" class="grey_text grey_border white_background" required/>
                                <?php
                                    $enums_to_use = Classes::ALL();
                                    $column_name = "class";
                                    include $form_component_dir."Radio.php";
                                ?>
                                <input type="number" id="level" name="level" class="grey_text grey_border white_background" min="1" max="20"/>
                                <input type="number" id="experience" name="experience" class="grey_text grey_border white_background" min="0"/>
                                <input type="text" id="background" name="background" class="grey_text grey_border white_background"/>
                                <?php
                                    $enums_to_use = Races::ALL();
                                    $column_name = "race";
                                    include $form_component_dir."Radio.php";

                                    $enums_to_use = AlignmentsGE::ALL();
                                    $column_name = "alignment_ge";
                                    include $form_component_dir."Radio.php";
                                    
                                    $enums_to_use = AlignmentsLC::ALL();
                                    $column_name = "alignment_lc";
                                    include $form_component_dir."Radio.php";

                                    $enums_to_use = Abilities::ALL();
                                    $unique_descriptor = "modifier";
                                    include $form_component_dir."MultiNumber.php";

                                    $unique_descriptor = "saving";
                                    include $form_component_dir."MultiSelect.php";

                                    $enums_to_use = Skills::ALL();
                                    $unique_descriptor = "proficiency";
                                    include $form_component_dir."MultiSelect.php";

                                    $unique_descriptor = "expertise";
                                    include $form_component_dir."MultiSelect.php";

                                    $column_name = "jack";
                                    include $form_component_dir."Checkbox.php";
                                ?>
                                <input type="number" id="speed" name="speed" class="grey_text grey_border white_background" min="0"/>
                                <input type="number" id="hp_max" name="hp_max" class="grey_text grey_border white_background" min="0"/>
                                <?php
                                    $enums_to_use = EffectDice::ALL();
                                    $unique_descriptor = "hit_dice";
                                    include $form_component_dir."MultiNumber.php";

                                    $enums_to_use = Abilities::ALL();
                                    $column_name = "racial_spell_casting_ability";
                                    include $form_component_dir."Radio.php";

                                    $enums_to_use = Abilities::ALL();
                                    $column_name = "class_spell_casting_ability";
                                    include $form_component_dir."Radio.php";

                                    $item_type = ItemTypes::Spell();
                                    include $form_component_dir."ItemSelect.php";

                                    include $form_component_dir."SpellSlot.php";

                                    $item_type = ItemTypes::Weapon();
                                    include $form_component_dir."ItemNumber.php";
                                    
                                    $item_type = ItemTypes::Armour();
                                    include $form_component_dir."ItemRadio.php";

                                    $column_name = "shield";
                                    include $form_component_dir."Checkbox.php";

                                    include $form_component_dir."Features.php";
                                ?>
                            </div>
                            <input type="submit" value="Create" class="white_background highlight_text grey_border"/>
                        </form>
                        <script src="<?php echo $file_root; ?>scripts/form_verification.js"></script>
                    </div>
<?php
                    include_once "./global_components/footer.php";
                } else {
                    header("Location: default.php?err=character_exists");
                    exit;
                }
            } else {
                header("Location: default.php?err=not_in_game");
                exit;
            }
        } else {
            header("Location: default.php?err=gid_or_rid_unset");
            exit;
        }
    } else {
        header("Location: default.php?err=not_logged_in");
        exit;
    }
?>