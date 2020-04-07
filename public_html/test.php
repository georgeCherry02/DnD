<?php  
    $file_location = "root";
    $page_title = "Test";

    include_once "../inc/base.php";

    include_once "./global_components/header.php";
    include_once "./global_components/navbar.php";

    $item_type = ItemTypes::Armour();

    echo "Table name: ".$item_type->getTableName()."<br/>";
    echo "Pretty name: ".$item_type->getPrettyName()."<br/>";
    echo "Item Limit Column: ".$item_type->getItemLimitColumn()."<br/>";
    echo "Item List Column: ".$item_type->getItemListColumn()."<br/>";
    echo "Valid Table Column names: ".implode($item_type->getValidTableColumns())."<br/>";

    include_once "./global_components/footer.php";
?>