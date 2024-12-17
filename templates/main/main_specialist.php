<!-- start page title -->
<div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Профиль</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                       <div class="row">

                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="card d-flex">
                                    <div class="card-body row">
										<div class="col-10">
											<i class="display-3 fa fa-user"></i>
											<hr>
											<p><br><span class="h4"> <b>ФИО:</b> <?php echo get_user($_SESSION['id'])['name']; ?></span></p>
											<hr>
											<p><br><span class="h4"> <b>Статистика:</b></span>
											<hr>
                                            <style>
                                                #counter li {
                                                    list-style: none;
                                                }
                                            </style>
											<ul id="counter">
                                                <?php 
                                                    $clients = get_spec_clients($_SESSION['id']);
                                                    $client_current_count = 0;
                                                    $client_left_count = 0;
                                                    $specialist_hours = 0;
                                                    foreach ($clients as $client) {
                                                        $client_order = get_order_by_client($client['id'], date('Y'));
                                                        if ($client_order['cancel_date'] == null || $client_order['cancel_date'] == "0000-00-00") {
                                                            $client_current_count++;
                                                            $specialist_hours += count_hours_for_spec($client['id'], $_SESSION['id'], date('Y'));
                                                        } else {
                                                            $client_left_count++;
                                                        }
                                                    }
                                                ?>
                                                <li><span class="h4"><i class="fas fa-walking"></i> Клиентов: <?php echo count($clients); ?> (на исполнении - <?php echo $client_current_count; ?>, ушедшие - <?php echo $client_left_count; ?>)</span></li>
                                                <li><span class="h4"><i class="fas fa-watch"></i> Часов переведено: <i><?php echo $specialist_hours; echo ' ч.'; ?></i></span></li>
											</ul>
											</p>
											<p><?php  ?></p>
										</div>
									</div>
                                </div>
                            </div> <!-- end col -->
                        </div>