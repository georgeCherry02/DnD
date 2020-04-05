<?php
    $file_location = "root";
    $page_title =    "Create";

    include_once "../inc/base.php";
    include_once "../inc/classes/ItemManager.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";

    $form_create_types = array("armour", "spell", "stat_block", "weapon");

    if (isset($_SESSION["Logged_in"]) && $_SESSION["Logged_in"]) {
        if (isset($_POST["form_type"]) && isset($_POST["name"])) {
            // Form type is sanitised in function
            $status = ItemManager::create_item();
            switch ($status[0]) {
                case "0":
                    // Standard operation - Duplicate ID
                    // Check if there was a duplicate
                    if (!empty($status[1])) {
                        // Sanitise type
                        if (in_array($_POST["form_type"], $form_create_types)) {
?>  
<div class='dark_green_border main_green message_container'>
    <h4 class='dark_green_text'>There's a similar item already uploaded to the database, would you like to compare them?</h4>
    <p class='dark_green_text'>Any properties of the item you defined will be identical, only properties left undefined by you may differ.</p>
    <form action="./duplicate_resolution.php" method="POST">
        <input type="hidden" name="type" value="<?php echo filter_input(INPUT_POST, "form_type", FILTER_SANITIZE_SPECIAL_CHARS); ?>"/>
        <input type="hidden" name="old_id" value="<?php echo filter_var($status[1][0]["ID"], FILTER_SANITIZE_SPECIAL_CHARS); ?>"/>
        <div>
            <div class='col-4'></div>
            <label for="yes" class='col-1 dark_green_text'>Yes</label>
            <div class='col-1'>
                <input type="radio" name="compare_answer" value="yes" id="yes" class="light_green"/>
            </div>
            <label for="no" class='col-1 dark_green_text'>No</label>
            <div class='col-1'>
                <input type="radio" name="compare_answer" value="no" id="no"/>
            </div>
            <div class='col-4'></div>
        </div>
        <input type="submit" value="Proceed" class="light_green dark_green_text dark_green_border"/>
    </form>
</div>
<script src='<?php echo $file_root; ?>scripts/form_verification.js'></script>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>
<?php
                        }
                    } else {
                        header("Location: ?create=1");
                        exit;
                    }
                    break;
                case "1":
                    // Invalid type - null
                    // Send to home with server error, this shouldn't ever happen
                    header("Location: ./default.php?err=server");
                    exit;
                    break;
                case "2":
                    // DB error - Location
                    // Send to home with server error
                    header("Location: ./default.php?err=server");
                    exit;
                    break;
                case "3":
                    // Reached item capacity - null
                    // Send to create with capacity limit message
                    header("Location: ?err=capacity");
                    break;
                case "4":
                    // Data corruption - null
                    // Maybe reset the appropriate item id column if this occurs?
                    if (in_array($_POST["form_type"], $form_create_types)) {
                        $status = ItemManager::clean_item_type_data();
                        if ($status) {
                            header("Location: ?err=data_corruption");
                            exit;
                        } else {
                            header("Location: ./default.php?err=server");
                            exit;
                        }
                    } else {
                        header("Location: ./default.php?err=server");
                    }
                    break;
            }
        } else if (!isset($_GET["choice"])) {
            if (isset($_GET["create"]) && $_GET["create"] == 1) {
                $popup_message_name = "create/successful";
                include_once "./global_components/popup_message.php";
            } else if (isset($_GET["kept"])) {
                if ($_GET["kept"] == "yes") {
                    $popup_message_name = "create/kept_alternate";
                } else if ($_GET["kept"] == "no") {
                    $popup_message_name = "create/discarded_alternate";
                } else {
                    header("Location: ./create.php");
                    exit;
                }
                include_once "./global_components/popup_message.php";
            } else if (isset($_GET["err"])) {
                if ($_GET["err"] == "server") {
                    $popup_message_name = "server_error.php";
                } else if ($_GET["err"] == "capacity") {
                    $popup_message_name = "create/capacity";
                } else if ($_GET["err"] == "data_corruption") {
                    $popup_message_name = "create/data_corruption";
                } else {
                    header("Location: ./create.php");
                    exit;
                }
                include_once "./global_components/popup_message.php";
            }
?>
<h2 class='dark_red_text'>What would you like to create?</h2>
<div>
    <div class='col-3'>
        <a href='?choice=armour'>
            <div class='option_container'>
                <img class='line' src='./resources/icons/helmet.svg'/>
                <img class='colour' src='./resources/icons/helmet_colour.svg'/>
            </div>
        </a>
    </div>
    <div class='col-3'>
        <a href='?choice=weapon'>
            <div class='option_container'>
                <img class='line' src='./resources/icons/swords.svg'/>
                <img class='colour' src='./resources/icons/swords_colour.svg'/>
            </div>
        </a>
    </div>
    <div class='col-3'>
        <a href='?choice=spell'>
            <div class='option_container'>
                <img class='line' src='./resources/icons/book.svg'/>
                <img class='colour' src='./resources/icons/book_colour.svg'/>
            </div>
        </a>
    </div>
    <div class='col-3'>
        <a href='?choice=stat_block'>
            <div class='option_container'>
                <img class='line' src='./resources/icons/scroll.svg'/>
                <img class='colour' src='./resources/icons/scroll_colour.svg'/>
            </div>
        </a>
    </div>
</div>
<?php
        } else {
?>
<div class="create_form_container main_green dark_green_border">
<?php
            switch($_GET["choice"]) {
                case "armour":
                case "weapon":
                case "spell":
                case "stat_block":
                    include_once "./create_forms/".$_GET["choice"].".php";
                    break;
                default: 
                    header("Location: ");
                    exit;
            }
?>
</div>
<?php
        }
    } else {
        include_once "./global_components/login_message.php";
    }

    include_once "./global_components/footer.php";
?>