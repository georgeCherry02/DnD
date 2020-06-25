<?php
    $file_location = "root";
    $page_title = "Create";

    include_once "../inc/base.php";
    include_once "../inc/classes/Game.php";
    include_once "../inc/classes/UserAdmin.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";

    if (isset($_SESSION["Logged_in"]) && $_SESSION["Logged_in"]) {
        if (isset($_POST["name"])) {
            $name = $_POST["name"];
            if (filter_input(INPUT_POST, "player_amount", FILTER_VALIDATE_INT)) {
                $player_ids = array();
                $player_colours = array();
                $player_amount = $_POST["player_amount"];
                if ($player_amount < 1 || $player_amount > 6) {
                    header("Location: default.php");
                    exit;
                }
                for ($i = 1; $i <= $player_amount; $i++) {
                    $player_name = $_POST["player_".$i."_name"];
                    $player_id = UserAdmin::id_from_username($player_name);
                    if ($player_id) {
                        array_push($player_ids, $player_id);
                        array_push($player_colours, $_POST["player_".$i."_colour"]);
                    } else {
                        header("Location: default.php");
                        exit;
                    }
                }
                array_push($player_ids, $_SESSION["Logged_in_id"]);
                array_push($player_colours, $_POST["colour"]);
                $create_game_sql = "INSERT INTO `Games` (`Name`, `Owner_ID`, `Player_IDs`, `Player_Colours`, `State`, `Connections`) VALUES (:name, :oid, :pids, :pcs, :state, :conns)";
                $create_game_var = array(":name" => $name, ":oid" => $_SESSION["Logged_in_id"], ":pids" => json_encode($player_ids), ":pcs" => json_encode($player_colours), ":state" => "{\"puddles\": []}", ":conns" => "[]");
                try {
                    DB::query($create_game_sql, $create_game_var);
                } catch (PDOException $e) {
                    header("Location: default.php");
                    exit;
                }
                header("Location: games.php");
                exit;
            } else {
                header("Location: default.php");
                exit;
            }
        } else {
?>
        <div class="create_form_container light_background grey_border">
            <form action="" method="POST" onsubmit="return validate_game_creation();">
                <div class="col-4 labels_container highlight_text">
                    <label for="name" class="required">Name:</label>
                    <label for="players">Players:</label>
                    <label for="colour">Your Colour:</label>
                </div>
                <div class="col-6 inputs_container">
                    <input type="text" name="name" id="game_name" class="white_background grey_text grey_border" required><br/>
                    <?php
                        include "./create_forms/components/Players.php";
                    ?>
                    <input type="text" name="colour" id="player_colour" class="white_background grey_text grey_border" required>
                </div>
                <input type="submit" value="Create" class="white_background highlight_text grey_border"/>
            </form>
        </div>
<?php
        }
    }

    include_once "./global_components/footer.php";
?>