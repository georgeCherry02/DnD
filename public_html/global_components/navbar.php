<div class='navbar_container dark_red'>
    <div class='menu_button' onclick='toggle_navbar()'><i class='fas fa-dragon light_red_text'></i></div>
    <h3 class='light_brown_text'>Flat Dragons</h3>
    <?php
        if (isset($_SESSION["Logged_in"]) && $_SESSION["Logged_in"]) {
    ?>
            <div class='logout_button'><a href='<?php echo $file_root; ?>actions/logout.php'><i class='fas fa-sign-out-alt light_red_text'></i></a></div>
    <?php
        }
    ?>
</div>
<div id='navbar_dropdown' class='light_red' style='max-height: 0;'>
    <div class='col-1'></div>
    <div class='col-1'>
        <p><a href='default.php' class='dark_red_text'>Home</a></p>
    </div>
    <div class='col-2'></div>
    <div class='col-1'>
        <p><a href='characters.php' class='dark_red_text'>Characters</a></p>
    </div>
    <div class='col-1'></div>
    <div class='col-1'></div>
    <div class='col-1'>
        <p><a href='games.php' class='dark_red_text'>Games</a></p>
    </div>
    <div class='col-2'></div>
    <div class='col-1'>
        <p><a href='create.php' class='dark_red_text'>Create</a></p>
    </div>
    <div class='col-1'></div>
</div>
<script src='<?php echo $file_root; ?>scripts/menus.js'></script>
<div class='main_content_container'>