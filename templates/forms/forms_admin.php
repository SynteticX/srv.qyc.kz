<!-- start page title -->
<div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Отчеты специалистов</h4>
									<a class="btn btn-outline-info" href="newform.php?selectclient=0" title="Отправить форму" data-toggle="tooltip"><i class="fa fa-plus"></i> Отправить форму</a>
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
												<!--<p>Месяц</p>-->
												<div class="col-2">
													<span>Клиент</span>
													<select class="form-select" name="client_id" id="client_id">
														<option selected value="">Все</option>
														<?php
														foreach ($clients as $c) {
															echo '<option value="'.$c["id"].'">' . $c["name"] . " - " . get_order_by_client($c["id"], date('Y'))['order_num'] . '</option>';
														}
														?>
													</select>
												</div>
												<div class="col-2">
													<span>Специалист</span>
													<select class="form-select" name="spec_id" id="spec_id">
														<option selected value="">Все</option>
														<?php
														foreach ($users as $u) {
															if ($u['usergroup'] == 2) {echo '<option value="'.$u["id"].'">'. $u["name"] .'</option>';}
														}
														?>
													</select>
												</div>
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
												<div class="col-2">
													<span>ГУ</span>
													<div class="dropdown" id="gu_id">
													  <button id="gu_id_button" class="btn btn-outline dropdown-toggle form-select" style="border:1px solid #ced4da;text-align: left;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
														Выбрать
													  </button>
													  <ul class="dropdown-menu" style="padding: 5px 5px 5px 10px;border:1px solid #ced4da;width: max-content;height: 300px;overflow: auto;">
														<li>
															<label style="font-weight: 400;">
																<input id="gu_id_toggle" type="checkbox" onClick="toggle(this)" />
																 Выбрать все
															</label>
														</li>
													  <?php foreach ($gu_ids as $gu_id) { ?>
																<li>
																	<label style="font-weight: 400;">
																		<input type="checkbox" name="gu_id[]" value="<?php echo $gu_id["id"]; ?>">
																		<?php echo $gu_id["name"]; ?>
																	</label>
																</li>
														<?php } ?>
													  </ul>
													</div>
												</div>
												<!-- <div class="col-1">
													<span>ПСУ</span>
													<select class="form-select" name="psu_id" id="psu_id">
														<option selected value="">Все</option>
														<?php
														foreach ($psu_ids as $psu_id) {
															echo '<option value="'.$psu_id["id"].'">'. $psu_id["name"] .'</option>';
														}
														?>
													</select>
												</div> -->
												<button type="submit" form="query" class="btn btn-primary zoom"><i class="fas fa-search"></i> Найти</button>
												<button type="submit" form="query" class="btn btn-secondary zoom" onClick="untoggle()"><i class="fas fa-filter"></i> Сбросить фильтр</button>
												<a class="btn btn-secondary zoom" id="excel"><i class="fas fa-table"></i> Скачать в Excel</a>
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
													// Выводим подсчет часов
													let hours = 0;
													for (let td of $('#table-forms > tbody > tr > td:nth-child(43)')) {
														hours += (Number(td.innerHTML.replace(' ч.', '')));
													}
													let counter = $('<h5>Итог часов: ' + hours + ' ч.</h5>');
													$('.counter').append(counter);
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
											<script>
												$(document).ready(async () => {
													// Вывод форм с ошибками
													let errors = await getFormAsync({'getTable': 'errors'})
														.then((data) => {
															data = JSON.parse(data);
															data.forEach(error => {
																if (error['form_id'] != 'NULL' && error['form_id'] && error['status'] != 1) {
																	if ($('#tr_' + error['form_id'])) {
																		$('#tr_' + error['form_id']).addClass('bg-danger');
																		$('#comments_' + error['form_id']).append('Ошибка: ' + error.text)
																	}
																}
															});
														});
												})
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
										<div class="counter"></div>   
                                        <div class="table-rep-plugin">
                                            <div class="table-wrapper">
                                            	<div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
												<?php get_cell_color_script() ?>
                                                <table id="table-forms" class="table table-bordered pin_table_admin_forms">
                                                    <thead>
                                                    <tr>
                                                        <th># <i class="fas fa-sort" onClick="sortTable('table-forms', 0);"></i></th>
                                                        <th>Номер заказа <i class="fas fa-sort" onClick="sortTable('table-forms', 1);"></i></th>
                                                        <th>Дата заказа <i class="fas fa-sort" onClick="sortTable('table-forms', 2);"></i></th>
                                                        <th>Месяц <i class="fas fa-sort" onClick="sortTable('table-forms', 3);"></i></th>
                                                        <th>Клиент <i class="fas fa-sort" onClick="sortTable('table-forms', 4);"></i></th>
                                                        <th>Специалист <i class="fas fa-sort" onClick="sortTable('table-forms', 5);"></i></th>
                                                        <th>ИИН клиента</th>
                                                        <th>Статус заказа <i class="fas fa-sort" onClick="sortTable('table-forms', 7);"></i></th>
                                                        <th>Моб. телефон</th>
                                                        <th>Адрес проживания</th>
                                                        <th>Остаток на начало <i class="fas fa-sort" onClick="sortTable('table-forms', 10);"></i></th>
														<?php
														foreach (range(1,31) as $i) {
															echo '<th>'.$i.'</th>';
														}?>
                                                        <th>Итого</th>
                                                        <th>Сумма зарплаты</th>
                                                        <th>Остаток на конец</th>
                                                        <th>ГУ</th>
                                                        <th>ПСУ прикреплен</th>
                                                        <th>Дата отмены</th>
                                                        <th>Причина отмены</th>
                                                        <th>Примечания</th>
                                                        <th>Настройки</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
