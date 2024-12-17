<?php
include('templates/header.php');
include('engine.php');


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
                <div id="loading-container"></div>
                <script>
                    document.addEventListener('DOMContentLoaded', function(){
                        let modal = document.querySelector('#loading-container');
                        // создаем объект FormData
                        let formData = new FormData();
                        formData.append('loading', '1');

                        // отправляем POST запрос
                        fetch('modal.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(html => {
                            // добавляем полученный HTML-код на страницу
                            modal.innerHTML = html;
                        });
                        
                    });
                </script>
                <div class="page-content">
                    <div class="container-fluid">
						
						<?php
						//Вывод для администратора
						if ($_SESSION['group'] == 1) {
                            include('./templates/main/main_admin.php');
                            include('./templates/dashboards/dash_admin.php');
						} ?>
						<?php
						//Вывод для специалиста
						if ($_SESSION['group'] == 2) {
                            include('./templates/main/main_specialist.php');
						} ?>
						<?php
						//Вывод для модератора
						if ($_SESSION['group'] == 3) {
                            include('./templates/main/main_moder.php');
                            include('./templates/dashboards/dash_moder.php');
						?>
                       <div class="row">

                        </div>
                        <div class="row">
                            <div class="container">
                                
                            </div>
                        </div>
						<?php } ?>
						<?php
						//Вывод для ГУ
						if ($_SESSION['group'][0] == '%') {
                            // Таблица ГУ 
                            $gu = get_gu(substr($_SESSION['group'], 1));
						?>
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Профиль ГУ <?php echo $gu['name'] ?></h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                       <div class="row">

                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card d-flex">
                                    <div class="card-body row">
                                        <table id="tech-companies-1" class="table table-bordered" style="display:flex;flex-direction:column;">
                                            <thead>
                                                <tr style="display:flex;">
                                                    <th class="col-1">Месяц</th>
                                                    <th class="col-2">Часы</th>
                                                    <th class="col-1">Сумма</th>
                                                    <th class="col-1">Статус оплаты</th>
                                                    <th class="col-8">Комментарий к статусу оплаты</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (range(1,12) as $month) {?>
                                                    <tr style="display:flex;">
                                                        <td class="col-1"><?php echo get_month_name($month); ?></td>
                                                        <td class="col-2"><?php $hours = count_hours_by_gu_for_month($gu['id'], date('Y'), $month); echo $hours . ' ч.'; ?></td>
                                                        <td class="col-1"><?php echo money($hours * get_setting('gu_salary_' . date('Y'))) . ' тг.'; ?></td>
                                                        <td class="col-1"><?php $transaction = getTransaction(date('Y'), $month, $gu['id']); if ($transaction['transaction_status'] == 0) {echo "Не оплачен";} else {echo "Оплачен";} ?></td>
                                                        <td class="col-8"><?php echo $transaction['transaction_comment']; ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
									</div>
                                </div>
                            </div> <!-- end col -->
                        </div>
						<?php } ?>                        
                        
                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->
                
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->
        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>
<?php
include('templates/footer.html');
?>