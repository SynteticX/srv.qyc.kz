<!-- start page title -->
<div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Отчеты специалистов ГУ <?php $gu['name'] ?></h4>
									
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
						<!-- start search -->
                       <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="w-100">
                                            <form class="hstack gap-3 d-flex" action="forms.php" id="query" method="get">
												<div class="col-1">
													<span>Год</span>
													<select class="form-select" name="year" id="year">
														<option selected value="">Все</option>
														<?php
														foreach (range(2024,date('Y')) as $y) {
															echo '<option value="'.$y.'">'. $y .'</option>';
														}
														?>
													</select>
												</div>
												<div class="col-1">
													<span>Месяц</span>
													<select class="form-select" name="month" id="month">
														<option selected value="">Все</option>
														<?php
														foreach (range(1,12) as $m) {
															echo '<option value="'.$m.'">'. get_month_name($m) .'</option>';
														}
														?>
													</select>
												</div>
												<button type="submit" form="query" class="btn btn-primary"><i class="fas fa-search"></i> Найти</button>
												<button type="submit" form="query" class="btn btn-secondary" onClick="untoggle()"><i class="fas fa-filter"></i> Сбросить фильтр</button>
												<a class="btn btn-secondary" id="excel"><i class="fas fa-table"></i> Скачать в Excel</a>
											</form>
											<script language="JavaScript">
												//Задаю параметры с проверкой на undefined
												let params = {
													"client_id" : <?php echo ($_GET["client_id"]) ? $_GET["client_id"] : 'undefined'; ?>,
													"spec_id" : <?php echo ($_GET["spec_id"]) ? $_GET["spec_id"] : 'undefined'; ?>,
													"gu_id" : [<?php if ($_GET["gu_id"] != null) {foreach ( $_GET["gu_id"] as $gu_id ) {echo $gu_id.',';}} else {echo 'undefined';} ?>],
													"psu_id" : <?php echo ($_GET["psu_id"]) ? $_GET["psu_id"] : 'undefined'; ?>,
													"year" : <?php echo ($_GET["year"]) ? $_GET["year"] : 'undefined'; ?>,
													"month" : <?php echo ($_GET["month"]) ? $_GET["month"] : 'undefined'; ?>
												}
												//Получаю массив с именами ГУ в объект
												let gu_ids = {<?php foreach ($gu_ids as $gu_id) { echo $gu_id['id'].':"'.$gu_id['name'].'",'; } ?>};
												//Изменение параметров поиска при загрузке страницы
												window.addEventListener('load', () => {
													//alert(params["gu_id"]);
													$("#client_id").val(params["client_id"]).change();
													$("#spec_id").val(params["spec_id"]).change();
													$("#year").val(params["year"]).change();
													$("#month").val(params["month"]).change();
													if (params["gu_id"][0] != undefined) {
														let checkboxes = document.getElementsByName('gu_id[]');
														//console.log(checkboxes[0].value);
														// for(let index of params["gu_id"]) {
															// checkboxes[index-1].checked = true;
														// }
														for(let index of params["gu_id"]) {
															for (let checkbox of checkboxes) {
																if (checkbox.getAttribute('value') == index) {
																	checkbox.checked = true;
																}
															}
														}
													}
													check_gu();
													$("#psu_id").val(params["psu_id"]).change();
												});
												//Отслеживаем событие клика на ...
												//$(document).on("click", "#order_status_1", alertStatus);	
												//Выбрать все чекбоксы ГУ
												function toggle(source) {
												  //alert(params["client_id"]);
												  let checkboxes = document.getElementsByName('gu_id[]');
												  for(let index = 0; index < checkboxes.length; index++) {
													checkboxes[index].checked = source.checked;
												  }
												  check_gu();
												}
												//Сброс фильтра
												function untoggle() {
												  let checkboxes = document.getElementsByName('gu_id[]');
												  for(let index = 0; index < checkboxes.length; index++) {
													checkboxes[index].checked = false;
												  }
												  
												    $("#client_id").val("").change();
													$("#spec_id").val("").change();
													$("#year").val("").change();
													$("#month").val("").change();
													$("#psu_id").val("").change();
												}
												// Изменение надписи на кнопке выбора ГУ
												function check_gu() {
													// Количество выбранных ГУ
													let checked = $("input[name='gu_id[]']:checked").length;
													
													if (checked == 1) {
														let checkboxes = document.getElementsByName('gu_id[]');
														let checkbox_index = 0;
														for (let checkbox of checkboxes) {
															if (checkbox.checked == true) {
																checkbox_index = checkbox.value;
															}
														}
														$('#gu_id_button').html(gu_ids[checkbox_index]);
													}
													if (checked == 0) {
														$('#gu_id_button').html(' Выбрать ');
													}
													if (checked > 1) {
														$('#gu_id_button').html(' Выбрано ' + checked);
													}
												}
												// Событие клика по галочке ГУ
												$(document).on("click", "input[name='gu_id[]']", function(){
													check_gu();
												});
											</script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<!-- end search -->
						<!-- start main block -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body"> 
										<div class="count-gu"></div>
										<?php if ($_GET['year'] != null && $_GET['month'] != null && get_moderator_report($_GET['year'], $_GET['month'])) { ?>
											<!-- Проверяем, был ли готов отчет от модератора за этот месяц. Если нет то пишем что отчет еще не готов -->
											<div class="btn btn-success">Отчет готов</div>
										            
											<div class="table-rep-plugin">
												<div class="table-wrapper">
													<div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
													<?php get_cell_color_script() ?>
													<table id="table-forms" class="table table-bordered pin_table_gu_forms">
														<thead>
														<tr>
															<th>#</th>
															<th>Месяц</th>
															<th>Клиент</th>
															<th>Специалист</th>
															<th>Статус заказа</th>
															<th>Адрес проживания</th>
															<th>Итого</th>
															<th>ГУ</th>
															<th>Дата отмены</th>
															<th>Причина отмены</th>
															<th>Комментарий</th>
														</tr>
														</thead>
														<tbody>
														<?php foreach ($forms as $f) {
															$order = get_order($f['order_id']);
															$client = get_client($f['client_id']);
															$user = get_user($f['spec_id']);
															$year = date_format(date_create($f['order_date']), 'Y');
															?>
															<tr>
																<td><?php echo $order['order_num']; ?></td>
																<td><?php echo get_month_name(intval(($f['month']))); ?></td>
																<td id="client_name_<?php echo $f['id']; ?>"><?php echo $client['name']; ?></td>
																<td><?php echo $user['name']; ?></td>
																<td><?php if ($order['order_status'] == 1) {echo '<span class="fa fa-circle" style="color:green;"></span> Активен';} else {echo '<span class="fa fa-circle" style="color:red;"></span> Отменен';} ?></td>
																<td><?php echo $order['address']; ?></td>
																<td><?php $total = 0.0; foreach (range(1,31) as $d) {$total += $f[$d];} echo $total; ?></td>
																<td><?php echo get_gu($order['gu_id'])['name']; ?></td>
																<td><?php if ($order['cancel_date'] != '0000-00-00' && $order['cancel_date'] != null) {echo $order['cancel_date'];} else {echo '----';} ?></td>
																<td><?php if ($order['cancel_date'] != '0000-00-00' && $order['cancel_date'] != null) {echo $order['cancel_reason'];} else {echo '----';} ?></td>
															</tr>
														<?php } ?>
														</tbody>
													</table>
													<script>
														window.addEventListener('load', () => {
															// Выводим подсчет часов ГУ если выбрано только одно
															let hours = 0;
															if (params['gu_id'].length == 1) {
																for (let td of $('#table-forms > tbody > tr > td:nth-child(7)')) {
																	hours += (Number(td.innerHTML.replace(' ч.', '')));
																}
																let counter = $('<h5 class="btn btn-info">Итого часов: ' + hours + ' ч.</h5>');
																$('.count-gu').append(counter);
															}
														});
													</script>
												</div></div>
			
											</div>
										<?php } else {?>
											 <!-- Если отчет от модератора не готов, выводим это сообщение  -->
											 <h4>Отчет за этот месяц еще не готов</h4>
										<?php } ?>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div>