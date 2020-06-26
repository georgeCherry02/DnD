<?php
    $file_location = "root";
    $page_title = "Create";

    include_once "../inc/base.php";

    if (isset($_SESSION["Logged_in_id"]) && $_SESSION["Logged_in"]) {
        include_once "../inc/classes/Game.php";
        include_once "../inc/classes/ItemManager.php";

        include_once "./global_components/header.php";
        include_once "./global_components/navbar.php";
        if (isset($_POST["name"])) {
            // Gather data
            $room_name = $_POST["name"];
            if (filter_input(INPUT_POST, "game_id", FILTER_VALIDATE_INT)) {
                $game_id = $_POST["game_id"];
            } else {
                header("Location: default.php");
                exit;
            }
            $imageFileType = strtolower(pathinfo(basename($_FILES["background"]["name"]), PATHINFO_EXTENSION));
            if ($imageFileType != "jpg") {
                header("Location: create_room.php?id=".$game_id."&err=type");
                exit;
            }
            // Gather basic data
            $item_type = ItemTypes::StatBlock();
            $owned_stat_block_ids = json_decode(ItemManager::get_owned_npcs());
            $room_stat_blocks = array("ids" => array(), "amounts" => array());
            for ($i = 0; $i < sizeof($owned_stat_block_ids); $i++) {
                $post_key = $item_type->getName() . "_" . $owned_stat_block_ids[$i];
                $post_val = filter_var($_POST[$post_key], FILTER_VALIDATE_INT);
                array_push($room_stat_blocks["ids"], $owned_stat_block_ids[$i]);
                array_push($room_stat_blocks["amounts"], $post_val);
            }
            // Commit to database
            $room_sql = "INSERT INTO `Rooms` (`Game_ID`, `Name`, `NPC_IDs`) VALUES (:gid, :name, :npcs)";
            $room_var = array(":gid" => $game_id, ":name" => $room_name, ":npcs" => json_encode($room_stat_blocks));
            try {
                $room_id = DB::query($room_sql, $room_var);
            } catch (PDOException $e) {
                header("Location: create_room.php?id=".$game_id."&err=server");
                exit;
            }

            // Upload file
            $room_background_path = "./resources/rooms/background".$room_id.".jpg";
            if (move_uploaded_file($_FILES["background"]["tmp_name"], $room_background_path)) {
                header("Location: game.php?id=".$game_id);
                exit;
            } else {
                header("Location: create_room.php?id=".$game_id."&err=img");
                exit;
            }
        } else if (isset($_GET["id"])) {
?> 
            <div class="create_form_container light_background grey_border">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="game_id" value="<?php echo $_GET["id"]; ?>"/>
                    <div class="col-4 labels_container highlight_text">
                        <label for="name" class="required">Name</label>
                        <label for="background_image">Background</label>
                        <label for="npc_list">NPCs</label>
                    </div>
                    <div class="col-6 inputs_container">
                        <input type="text" name="name" id="room_name" class="white_background grey_text grey_border" required/><br/>
                        <label class="input_label white_background grey_text grey_border">
                            Background:
                            <input type="file" name="background" id="background_upload" style="display: none;">
                        </label>
                        <?php
                            // Fetch the players NPCs
                            $item_type = ItemTypes::StatBlock();
                            $npc_ids = json_decode(ItemManager::get_owned_npcs());
                            $npc_data = ItemManager::get_all_item_data($npc_ids, $item_type);
                            $item_data = $npc_data;
                            include "./create_forms/components/ItemNumber.php";
                        ?>
                    </div>
                    <input type="submit" value="Create" class="white_background highlight_text grey_border"/>
                </form>
            </div>
<?php
        } else {
            header("Location: default.php");
            exit;
        }
        include_once "./global_components/footer.php";
    } else {
        header("Location: default.php");
        exit;
    }
?>