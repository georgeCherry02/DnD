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
<h4 class="black_text">Here are the two <?php echo $item_type->getPrettyName(); ?>s!</h4>
<div class="duplicate_resolution_response_container">
    <div class='message_container message_container_in_page light_background grey_text grey_border'>
        <h4>Would you like to keep your version or use the new version?</h4>
        <form action="" method="POST" onsubmit="return validate_duplicate_resolution()">
            <div>
                <input type="hidden" value="<?php echo $new_item_id; ?>" name="new_id"/>
                <input type="hidden" value="<?php echo $old_item_id; ?>" name="old_id"/>
                <input type="hidden" value="<?php echo $item_type->getName(); ?>" name="type"/>
                <div class="col-3"></div>
                <label class="col-2 grey_text" for="duplicate_resolution_keep">Keep old</label>
                <div class="col-1">
                    <input type="radio" name="keep_answer" id="duplicate_resolution_keep" value="keep"/>
                </div>
                <label class="col-2 grey_text" for="duplicate_resolution_new">Use new</label>
                <div class="col-1">
                    <input type="radio" name="keep_answer" id="duplicate_resolution_new" value="new"/>
                </div>
                <div class="col-3"></div>
            </div>
            <input type="submit" value="Proceed" class="white_background highlight_text grey_border"/>
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
                    // Create Card wrapper
                    echo "<div class='duplicate_card spell_card light_background grey_border'>";
                    $prepared_info = array();
                    // Parse information
                    include "./duplicate_cards/".$item_type->getName().".php";
                    // Manage Name
                    $item_ownership_description = "Pre-existing item";
                    if ($card_count == 1) {
                        $item_ownership_description = "Your item";
                    }
                    $name = htmlspecialchars($item_info["Name"]);
                    echo "<h2 class='grey_text'>".$item_ownership_description.": ".$name."</h2>";
                    // Create information container html
                    $output_html = "<div class='information_container grey_border'>";
                    // Render information by adding to output html in sections
                    foreach ($prepared_info as $info_name => $info_value) {
                        $output_html .= "<div class='feature_container'>";
                        $output_html .= "<label class='highlight_text'>".$info_name.":</label>";
                        if ($info_name == "Features" || $info_name == "Spells") {
                            $output_html .= $info_value;
                        } else {
                            $output_html .= "<p class='grey_text'>".$info_value."</p>";
                        }
                        $output_html .= "</div>";
                    }
                    // Manage Description
                    $description = "";
                    $description_lines = explode("\n", $item_info["Description"]);
                    for ($i = 0; $i < sizeof($description_lines); $i++) {
                        $description .= htmlspecialchars($description_lines[$i]) . "<br/>";
                    }
                    $output_html .= "<div class='feature_container'><label class='highlight_text'>Description:</label>";
                    $output_html .= "<p class='grey_text item_description'>".$description."</p></div>";
                    // Close and echo information container
                    $output_html .= "</div>";
                    echo $output_html;
                    // Close Card wrapper
                    echo "</div>";
                    $card_count++;
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