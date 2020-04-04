<?php  
    $file_location = "root";
    $page_title = "Test";

    include_once "../inc/base.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";
?>
<h2 class="dark_red_text">Here are the two armours!</h2>
<div class="duplicate_resolution_response_container">
    <div class='message_container message_container_in_page main_green dark_green_text dark_green_border'>
        <h4>Would you like to keep your version or use the new version?</h4>
        <form action="" method="POST" onsubmit="return validate_duplicate_resolution()">
            <div>
                <div class="col-4"></div>
                <label class="col-1 dark_green_text" for="duplicate_resolution_keep">Keep</label>
                <div class="col-1">
                    <input type="radio" name="keep_answer" id="duplicate_resolution_keep" value="keep"/>
                </div>
                <label class="col-1 dark_green_text" for="duplicate_resolution_new">New</label>
                <div class="col-1">
                    <input type="radio" name="keep_answer" id="duplicate_resolution_new" value="new"/>
                </div>
                <div class="col-4"></div>
            </div>
            <input type="submit" value="Proceed" class="light_green dark_green_text dark_green_border"/>
        </form>
    </div>
</div>
<div>
<div class='col-6'>
    <div class="armour_card main_green dark_green_border">
        <h2 class="dark_green_text">Your item</h2>
        <h3 class="dark_green_text">Chestplate</h3>
        <h4 class="dark_green_text">Base AC: 10</h4>
        <h4 class="dark_green_text">Additional Modifiers: Strength, Dexterity</h4>
        <h4 class="dark_green_text">Strength Required: 14</h4>
        <h4 class="dark_green_text">Stealth Disadvantage: Yes</h4>
        <h4 class="dark_green_text">Weight: 65lb</h4>
        <h4 class="dark_green_text">Value: 20gp</h4>
    </div>
</div>
<div class='col-6'>
    <div class="armour_card main_green dark_green_border">
        <h2 class="dark_green_text">Your item</h2>
        <h3 class="dark_green_text">Chestplate</h3>
        <h4 class="dark_green_text">Base AC: 10</h4>
        <h4 class="dark_green_text">Additional Modifiers: Strength, Dexterity</h4>
        <h4 class="dark_green_text">Strength Required: 14</h4>
        <h4 class="dark_green_text">Stealth Disadvantage: Yes</h4>
        <h4 class="dark_green_text">Weight: 65lb</h4>
        <h4 class="dark_green_text">Value: 20gp</h4>
    </div>
</div>
</div>
<link rel="stylesheet" href="css/duplicate.css" type="text/css"/>
<link rel='stylesheet' href='<?php echo $file_root; ?>css/form_message.css' type='text/css'/>
<?php
    include_once "./global_components/footer.php";
?>