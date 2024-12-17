<?php
include('db.php');

// Убедитесь, что запрос является POST-запросом
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем JSON из тела запроса
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if ($data === null) {
        // Ошибка в данных JSON
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['message' => 'Неверный формат JSON']);
    } else {
        // Обрабатываем данные здесь
        // file_put_contents("test.txt", $data[1]);
        $table = "parser_psu";
        $type = $data[0];
        $row = $data[1];

        // Распаковка строки с клиентом
        if ($type == "client") {
            
            send_client_to_db($row);

        }
        
        // Распаковка строки с актом
        if ($type == "act") {
            
            send_act_to_db($row);

        }

        // Отправляем ответ
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Данные получены', 'data' => $data]);
    }
} else {
    // Не POST-запрос
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['message' => 'Метод не разрешен']);
}


function send_client_to_db($row) {
    // Распаковываем архив
    $order_id = $row[0];
    $date_string = $row[1];
    $iin = $row[2];
    $fio = $row[3];
    $address = $row[4];
    $phone = $row[5];
    $status = $row[6];
    $specialists = [];

    // Проверка, есть ли элементы в подмассиве $row[7]
    if (!empty($row[7])) {
        // Добавление первого специалиста (всегда присутствует)
        $specialists[] = $row[7][0];

        // Проверка и добавление второго специалиста, если он есть
        if (isset($row[7][1])) {
            $specialists[] = $row[7][1];
        }
    }


    // Берем данные из БД
    $table = "parser_psu";
    $params = [
        "order_id" => $order_id, 
        "fio" => $fio
    ];
    $old_row = get_row_from_table($table, $params);

    // Если строка не найдена в БД, формируем новую
    if (!$old_row) {

        // Форматируем дату
        $date_object = DateTime::createFromFormat('d.m.Y', $date_string);
        $mysql_date_format = $date_object->format('Y-m-d');

        $row = [
            "order_id" => $order_id,
            "date" => $mysql_date_format,
            "iin" => $iin,
            "fio" => $fio,
            "address" => $address,
            "phone" => $phone,
            "status" => $status,
            "specialists" => json_encode($specialists),
        ];
        
        sendRowToTable($table, $row);
    } else {

        // Форматируем дату
        $date_object = DateTime::createFromFormat('d.m.Y', $date_string);
        $mysql_date_format = $date_object->format('Y-m-d');

        $row = [
            "order_id" => $order_id,
            "date" => $mysql_date_format,
            "iin" => $iin,
            "fio" => $fio,
            "address" => $address,
            "phone" => $phone,
            "status" => $status,
            "specialists" => json_encode($specialists),
        ];

        updateRowInTable($table, $row, $params);

    }
}


function send_act_to_db($row) {
    require "../config.php";
    $order_id = $row[6];
    $year = $row[0];
    $month = $row[1];
    $hours = $row[3];
    $specialist_name = $row[4];
    if (isset($row[7])) {
        $spec_from_client_row = $row[7];
    }

    // Берем данные из БД
    $table = "parser_psu";
    $params = ["order_id" => $order_id];
    $old_row = get_row_from_table($table, $params);

    if ($old_row) {
        $spec_list = json_decode($old_row['specialists'], true);
        foreach ($spec_list as $spec_name) {
            if (!str_contains($spec_name, $specialist_name)) {
                
            }
        }
        // Декодируем JSON-данные актов
        $acts = json_decode($old_row['acts'], true);
        if (!$acts) {
            $acts = [];
        }

        // Обновляем или добавляем данные акта
        if (!isset($acts[$year])) {
            $acts[$year] = [];
        }
        if (!isset($acts[$year][$month])) {
            $acts[$year][$month] = [];
        }

        // Проверяем, существует ли уже запись для данного специалиста
        $updated = false;
        foreach ($acts[$year][$month] as &$act) {
            if ($act['specialist_name'] === $specialist_name) {
                $act['hours'] = $hours;
                $updated = true;
                break;
            }
        }
        if (!$updated) {
            $acts[$year][$month][] = [
                'specialist_name' => $specialist_name,
                'hours' => $hours
            ];
        }

        // Сохраняем обновленные данные обратно в БД
        $old_row['acts'] = json_encode($acts);
        updateRowInTable($table, $old_row, $params);
    }
}
