<div class="col-12">
							<div class="card">
								<div class="card-body">              
									<div class="table-rep-plugin">
										<div class="table-wrapper">
											<div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
											<script>
												// Получаем массив заказов для колонки "Привязанные заказы"
												$(document).ready(async () => {
													let params = {"getTable": "orders"};
													let orders = await getFormAsync(params).then(
														(data) => JSON.parse(data)
													).then((orders) => {
														let clients = $('td.client_orders');
														for (let client of clients) {
															console.log(client)
															for (let order of orders) {
																if (client.parentNode.dataset.clientId == order['client_id']) {
																	// client.textContent = "";
																	background = (order['order_status'] == 1) ? 'bg-secondary' : 'bg-danger';
																	client.style.fontSize = "18px";
																	client.innerHTML += `<span class="badge ${background} mx-1">${order['order_num']}</span>`;
																}
															}
														}
													})
												})

												// Проверяем срок ЭЦП клиентов
												$(document).ready(() => {
													let params = {"getTable": "clients"};
													let clients = getFormAsync(params).then(
														(data) => JSON.parse(data)
													).then((clients) => {
														let date = Date.now();
														for (let client of clients) {
															$(`tr[data-client-id="${client.id}"]`).attr(`data-sortpriority`, 3);
															let client_date = Date.parse(client['ecp']);
															// Разворачиваем дату. По просьбе клиента
															let ecp = client.ecp;
															if (client.ecp) {
																ecp = ecp.split('-');
																ecp = `${ecp[2]}-${ecp[1]}-${ecp[0]}`;
															}
															let ecp_online = (client.ecp_online == 'on') ? '<h5 class="my-0"><span class="badge bg-secondary">Удаленно</span></h5>' : ''
															$(`tr[data-client-id="${client.id}"] > td.client_ecp`).append(ecp_online);
															// Просроченный ЭЦП
															if (client_date - date <= 0) {
																$(`tr[data-client-id="${client.id}"]`).attr(`data-sortpriority`, 2);
																$(`tr[data-client-id="${client.id}"] > td.client_ecp`).text(``);
																$(`tr[data-client-id="${client.id}"] > td.client_ecp`).append(`<div class="alert alert-danger my-0 p-0 d-flex" style="max-width: 300px;" role="alert">${ecp}&nbsp;&nbsp;${ecp_online}</div>`);
															}
															// 7 дней до просрочки ЭЦП
															if (client_date - date <= 604800000 && client_date - date > 0) {
																$(`tr[data-client-id="${client.id}"]`).attr(`data-sortpriority`, 2);
																$(`tr[data-client-id="${client.id}"] > td.client_ecp`).text(``);
																$(`tr[data-client-id="${client.id}"] > td.client_ecp`).append(`<div class="alert alert-warning my-0 p-0 d-flex" style="max-width: 300px;" role="alert">${ecp}&nbsp;&nbsp;${ecp_online}</div>`);
															}
														}
													})
												});
											</script>
											<table id="tech-companies-1" class="table table-striped table-bordered">
												<thead>
												<tr>
													<th># <i class="fas fa-sort" onClick="sortTable('tech-companies-1', 0);"></i></th>
													<th>ФИО <i class="fas fa-sort" onClick="sortTable('tech-companies-1', 1);"></i></th>
													<th>Привязанные заказы</th>
													<th>ИИН</th>
													<th>Телефон</th>
													<th>Срок ЭЦП <i class="fas fa-sort" onClick="sortTable('tech-companies-1', 5);"></i></th>
													<th>Настройка</th>
												</tr>
												</thead>
												<tbody>
												<?php
												$id_count = 0;
												foreach ($clients as &$u) {
													$id_count++;
													?>
												<tr data-client-id="<?php echo $u['id']; ?>">
													<td><?php echo $id_count; ?></td>
													<td><a href="<?php if ($_SESSION['group'] == 1) {echo "?dossier=" . $u['id'];} else {echo '#';} ?>"><?php echo $u['name']; ?></a></td>
													<!-- Выводим заказы клиента через JS API -->
													<td class="client_orders"></td>
													<td><?php echo $u['iin']; ?></td>
													<td><?php echo $u['phone']; ?></td>
													<td class="client_ecp"><?php 
														if ($u['ecp']) { $ecp = explode('-', $u['ecp']); echo $ecp[2] . '-' . $ecp[1] . '-' . $ecp [0]; } else {echo '-------';} ?></td>
													<td>
														<!-- <a href="building.php" title="Посмотреть" data-toggle="tooltip"><span class="fa fa-eye"></span></a> -->
														<a href="sendform.php?editclient=<?php echo $u['id'];?>" class="ms-3" title="Редактировать" data-toggle="tooltip"><span class="fa fa-pen"></span></i></a>
														<a href="deleteform.php?editclient=<?php echo $u['id'];?>" class="ms-3" title="Удалить" data-toggle="tooltip"><span class="fa fa-trash"></span></i></a>
													</td>
												</tr><?php } ?>
												</tbody>
											</table>
										</div></div>
									</div>
								</div>
							</div>
						</div> <!-- end col -->