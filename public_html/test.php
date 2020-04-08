<?php  
    $file_location = "root";
    $page_title = "Test";

    include_once "../inc/base.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";

    $type = ItemTypes::Armour();
    echo $type->getClassDisplayName();

    include_once "./global_components/footer.php";
?>