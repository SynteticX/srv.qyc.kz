<!-- start page title -->
<div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Отчеты специалистов</h4>
									<!-- Подтверждение готовности отчета -->
									<?php if (check_moderator_report()) { ?>
										<a class="btn btn-outline-info" disabled>Отчет уже отправлен</a>
									<?php } else { ?>
										<a class="btn btn-outline-primary" onclick="let modal = new bootstrap.Modal(document.getElementById('submit_report')); modal.show();">Отчет готов</a>
									<?php 
											echo get_modal(
												"submit_report", 
												'Подтвердите действие', 
												'Вы уверены что хотите отправить отчет всем ГУ?', 
												"postFormAsync({'moderator_report': true}).then(popup('success', 'Статус отчета изменен'));");
									} ?>
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
															echo '<option value="'.$c["id"].'">'. $c["name"] .'</option>';
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
											<?php get_cell_color_script() ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<!-- end search -->
						<!-- Отчеты - Вид 1 -->
						<?php if (!isset($_GET["view"])) { ?>
						<!-- start main block -->							   
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">              
                                        <div class="table-rep-plugin">
                                            <div class="table-wrapper">
                                            	<div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
                                                <table id="table-forms" class="table table-bordered pin_table_moderator_forms">
                                                    <thead>
                                                    <tr>					  
                                                        <th>Месяц <i class="fas fa-sort" onClick="sortTable('table-forms', 0);"></i></th>
                                                        <th>Клиент <i class="fas fa-sort" onClick="sortTable('table-forms', 1);"></i></th>
                                                        <th>Специалист <i class="fas fa-sort" onClick="sortTable('table-forms', 2);"></i></th>				  
                                                        <th>Статус <i class="fas fa-sort" onClick="sortTable('table-forms', 3);"></i></th>			   
                                                        <th>Адрес проживания</th>
                                                        <th>Остаток на начало <i class="fas fa-sort" onClick="sortTable('table-forms', 5);"></i></th>
														<th>Чекбоксы</th>
														<?php
														foreach (range(1,31) as $i) {
															echo '<th>'.$i.'</th>';
														}?>
                                                        <th>Итого</th>
																							
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
													<?php foreach ($forms as $f) {
														$order = get_order($f['order_id']);
														$client = get_client($f['client_id']);
														$user = get_user($f['spec_id']);

														?>
                                                        <tr>										
                                                            <td><?php echo get_month_name(intval($f['month'])); ?></td>
                                                            <td id="client_name_<?php echo $f['id']; ?>"><a name="client" href="<?php echo $client['link']; ?>"><?php echo $client['name']; ?></a></td>
                                                            <td><?php echo $user['name']; ?></td>
                                                            <td><?php if ($order['order_status'] == 1) {echo '<span class="fa fa-circle" style="color:green;"></span> Активен';} else {echo '<span class="fa fa-circle" style="color:red;"></span> Отменен';} ?></td>
                                                            <td><?php echo $order['address']; ?></td>
                                                            <td><?php echo floatval($order['begin_hours'] - count_hours_for_month($f['client_id'],get_year($order['order_date']),$f['month'])); ?></td>
                                                            <td>
																<p>
																	<input type="checkbox" name="checkbox" id="checkbox1_<?php echo $f['id']; ?>">
																	 На портале
																</p>
																<p>
																	<input type="checkbox" name="checkbox" id="checkbox2_<?php echo $f['id']; ?>">
																	 ЛСИ подписал
																</p>
																<script>
																	// Обработка чекбоксов

																	let checkbox1_<?php echo $f['id']; ?> = document.querySelector('#checkbox1_<?php echo $f['id']; ?>');
																	checkbox1_<?php echo $f['id']; ?>.value = `<?php echo ($f['checkbox1']) ? $f['checkbox1'] : 'undefined'; ?>`;
																	// Отправка на сервер
																	checkbox1_<?php echo $f['id']; ?>.addEventListener('click', () => {
																		if (checkbox1_<?php echo $f['id']; ?>.value == 'on') {
																			checkbox1_<?php echo $f['id']; ?>.value = 'off';
																		} else {
																			checkbox1_<?php echo $f['id']; ?>.value = 'on';
																		}
																		let params = {
																			"sendform": <?php echo $f['id']; ?>,
																			"checkbox1": checkbox1_<?php echo $f['id']; ?>.value,
																		}
																		let post = postForm(params)
																		.then(response => {
																			result = JSON.parse(response);
																			if (result['status'] == 'success') {
																				popup('success', 'Данные отправлены!');
																				// Обновляем цвет ячейки клиента
																				let form_<?php echo $f['id']?> = {
																					"id": <?php echo ($f['id']) ? $f['id'] : 'undefined'; ?>,
																					"checkbox1": checkbox1_<?php echo $f['id']; ?>.value,
																					"checkbox2": checkbox2_<?php echo $f['id']; ?>.value
																				}
																				setTimeout(() => {
																					set_cell_color($("#client_name_<?php echo $f['id']?>"), form_<?php echo $f['id']?>);
																				}, 2000);
																			}
																		});
																	});
																	let checkbox2_<?php echo $f['id']; ?> = document.querySelector('#checkbox2_<?php echo $f['id']; ?>');
																	checkbox2_<?php echo $f['id']; ?>.value = `<?php echo ($f['checkbox2']) ? $f['checkbox2'] : 'undefined'; ?>`;
																	// Отправка на сервер
																	checkbox2_<?php echo $f['id']; ?>.addEventListener('click', () => {
																		if (checkbox2_<?php echo $f['id']; ?>.value == 'on') {
																			checkbox2_<?php echo $f['id']; ?>.value = 'off';
																		} else {
																			checkbox2_<?php echo $f['id']; ?>.value = 'on';
																		}
																		let params = {
																			"sendform": <?php echo $f['id']; ?>,
																			"checkbox2": checkbox2_<?php echo $f['id']; ?>.value,
																		}
																		let post = postForm(params)
																		.then(response => {
																			let result = JSON.parse(response);
																			if (result['status'] == 'success') {
																				// Выводим сообщения
																				popup('success', 'Данные отправлены!');
																				// Обновляем цвет ячейки клиента
																				let form_<?php echo $f['id']?> = {
																					"id": <?php echo ($f['id']) ? $f['id'] : 'undefined'; ?>,
																					"checkbox1": checkbox1_<?php echo $f['id']; ?>.value,
																					"checkbox2": checkbox2_<?php echo $f['id']; ?>.value
																				}
																				setTimeout(() => {
																					set_cell_color($("#client_name_<?php echo $f['id']?>"), form_<?php echo $f['id']?>);
																				}, 2000);
																			}
																		});
																	});

																	// Отображение значения чекбокса
																	window.addEventListener("DOMContentLoaded", function() {
																		checkbox1_<?php echo $f['id']; ?>.value = `<?php echo ($f['checkbox1']) ? $f['checkbox1'] : 'undefined'; ?>`;
																		if (checkbox1_<?php echo $f['id']; ?>.value == 'on') {
																			checkbox1_<?php echo $f['id']; ?>.setAttribute('checked', '');
																		}
																		checkbox2_<?php echo $f['id']; ?>.value = `<?php echo ($f['checkbox2']) ? $f['checkbox2'] : 'undefined'; ?>`;
																		if (checkbox2_<?php echo $f['id']; ?>.value == 'on') {
																			checkbox2_<?php echo $f['id']; ?>.setAttribute('checked', '');
																		}
																		// Обновляем цвет ячейки клиента
																		let form_<?php echo $f['id']?> = {
																			"id": <?php echo ($f['id']) ? $f['id'] : 'undefined'; ?>,
																			"checkbox1": "<?php echo ($f['checkbox1']) ? $f['checkbox1'] : 'undefined'; ?>",
																			"checkbox2": "<?php echo ($f['checkbox2']) ? $f['checkbox2'] : 'undefined'; ?>"
																		}
																		set_cell_color($("#client_name_<?php echo $f['id']?>"), form_<?php echo $f['id']?>);
																	});
																</script>
															</td>
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
                                                            <td><?php echo $order['begin_hours']-count_hours($client['id'], date('Y')); ?></td>
                                                            <td><?php echo get_gu($order['gu_id'])['name']; ?></td>
                                                            <td><?php echo get_psu($order['psu_id'])['name']; ?></td>
                                                            <td><?php if ($order['cancel_date'] != '0000-00-00' && $order['cancel_date'] != null) {echo $order['cancel_date'];} else {echo '----';} ?></td>
                                                            <td><?php if ($order['cancel_date'] != '0000-00-00' && $order['cancel_date'] != null) {echo $order['cancel_reason'];} else {echo '----';} ?></td>					
															<?php 
															// Получаем цвет ячейки
															$matches = array();
															preg_match('/\{([^}]+)\}/', $f['comment'], $matches);

															if (!empty($matches)) { ?>
																<td style="background-color: <?php echo $matches[1];?>;">
															<?php } else { ?>
																<td>
															<?php } echo preg_replace('/\{.*?\}/', '', $f['comment']);?> </td>
															<td>
																<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#comment_<?php echo $f['id'];?>_modal">
																	<i id="comment_<?php echo $f['id'];?>" class="fa fa-pen"></i>
																</button>
																<script>
																	let modal<?php echo $f['id'];?> = $(`<div class="modal fade" id="comment_<?php echo $f['id'];?>_modal" tabindex="-1" aria-labelledby="comment_<?php echo $f['id'];?>_modalLabel" aria-hidden="true" style="background: #00000060;">
																		<div class="modal-dialog">
																			<div class="modal-content">
																			<div class="modal-header">
																				<h1 class="modal-title fs-5" id="comment_<?php echo $f['id'];?>_modalLabel">Добавить примечание</h1>
																				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																			</div>
																			<div class="modal-body">
																			<form id="comment_<?php echo $f['id'];?>_form" method="POST">
																				<input hidden name="id" value="<?php echo $f['id'];?>">
																				<textarea name="comment" rows="4" cols="50"></textarea>
																				<br>
																				<label>
																					<input type="radio" name="color" value="white" checked> Обычное
																				</label>
																				<label>
																					<input type="radio" name="color" value="red"> Невозможно вбить на Портале
																				</label>
																				<label>
																					<input type="radio" name="color" value="violet"> Успешно подписан
																				</label>
																				<label>
																					<input type="radio" name="color" value="orange"> Успешно подписан акт ЛСИ
																				</label>
																			</form>
																			</div>
																			<div class="modal-footer">
																				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
																				<button type="button" class="btn btn-primary" id="comment_<?php echo $f['id'];?>_send">Отправить</button>
																			</div>
																			</div>
																		</div>
																	</div>`);
																		$('.main-content').append(modal<?php echo $f['id'];?>);
																	$('#comment_<?php echo $f['id'];?>_send').click(() => {
																		// Получаем данные формы
																		var formData = $("#comment_<?php echo $f['id'];?>_form").serializeArray();
																		var formObject = {};

																		$.each(formData, function(index, field){
																		formObject[field.name] = field.value;
																		});

																		async function sendSubmitActs() {
																			// Конвертируем PHP список форм в JS
																			let forms = [<?php echo $f['id'];?>];
																			if (forms.length > 0) {
																				// Делаем POST-запрос
																				let params = {
																					'sendform_list': forms,
																					'query': {
																						'comment': `{${formObject['color']}}${formObject['comment']}`
																					}
																				}
																				return await postFormAsync(params);
																			}
																		}
																		sendSubmitActs().then((result) => {
																			if (JSON.parse(result).status == 'success') {
																				popup('success', 'Данные успешно отправлены');
																				// Перезагрузка страницы через 3 секунды
																				setTimeout(() => {
																					location.reload();
																				}, 2000);
																			}
																		});
																	});
																</script>
																<a class="mx-1" onclick="let modal = new bootstrap.Modal(document.getElementById('delete_act_<?php echo $f['id'];?>')); modal.show();" title="Вернуть на доработку" data-toggle="tooltip"><i class="fas fa-reply"></i></a>
																	<?php echo get_modal(
																		"delete_act_" . $f['id'], 
																		'Подтвердите действие', 
																		'Вы уверены что хотите вернуть акт на доработку?', 
																		"postFormAsync({'delete_act':" . $f['id'] . ", spec_id: " . $f['spec_id'] . ", year: " . explode('-', $order['order_date'])[0] . ", month:  " . intval($f['month']) . "}).then(popup('success', 'Возвращено на доработку'));"); ?>
															</td>																																																																																		 
                                                        </tr>
													<?php } ?>
                                                    </tbody>
                                                </table>
                                            </div></div>
        
                                        </div>
        
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div>
						<!-- END VIEW 1 -->
						<?php } else if ($_GET['view'] == 2) {?>
						<!-- Отчеты - Вид 2 -->
						<!-- start main block -->							   
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">              
                                        <div class="table-rep-plugin">
                                            <div class="table-wrapper">
                                            	<div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
                                                <table id="table-forms" class="table table-bordered pin_table_moderator_forms">
                                                    <thead>
                                                    <tr>					  
                                                        <th>Месяц <i class="fas fa-sort" onClick="sortTable('table-forms', 0);"></i></th>
                                                        <th>Клиент <i class="fas fa-sort" onClick="sortTable('table-forms', 1);"></i></th>
                                                        <th>Специалист <i class="fas fa-sort" onClick="sortTable('table-forms', 2);"></i></th>	
														<th>Чекбоксы</th>
														<?php
														foreach (range(1,31) as $i) {
															echo '<th>'.$i.'</th>';
														}?>
                                                        <th>Итого</th>
                                                        <th>ГУ</th>
                                                        <th>Примечания</th>
                                                        <th>Настройки</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
													<?php foreach ($forms as $f) {
														$order = get_order($f['order_id']);
														$client = get_client($f['client_id']);
														$user = get_user($f['spec_id']);

														?>
                                                        <tr>										
                                                            <td><?php echo get_month_name(intval($f['month'])); ?></td>
                                                            <td id="client_name_<?php echo $f['id']; ?>"><a name="client" href="<?php echo $client['link']; ?>"><?php echo $client['name']; ?></a></td>
                                                            <td><?php echo $user['name']; ?></td>
                                                            <td>
																<p>
																	<input type="checkbox" name="checkbox" id="checkbox1_<?php echo $f['id']; ?>">
																	 На портале
																</p>
																<p>
																	<input type="checkbox" name="checkbox" id="checkbox2_<?php echo $f['id']; ?>">
																	 ЛСИ подписал
																</p>
																<script>
																	// Обработка чекбоксов

																	let checkbox1_<?php echo $f['id']; ?> = document.querySelector('#checkbox1_<?php echo $f['id']; ?>');
																	checkbox1_<?php echo $f['id']; ?>.value = `<?php echo ($f['checkbox1']) ? $f['checkbox1'] : 'undefined'; ?>`;
																	// Отправка на сервер
																	checkbox1_<?php echo $f['id']; ?>.addEventListener('click', () => {
																		if (checkbox1_<?php echo $f['id']; ?>.value == 'on') {
																			checkbox1_<?php echo $f['id']; ?>.value = 'off';
																		} else {
																			checkbox1_<?php echo $f['id']; ?>.value = 'on';
																		}
																		let params = {
																			"sendform": <?php echo $f['id']; ?>,
																			"checkbox1": checkbox1_<?php echo $f['id']; ?>.value,
																		}
																		let post = postForm(params)
																		.then(response => {
																			result = JSON.parse(response);
																			if (result['status'] == 'success') {
																				popup('success', 'Данные отправлены!');
																				// Обновляем цвет ячейки клиента
																				let form_<?php echo $f['id']?> = {
																					"id": <?php echo ($f['id']) ? $f['id'] : 'undefined'; ?>,
																					"checkbox1": checkbox1_<?php echo $f['id']; ?>.value,
																					"checkbox2": checkbox2_<?php echo $f['id']; ?>.value
																				}
																				setTimeout(() => {
																					set_cell_color($("#client_name_<?php echo $f['id']?>"), form_<?php echo $f['id']?>);
																				}, 2000);
																			}
																		});
																	});
																	let checkbox2_<?php echo $f['id']; ?> = document.querySelector('#checkbox2_<?php echo $f['id']; ?>');
																	checkbox2_<?php echo $f['id']; ?>.value = `<?php echo ($f['checkbox2']) ? $f['checkbox2'] : 'undefined'; ?>`;
																	// Отправка на сервер
																	checkbox2_<?php echo $f['id']; ?>.addEventListener('click', () => {
																		if (checkbox2_<?php echo $f['id']; ?>.value == 'on') {
																			checkbox2_<?php echo $f['id']; ?>.value = 'off';
																		} else {
																			checkbox2_<?php echo $f['id']; ?>.value = 'on';
																		}
																		let params = {
																			"sendform": <?php echo $f['id']; ?>,
																			"checkbox2": checkbox2_<?php echo $f['id']; ?>.value,
																		}
																		let post = postForm(params)
																		.then(response => {
																			let result = JSON.parse(response);
																			if (result['status'] == 'success') {
																				// Выводим сообщения
																				popup('success', 'Данные отправлены!');
																				// Обновляем цвет ячейки клиента
																				let form_<?php echo $f['id']?> = {
																					"id": <?php echo ($f['id']) ? $f['id'] : 'undefined'; ?>,
																					"checkbox1": checkbox1_<?php echo $f['id']; ?>.value,
																					"checkbox2": checkbox2_<?php echo $f['id']; ?>.value
																				}
																				setTimeout(() => {
																					set_cell_color($("#client_name_<?php echo $f['id']?>"), form_<?php echo $f['id']?>);
																				}, 2000);
																			}
																		});
																	});

																	// Отображение значения чекбокса
																	window.addEventListener("DOMContentLoaded", function() {
																		checkbox1_<?php echo $f['id']; ?>.value = `<?php echo ($f['checkbox1']) ? $f['checkbox1'] : 'undefined'; ?>`;
																		if (checkbox1_<?php echo $f['id']; ?>.value == 'on') {
																			checkbox1_<?php echo $f['id']; ?>.setAttribute('checked', '');
																		}
																		checkbox2_<?php echo $f['id']; ?>.value = `<?php echo ($f['checkbox2']) ? $f['checkbox2'] : 'undefined'; ?>`;
																		if (checkbox2_<?php echo $f['id']; ?>.value == 'on') {
																			checkbox2_<?php echo $f['id']; ?>.setAttribute('checked', '');
																		}
																		// Обновляем цвет ячейки клиента
																		let form_<?php echo $f['id']?> = {
																			"id": <?php echo ($f['id']) ? $f['id'] : 'undefined'; ?>,
																			"checkbox1": "<?php echo ($f['checkbox1']) ? $f['checkbox1'] : 'undefined'; ?>",
																			"checkbox2": "<?php echo ($f['checkbox2']) ? $f['checkbox2'] : 'undefined'; ?>"
																		}
																		set_cell_color($("#client_name_<?php echo $f['id']?>"), form_<?php echo $f['id']?>);
																	});
																</script>
															</td>
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
                                                            <td><?php echo get_gu($order['gu_id'])['name']; ?></td>
                                                            <?php 
															// Получаем цвет ячейки
															$matches = array();
															preg_match('/\{([^}]+)\}/', $f['comment'], $matches);

															if (!empty($matches)) { ?>
																<td style="background-color: <?php echo $matches[1];?>;">
															<?php } else { ?>
																<td>
															<?php } echo preg_replace('/\{.*?\}/', '', $f['comment']);?> </td>
															<td>
																<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#comment_<?php echo $f['id'];?>_modal">
																	<i id="comment_<?php echo $f['id'];?>" class="fa fa-pen"></i>
																</button>
																<script>
																	let modal<?php echo $f['id'];?> = $(`<div class="modal fade" id="comment_<?php echo $f['id'];?>_modal" tabindex="-1" aria-labelledby="comment_<?php echo $f['id'];?>_modalLabel" aria-hidden="true" style="background: #00000060;">
																		<div class="modal-dialog">
																			<div class="modal-content">
																			<div class="modal-header">
																				<h1 class="modal-title fs-5" id="comment_<?php echo $f['id'];?>_modalLabel">Добавить примечание</h1>
																				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																			</div>
																			<div class="modal-body">
																			<form id="comment_<?php echo $f['id'];?>_form" method="POST">
																				<input hidden name="id" value="<?php echo $f['id'];?>">
																				<textarea name="comment" rows="4" cols="50"></textarea>
																				<br>
																				<label>
																					<input type="radio" name="color" value="white" checked> Обычное
																				</label>
																				<label>
																					<input type="radio" name="color" value="red"> Невозможно вбить на Портале
																				</label>
																				<label>
																					<input type="radio" name="color" value="violet"> Успешно подписан
																				</label>
																				<label>
																					<input type="radio" name="color" value="orange"> Успешно подписан акт ЛСИ
																				</label>
																			</form>
																			</div>
																			<div class="modal-footer">
																				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
																				<button type="button" class="btn btn-primary" id="comment_<?php echo $f['id'];?>_send">Отправить</button>
																			</div>
																			</div>
																		</div>
																	</div>`);
																		$('.main-content').append(modal<?php echo $f['id'];?>);
																	$('#comment_<?php echo $f['id'];?>_send').click(() => {
																		// Получаем данные формы
																		var formData = $("#comment_<?php echo $f['id'];?>_form").serializeArray();
																		var formObject = {};

																		$.each(formData, function(index, field){
																		formObject[field.name] = field.value;
																		});

																		async function sendSubmitActs() {
																			// Конвертируем PHP список форм в JS
																			let forms = [<?php echo $f['id'];?>];
																			if (forms.length > 0) {
																				// Делаем POST-запрос
																				let params = {
																					'sendform_list': forms,
																					'query': {
																						'comment': `{${formObject['color']}}${formObject['comment']}`
																					}
																				}
																				return await postFormAsync(params);
																			}
																		}
																		sendSubmitActs().then((result) => {
																			if (JSON.parse(result).status == 'success') {
																				popup('success', 'Данные успешно отправлены');
																				// Перезагрузка страницы через 3 секунды
																				setTimeout(() => {
																					location.reload();
																				}, 2000);
																			}
																		});
																	});
																</script>
															</td>																																																																																		 
                                                        </tr>
													<?php } ?>
                                                    </tbody>
                                                </table>
                                            </div></div>
        
                                        </div>
        
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div>
						<?php } ?>
						<!-- END VIEW 2 -->