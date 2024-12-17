<?php
include('templates/header.php');
?>
<?php 
session_start(); 
// Include config file
require_once "config.php";
require_once "engine.php";
$orders_temp = get_all_from_table('orders');
if ($_GET['active']) {
    switch ($_GET['active']) {
        case 1:
            $orders = array();
            foreach ($orders_temp as $order) {
                if ($order['order_status'] == 1) {
                    $orders[] = $order;
                }
            }
            break;
        case 2:
            $orders = array();
            foreach ($orders_temp as $order) {
                if ($order['order_status'] == 2) {
                    $orders[] = $order;
                }
            }
            break;
    }
} else {
    $orders = $orders_temp;
}
// Prepare a select statement
$sql = "SELECT * FROM users";
$users = array();
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_array($result)){
			array_push($users, $row);
        }
        // Free result set
        mysqli_free_result($result);
    } else{
        echo "No records matching your query were found.";
    }
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
// Prepare a select statement
$sql = "SELECT * FROM clients";
$clients = array();
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_array($result)){
			array_push($clients, $row);
        }
        // Free result set
        mysqli_free_result($result);
    } else{
        echo "No records matching your query were found.";
    }
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
// Prepare a select statement
$sql = "SELECT * FROM gu_ids";
$gu_ids = array();
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_array($result)){
			array_push($gu_ids, $row);
        }
        // Free result set
        mysqli_free_result($result);
    } else{
        echo "No records matching your query were found.";
    }
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
// Prepare a select statement
$sql = "SELECT * FROM psu_ids";
$psu_ids = array();
if($result = mysqli_query($link, $sql)){
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_array($result)){
			array_push($psu_ids, $row);
        }
        // Free result set
        mysqli_free_result($result);
    } else{
        echo "No records matching your query were found.";
    }
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}



