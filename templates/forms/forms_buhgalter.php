<!-- start page title -->
<div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Отчеты специалистов</h4>
																																																   
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
													// Выводим подсчет часов ГУ если выбрано только одно
													let hours = 0;
													if (params['gu_id'].length == 1) {
														for (let td of $('#table-forms > tbody > tr > td:nth-child(8)')) {
															hours += (Number(td.innerHTML.replace(' ч.', '')));
														}
														let counter = $('<h5>Итог часов за ГУ: ' + hours + ' ч.</h5>');
														$('.count-gu').append(counter);
													}
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
						<!-- start main block -->							   
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">     
										<div class="count-gu"></div>         
                                        <div class="table-rep-plugin">
                                            <div class="table-wrapper">
                                            	<div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
												<?php get_cell_color_script() ?>
                                                <table id="table-forms" class="table table-bordered pin_table_buh_forms">
                                                    <thead>
                                                    <tr>					  
                                                        <th>Номер заказа <i class="fas fa-sort" onClick="sortTable('table-forms', 0);"></i></th>
                                                        <th>Клиент <i class="fas fa-sort" onClick="sortTable('table-forms', 1);"></i></th>
                                                        <th>Специалист <i class="fas fa-sort" onClick="sortTable('table-forms', 2);"></i></th>				  
														<th>ГУ <i class="fas fa-sort" onClick="sortTable('table-forms', 3);"></i></th>
														<th>ПСУ прикреплен <i class="fas fa-sort" onClick="sortTable('table-forms', 4);"></i></th>
														<th>Месяц <i class="fas fa-sort" onClick="sortTable('table-forms', 5);"></i></th>
                                                        <th>Кол-во часов за месяц <i class="fas fa-sort" onClick="sortTable('table-forms', 6);"></i></th>
                                                        <th>Примечания модератора <i class="fas fa-sort" onClick="sortTable('table-forms', 7);"></i></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
													<?php foreach ($forms as $f) {
														$order = get_order($f['order_id']);
														$client = get_client($f['client_id']);
														$user = get_user($f['spec_id']);
														?>
                                                        <tr>										
                                                            <td><?php echo $order['order_num']; ?></td>
                                                            <td id="client_name_<?php echo $f['id']; ?>"><?php echo $client['name']; ?></td>
															<script>
																// Отображение значения чекбокса
																window.addEventListener("DOMContentLoaded", function() {
																	// Обновляем цвет ячейки клиента
																	let form_<?php echo $f['id']?> = {
																		"id": <?php echo ($f['id']) ? $f['id'] : 'undefined'; ?>,
																		"checkbox1": "<?php echo ($f['checkbox1']) ? $f['checkbox1'] : 'undefined'; ?>",
																		"checkbox2": "<?php echo ($f['checkbox2']) ? $f['checkbox2'] : 'undefined'; ?>"
																	}
																	set_cell_color($("#client_name_<?php echo $f['id']?>"), form_<?php echo $f['id']?>);
																});
															</script>
                                                            <td><?php echo $user['name']; ?></td>
                                                            <td><?php echo get_gu($order['gu_id'])['name']; ?></td>
                                                            <td><?php echo get_psu($order['psu_id'])['name']; ?></td>
                                                            <td><?php echo get_month_name(round($f['month'])); ?></td>
                                                            <td><?php echo count_hours_by_form($f) . ' ч.'; ?></td>	
															<?php 
															// Получаем цвет ячейки
															$matches = array();
															preg_match('/\{([^}]+)\}/', $f['comment'], $matches);

															if (!empty($matches)) { ?>
																<td style="background-color: <?php echo $matches[1];?>;">
															<?php } else { ?>
																<td>
															<?php } echo preg_replace('/\{.*?\}/', '', $f['comment']);?> </td>																																																																				 
                                                        </tr>
													<?php } ?>
                                                    </tbody>
                                                </table>
												<div class="count-gu"></div>
                                            </div></div>
        
                                        </div>
        
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div>