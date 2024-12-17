<?php
include('db.php');

// Берем данные из БД
$table = "parser_psu";
$params = [
    "order_id" => '52272'
];
$old_row = get_row_from_table($table, $params);

// print_r($old_row);

// print_r(json_decode($old_row["specialists"]));

$str = json_decode($old_row["hours1"]);
echo $str;