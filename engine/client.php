<?php
// Получаем массив заказов на клиента
function get_client_orders($client_id) {
    $result = [];

    foreach (get_all_from_table('orders') as $order) {
        if ($order['client_id'] == $client_id) {
            $result[] = $order;
        }
    }

    return $result;
}

// Получаем клиента по ID
function get_client_by_id($client_id) {
    foreach (get_all_from_table('clients') as $client) {
        if ($client['id'] == $client_id) {
            return $client;
        }
    }
}

// Обновление досье клиента
function updateClientDossier($client_id, $dossier) {
    if (is_array($dossier)) {
        $dossier = json_encode($dossier);
    }
    updateRowInTable("clients", ["dossier" => $dossier], ["id" => $client_id]);
}