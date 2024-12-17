<?php
include('templates/header.php');
?>
<?php 
session_start(); 
// Include config file
require_once "config.php";
require_once "engine.php";
$clients = get_all_from_table("parser_psu");

function get_table_for_year($year, $clients) {
$table = <<<HTML
    <div class="table-rep-plugin">
        <div class="table-wrapper">
            <div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns" style="max-height: 800px;">
                <table id="editableTable" class="table table-bordered table-hover pin_table_psu_forms">
                    <thead>
                        <tr>
                            <th colspan="2" >ID заказа</th>
                            <th colspan="2" >Дата заказа</th>
                            <th colspan="2" >ИИН</th>
                            <th colspan="2" >ФИО</th>
                            <th colspan="1">Телефон</th>
                            <th colspan="2" >Адрес</th>
                            <th colspan="5" >Статус</th>
                            <th colspan="2" >Специалист</th>
                            <th colspan="2" >ПСУ</th>
                            <th colspan="2" >На начало</th>
                            <th colspan="2" >Январь</th>
                            <th colspan="2" >Февраль</th>
                            <th colspan="2" >Март</th>
                            <th colspan="2" >Апрель</th>
                            <th colspan="2" >Май</th>
                            <th colspan="2" >Июнь</th>
                            <th colspan="2" >Июль</th>
                            <th colspan="2" >Август</th>
                            <th colspan="2" >Сентябрь</th>
                            <th colspan="2" >Октябрь</th>
                            <th colspan="2" >Ноябрь</th>
                            <th colspan="2" >Декабрь</th>
                            <th colspan="2" >Отраб.</th>
                            <th colspan="2" >Комментарий</th>
                            <!-- Остальные заголовки -->
                        </tr>
                    </thead>
                    <tbody>
HTML;
                        foreach ($clients as $c) {
                            $spec_counter = 0;
                            $specialists = json_decode($c['specialists'], true);
                            $acts = json_decode($c["acts"], true);
                            $months = [
                                "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"
                            ];

                            // Если специалистов два, выведет две строки на один заказ
                            if (count($specialists) > 1) {
                                foreach ($specialists as $specialist) {
                                    $table .= '<tr>';
                                        $table .= '<td colspan="2" >' . $c['order_id'] . '</td>';
                                        $table .= '<td colspan="2" class="editable" data-field="date" data-id="' . $c['order_id'] . '">' . $c['date'] . '</td>';
                                        $table .= '<td colspan="2" class="editable" data-field="iin" data-id="' . $c['order_id'] . '">' . $c['iin'] . '</td>';
                                        $table .= '<td colspan="2" class="editable" data-field="fio" data-id="' . $c['order_id'] . '">' . $c['fio'] . '</td>';
                                        $table .= '<td colspan="1" class="editable" data-field="phone" data-id="' . $c['order_id'] . '">' . $c['phone'] . '</td>';
                                        $table .= '<td colspan="2" class="editable" data-field="address" data-id="' . $c['order_id'] . '">' . $c['address'] . '</td>';
                                        $table .= '<td colspan="5" class="editable" data-field="status" data-id="' . $c['order_id'] . '">' . $c['status'] . '</td>';
                                        $table .= '<td colspan="2" class="editable" data-field="specialist" data-id="' . $c['order_id'] . '">' . $specialists[$spec_counter] . '</td>';
                                        $table .= '<td colspan="2" class="editable" data-field="psu" data-id="' . $c['order_id'] . '">' . $c['psu'] . '</td>';
                                        $table .= '<td colspan="2" class="editable" data-field="begin" data-id="' . $c['order_id'] . '">' . $c['begin'] . '</td>';
                                        foreach ($months as $mth) {
                                            if (str_contains($specialists[$spec_counter], $acts[$year][$mth][$spec_counter]["specialist_name"])) {
                                                $table .= '<td colspan="2" class="editable" data-field="hours-' . $year . '-' . $mth . '-' . $spec_counter . '" data-id="' . $c['order_id'] . '">' . $acts[$year][$mth][$spec_counter]["hours"] . '</td>';
                                        
                                            }
                                        }
                                    $table .= '</tr>';

                                    $spec_counter += 1;
                                }
                            } else {
                                $table .= '<tr>';
                                    $table .= '<td colspan="2" >' . $c['order_id'] . '</td>';
                                    $table .= '<td colspan="2" class="editable" data-field="date" data-id="' . $c['order_id'] . '">' . $c['date'] . '</td>';
                                    $table .= '<td colspan="2" class="editable" data-field="iin" data-id="' . $c['order_id'] . '">' . $c['iin'] . '</td>';
                                    $table .= '<td colspan="2" class="editable" data-field="fio" data-id="' . $c['order_id'] . '">' . $c['fio'] . '</td>';
                                    $table .= '<td colspan="1" class="editable" data-field="phone" data-id="' . $c['order_id'] . '">' . $c['phone'] . '</td>';
                                    $table .= '<td colspan="2" class="editable" data-field="address" data-id="' . $c['order_id'] . '">' . $c['address'] . '</td>';
                                    $table .= '<td colspan="5" class="editable" data-field="status" data-id="' . $c['order_id'] . '">' . $c['status'] . '</td>';
                                    $table .= '<td colspan="2" class="editable" data-field="specialist" data-id="' . $c['order_id'] . '">' . $specialists[$spec_counter] . '</td>';
                                    $table .= '<td colspan="2" class="editable" data-field="psu" data-id="' . $c['order_id'] . '">' . $c['psu'] . '</td>';
                                    $table .= '<td colspan="2" class="editable" data-field="begin" data-id="' . $c['order_id'] . '">' . $c['begin'] . '</td>';
                                    foreach ($months as $mth) {
                                        if (str_contains($specialists[$spec_counter], $acts[$year][$mth][$spec_counter]["specialist_name"])) {
                                            $table .= '<td colspan="2" class="editable" data-field="hours-' . $year . '-' . $mth . '-' . $spec_counter . '" data-id="' . $c['order_id'] . '">' . $acts[$year][$mth][$spec_counter]["hours"] . '</td>';
                                        }
                                    }
                                    $table .= '<td colspan="2" class="editable" data-field="begin" data-id="' . $c['order_id'] . '">' . $c['begin'] . '</td>';
                                $table .= '</tr>';
                            }
                            
                        }
                        $table .= <<<HTML
                    </tbody>
                </table>
            </div>
        </div>
    </div>
HTML;
return $table;
}

