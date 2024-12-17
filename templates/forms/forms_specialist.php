<!-- start page title -->
<div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">История актов <?php echo date('Y'); ?></h4>
									<!-- <a class="btn btn-outline-info" href="newform.php?selectclient=0" title="Отправить форму" data-toggle="tooltip"><i class="fa fa-plus"></i> Отправить форму</a> -->
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
						<!-- start main block -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">              
                                        <div class="table-rep-plugin">
                                            <div class="table-wrapper">
                                            	<div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
												<?php get_cell_color_script(); ?>
												<?php 
													// Берем из БД список клиентов
													$spec = $_SESSION["id"];
													$clients = get_spec_clients($spec);
													// Получаем формы клиентов
													
												?>
                                                <table id="table-forms" class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th class="col-2">Клиент</th>
														<?php 
															// Генерируем столбцы месяцев
															foreach (range(1,12) as $m) {
														?>
															<th class="col"><?php echo get_month_name($m); ?></th>
														<?php
															// Генерируем столбцы месяцев (конец блока)
															}
														?>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
													<?php 
														foreach ($clients as $client) {
													?>
                                                        <tr>
															<td class="col-2"><?php echo $client["name"];?></td>
															<?php 
																// Генерируем ячейки месяцев
																foreach (range(1,12) as $m) {
															?>
																<td class="col" id="client<?php echo $client["id"] . "_" . $m?>"></td>
															<?php
																// Генерируем ячейки месяцев (конец блока)
																}
															?>
                                                        </tr>
													<?php 
														} 
													?>
                                                    </tbody>
                                                </table>

												<script>
													// Подгружаем месяца клиента
													$(document).ready(async () => {
														// Загружаем список клиентов специалиста
														let clients = await getFormAsync({"getSpecClients": <?php echo $_SESSION["id"];?>}).then(
															(response) => JSON.parse(response)
														)
														
														for (let client of clients) {
															// Загружаем формы клиента на год
															let params = {
																"getforms": 0,
																"client_id": client['id'],
            													"spec_id": <?php echo ($_SESSION["id"]) ? $_SESSION["id"] : 'undefined';?>,
																"year": <?php echo date('Y');?>,
																"accepted": "true"
															}
															let forms = await getFormAsync(params).then(
																(response) => JSON.parse(response)
															)
															
															for (let month of [...Array(13).keys()]) {
																if (month == 0) {continue;}
																if (!forms) {continue;}
																let form = forms.find((form) => parseInt(form.month) == month)
																// Если форма на месяц уже подписана, то добавляем в таблицу модальное окно и кнопку
																if (form) {
																	let btn = `<button class="btn btn-secondary" onclick="let modal = new bootstrap.Modal(document.getElementById('Modal_${form.client_id}_${month}')); modal.show();">${countHoursByForm(form)} ч. <i class="fas fa-eye"></i></button>`;
																	$(`#client${form.client_id}_${month}`).append(btn)

																	// Формируем модальное окно
																	function validate_hours(hours) {
																		if (hours != null && hours != undefined) {
																			return hours;
																		} else {
																			return 0
																		}
																	}
																	function get_online(status, hours) {
																		if (status == 1 && hours > 0) {
																			return "Очно"
																		} else if (status == 2 && hours > 0) {
																			return "Онлайн"
																		} else {
																			return ""
																		}
																	}
																	let modal = `<!-- Modal -->
																				<div class="modal fade" style="background: #00000060" id="Modal_${form.client_id}_${month}" tabindex="-1" aria-hidden="true">
																					<div class="modal-dialog">
																						<div class="modal-content">
																						<div class="modal-header">
																							<h1 class="modal-title fs-5">${get_month_name(form['month'])} <?php echo date('Y');?> - ${client.name}</h1>
																							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																						</div>
																						<div class="modal-body">
																						<table id="tech-companies-1" class="table table-bordered">
																							<thead>
																								<tr class="">
																									<th class="p-2">День</th>
																									<th class="p-2">Часы</th>
																									<th class="p-2">Формат услуги</th>
																								</tr>
																							</thead>
																							<tbody>
																								<?php foreach (range(1,31) as $d) {?>
																									<tr class="<?php //if (is_vacation($vacations, $d) == true) {echo 'bg-qyc';} ?>">
																									<form method="post">
																										<td class="p-2"><b><?php echo $d; ?></b></td>
																										<td class="p-2">${validate_hours(form[<?php echo $d; ?>])}</td>
																										<td class="p-2">${get_online(form['online_<?php echo $d; ?>'], form[<?php echo $d; ?>])}</td>
																									</form>
																									</tr>
																									
																									<?php } ?>
																							</tbody>
																						</table>
																						</div>
																						<div class="modal-footer">
																							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
																						</div>
																						</div>
																					</div>
																				</div>`
																	$(document.body).append(modal)
																}
															}
														}
													})
												</script>
												
                                            </div></div>
        
                                        </div>
        
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div>