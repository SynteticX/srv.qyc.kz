<?php
require_once "config.php";
require "engine.php";
$users = get_all_from_table('users');
// Define variables and initialize with empty values
$client_id = $_GET['editform'];
$client_order = get_order_by_client($client_id,date('Y'));
$client = get_client($_GET['editform']);
$order_id = $client_order['id'];
$spec_id = $_SESSION['id'];
$day = "";
$order_id_err = $client_id_err = $spec_id_err = $day_err = $month_err = "";
$date = date("Y-m-d");
$startPeriod = get_period(date('Y-m-d'), $client_id);
$startDay = date_format(date_create($startPeriod),'d');
$month = date_format(date_create($startPeriod),'m');
$year = date_format(date_create($startPeriod),'Y');

// Остаток часов
$remaining_hours = remaining_hours($_GET['editform'], $year);

// Проверяем, есть ли уже форма на этот месяц
$forms = get_forms_by_order($client_order['id']);
$form = 0;
if ($forms != 0) {
    foreach ($forms as $f) {
        if ($f['spec_id'] == $_SESSION['id'] && $f['month'] == $month) {
            $form = $f;
            break;
        }
    }
}


// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate spec_id
    // $input_spec_id = trim($_POST["spec_id"]);
    // if(empty($input_spec_id)){
        // $spec_id_err = "Please enter a spec_id.";
    // } else{
        // $spec_id = $input_spec_id;
    // }
    // Validate hours
    $hours = null;
    $input_hours = trim($_POST["hours"]);
    if(empty($input_hours)){
        $hours_err = "Please enter a hours.";
    } else {
        // Проверка на превышение остатка часов у специалиста
        if ($input_hours > $remaining_hours) {
            alert('Вы превысили лимит часов', 'https://srv.qyc.kz/sendform.php?editform='.$_GET['editform']);
        } else {
            $hours = $input_hours;
        }
    }
    // Validate online
    $input_online = trim($_POST["online"]);
    if(empty($input_online)){
        $online_err = "Please enter a online.";
    } else{
        $online = $input_online;
    }
    // Validate day
    $input_day = trim($_POST["date"]);
    if(empty($input_day)){
        $day_err = "Please enter a day.";
    } else{
        $day = date_format(date_create($input_day), 'd');
        $month = date_format(date_create($input_day), 'm');
    }
    if (is_order_active($order_id) == 2) {
        alert('Заказ на этого клиента был отменен!', 'forms.php');
    }
    // if ($_POST['create_new_form'] == 1) {
        // echo $order_id;
        // //create_form($spec_id, $client_id, $order_id, $month);
    // }
    //Если форма на месяц не была создана
    if ($form == 0 and is_spec_day_limit_reached($spec_id, $input_day, $client_id, $hours) == 0) {
        $sql = "INSERT INTO forms (order_id, client_id, spec_id, `".intval($day)."`, `online_".intval($day)."`, month) VALUES (?, ?, ?, ?, ?, ?)";
        echo $sql;
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_order_id, $param_client_id, $param_spec_id, $param_day, $param_online, $param_month);
            
            // Set parameters
            $param_order_id = $order_id;
            $param_client_id = $client_id;
            $param_spec_id = $spec_id;
            $param_day = $hours;
            $param_online = $online;
            $param_month = $month;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: sendform.php?editform=".$client_id);
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    } else if (is_spec_day_limit_reached($spec_id, $input_day, $client_id, $hours) == 0) {
        //Если форма на месяц была создана
        $sql = "UPDATE forms SET `".intval($day)."`=?, `online_".intval($day)."`=? WHERE id = ".intval($form['id']);
        echo $sql;
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_day, $param_online);
            // Set parameters
            $param_day = $hours;
            $param_online = $online;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: sendform.php?editform=".$client_id);
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Close connection
    mysqli_close($link);
}
?>
<?php
include('templates/header.php');
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
						<?php if ($_SESSION['group'] == 2) {?>
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Отчет за <?php echo date_format(date_create($startPeriod), 'm.Y');?> на клиента <?php echo $client['name'];?></h4>
                                </div>
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h5 class="mb-sm-0">Остаток часов: <?php echo $remaining_hours.' ч.'; ?></h5>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">              
										<div class="table-rep-plugin">
											<div class="table-wrapper">
												<div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns"> 
												<?php 
													// $spec_clients = get_spec_clients($_SESSION["id"]);
													$days = cal_days_in_month(CAL_GREGORIAN, $month, date('Y'));
													// Если форма на текущий месяц, ограничивает доступ к заполнению только до текущего числа
													if (intval($month) == intval(date('m'))) {
														// Фикс на декабрь (разрешает заполнять до 31 числа)
														if (intval($month) ==  12) {
															$days = 31;
														} else {
															$days = intval(date('d'));
														}
													}
													$vacations = get_vacations($month, date('Y'));
													//Проверка на наличие формы на этот месяц
													if (gettype($form) == 'array') {
														$f = $form;
												?>
													<div><span class="bg-qyc" style="width:10px;height:10px;"> 1 </span><span> - Выходные дни. Рекомендуется сдавать не больше 4-х часов</span></div>
													<div class="d-flex">
														<div class="container col-6">
															<table id="tech-companies-1" class="table table-bordered">
																<thead>
																	<tr class="">
																		<th class="p-2">День</th>
																		<th class="p-2">Часы</th>
																		<th class="p-2">Формат услуги</th>
																		<th class="p-2">Заполнено за день</th>
																		<th class="p-2"></th>
																	</tr>
																</thead>
																<tbody>
																	<?php foreach (range($startDay,$days) as $d) {?>
																		<tr class="<?php if (is_vacation($vacations, $d) == true) {echo 'bg-qyc';} ?>">
																		<form method="post">
																			<td class="p-2"><b><?php echo $d; ?></b></td>
																			<?php if (floatval($f[$d]) == null && $f['accepted'] != 'true') {?>
																			<td class="p-2">
																				<a class="btn btn-outline-primary ms-3 p-2" data-bs-toggle='modal' data-bs-target='#modalAdd<?php echo $d;?>' title="" data-toggle="tooltip"><span class="fa fa-plus"></span> Отправить часы</a>
																			</td>
																			<?php } else {?>
																			<td class="p-2">
																				<?php echo ($f[$d]) ? $f[$d] : '0';echo ' ч. '; ?>
																				<?php if ($f['accepted'] != 'true') { ?>
																					<a class="btn btn-outline-primary ms-3 p-1" data-bs-toggle='modal' data-bs-target='#modalAdd<?php echo $d;?>' title="" data-toggle="tooltip"><span class="fa fa-pen"></span></a>
																				<?php } ?>
																			</td>
																			<?php } ?>
																			<td class="p-2"><?php if (get_online_status($f['id'], $d) == 1 and floatval($f[$d]) > 0){echo 'Онлайн';} else if (get_online_status($f['id'], $d) == 2 and floatval($f[$d]) > 0) {echo 'Очно';}?></td>
																			<td class="p-2"><?php echo hours_per_day($_SESSION["id"], date_format(date_create($d.'-'.$month.'-'.$year),'Y-m-d')); ?> ч.</td>
																			<td class="p-2"></td>
																		</form>
																		</tr>
																		
																		<?php } ?>
																</tbody>
															</table>
														</div>
														<div class="col-6"></div>
													</div>
													<?php
														} else {
														//Создает новую форму, если нет созданной ?>
														<div>
															<center>
																<h3>Отчет на этот месяц еще не был создан. Создать его?</h3>
																<a class="btn btn-primary" data-bs-toggle='modal' data-bs-target='#addNewForm' class="ms-3" title="" data-toggle="tooltip">Создать заказ</a>
															</center>
														</div>
													<?php } ?>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div>
						<?php } ?>
                    </div> 
					<!-- container-fluid -->
					<!-- Модальные окна -->
					<?php foreach (range($startDay,$days) as $d) {?>
					<div class="modal fade" id="modalAdd<?php echo $d;?>" tabindex="-1" aria-labelledby="modalAddLabel<?php echo $d;?>" aria-hidden="true">
					  <div class="modal-dialog">
						<div class="modal-content">
						  <div class="modal-header">
							<h1 class="modal-title fs-5" id="modalAddLabel<?php echo $d;?>">Отправить часы</h1>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						  </div>
						  <div class="modal-body">
							<form style="cursor: pointer;" id="addForm<?php echo $d;?>" method="post">
								<fieldset>
									<div class="mb-3">
									  <label for="fio" class="form-label">ФИО клиента</label>
									  <input disabled value="<?php echo $client['name'];?>" type="text" id="fio" class="form-control" placeholder="">
									  <input hidden type="text" id="client_id" name="client_id" value="<?php echo $client['id'];?>">
									</div>
									<div class="mb-3">
									  <label for="date" class="form-label">Дата </label>
									  <input disabled value="<?php echo date_format(date_create($d.'-'.$month.'-'.$year),'Y-m-d');?>" type="date" class="form-control" min="<?php echo date_format(date_create($startPeriod), 'Y-m-01'); ?>" max="<?php echo date_format(date_create($startPeriod), 'Y-m-'.$days); ?>" placeholder="">
									  <input hidden value="<?php echo date_format(date_create($d.'-'.$month.'-'.$year),'Y-m-d');?>" type="date" id="date" name="date" class="form-control" min="<?php echo date_format(date_create($startPeriod), 'Y-m-01'); ?>" max="<?php echo date_format(date_create($startPeriod), 'Y-m-'.$days); ?>" placeholder="">
									  
									</div>
									  <div class="mb-3">
										<label for="hours" class="form-label">Количество часов</label>
										<select class="form-select" name="hours" id="hours">
											<?php
											$i = 0;
											while ($i <= 8) {?>
												<option value="<?php echo $i;?>"><?php echo $i;?></option>
											<?php
											$i = $i + 0.5;
											}
											?>
										</select>
									  </div>
									  <div class="mb-3">
										<div class="form-check">
										  <input class="form-check-input" value="1" type="radio" name="online" id="online">
										  <label class="form-check-label" for="online">
											Онлайн
										  </label>
										</div>
										<div class="form-check">
										  <input class="form-check-input" value="2" type="radio" name="online" id="online" checked>
										  <label class="form-check-label" for="online">
											Очно
										  </label>
										</div>
									  </div>
								  </fieldset>
								  <button type="submit" class="btn btn-primary" form="addForm<?php echo $d;?>">Отправить</button>
							</form>
						  </div>
						</div>
					  </div>
					</div>
					<?php } ?>
					<!-- Модальное окно для создания новой формы -->
					<div class="modal fade" id="addNewForm" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
					  <div class="modal-dialog">
						<div class="modal-content">
						  <div class="modal-header">
							<h1 class="modal-title fs-5" id="modalAddLabel">Создание нового отчета</h1>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						  </div>
						  <div class="modal-body">
							<form style="cursor: pointer;" id="addForm" method="post">
								<fieldset>
									<div class="mb-3">
									  <label for="fio" class="form-label">ФИО клиента</label>
									  <input disabled value="<?php echo $client['name'];?>" type="text" id="fio" class="form-control" placeholder="">
									  <input hidden type="text" id="client_id" name="client_id" value="<?php echo $client['id'];?>">
									  <!-- Отправка переменной для создания новой формы -->
									  <input hidden type="text" name="create_new_form" value="1">
									</div>
									<div class="mb-3">
									  <label for="date" class="form-label">Месяц </label>
									  <input disabled value="<?php echo get_month_name(intval(date_format(date_create($startPeriod),'m')));?>" type="text" class="form-control">
									  <input hidden value="<?php echo date_format(date_create('01-'.$month.'-'.date('Y')),'Y-m-d');?>" type="date" id="date" name="date" class="form-control" min="<?php echo date_format(date_create($startPeriod), 'Y-m-01'); ?>" max="<?php echo date_format(date_create($startPeriod), 'Y-m-'.$days); ?>" placeholder="">
									</div>
									  <div class="mb-3">
										<input hidden type="text" id="hours" name="hours" value="">
									  </div>
									  <div class="mb-3">
										<div class="form-check">
										  <input class="form-check-input" value="1" type="radio" name="online" id="online" checked hidden>
										</div>
									  </div>
								  </fieldset>
								  <button type="submit" class="btn btn-primary" form="addForm">Отправить</button>
							</form>
						  </div>
						</div>
					  </div>
					</div>
					<!-- Конец модального окно для создания новой формы -->
					<!-- Модальные окна -->
					<!-- Float circle -->
					<style>
					.circle-button {
						position: fixed;
						right: 0px;
						bottom: 0px;
						transform: translate(-20px, -20px);
						background: #098e79;
						border-radius: 50%;
						--width: auto;
						min-width: 55px;
						min-height: 55px;
						width: auto;
						height: auto;
						color: #fff;
						text-align: center;
						line-height: 53px;
						font-size: 35px;
						z-index: 9999;
					}
					</style>
					<div class="circle-button">
						<span style="padding:10px">
						<?php
						//Считаем часы за клиента в кружке
						$hsum = 0;
						foreach (range($startDay,$days) as $d) {
							if ($f[$d] != null) {
								$hsum += $f[$d];
							}
						}
						echo $hsum;
						?>
						</span>
					</div>
					<!-- End float circle -->
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