// Close connection
mysqli_close($link);?>
<?php 
// Вывод для админа и модератора
if ($_SESSION['group']  == 1 || $_SESSION['group']  == 3) {?>
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
                                    <h4 class="mb-sm-0">Заказы</h4>
									<a class="btn btn-outline-primary" href="newform.php?neworder=1" title="Создать заказ" data-toggle="tooltip"><span class="fa fa-plus"> Создать заказ</span></a>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        <!-- start search -->
                       <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="w-100 row">
                                            <form class="col-4 hstack gap-3 d-flex" id="query">
												<!-- <div class="col-2">
													<span>Специалист</span>
													<select class="form-select" name="spec_id" id="spec_id">
														<option selected value="">Все</option>
														<?php
														// foreach ($users as $u) {
														// 	if ($u['usergroup'] == 2) {echo '<option value="'.$u["id"].'">'. $u["name"] .'</option>';}
														// }
														?>
													</select>
												</div> -->
												<div class="col-12">
													<span>Статус заказа</span>
													<select class="form-select" name="order_status" id="order_status">
														<option selected value="all">Все</option>
														<option value="active">Активные</option>
														<option value="canceled">Отмененные</option>
													</select>
												</div>
												
											</form>
                                            <!-- <button class="col-1 btn btn-primary zoom" onClick="(document.querySelector('#order_status').value == 'active') ? window.location.href = 'orders.php?active=1' : window.location.href = 'orders.php'"><i class="fas fa-search"></i> Найти</button> -->
                                            <button class="col-1 btn btn-primary zoom" onClick="switch (document.querySelector('#order_status').value) {case 'all': window.location.href = 'orders.php'; break; case 'active': window.location.href = 'orders.php?active=1'; break; case 'canceled': window.location.href = 'orders.php?active=2'; break;}"><i class="fas fa-search"></i> Найти</button>
											<script language="JavaScript">
												$(document).on('DOMContentLoaded', () => {
                                                    order_status = <?php echo ($_GET['active']) ? $_GET['active'] : 'undefined';?>;
                                                    if (order_status) {
                                                        switch (order_status) {
                                                            case 1:
                                                                $('#order_status').val('active');
                                                                break;
                                                            case 2:
                                                                $('#order_status').val('canceled');
                                                                break;
                                                        }
                                                    }
                                                });
											</script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<!-- end search -->
                        <!--<div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="w-100">
                                            <div class="hstack gap-3 d-flex">
                                                <input class="form-control me-auto" type="text" placeholder="Search by topic..." >
                                                <input class="form-control me-auto" type="text" placeholder="Search by students..." >
                                                <button type="button" class="btn btn-secondary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>-->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">              
                                        <div class="table-rep-plugin">
                                            <div class="table-wrapper">
                                            	<div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
                                                <table id="tech-companies-1" class="table table-striped table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <!-- <th>#  <i class="fas fa-sort" onClick="sortTable('tech-companies-1', 0);"></i></th> -->
                                                        <th>Номер заказа <i class="fas fa-sort" onClick="sortTable('tech-companies-1', 0);"></i></th>
                                                        <th>Дата заказа</th>
                                                        <th>ФИО клиента <i class="fas fa-sort" onClick="sortTable('tech-companies-1', 2);"></i></th>
                                                        <th>Специалисты <i class="fas fa-sort" onClick="sortTable('tech-companies-1', 3);"></i></th>
                                                        <th>Адрес</th>
                                                        <th>Статус заказа</th>
                                                        <th>Количество часов</th>
                                                        <th>Дата отмены</th>
                                                        <th>Причина отмены</th>
                                                        <th>ГУ  <i class="fas fa-sort" onClick="sortTable('tech-companies-1', 9);"></i></th>
                                                        <th>Комментарии  <i class="fas fa-sort" onClick="sortTable('tech-companies-1', 10);"></i></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
													<?php foreach ($orders as $f) {?>
                                                        <tr>
                                                            <!-- <td><?php echo $f['id']; ?></td> -->
                                                            <td><?php echo $f['order_num']; ?></td>
                                                            <td><?php echo $f['order_date']; ?></td>
                                                            <td><?php foreach ($clients as $c) { if ($c['id'] == $f['client_id']) {echo $c['name']; break;}} ?></td>
															<?php
															if (strpos($f['specialists'], ' ') !== false) {
																$specs = explode(' ',$f['specialists']);
															?>
																<td><?php foreach($specs as $sp){foreach ($users as $u) { if ($u['id'] == $sp) {echo $u['name'].'<br>'; break;}}} ?></td>
															<?php
															} else {
															?>
                                                            <td><?php foreach ($users as $u) { if ($u['id'] == $f['specialists']) {echo $u['name'];}} ?></td>
															<?php } ?>
                                                            <td><?php echo $f['address']; ?></td>
                                                            <td><?php if ($f['order_status'] == 1) {echo 'Активен';} else if ($f['order_status'] == 2) {echo 'Отменен';} ?></td>
                                                            <td><?php echo $f['begin_hours']; ?></td>
                                                            <td><?php if ($f['cancel_date'] == '0000-00-00') {echo '';} else { echo $f['cancel_date']; } ?></td>
                                                            <td><?php echo $f['cancel_reason']; ?></td>
															<td><?php foreach ($gu_ids as $u) { if ($u['id'] == $f['gu_id']) {echo $u['name']; break;}} ?></td>
															<td data-order-id="<?php echo $f['id']; ?>"></td>
															<td>
																<a class="btn-outline-primary" title="Посмотреть отмененные заказы" data-toggle="tooltip" onclick="toggleCanceled()"><span class="fa fa-eye"></span></a>
																<a href="sendform.php?editorder=<?php echo $f['id'];?>" class="ms-3" title="Редактировать" data-toggle="tooltip"><span class="fa fa-pen"></span></i></a>
																<a href="deleteform.php?editorder=<?php echo $f['id'];?>" class="ms-3" title="Удалить" data-toggle="tooltip"><span class="fa fa-trash"></span></i></a>
															</td>
                                                        </tr>
													<?php } ?>
                                                    </tbody>
                                                </table>
                                                <script>
                                                    // Переключаем комменты на заказах
                                                    $(document).ready(async () => {
                                                        // Получаем все отчеты
                                                        let forms = await getFormAsync({'getTable': 'forms'}).then(data => JSON.parse(data));
                                                        let orders = await getFormAsync({'getTable': 'orders'}).then(data => JSON.parse(data));

                                                        for (let order of orders) {
                                                            // Собираем все комментарии к заказу по отчетам
                                                            let comments = [];
                                                            for (let form of forms) {
                                                                if (form['order_id'] == order['id'] && form['comment']) {
                                                                    comments.push({
                                                                        year: order['order_date'].split("-")[0],
                                                                        month: form['month'],
                                                                        comment: form['comment']
                                                                    })
                                                                }
                                                            }
                                                            // Генерируем кнопку и комментарии для HTML
                                                            let htmlData = '';
                                                            if (comments.length > 0) {
                                                                let commentData = '';
                                                                for (let comment of comments) {
                                                                    comment.comment = comment.comment.replace(/\{[^}]*\}/g, "");
                                                                    commentData += `${comment.year + '|' + comment.month + '|' + comment.comment}<hr>`;
                                                                }
                                                                htmlData = `
                                                                    <button class="btn btn-secondary" onclick="this.parentNode.innerHTML = '${commentData}'"><i class="fa fa-eye"></i> ${comments.length}</button>
                                                                `;
                                                            }
                                                            // Выводим на сайт
                                                            $(`[data-order-id="${order['id']}"]`).append(htmlData)
                                                            
                                                        }
                                                        $("#comment_<?php echo $f['id']; ?>")
                                                    });
                                                </script>
                                                <script>
                                                    let isToggleCanceled = false;
                                                    function toggleCanceled() {
                                                        if (isToggleCanceled) {
                                                            $('tr > th:nth-child(8)').prop('hidden', false);
                                                            $('tr > th:nth-child(9)').prop('hidden', false);
                                                            $('tr > td:nth-child(8)').prop('hidden', false);
                                                            $('tr > td:nth-child(9)').prop('hidden', false);
                                                            isToggleCanceled = false;
                                                        } else {
                                                            $('tr > th:nth-child(8)').prop('hidden', true);
                                                            $('tr > th:nth-child(9)').prop('hidden', true);
                                                            $('tr > td:nth-child(8)').prop('hidden', true);
                                                            $('tr > td:nth-child(9)').prop('hidden', true);
                                                            isToggleCanceled = true;
                                                        }
                                                    }
                                                    $(document).ready(() => {toggleCanceled()})
                                                </script>
                                            </div></div>
        
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

<?php
include('templates/footer.html');
}
?>