//Сортировка по алфавиту
// $clients = sort_by_name($users);
if ($_SESSION['group']  == 1) {
?>
    <body data-sidebar="dark">

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

        <!-- Begin page -->
        <div id="layout-wrapper">

            <?php
			include('templates/top.php');
			include('templates/sidebar.php');
			?>

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Данные с портала (парсинг)</h4>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">   
                                        <div class="mt-3">
                                            <!-- Вкладки навигации -->
                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                <?php foreach (range(date('Y'), 2021, -1) as $tab_year) { ?>
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link <?php if ($tab_year == date('Y')) { echo 'active'; }?>" 
                                                        id="year-<?php echo $tab_year;?>-tab" 
                                                        data-bs-toggle="tab" 
                                                        href="#year-<?php echo $tab_year;?>" 
                                                        role="tab" 
                                                        aria-controls="year-<?php echo $tab_year;?>" 
                                                        aria-selected="<?php if ($tab_year == date('Y')) { echo 'true'; } else { echo 'false'; }?>">
                                                            <?php echo $tab_year;?>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>

                                            <!-- Панели вкладок -->
                                            <div class="tab-content" id="myTabContent">
                                                <?php foreach (range(date('Y'), 2021, -1) as $tab_year) { ?>
                                                    <div class="tab-pane fade <?php if ($tab_year == date('Y')) { echo 'show active'; }?>" id="year-<?php echo $tab_year;?>" role="tabpanel" aria-labelledby="year-<?php echo $tab_year;?>-tab">
                                                        <?php echo get_table_for_year($tab_year, $clients); ?>
                                                    </div>
                                                <?php } ?>
                                                <script>
                                                $(document).ready(function(){
                                                    $(".editable").dblclick(function(){
                                                        var originalContent = $(this).text();
                                                        $(this).addClass("editMode");
                                                        $(this).html("<input type='text' style='width: 300px; height: 60px' value='" + originalContent + "' />");
                                                    });

                                                    $(".editable").on("focusout", "input", function(){
                                                        var newContent = $(this).val();
                                                        var field = $(this).parent().data("field");
                                                        var id = $(this).parent().data("id");
                                                        $(this).parent().removeClass("editMode");
                                                        $(this).parent().text(newContent);

                                                        // AJAX запрос для сохранения изменений
                                                        $.ajax({
                                                            url: "api/parser.php", // PHP скрипт для обновления данных
                                                            type: "POST",
                                                            data: {
                                                                type: 'site_data',
                                                                id: id,
                                                                field: field,
                                                                value: newContent
                                                            },
                                                            success: function(response){
                                                                // Обработка ответа от сервера
                                                            }
                                                        });
                                                    });
                                                });
                                                </script>
                                            </div>

                                            <script>
                                                document.addEventListener("DOMContentLoaded", function() {
                                                    var triggerTabList = [].slice.call(document.querySelectorAll('#myTab a'));
                                                    triggerTabList.forEach(function (triggerEl) {
                                                        var tabTrigger = new bootstrap.Tab(triggerEl);

                                                        triggerEl.addEventListener('click', function (e) {
                                                            e.preventDefault();
                                                            tabTrigger.show();
                                                        });
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div>
                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->
                
            </div>
            <!-- end main content-->
        </div>
        <!-- END layout-wrapper -->
        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>
        <script src="./static/js/pinTable.js"></script>

<?php
include('templates/footer.html');
}
?>