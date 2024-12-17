<?php
// Создание нового товара
function warehouse_create_item($name, $cat) {
    sendRowToTable("warehouse_items", ["name" => $name, "category" => $cat]);
}

// Изменение товара
function warehouse_change_item($item) {
    // Декодируем JSON только если $item не является массивом
    if (!is_array($item)) {
        $item = json_decode($item, true);
    }
    
    // Сериализуем массивы в JSON перед записью в базу данных
    foreach ($item as $key => $value) {
        if (is_array($value)) {
            $item[$key] = json_encode($value);
        }
    }
    
    updateRowInTable("warehouse_items", $item, ["id" => $item['id']]);
}


// Удаление товара
function warehouse_delete_item($id) {
    del_row_from_db($id, "warehouse_items");
}

// Создание категории
function warehouse_create_cat($cat) {
    sendRowToTable("warehouse_category", ["name" => $cat]);
}

// Удаление категории
function warehouse_delete_cat($id) {
    del_row_from_db($id, "warehouse_category");
}

// Создание перемещения товара
function warehouse_create_transfer($item_id, $warehouse_id, $amount, $user_id, $name = null, $order = null) {
    if ($name == null) {
        sendRowToTable("warehouse_transfers", ["item_id" => $item_id, "warehouse_id" => $warehouse_id, "amount" => $amount, "user_id" => $user_id]);
    } else {
        sendRowToTable("warehouse_transfers", ["item_id" => $item_id, "warehouse_id" => $warehouse_id, "amount" => $amount, "user_id" => $user_id, "name" => $name, "order_id" => $order]);
    }
}
?>