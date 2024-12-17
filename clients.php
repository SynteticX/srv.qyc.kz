<?php
include('templates/header.php');
?>
<?php 
// Include config file
require "config.php";
require "engine.php";
// Получаем всех клиентов
$clients = get_all_from_table('clients');
// Получаем ГУ
$gu_ids = get_all_from_table('gu_ids');
// Получаем ПСУ
$psu_ids = get_all_from_table('psu_ids');

//Сортировка по алфавиту
$clients = sort_by_name($clients);
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

					<!-- start page title -->
					<div class="row">
						<div class="col-12">
							<div class="page-title-box d-sm-flex align-items-center justify-content-between">
								<h4 class="mb-sm-0">Управление клиентами</h4>
								<a role="button" href="/newform.php?newclient=1" class="btn btn-outline-info"><i class="fas fa-plus"></i> Добавить клиента</a>
							</div>
						</div>
					</div>
					<div class="row">
						
						<?php 
						//Вывод для администратора и модератора
						if ($_SESSION['group']  == 1 || $_SESSION['group']  == 3) {
							if ($_GET['dossier']) {
								$client_id = $_GET["dossier"];
								include('templates/clients/clients_dossier.php'); 
							} else {
								include('templates/clients/clients_admin.php');  
							}
						}
						//Вывод для специалиста
						else if ($_SESSION['group'] == 2) {
							include('templates/clients/clients_specialist.php');  
						}
						?>
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
?>