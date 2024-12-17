<?php
// Глобальные переменные специалиста
$spec_clients = get_spec_clients($_SESSION["id"]);
$count_id = 1;
$total_hours = 0;
$total_salary = 0;
$remaining_hours_total = 0;
$current_date = date('Y-m-d');
$year = date('Y');
$month = date('m');
$act = getAct($_SESSION['id'], $year, $month);

// Функция для получения параметров
function prepare_form_params($client_id, $spec_id, $date) {
    return [
        "client_id" => $client_id,
        "spec_id" => $spec_id,
        "gu_id" => "",
        "psu_id" => "",
        "year" => date('Y', strtotime($date)),
        "month" => date('m', strtotime($date)),
        "accepted" => "",
    ];
}

?>

<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="table-rep-plugin">
                <div class="table-wrapper">
                    <div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
                        <?php get_cell_color_script(); ?>
                        <table id="tech-companies-1" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="col-1">ID <i class="fas fa-sort" onClick="sortTable('tech-companies-1', 0);"></i></th>
                                    <th>ФИО <i class="fas fa-sort" onClick="sortTable('tech-companies-1', 1);"></i></th>
                                    <th>Остаток часов <i class="fas fa-sort" onClick="sortTable('tech-companies-1', 2);"></i></th>
                                    <th>Отправить часы</th>
                                    <th>Часов потрачено</th>
                                    <th>Зарплата</th>
                                </tr>
                            </thead>
                            <tbody>
                                <? 
                                    // Определяем переменные
                                    $tariff = getSpecialistTariff($_SESSION["id"]);
                                ?>
                                <?php foreach ($spec_clients as $u): ?>
                                    <?php
                                    $params = prepare_form_params($u['id'], $_SESSION["id"], get_period($current_date, $u['id']));
                                    $forms = get_forms_by_params($params) ?: [];
                                    $form_data = $forms[0] ?? [];
                                    $remaining_hours = remaining_hours($u['id'], $year);
                                    $order = get_order_by_client($u['id'], $year);

                                    // Фикс для accepted (спам в логах из-за отсутствия проверок)
                                    if (!empty($form_data["accepted"])) {
                                        $form_data["accepted"] = null;
                                    }

                                    if (!($order['cancel_date'] && $order['cancel_date'] !== '0000-00-00')):
                                        $user_hours = count_hours_by_form($form_data);
                                        $salary = $user_hours * $tariff;
                                        $total_hours += $user_hours;
                                        $total_salary += $salary;
                                        $remaining_hours_total += $remaining_hours;
                                    ?>
                                        <tr>
                                            <td><?= $count_id++; ?></td>
                                            <td id="client_name_<?= $u['id']; ?>"><?= $u['name']; ?></td>
                                            <td><?= $remaining_hours . ' ч.'; ?></td>
                                            <td>
                                                <?php if (!$act && $remaining_hours > 0 && $form_data['accepted'] !== 'true'): ?>
                                                    <a class="btn btn-outline-success" href="sendform.php?editform=<?= $u['id']; ?>" title="Редактировать"><span class="fa fa-plus"></span></a>
                                                <?php elseif ($act && count_hours_by_form($form_data) > 0): ?>
                                                    Акт подписан
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $user_hours . ' ч.'; ?></td>
                                            <td><?= $salary . ' тг.'; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td colspan="4"><b>Итого:</b></td>
                                    <td><b><?= $total_hours; ?> ч.</b></td>
                                    <td><b><?= $total_salary; ?> тг.</b></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Подписать акт -->
                        <?php if (!$act): ?>
                            <a id="get-send-info" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#acceptAction">Подписать акт</a>
                            <div class="modal fade" id="acceptAction" tabindex="-1" aria-labelledby="acceptActionLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Подтвердите действие</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Вы не сможете изменить данные за этот месяц после подписания акта.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                            <button type="button" id="submitAct" class="btn btn-primary">Подписать акт</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Прописываем ширину всех ячеек
$(document).ready(() => {
    const columnWidths = [60, 450, 150, 150, 150, 150];
    columnWidths.forEach((width, index) => {
        $(`tr > th:nth-child(${index + 1})`).css("width", `${width}px`);
        $(`tr > td:nth-child(${index + 1})`).css("width", `${width}px`);
    });

    // Отправка акта
    $('#submitAct').on('click', async () => {
        const params = {
            'accept_act': true,
            'spec_id': <?= $_SESSION["id"]; ?>,
            'year': <?= $year; ?>,
            'month': <?= $month; ?>
        };
        const result = await postFormAsync(params);
        if (result.status === 'success') {
            popup('success', 'Данные успешно отправлены');
            setTimeout(() => location.reload(), 2000);
        }
    });
});
</script>
