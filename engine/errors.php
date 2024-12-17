<?php
// $errors = get_all_from_table('errors');
// Создание ошибки в БД
function create_error($error) {
    sendRowToTable("errors", ["text" => $error['text'], "type" => $error['type'],"deadline" => $error['deadline'], "form_id" => $error['form_id']]);
}

// Обновление данных ошибки в БД
function update_error($error) {
    updateRowInTable("errors", ["text" => $error['text'], "type" => $error['type'],"deadline" => $error['deadline']], ["id" => $error['id']]);
}

// Архивация ошибки
function archive_error($error_id) {
    updateRowInTable("errors", ["status" => "1"], ["id" => $error_id]);
}

// Получение всех ошибок из архива
function get_archived_errors() {
    global $errors;
    $result = [];

    foreach ($errors as $e) {
        if ($e['status'] == 1) {
            $result[] = $e;
        }
    }

    return $result;
}