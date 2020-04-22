<div class='navbar_container black_background'>
    <div class='menu_button' onclick='toggle_navbar()'><i class='fas fa-dragon highlight_text'></i></div>
    <h3 class='white_text'>Flat Dragons</h3>
    <?php
        if (isset($_SESSION["Logged_in"]) && $_SESSION["Logged_in"]) {
    ?>
            <div class='logout_button'><a href='<?php echo $file_root; ?>actions/logout.php'><i class='fas fa-sign-out-alt highlight_text'></i></a></div>
    <?php
        }
    ?>
</div>
<div id='navbar_dropdown' class='grey_background' style='max-height: 0;'>
    <div class='col-2'></div>
    <div class='col-2'>
        <p><a href='default.php' class='white_text'>Home</a></p>
    </div>
    <div class='col-1'></div>
    <div class='col-2'>
        <p><a href='games.php' class='white_text'>Games</a></p>
    </div>
    <div class='col-1'></div>
    <div class='col-2'>
        <p><a href='create.php' class='white_text'>Create</a></p>
    </div>
    <div class='col-2'></div>
</div>
<script src='<?php echo $file_root; ?>scripts/menus.js'></script>
<div class='main_content_container'>