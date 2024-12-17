<?php
require "engine.php";
// Функционал для склада
require_once "engine/warehouse.php";

// POST-запросы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputData = file_get_contents('php://input');
    $params = json_decode($inputData, true);
    // Отправлять ли стандартный ответ на запрос
    $default_response = true;
    // Отправка SQL запроса на изменение конкретной формы по ID
    if (isset($params['sendform'])) {
        $params['id'] = $params['sendform'];
        unset($params['sendform']);
        $result = set_form($params);
    }
    // Отправка SQL запроса на изменение нескольких форм по ID
    if (isset($params['sendform_list'])) {
        foreach ($params['sendform_list'] as $id) {
            $query = $params['query'];
            $query['id'] = $id;
            $result = set_form($query);
        }
    }
    // Создаем заказ
    if (isset($params['addOrder'])) {
        unset($params['id']);
        unset($params['addOrder']);
        $result = sendRowToTable('orders', $params);
    }

    // Меняем срок ЭЦП клиента по его имени
    if (isset($params['setEcp'])) {
        $ecp_online = ($params['ecp_online']) ? $params['ecp_online'] : null;
        $result = updateRowInTable("clients", ["ecp" => $params['ecp'], "ecp_online" => $ecp_online], ["name" => $params['setEcp']]);
    }
    // Удаляем срок ЭЦП клиента
    if (isset($params['delEcp'])) {
        $result = updateRowInTable("clients", ["ecp" => null], ["id" => $params['delEcp']]);
    }

    // ================================================
    //    ===      =====    ==
    //   == ==     ==  ==   ==
    //  =======    =====    ==
    // ==     ==   ==       ==
    // ================================================
    // // DB - getRow
    // if (isset($params['DBgetRow'])) {
    //     $result = sendRowToTable('orders', $params);
    // }

    // // Отправка SQL запроса на добавление примечания от модератора
    // if (isset($params['comment'])) {
    //     $params['comment'] = "{" . $params['color'] . "}" . $params['comment'];
    //     $result = set_form($params);
    // }
    // Создаем номера заказов из массива с ID заказов
    if (isset($params['guOrderNums'])) {
        $result = array();
        foreach ($params['guOrderNums'] as $order_id) {
            array_push($result, get_order_num_by_id($order_id));
        }
        header('Content-Type: application/json');
        echo json_encode($result);
        $default_response = false;
    }

    // Подписываем акт специалиста
    if (isset($params['accept_act'])) {
        acceptSpecialistAct($params['spec_id'], $params['year'], $params['month']);
    }

    // Отменяем акт специалиста (возврат на доработку)
    if (isset($params['delete_act'])) {
        deleteSpecialistAct($params['spec_id'], $params['year'], $params['month'], $params['delete_act']);
        header('Content-Type: application/json');
        echo json_encode(getAct($params['spec_id'], $params['year'], $params['month']));
        $default_response = false;
    }

    // Отправляем отчеты от модератора ГУ за прошлый период
    if (isset($params['moderator_report'])) {
        set_moderator_report();
    }

    // Меняем тариф специалиста
    if (isset($params['set_spec_tariff'])) {
        setSpecialistTariff($params['spec_id'], $params['set_spec_tariff']);
    }

    // Создание ошибки
    if (isset($params['create_error'])) {
        create_error($params["create_error"]);
    }

    // Обновление ошибки
    if (isset($params['update_error'])) {
        update_error($params["update_error"]);
    }

    // Удаляем ошибку с формы
    if (isset($params['archive_error'])) {
        archive_error($params['archive_error']);
    }

    // ==============
    // Склад
    // ==============

    // Создание нового товара на складе
    if (isset($params['warehouse_create_item'])) {
        warehouse_create_item($params['name'], $params['cat']);
    }

    // Изменение товара на складе
    if (isset($params['warehouse_change_item'])) {
        warehouse_change_item($params['warehouse_change_item']);
    }

    // Удаление товара на складе
    if (isset($params['warehouse_delete_item'])) {
        warehouse_delete_item($params['warehouse_delete_item']);
    }

    // Создание категории на складе
    if (isset($params['warehouse_create_cat'])) {
        warehouse_create_cat($params['cat']);
    }

    // Создание перемещения товара
    if (isset($params['warehouse_create_transfer'])) {
        if (isset($params['name'])) {
            warehouse_create_transfer($params['item_id'], $params['warehouse_id'], $params['amount'], $params['user_id'], $params['name'], $params['order']);
        } else {
            warehouse_create_transfer($params['item_id'], $params['warehouse_id'], $params['amount'], $params['user_id']);
        }
    }

    // Обновление досье клиента
    if (isset($params["updateClientDossier"])) {
        updateClientDossier($params["updateClientDossier"], $params["dossier"]);
        // Проверяем, был ли загружен файл
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            // Получаем информацию о файле
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileSize = $_FILES['photo']['size'];
            $fileType = $_FILES['photo']['type'];
            
            // Указываем директорию для сохранения
            $uploadFileDir = 'uploads/'; // Убедитесь, что эта директория существует и доступна для записи
            $dest_path = $uploadFileDir . $fileName;

            // Перемещаем загруженный файл
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                // Успешная загрузка
                echo json_encode(['success' => true, 'message' => 'Файл успешно загружен']);
            } else {
                // Ошибка перемещения файла
                echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении файла']);
            }
        } else {
            // Ошибка загрузки файла
            echo json_encode(['success' => false, 'message' => 'Не удалось загрузить файл']);
        }
    }

    // Стандартный ответ на запрос
    if ($default_response) {
        // Формируем ответ
        $response = array(
            'status' => 'success',
            'message' => 'POST-запрос успешно обработан'
        );

        // Устанавливаем заголовок Content-Type на application/json
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
// GET-запросы
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = '';
    // Form id
    if (isset($_GET['getform'])) {
        $result = get_form($_GET['getform']);
    }
    // Получение массива форм по параметрам
    if (isset($_GET['getforms'])) {
        $params = [
            "client_id" => "",
            "spec_id" => "",
            "gu_id" => "",
            "psu_id" => "",
            "year" => "",
            "month" => "",
            "accepted" => "true",
            "checkbox1" => "",
            "checkbox2" => ""
        ];
        if (isset($_GET['client_id'])) { $params['client_id'] = $_GET['client_id']; }
        if (isset($_GET['spec_id'])) { $params['spec_id'] = $_GET['spec_id']; }
        if (isset($_GET['gu_id'])) { $params['gu_id'] = $_GET['gu_id']; }
        if (isset($_GET['psu_id'])) { $params['psu_id'] = $_GET['psu_id']; }
        if (isset($_GET['year'])) { $params['year'] = $_GET['year']; }
        if (isset($_GET['month'])) { $params['month'] = $_GET['month']; }
        if (isset($_GET['accepted'])) { $params['accepted'] = $_GET['accepted']; }
        if (isset($_GET['checkbox1'])) { $params['checkbox1'] = $_GET['checkbox1']; }
        if (isset($_GET['checkbox2'])) { $params['checkbox2'] = $_GET['checkbox2']; }
        if (isset($_GET['comment'])) { $params['comment'] = $_GET['comment']; }
        $result = get_forms_by_params($params);
    }
    // Получаем заказ из БД
    if (isset($_GET['getOrder'])) {
        $result = get_order($_GET['getOrder']);
    }
    // Получаем клиента из БД по ИИН
    if (isset($_GET['getClientByIIN'])) {
        $result = get_rows_from_table('clients', ["iin" => $_GET['getClientByIIN']]);
    }
    // Получаем заказ из БД по номеру
    if (isset($_GET['getOrderByNum'])) {
        $result = get_rows_from_table('orders', ["order_num" => $_GET['getOrderByNum']]);
    }
    // Получаем настройку из БД
    if (isset($_GET['get_setting'])) {
        $result = get_setting($_GET['get_setting']);
    }
    // Получаем список клиентов специалиста
    if (isset($_GET['getSpecClients'])) {
        $result = get_spec_clients($_GET['getSpecClients']);
    }
    // Checkbox color
    if (isset($_GET['getCheckboxColor'])) {
        $result = get_checkbox_color(get_form($_GET['getCheckboxColor']));
    }
    // Transaction
    if (isset($_GET['getTransaction'])) {
        $result = getTransaction($_GET['year'], $_GET['month'], $_GET['gu']);
    }
    // Получаем таблицу
    if (isset($_GET['getTable'])) {
        $result = get_all_from_table($_GET['getTable']);
        if (isset($_GET['sortBy'])) {
            if ($_GET['sortBy'] == 'name') {
                $result = sort_by_name($result);
            }
        }
    }
    // Получаем дату текущего периода
    if (isset($_GET['getPeriod'])) {
        $result = get_current_period();
    }
    // Получаем дату прошлого периода
    if (isset($_GET['getPrevPeriod'])) {
        $result = get_previous_period();
    }
    // Считаем часы за месяц для ГУ
    if (isset($_GET['guHours'])) {
        $result = count_hours_by_gu_for_month($_GET['guHours'], $_GET['year'], $_GET['month']);
    }

    // Устанавливаем заголовок Content-Type на application/json
    header('Content-Type: application/json');
    echo json_encode($result);
}
if (isset($_GET['loginto'])) {
    if (get_user($_GET['loginto'])['id'] > 0) {
        $user = get_user($_GET['loginto']);
        // Успешная аутентификация - выполняем необходимые действия для входа пользователя
        session_start(); // Запускаем сессию

        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $user['id'];
        $_SESSION["username"] = $user['username'];
        $_SESSION["group"] = $user['usergroup'];
        return true;
    } else {
        // Пользователь не найден
        return false;
    }
}