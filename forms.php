<?php
include('templates/header.php');
?>
<?php session_start(); 
if($_SESSION["loggedin"] != true){
    echo 'not logged in';
    header("Location: login.php");
    exit;
}
?>
<?php 
$start = microtime(true); // Начало выполнения скрипта
//Конфигурация сайта
require "config.php";
//Движок сайта
require "engine.php";
// Получаем пользователей
$users = get_all_from_table('users');
// Получаем всех клиентов
$clients = get_all_from_table('clients');
$orders = get_all_from_table('orders');
// Получаем ГУ
$gu_ids = get_all_from_table('gu_ids');
// Получаем ПСУ
// $psu_ids = get_all_from_table('psu_ids');

//Сортируем массивы по алфавиту
$clients = sort_by_name($clients);
$users = sort_by_name($users);
// $gu_ids = sort_by_name($gu_ids); // Отключено, так как ломает историю выбора чекбоксов в JS
// $psu_ids = sort_by_name($psu_ids);

$error_types = explode(',', get_setting('error_type'));

// Получаем формы
// Prepare a select statement
$sql = "SELECT * FROM forms";
$forms = array();
//Заданы параметры поиска
if (isset($_GET["year"]) and $_GET["year"] != null or isset($_GET["month"]) and $_GET["month"] != null or isset($_GET["client_id"]) and $_GET["client_id"] != null or isset($_GET["spec_id"]) and $_GET["spec_id"] != null or isset($_GET["gu_id"]) and $_GET["gu_id"] != null or isset($_GET["psu_id"]) and $_GET["psu_id"] != null) {
	if ($_SESSION['group'][0] == '%') {
		$gu_id = substr($_SESSION['group'], 1);
	} else {
		$gu_id = $_GET["gu_id"] ?? null;
	}
	$params = [
		"client_id" => $_GET["client_id"] ?? null,
		"spec_id" => $_GET["spec_id"] ?? null,
		// Если аккаунт принадлежит ГУ
		"gu_id" => $gu_id,
		"psu_id" => $_GET["psu_id"] ?? null,
		"year" => $_GET["year"] ?? null,
		"month" => $_GET["month"] ?? null,
		//"accepted" => "true" // Только подписанные формы
	];
	$forms = get_forms_by_params($params);
	if ($forms == null) {
		$forms = array();
	}
	$acts = getActs();
	//Проверяем, были ли подписаны акты у специалистов на эти месяцы
}
// Close connection
// mysqli_close($link);

if ($_SESSION['group']  != null) {
?>
    <body data-sidebar="dark">
    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

        <!-- Begin page -->
        <div id="layout-wrapper">

			<div id="alert-container"></div>


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

						<?php 
						//Добавление формы для администратора
						if ($_SESSION['group']  == 1) {
							include('templates/forms/forms_admin.php'); 
						}
						//Добавление формы для модератора
						if ($_SESSION['group']  == 3) {
							include('templates/forms/forms_moderator.php');
						}
						//Добавление формы для бухгалтера
						if ($_SESSION['group']  == 4) {
							include('templates/forms/forms_buhgalter.php');
						}
						// Формы для ГУ
						if ($_SESSION['group'][0] == '%') {
							// Таблица ГУ 
							$gu = get_gu(substr($_SESSION['group'], 1));
							include('templates/forms/forms_gu.php');
						}
						// Формы для специалиста
						if ($_SESSION['group'] == '2') {
							include('templates/forms/forms_specialist.php');
						}
						?>
                        
                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->
                
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->
        
<?php
}?>

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script src="./static/js/table2excel.js"></script>
<script src="./static/js/pinTable.js"></script>

<?php
include('templates/footer.html');
?>