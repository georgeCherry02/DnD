<?php
    $file_location =    "root";
    $page_title =       "Duplicate Choice";

    include_once "../inc/base.php";
    include_once "../inc/classes/ItemManager.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";

    // Check they actually asked to compare
    if (isset($_POST["keep_answer"])) {
        if ($_POST["keep_answer"] == "keep") {
            header("Location: ./create.php?kept=yes");
            exit;
        } else if ($_POST["keep_answer"] == "new") {
            // Pop off last item from array and then push the old item id
            $status = ItemManager::replace_last_inserted_of_type();
            if ($status) {
                header("Location: ./create.php?kept=no");
                exit;
            } else {
                header("Location: ./default.php?err=server");
                exit;
            }
        } else {
            header("Location: ./default.php");
            exit;
        }
    } else if (isset($_POST["compare_answer"])) {
        if ($_POST["compare_answer"] == "no") {
            header("Location: ./create.php?kept=yes");
            exit;
        } else if ($_POST["compare_answer"] !== "yes") {
            header("Location: ./default.php");
            exit;
        }
    } else {
        header("Location: ./default.php");
        exit;
    }

    // Sanitise type
    try {
        $item_type = ItemTypes::fromName($_POST["type"]);
    } catch (OutOfRangeException $e) {
        header("Location: default.php?err=server");
        exit;
    }

    $old_item_id = filter_input(INPUT_POST, "old_id", FILTER_VALIDATE_INT);
    $new_item_id = ItemManager::get_last_inserted_of_type($item_type);
?>
<h4 class="dark_red_text">Here are the two <?php echo $item_type->getPrettyName(); ?>!</h4>
<div class="duplicate_resolution_response_container">
    <div class='message_container message_container_in_page main_green dark_green_text dark_green_border'>
        <h4>Would you like to keep your version or use the new version?</h4>
        <form action="" method="POST" onsubmit="return validate_duplicate_resolution()">
            <div>
                <input type="hidden" value="<?php echo $new_item_id; ?>" name="new_id"/>
                <input type="hidden" value="<?php echo $old_item_id; ?>" name="old_id"/>
                <input type="hidden" value="<?php echo $item_type->getName(); ?>" name="type"/>
                <div class="col-4"></div>
                <label class="col-1 dark_green_text" for="duplicate_resolution_keep">Keep old</label>
                <div class="col-1">
                    <input type="radio" name="keep_answer" id="duplicate_resolution_keep" value="keep"/>
                </div>
                <label class="col-1 dark_green_text" for="duplicate_resolution_new">Use new</label>
                <div class="col-1">
                    <input type="radio" name="keep_answer" id="duplicate_resolution_new" value="new"/>
                </div>
                <div class="col-4"></div>
            </div>
            <input type="submit" value="Proceed" class="light_green dark_green_text dark_green_border"/>
        </form>
    </div>
</div>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>
<script src='<?php echo $file_root; ?>scripts/form_verification.js'></script>
<div>
    <?php
        if ($new_item_id) {
            $data = ItemManager::get_all_item_data(array($old_item_id, $new_item_id), $item_type);
            if ($data) {
                $card_count = 0;
                foreach ($data as $item_info) {
                    echo "<div class='col-6'>";
                    include "./duplicate_cards/".$item_type->getName().".php";
                    echo "</div>";
                }
            } else {
                header("Location: default.php?err=server2");
                exit;
            }
        } else {
            header("Location: default.php?err=server3");
            exit;
        }
    ?>
</div>
<?php
    include_once "./global_components/footer.php";
?>