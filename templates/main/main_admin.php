<?php 
// Настройки
if (isset($_POST['start_day'])) {
	set_setting('start_day',intval($_POST['start_day']));
}
if (isset($_POST['salary'])) {
	set_setting('salary_' . date('Y'),floatval($_POST['salary']));
}
if (isset($_POST['gu_salary'])) {
	set_setting('gu_salary_' . date('Y'),floatval($_POST['gu_salary']));
}
if (isset($_POST['full_tariff'])) {
	set_setting('full_tariff_' . date('Y'),floatval($_POST['full_tariff']));
}
$start_day = get_setting('start_day');
$salary = get_setting('salary_' . date('Y'));
$gu_salary = get_setting('gu_salary_' . date('Y'));
$full_tariff = get_setting('full_tariff_' . date('Y'));
?>
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Панель управления</h4>
        </div>
    </div>
</div>
<!-- end page title -->
<div class="row">

</div>
<div class="row">
    <div class="col">
        <div class="card d-flex">
            <div class="card-body row">
                <div class="col">
                    <i class="h5 fa fa-cog"></i><span class="h5"> Настройки</span>
                    <hr>
                    <div class="d-flex elements">
                        <!-- Дата начала периода -->
                        <form id="startDate" method="post" class="col-sm">
                            <label for="start_day" class="form-label">Дата начала периода</label>
                            <select class="form-select" name="start_day" id="start_day">
                                <?php
                                $i = 1;
                                while ($i <= 28) {?>
                                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                <?php
                                $i += 1;
                                }
                                ?>
                            </select>
                            <p></p>
                            <button class="btn btn-primary zoom" type="submit"><span class="fa fa-save" style="color:white"></span> Сохранить</button>
                        </form>
                        <script>
                        document.addEventListener("DOMContentLoaded", function(){
                            $('#start_day').val("<?php echo $start_day; ?>").change();
                        });
                        </script>

                        <!-- Тариф на год -->
                        <form id="salarySetting" method="post" class="col-sm mx-2">
                            <label for="salary" class="form-label">Тариф на <?php echo date('Y'); ?> год</label>
                            <input class="form-control" name="salary" id="salaryInput" type="text">
                            <p></p>
                            <button class="btn btn-primary zoom" type="submit"><span class="fa fa-save" style="color:white"></span> Сохранить</button>
                        </form>
                        <script>
                        document.addEventListener("DOMContentLoaded", function(){
                            $('#salaryInput').val("<?php echo $salary; ?>").change();
                        });
                        </script>

                        <!-- Тариф на год GU -->
                        <form id="gu_salarySetting" method="post" class="col-sm mx-2">
                            <label for="gu_salary" class="form-label">Тариф для ГУ на <?php echo date('Y'); ?> год</label>
                            <input class="form-control" name="gu_salary" id="gu_salaryInput" type="text">
                            <p></p>
                            <button class="btn btn-primary zoom" type="submit"><span class="fa fa-save" style="color:white"></span> Сохранить</button>
                        </form>
                        <script>
                        document.addEventListener("DOMContentLoaded", function(){
                            $('#gu_salaryInput').val("<?php echo $gu_salary; ?>").change();
                        });
                        </script>

                        <!-- Тариф на год полный -->
                        <form id="full_tariffSetting" method="post" class="col-sm mx-2">
                            <label for="full_tariff" class="form-label">Полный тариф на <?php echo date('Y'); ?> год</label>
                            <input class="form-control" name="full_tariff" id="full_tariffInput" type="text">
                            <p></p>
                            <button class="btn btn-primary zoom" type="submit"><span class="fa fa-save" style="color:white"></span> Сохранить</button>
                        </form>
                        <script>
                        document.addEventListener("DOMContentLoaded", function(){
                            $('#full_tariffInput').val("<?php echo $full_tariff; ?>").change();
                        });
                        </script>

                        <div class="col d-flex mx-2">
                            <!-- Кнопка для вызова модального окна -->
                            <button type="button" class="my-5 p-3 btn btn-primary" data-bs-toggle="modal" data-bs-target="#specialTariffsModal">
                                <span class="fa fa-user" style="color:white"></span> Специальные тарифы
                            </button>

                            <!-- Модальное окно -->
                            <div class="modal " id="specialTariffsModal" tabindex="-1" aria-labelledby="specialTariffsModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="specialTariffsModalLabel">Таблица для редактирования</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Таблица -->
                                    <table class="table" id="editableTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">ФИО</th>
                                            <th scope="col">Тариф</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                        $specialists = get_all_specialists();
                                        $spec_table = "";
                                        foreach ($specialists as $spec) {
                                            $spec_tariff = getSpecialistTariff($spec['id']);
                                            $spec_table .= <<<HTML
                                            <tr>
                                                <td>{$spec['name']}</td>
                                                <td contenteditable="false" data-id="{$spec['id']}">{$spec_tariff}</td>
                                            </tr>
HTML;
                                        } 
                                        echo $spec_table;
                                        ?>
                                    </tbody>
                                    </table>
                                </div>
                                </div>
                            </div>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const table = document.getElementById('editableTable');

                                    table.addEventListener('dblclick', function (e) {
                                        const target = e.target;
                                        const column = target.cellIndex; // Получаем индекс колонки

                                        if (column === 1) { // Индексы начинаются с 0, поэтому вторая колонка имеет индекс 1
                                            target.setAttribute('contenteditable', true);
                                            target.focus(); // Ставим фокус на элемент для немедленного редактирования

                                            target.addEventListener('blur', function () {
                                                target.setAttribute('contenteditable', false); // Убираем режим редактирования при потере фокуса

                                                formData = {
                                                    "spec_id": target.dataset.id,
                                                    "set_spec_tariff": target.textContent
                                                }

                                                postFormAsync(formData).then(() => {
                                                    popup('success', 'Сохранено')
                                                })

                                                popup('success', 'Сохранено')
                                            }, { once: true }); // Обработчик события выполнится только один раз
                                        }
                                    });
                                });

                            </script>
                            <!-- END Модальное окно -->
                        
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>