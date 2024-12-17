<?php
// Получаем всех специалистов
function get_all_specialists() {
    return get_rows_from_table("users", ["usergroup" => 2]);
}
// =========================================
//                Акты
// =========================================
// Получаем таблицу с подписанными актами
function getActs() {
    return get_all_from_table("acts");
}
// Получаем акт на специалиста на месяц
function getAct($spec_id, $year, $month) {
    $params = [
        "spec_id" => $spec_id,
        "year" => $year,
        "month" => $month
    ];
    return get_row_from_table("acts", $params);
}
// Удаляем акт (возвращаем отчеты на доработку)
function deleteSpecialistAct($spec_id, $year, $month, $form = 0) {
    $act_id = getAct($spec_id, $year, $month)['id'];
    del_row_from_db($act_id, "acts");

    // Если возвращается конкретный отчет, поэтому ему убираем accepted
    if ($form != 0) {
        $params = [
            "id" => $form,
            "accepted" => 'false',
        ];
        set_form($params);
    }
}

// Функция подписывает акт специалиста
function acceptSpecialistAct($spec_id, $year, $month) {
    sendRowToTable("acts", [
        "spec_id" => $spec_id,
        "year" => $year,
        "month" => $month,
    ]);
    // Подписываем формы специалиста
    $params = [
        "spec_id" => $spec_id,
        "year" => $year,
        "month" => $month
    ];
    $forms = get_forms_by_params($params);
    foreach ($forms as $form) {
        $params = [
            "id" => $form['id'],
            "accepted" => 'true',
        ];
        set_form($params);
    }
}

// ==========================================
//                 Тарифы
// ==========================================

// Получаем информацию о тарифе специалиста
function getSpecialistTariff($spec_id) {
    $tariffs = get_all_from_table("tariffs"); // Получаем все тарифы из таблицы

    // Перебираем массив тарифов в поисках нужного spec_id
    foreach ($tariffs as $tariff) {
        // Проверяем, соответствует ли spec_id текущего тарифа искомому
        if (isset($tariff['spec_id']) && $tariff['spec_id'] == $spec_id) {
            return $tariff['salary']; // Возвращаем тариф, если найден соответствующий spec_id
        }
    }

    return get_setting('salary_' . date('Y')); // Возвращаем null, если тариф не найден
}

// Меняем тариф специалиста
function setSpecialistTariff($spec_id, $tariff) {
    $row = get_row_from_table("tariffs", ["spec_id" => $spec_id]);
    if ($row) {
        updateRowInTable("tariffs", ["salary" => $tariff], ["id" => $row['id']]);
    } else {
        sendRowToTable("tariffs", ["spec_id" => $spec_id, "salary" => $tariff]);
    }
}