<?php
$tag1 = microtime(true) - $start;
echo <<<JAVASCRIPT
<script>
    console.log("Начало цикла: " + {$tag1});
</script>
JAVASCRIPT;
?>
													<?php foreach ($forms as $f) {
														$order = get_order($f['order_id']);
														$client = findInNestedArray($clients, "id", $client["id"]);
														$user = get_user($f['spec_id']);
														$year = date_format(date_create($order['order_date']), 'Y');
														// Выводим, только если форма была подписана
														if ($f['accepted']) {
														?>
                                                        <tr id="tr_<?php echo $f['id']; ?>">
                                                            <td><?php echo $f['id']; ?></td>
                                                            <td><?php echo $order['order_num']; ?></td>
                                                            <td><?php echo $order['order_date']; ?></td>
                                                            <td><?php echo get_month_name(intval(($f['month']))); ?></td>
                                                            <td id="client_name_<?php echo $f['id']; ?>">
																<a name="client" href="<?php echo $client['link']; ?>"><?php echo $client['name']; ?></a>
																<script>
																	let form_<?php echo $f['id']?> = {
																		"id": <?php echo ($f['id']) ? $f['id'] : 'undefined'; ?>,
																		"checkbox1": "<?php echo ($f['checkbox1']) ? $f['checkbox1'] : 'undefined'; ?>",
																		"checkbox2": "<?php echo ($f['checkbox2']) ? $f['checkbox2'] : 'undefined'; ?>"
																	}
																	$(document).ready(() => {
																		set_cell_color($("#client_name_<?php echo $f['id']?>"), form_<?php echo $f['id']?>);
																	});
																</script>
															</td>
                                                            
															
															<td><?php echo $user['name']; ?></td>
                                                            <td><?php echo $client['iin']; ?></td>
                                                            <td><?php if ($order['order_status'] == 1) {echo '<span class="fa fa-circle" style="color:green;"></span> Активен';} else {echo '<span class="fa fa-circle" style="color:red;"></span> Отменен';} ?></td>
                                                            <td><?php echo $client['phone']; ?></td>
                                                            <td><?php echo $order['address']; ?></td>
                                                            <td><?php 
																// Остаток часов на период
																$begin_hours_for_month = floatval($order['begin_hours'] - count_hours_for_month($f['client_id'],$year,$f['month']));
																echo $begin_hours_for_month; ?></td>
                                                            <?php
																$ls = get_vacations($f['month'], $year);
																foreach (range(1,31) as $i) {
																	foreach ($ls as &$l) {
																		if ($i == $l) {
																			echo '<td class="text-center cell-dark" style="'.get_online_status_as_style($f['id'],$i).'"><div class="'.get_online_status_as_div_class($f['id'],$i).'">'.$f[$i].'</div></td>';
																			break;
																		} else {
																			if ($l == $ls[count($ls)-1]) {
																				echo '<td class="text-center" style="'.get_online_status_as_style($f['id'],$i).'"><div class="'.get_online_status_as_div_class($f['id'],$i).'">'.$f[$i].'</div></td>';
																			}
																		}
																	}
																}
															?>
                                                            <td><?php $total = 0.0; foreach (range(1,31) as $d) {$total += $f[$d];} echo $total; ?></td>
                                                            <td><?php echo intval($total*getSpecialistTariff($order['specialists'])).' тг.'; ?></td>
                                                            <td><?php echo $begin_hours_for_month-$total; ?></td>
                                                            <td><?php echo get_gu($order['gu_id'])['name']; ?></td>
                                                            <td><?php echo get_psu($order['psu_id'])['name']; ?></td>
                                                            <td><?php if ($order['cancel_date'] != '0000-00-00' && $order['cancel_date'] != null) {echo $order['cancel_date'];} else {echo '----';} ?></td>
                                                            <td><?php if ($order['cancel_date'] != '0000-00-00' && $order['cancel_date'] != null) {echo $order['cancel_reason'];} else {echo '----';} ?></td>
                                                            <td id="comments_<?php echo $f['id'];?>"><?php echo $f['comment'];?></td>
															<td>
																<?php if ($order['cancel_date'] == '0000-00-00' || $order['cancel_date'] == null) { ?>
																	<a class="mx-2" href="newform.php?selectclient=<?php echo $f['client_id'];?>" title="Отправить форму" data-toggle="tooltip"><span class="fa fa-edit"></span></a>
																	<a class="mx-2" href="deleteform.php?editform=<?php echo $f['id'];?>" title="Удалить форму" data-toggle="tooltip"><span class="fa fa-trash"></span></a>
																	<a class="mx-2" onclick="let modal = new bootstrap.Modal(document.getElementById('delete_act_<?php echo $f['id'];?>')); modal.show();" title="Вернуть на доработку" data-toggle="tooltip"><i class="fas fa-reply"></i></a>
																	<a class="mx-2" onclick="let modal = new bootstrap.Modal(document.getElementById('add_error_<?php echo $f['id'];?>')); modal.show();" title="Добавить ошибку"><i class="fas fa-exclamation-circle"></i></a>
																	
																	<?php 
																	// Модальное окно "Вернуть акт на доработку"
																	echo get_modal(
																		"delete_act_" . $f['id'], 
																		'Подтвердите действие', 
																		'Вы уверены что хотите вернуть акт на доработку?', 
																		"postFormAsync({'delete_act':" . $f['id'] . ", spec_id: " . $f['spec_id'] . ", year: " . explode('-', $order['order_date'])[0] . ", month:  " . intval($f['month']) . "}).then(popup('success', 'Возвращено на доработку'));"
																	);
																	// Модальное окно для создания ошибки
																	$error_modal_content = <<<HTML
																		<label>Тип ошибки</label>
																		<select class="form-select" id="add_error_type_{$f['id']}">
HTML;
																	foreach ($error_types as $index => $t) {
																		$error_modal_content .= <<<HTML
																		<option value="{$index}">{$t}</option>
HTML;
																	}
																	
																	$error_modal_content .= <<<HTML
																		</select>
																		<label>Комментарий</label>
																		<textarea class="form-control" id="add_error_textarea_{$f['id']}"></textarea>
																		<label>Срок исполнения</label>
																		<input class="form-control" type="date" id="add_error_deadline_{$f['id']}">
HTML;
																	echo get_modal(
																		"add_error_" . $f['id'], 
																		'Подтвердите действие', 
																		$error_modal_content, 
																		"postFormAsync(
																			{
																				'create_error': {
																					'text': document.querySelector('#add_error_textarea_" . $f['id'] . "').value,
																					'date': Date.now(),
																					'type': document.querySelector('#add_error_type_" . $f['id'] . "').value,
																					'deadline': document.querySelector('#add_error_deadline_" . $f['id'] . "').value,
																					'form_id': " . $f['id'] . "
																				}
																			}
																		).then(popup('success', 'Форма обновлена')).then(document.querySelector('#tr_" . $f['id'] . "').classList.add('bg-danger'));"
																	);

																} else {?>
																	<a class="" href="deleteform.php?editform=<?php echo $f['id'];?>" title="Удалить форму" data-toggle="tooltip"><span class="fa fa-trash"></span></a>
																<?php } ?>
															</td>
                                                        </tr>
													<?php }
													} ?>
                                                    </tbody>
                                                </table>
												
                                            </div></div>
											<div class="counter"></div>   
                                        </div>
        
                                    </div>
                                </div>
                            </div> <!-- end col -->
						</div>

<?php
$end = microtime(true) - $start;
echo <<<JAVASCRIPT
<script>
    console.log("Конец скрипта: " + {$end});
</script>
JAVASCRIPT;
?>