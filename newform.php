<?php 
session_start();
require "engine.php";
//Проверка на наличие сессии
if ($_SESSION['group'] == null) {
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Создать пользователя</title>
		<!-- App favicon -->
		<link rel="shortcut icon" href="static/images/favicon.ico">

		<!-- Bootstrap Css -->
		<link href="static/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
		<!-- Icons Css -->
		<link href="static/css/icons.min.css" rel="stylesheet" type="text/css" />
		<!-- App Css-->
		<link href="static/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
		<style>
			.wrapper{
				width: 600px;
				margin: 0 auto;
			}
		</style>
		<!-- JAVASCRIPT -->
        <script src="static/libs/jquery/jquery.min.js"></script>
        <script src="static/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
	</head>
	<body>
		<div class="wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h2 class="mt-5 mb-3">Авторизуйтесь для продолжения</h2><hr>
						<form method="post">
							<div class="alert">
								<p><br>
									<a href="login.php" class="btn btn-secondary">Авторизоваться</a>
								</p>
							</div>
						</form>
					</div>
				</div>        
			</div>
		</div>
	</body>
</html>
<?php
}
?>
<?php 
//Добавление клиентов
if (($_SESSION['group']  == 1 || $_SESSION['group']  == 3) and isset($_GET['newclient'])) {
	// Include config file
	require_once "config.php";

	// Define variables and initialize with empty values
	$name = $iin = $phone = $portal_link = $gu_id = $psu = "";
	$ecp = null;
	$name_err = $iin_err = $phone_err = $gu_id_err = $psu_err = "";

	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		// Validate name
		$input_name = trim($_POST["name"]);
		if(empty($input_name)){
			$name_err = "Please enter a name.";
		} else{
			$name = $input_name;
		}
		
		// Validate iin
		$input_iin = trim($_POST["iin"]);
		if(empty($input_iin)){
			$iin_err = "Please enter an iin.";     
		} else{
			$iin = $input_iin;
		}		
		// Validate portal_link
		$input_portal_link = trim($_POST["portal_link"]);
		if(empty($input_portal_link)){
			$portal_link_err = "Please enter an portal_link.";     
		} else{
			$portal_link = $input_portal_link;
		}		
		// Validate phone
		$input_phone = trim($_POST["phone"]);
		if(empty($input_phone)){
			$phone_err = "Please enter an phone.";     
		} else{
			$phone = $input_phone;
		}		
		
		$input_ecp = trim($_POST["ecp"]);
		$input_ecp_online = trim($_POST["ecp_online"]);

		// Check input errors before inserting in database
		if(empty($name_err)){
			// Prepare an insert statement
			$sql = "INSERT INTO clients (name, iin, phone, link, ecp, ecp_online) VALUES (?, ?, ?, ?, ?, ?)";
			 
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "ssssss", $param_name, $param_iin, $param_phone, $param_portal_link, $param_ecp, $param_ecp_online);
				
				// Set parameters
				$param_name = $name;
				$param_iin = $iin;
				$param_phone = $phone;
				$param_portal_link = $portal_link;
				$param_ecp = $ecp;
				$param_ecp_online = $ecp_online;
				
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Records created successfully. Redirect to landing page
					header("location: clients.php");
					exit();
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}
			}
			
			// Close statement
			mysqli_stmt_close($stmt);
		}
		
		// Close connection
		mysqli_close($link);
	}
?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Создать клиента</title>
		<!-- App favicon -->
		<link rel="shortcut icon" href="static/images/favicon.ico">

		<!-- Bootstrap Css -->
		<link href="static/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
		<!-- Icons Css -->
		<link href="static/css/icons.min.css" rel="stylesheet" type="text/css" />
		<!-- App Css-->
		<link href="static/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
		<style>
			.wrapper{
				width: 600px;
				margin: 0 auto;
			}
		</style>
		<!-- JAVASCRIPT -->
		<script src="static/libs/jquery/jquery.min.js"></script>
        <script src="static/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
	</head>
	<body>
		<div class="wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h2 class="mt-5">Создать запись в журнале</h2>
						<p>Введите и подтвердите данные для отправки</p>
						<form method="post">
							<div class="form-group">
								<label>ФИО</label>
								<input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
								<span class="invalid-feedback"><?php echo $name_err;?></span>
							</div>
							<div class="form-group">
								<label>ИИН</label>
								<input type="text" name="iin" class="form-control" value="<?php echo $iin; ?>">
								<span class="text-danger"><?php echo $iin_err;?></span>
							</div>
							<div class="form-group">
								<label>Моб. телефон</label>
								<input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>">
								<span class="invalid-feedback"><?php echo $phone_err;?></span>
							</div>
							<div class="form-group">
								<label>Ссылка на портал</label>
								<input name="portal_link" class="form-control <?php echo (!empty($portal_link_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $portal_link; ?>">
								<span class="invalid-feedback"><?php echo $portal_link_err;?></span>
							</div>
							<div class="form-group">
								<label>Срок ЭЦП</label>
								<div class="d-flex">
									<input type="date" name="ecp" class="col form-control" value="<?php echo $ecp; ?>">
									<a class="col-1 btn btn-outline-danger mx-2" onclick="console.log('ok'); postFormAsync({'delEcp': <?php echo $client_id_once;?>}).then(data => console.log(data)).then(() => location.reload())"><i class="fas fa-trash"></i></a>
								</div>
								<div class="form-check border">
									<input class="form-check-input" type="checkbox" name="ecp_online" id="ecp_online">
									<label class="form-check-label" for="ecp_online">Удаленно</label>
								</div>
							</div>
							<div class="form-group mt-3 mb-3">
							<input type="submit" class="btn btn-primary" value="Создать">
							<a href="clients.php" class="btn btn-secondary ml-2">Отмена</a>
							</div>
						</form>
						<script>
							$(document).ready(() => {
								// Удаленно ли ЭЦП
								let ecp_online = "<?php echo ($ecp_online) ? $ecp_online : 'undefined'; ?>";
								if (ecp_online == 'on') {
									$('input[name="ecp_online"]').prop('checked', true);
								}

								// Проверяем, существует ли введенный ИИН и блокируем кнопку
								$('input[name="iin"]').change(async (event) => {
									let params = {"getClientByIIN": event.target.value};
									let orders = getFormAsync(params).then(
										(data) => JSON.parse(data)
									).then((orders) => {
										console.log(orders)
										if (orders) {
											$('input[type="submit"]').prop( "disabled", true );
											$('input[name="iin"]').parent().find('span').text("Такой клиент с ИИН уже существует!")
											$('input[name="iin"]').toggleClass('btn-danger', true);
										} else {
											$('input[type="submit"]').prop( "disabled", false );
											$('input[name="iin"]').parent().find('span').text("")
											$('input[name="iin"]').toggleClass('btn-danger', false);
										}
									})
								})
							})
						</script>
					</div>
				</div>        
			</div>
		</div>
	</body>
	</html>
	<?php
include('templates/footer.html');
}
?>
<?php 
//Добавление пользователя
if (($_SESSION['group']  == 1 || $_SESSION['group']  == 3) and isset($_GET['newuser'])) {
	// Include config file
	require_once "config.php";

	// Define variables and initialize with empty values
	$username = $email = $password = $usergroup = $name = "";
	$username_err = $email_err = $password_err = $usergroup_err = $name_err = "";

	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		// Validate username
		$input_username = trim($_POST["username"]);
		if(empty($input_username)){
			$username_err = "Please enter a username.";
		} else{
			$username = $input_username;
		}
		
		// Validate email
		$input_email = trim($_POST["email"]);
		if (empty($input_email)){
			$email_err = "Please enter an email.";     
		} else{
			$email = $input_email;
		}
		// Validate password
		$input_password = trim($_POST["password"]);
		if(empty($input_password)){
			$password_err = "Please enter an password.";     
		} else{
			$password = $input_password;
		}	
		// Validate usergroup
		$input_usergroup = trim($_POST["usergroup"]);
		if(empty($input_usergroup)){
			$usergroup_err = "Please enter a usergroup.";
		} else{
			$usergroup = $input_usergroup;
		}		
		// Validate name
		$input_name = trim($_POST["name"]);
		if(empty($input_name)){
			$name_err = "Please enter a name.";
		} else{
			$name = $input_name;
		}

		// Отправляем в gu_ids нового ГУ
		if ($usergroup == 4) {
			sendRowToTable("gu_ids", ["name" => $name]);
		}

		// Check input errors before inserting in database
		if(empty($name_err)){
			// Prepare an insert statement
			$sql = "INSERT INTO users (username, email, password, usergroup, name) VALUES (?, ?, ?, ?, ?)";
			 
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_email, $param_password, $param_usergroup, $param_name);
				$hash_password = password_hash($password, PASSWORD_DEFAULT);
				// Set parameters
				$param_username = $username;
				$param_email = $email;
				$param_password = $hash_password;
				if ($usergroup != 4) {
					$param_usergroup = $usergroup;
				} else {
					$param_usergroup = "%" . getLastGUID()+1;
				}
				$param_name = $name;
				
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Records created successfully. Redirect to landing page
					header("location: users.php");
					exit();
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}
			}
			
			// Close statement
			mysqli_stmt_close($stmt);
		}
		
		// Close connection
		mysqli_close($link);
	}
?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Создать пользователя</title>
		<!-- App favicon -->
		<link rel="shortcut icon" href="static/images/favicon.ico">

		<!-- Bootstrap Css -->
		<link href="static/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
		<!-- Icons Css -->
		<link href="static/css/icons.min.css" rel="stylesheet" type="text/css" />
		<!-- App Css-->
		<link href="static/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
		<style>
			.wrapper{
				width: 600px;
				margin: 0 auto;
			}
		</style>
	</head>
	<body>
		<div class="wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h2 class="mt-5">Создать запись в журнале</h2>
						<p>Введите и подтвердите данные для отправки</p>
						<form method="post">
							<div class="form-group">
								<label>Логин</label>
								<input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
								<span class="invalid-feedback"><?php echo $username_err;?></span>
							</div>
							<div class="form-group">
								<label>E-mail</label>
								<input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
								<span class="invalid-feedback"><?php echo $email_err;?></span>
							</div>
							<div class="form-group">
								<label>Пароль</label>
								<input type="text" name="password" class="form-control" value="<?php echo $password; ?>">
								<span class="invalid-feedback"><?php echo $password_err;?></span>
							</div>
							<div class="form-group">
								<label>Группа</label>
								<select name="usergroup" class="form-select">
									<option value="2" selected>Специалист</option>
									<option value="1">Администратор</option>
									<option value="3">Модератор</option>
									<option value="4">ГУ</option>
									<option value="5">Завсклад</option>
								</select>
								<span class="invalid-feedback"><?php echo $usergroup_err;?></span>
							</div>
							<div class="form-group">
								<label>ФИО <i>(для ГУ на русском пишем его название капсом. Пример: АЛМАТЫ)</i></label>
								<input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
								<span class="invalid-feedback"><?php echo $name_err;?></span>
							</div>
							<div class="form-group mt-3 mb-3">
							<input type="submit" class="btn btn-primary" value="Создать">
							<a href="users.php" class="btn btn-secondary ml-2">Отмена</a>
							</div>
						</form>
						
					</div>
				</div>        
			</div>
		</div>
	</body>
	</html>
<?php } ?>
<?php 
//Добавление заказа
if (($_SESSION['group']  == 1 || $_SESSION['group']  == 3) and isset($_GET['neworder'])) {
	// Include config file
	require_once "config.php";
	// Prepare a select statement
	$sql = "SELECT * FROM users";
	// Получаем пользователей
	$users = get_all_from_table('users');
	// Получаем всех клиентов
	$clients = get_all_from_table('clients');
	// Получаем ГУ
	$gu_ids = get_all_from_table('gu_ids');
	// Получаем ПСУ
	$psu_ids = get_all_from_table('psu_ids');

	//Сортируем массивы по алфавиту
	$clients = sort_by_name($clients);
	$users = sort_by_name($users);
	$gu_ids = sort_by_name($gu_ids);
	$psu_ids = sort_by_name($psu_ids);
	// Define variables and initialize with empty values
	$order_num = $order_date = $client_id = $specialists = $order_status = $begin_hours = $cancel_date = $cancel_reason = "";
	$order_num_err = $order_date_err = $client_id_err = $specialists_err = $begin_hours_err = $order_status_err = $cancel_date_err = $cancel_reason_err = "";
	$date = date("Y-m-d");
	
	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		// Validate order_num
		$input_order_num = trim($_POST["order_num"]);
		if(empty($input_order_num)){
			$order_num_err = "Please enter a order_num.";
		} else{
			$order_num = $input_order_num;
		}
		// Validate order_date
		$input_order_date = trim($_POST["order_date"]);
		if(empty($input_order_date)){
			$order_date_err = "Please enter a order_date.";
		} else{
			$order_date = $input_order_date;
		}
		
		// Validate client_id
		$input_client_id = trim($_POST["client_id"]);
		if (empty($input_client_id)){
			$client_id_err = "Please enter an client_id.";     
		} else{
			$client_id = $input_client_id;
		}
		// Validate specialists
		$input_specialists = trim($_POST["specialists"]);
		$input_specialists1 = trim($_POST["specialists1"]);
		if(empty($input_specialists)){
			$specialists_err = "Please enter an specialists.";     
		} else{
			if(empty($input_specialists1)){
				$specialists = $input_specialists;
			} else {
				if ($input_specialists == $input_specialists1) {
					$specialists_err = "Нельзя назначить специалиста дважды.";
				} else {
					$specialists = ''.$input_specialists.' '.$input_specialists1.'';
				}
			}
		}
		// Validate order_status
		$input_order_status = trim($_POST["order_status"]);
		if(empty($input_order_status)){
			$order_status_err = "Please enter an order_status.";     
		} else{
			$order_status = $input_order_status;
		}	
		// Address
		$address = trim($_POST["address"]);	
		// Validate begin_hours
		$input_begin_hours = trim($_POST["begin_hours"]);
		if(empty($input_begin_hours)){
			$begin_hours_err = "Please enter a begin_hours.";
		} else{
			$begin_hours = $input_begin_hours;
		}		
		// Validate cancel_date
		$input_cancel_date = trim($_POST["cancel_date"]);
		if(empty($input_cancel_date)){
			$cancel_date_err = "Please enter a cancel_date.";
		} else{
			$cancel_date = $input_cancel_date;
		}		
		// Validate cancel_reason
		$input_cancel_reason = trim($_POST["cancel_reason"]);
		if(empty($input_cancel_reason)){
			$cancel_reason_err = "Please enter a cancel_reason.";
		} else{
			$cancel_reason = $input_cancel_reason;
		}		
		// Validate gu_id
		$input_gu_id = trim($_POST["gu_id"]);
		if(empty($input_gu_id)){
			$gu_id_err = "Please enter a gu_id.";
		} else{
			$gu_id = $input_gu_id;
		}		
		// Validate psu_id
		$input_psu_id = trim($_POST["psu_id"]);
		if(empty($input_psu_id)){
			$psu_id_err = "Please enter a psu_id.";
		} else{
			$psu_id = $input_psu_id;
		}
		
		// ORDER_STATUS 1
		if($_POST['order_status'] == 1 && empty($specialists_err)){
			// Prepare an insert statement
			$sql = "INSERT INTO orders (order_num, order_date, client_id, specialists, address, order_status, begin_hours, gu_id, psu_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
			 
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "sssssssss", $param_order_num, $param_order_date, $param_client_id, $param_specialists, $param_address, $param_order_status, $param_begin_hours, $param_gu_id, $param_psu_id);
				// Set parameters
				$param_order_num = $order_num;
				$param_order_date = $order_date;
				$param_client_id = $client_id;
				$param_specialists = $specialists;
				$param_address = $address;
				$param_order_status = $order_status;
				$param_begin_hours = $begin_hours;
				$param_gu_id = $gu_id;
				$param_psu_id = $psu_id;
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Records created successfully. Redirect to landing page
					header("location: orders.php");
					exit();
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}
			}
			
			// Close statement
			mysqli_stmt_close($stmt);
		}
		// ORDER_STATUS 2
		if($_POST['order_status'] == 2 && empty($specialists_err)){
			// Prepare an insert statement
			$sql = "INSERT INTO orders (order_num, order_date, client_id, specialists, order_status, address, begin_hours, cancel_date, cancel_reason, gu_id, psu_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			 
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "sssssssssss", $param_order_num, $param_order_date, $param_client_id, $param_specialists, $param_address, $param_order_status, $param_begin_hours, $param_cancel_date, $param_cancel_reason, $param_gu_id, $param_psu_id);
				// Set parameters
				$param_order_num = $order_num;
				$param_order_date = $order_date;
				$param_client_id = $client_id;
				$param_specialists = $specialists;
				$param_address = $address;
				$param_order_status = $order_status;
				$param_begin_hours = $begin_hours;
				$param_cancel_date = $cancel_date;
				$param_cancel_reason = $cancel_reason;
				$param_gu_id = $gu_id;
				$param_psu_id = $psu_id;
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Records created successfully. Redirect to landing page
					header("location: orders.php");
					exit();
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}
			}
			
			// Close statement
			mysqli_stmt_close($stmt);
		}
		
		// Close connection
		mysqli_close($link);
	}
?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Создать заказ</title>
		<!-- App favicon -->
		<link rel="shortcut icon" href="static/images/favicon.ico">

		<!-- Bootstrap Css -->
		<link href="static/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
		<!-- Icons Css -->
		<link href="static/css/icons.min.css" rel="stylesheet" type="text/css" />
		<!-- App Css-->
		<link href="static/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
		<style>
			.wrapper{
				width: 600px;
				margin: 0 auto;
			}
			#cancel_block {
				display: none;
			}
		</style>
		<!-- JAVASCRIPT -->
        <script src="static/libs/jquery/jquery.min.js"></script>
        <script src="static/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
	</head>
	<body>
		<div class="wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h2 class="mt-5">Создать запись в журнале</h2>
						<p>Введите и подтвердите данные для отправки</p>
						<form method="post">
							<div class="form-group">
								<label>Номер заказа</label>
								<input type="text" name="order_num" class="form-control" value="<?php echo $order_num; ?>">
								<span class="text-danger"><?php echo $order_num_err;?></span>
							</div>
							<div class="form-group">
								<label>Дата заказа</label>
								<input type="date" name="order_date" class="form-control <?php echo (!empty($order_date_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $date; ?>">
								<span class="invalid-feedback"><?php echo $order_date_err;?></span>
							</div>
							<div class="form-group">
								<label>ФИО клиента</label>
								<select class="form-select" name="client_id">
									<option selected>Выберите клиента</option>
									<?php
									foreach ($clients as $c) {
										echo '<option value="'.$c['id'].'">'. $c['name'] .'</option>';
									}
									?>
								</select>
								<span class="invalid-feedback"><?php echo $client_id_err;?></span>
							</div>
							<div class="form-group">
								<label>Специалисты</label>
								<p><span class="invalid-feedback"><?php echo $specialists_err;?></span></p>
								<select class="form-select" name="specialists">
									<option selected>Выберите специалиста</option>
									<?php
									foreach ($users as $s) {
										if ($s['usergroup'] == 2) {
											echo '<option value="'.$s['id'].'">'. $s['name'] .'</option>';
										}
									}
									?>
								</select><br>
								<select class="form-select" name="specialists1">
									<option value="" selected>Выберите второго специалиста (если такой есть)</option>
									<?php
									foreach ($users as $s) {
										if ($s['usergroup'] == 2) {
											echo '<option value="'.$s['id'].'">'. $s['name'] .'</option>';
										}
									}
									?>
								</select>
							</div>
							<div class="form-group">
								<label>Адрес</label>
								<input name="address" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $address; ?>">
								<span class="invalid-feedback"><?php echo $address_err;?></span>
							</div>
							<div class="form-group"><br>
								<label>Количество часов</label>
								<input type="text" name="begin_hours" class="form-control" value="60">
								<span class="invalid-feedback"><?php echo $begin_hours_err;?></span>
							</div>
							<div class="form-group">
								<label>Статус заказа</label>
							</div>
							<div class="form-group">
								<select class="form-select" name="order_status" id="order_status_1">
									<option value="1" selected>Активен</option>
									<option value="2">Отменен</option>
								</select>
							</div>
							
							<script>
								const alertStatus = (e) => {
								  var radio = $('#order_status_1').find(":selected").val();
								  if( radio == 1 ){
									$('#cancel_block').css("display", "none");
								  }
								  if( radio == 2 ){
									$('#cancel_block').css("display", "block");
								  }
								};
								$(document).on("click", "#order_status_1", alertStatus);
							</script>
							<div id="cancel_block">
								<div class="form-group">
									<label>Дата отмены</label>
									<input type="date" name="cancel_date" class="form-control" value="<?php echo $cancel_date; ?>">
									<span class="invalid-feedback"><?php echo $cancel_date_err;?></span>
								</div>
								<div class="form-group">
									<label>Причина отмены</label>
									<input type="text" name="cancel_reason" class="form-control" value="<?php echo $cancel_reason; ?>">
									<span class="invalid-feedback"><?php echo $cancel_reason_err;?></span>
								</div>
							</div>
							<div class="form-group">
								<label>ГУ</label>
								<select class="form-select" name="gu_id">
									<option selected>Выберите ГУ</option>
									<?php
									foreach ($gu_ids as $s) {
										echo '<option value="'.$s['id'].'">'. $s['name'] .'</option>';
									}
									?>
								</select>
								<span class="invalid-feedback"><?php echo $gu_id_err;?></span>
							</div>
							<div hidden class="form-group">
								<label>ПСУ</label>
								<select class="form-select" name="psu_id" hidden>
									<option selected value="1">Алматы</option>
									<?php
									foreach ($psu_ids as $s) {
										echo '<option value="'.$s['id'].'">'. $s['name'] .'</option>';
									}
									?>
								</select>
								<span class="invalid-feedback"><?php echo $psu_id_err;?></span>
							</div>
							<div class="form-group mt-3 mb-3">
							<input type="submit" class="btn btn-primary" value="Создать">
							<a href="orders.php" class="btn btn-secondary ml-2">Отмена</a>
							</div>
						</form>
						<script>
							// Проверяем, существует ли введенный номер заказа и блокируем кнопку
							$(document).ready(() => {
								$('input[name="order_num"]').change(async (event) => {
									let params = {"getOrderByNum": event.target.value};
									let orders = getFormAsync(params).then(
										(data) => JSON.parse(data)
									).then((orders) => {
										console.log(orders)
										if (orders) {
											$('input[type="submit"]').prop( "disabled", true );
											$('input[name="order_num"]').parent().find('span').text("Такой заказ уже существует!")
											$('input[name="order_num"]').toggleClass('btn-danger', true);
										} else {
											$('input[type="submit"]').prop( "disabled", false );
											$('input[name="order_num"]').parent().find('span').text("")
											$('input[name="order_num"]').toggleClass('btn-danger', false);
										}
									})
								})
							})
						</script>
					</div>
				</div>        
			</div>
		</div>
		
	</body>
	</html>
<?php
include('templates/footer.html');
}
?>
<?php
//Выбор клиента перед добавлением формы
if (($_SESSION['group']  == 1 || $_SESSION['group']  == 3) and isset($_GET['selectclient'])) {
	// Include config file
	require_once "engine.php";
	require_once "config.php";
	// Prepare a select statement
	$sql = "SELECT * FROM clients";
	$clients = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				array_push($clients, $row);
			}
			// Free result set
			mysqli_free_result($result);
		} else{
			echo "No records matching your query were found.";
		}
	} else{
		echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
	}
	//Сортируем массивы по алфавиту
	$clients = sort_by_name($clients);

	if($_GET['selectclient'] > 0){
		//Проверяем был ли создан заказ
		if (exist_order($_GET['selectclient'], date('Y')) == 1) {
			// Проверяем есть ли достаточное кол-во часов на клиента
			if (count_hours($_GET['selectclient'], date('Y')) < get_setting('max_hours') or count_hours($_GET['selectclient'], date('Y')) == null) {
				header("location: newform.php?newform=".$_GET['selectclient']);
				exit();
			} else {
				alert('У этого клиента закончились часы!', 'forms.php');
			}
		} else {
			alert('На этого клиента нет заказов в этом году!', 'forms.php');
		}
	}
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		//Проверяем был ли создан заказ
		if (exist_order($_POST['client_id'], date('Y')) == 1) {
			// Проверяем есть ли достаточное кол-во часов на клиента
			if (count_hours($_POST['client_id'], date('Y')) < get_setting('max_hours') or count_hours($_POST['client_id'], date('Y')) == null) {
				header("location: newform.php?newform=".$_POST['client_id']);
				exit();
			} else {
				alert('У этого клиента закончились часы!', 'newform.php?selectclient=0');
			}
		} else {
			alert('На этого клиента нет заказов в этом году!', 'newform.php?selectclient=0');
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Создать пользователя</title>
		<!-- App favicon -->
		<link rel="shortcut icon" href="static/images/favicon.ico">

		<!-- Bootstrap Css -->
		<link href="static/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
		<!-- Icons Css -->
		<link href="static/css/icons.min.css" rel="stylesheet" type="text/css" />
		<!-- App Css-->
		<link href="static/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
		<style>
			.wrapper{
				width: 600px;
				margin: 0 auto;
			}
		</style>
		<!-- JAVASCRIPT -->
        <script src="static/libs/jquery/jquery.min.js"></script>
        <script src="static/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
	</head>
	<body>
		<div class="wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h2 class="mt-5 mb-3">Выберите клиента</h2><hr>
						<form method="post">
							<div class="alert">
								<select class="form-select" name="client_id">
									<option selected>Выберите клиента</option>
									<?php
									foreach ($clients as $c) {
										echo '<option value="'.$c['id'].'">'. $c['name'] .'</option>';
									}
									?>
								</select>
								<p><br>
									<input type="submit" value="Выбрать" class="btn btn-danger">
									<a href="forms.php" class="btn btn-secondary">Отмена</a>
								</p>
							</div>
						</form>
					</div>
				</div>        
			</div>
		</div>
	</body>
</html>
<?php } ?>
<?php 
//Добавление формы
if (($_SESSION['group']  == 1 || $_SESSION['group']  == 3) and isset($_GET['newform'])) {
	// Include config file
	require "config.php";
	// require "engine.php";
	// Prepare a select statement
	$sql = "SELECT * FROM users";
	$users = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				array_push($users, $row);
			}
			// Free result set
			mysqli_free_result($result);
		} else{
			echo "No records matching your query were found.";
		}
	} else{
		echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
	}
	// Prepare a select statement
	$sql = "SELECT * FROM clients";
	$clients = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				array_push($clients, $row);
			}
			// Free result set
			mysqli_free_result($result);
		} else{
			echo "No records matching your query were found.";
		}
	} else{
		echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
	}
	// Prepare a select statement
	$sql = "SELECT * FROM orders";
	$orders = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				array_push($orders, $row);
			}
			// Free result set
			mysqli_free_result($result);
		} else{
			echo "No records matching your query were found.";
		}
	} else{
		echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
	}
	// Prepare a select statement
	$sql = "SELECT * FROM gu_ids";
	$gu_ids = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				array_push($gu_ids, $row);
			}
			// Free result set
			mysqli_free_result($result);
		} else{
			echo "No records matching your query were found.";
		}
	} else{
		echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
	}
	// Prepare a select statement
	$sql = "SELECT * FROM psu_ids";
	$psu_ids = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				array_push($psu_ids, $row);
			}
			// Free result set
			mysqli_free_result($result);
		} else{
			echo "No records matching your query were found.";
		}
	} else{
		echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
	}
	// Define variables and initialize with empty values
	$client_id = $_GET['newform'];
	$client_order = get_order_by_client($client_id, date('Y'));
	$order_id = $client_order['id'];
	$spec_id = $day = $month = $online = "";
	$order_id_err = $client_id_err = $spec_id_err = $day_err = $month_err = "";
	$date = date("Y-m-d");
	
	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		// Validate spec_id
		$input_spec_id = trim($_POST["spec_id"]);
		if(empty($input_spec_id)){
			$spec_id_err = "Please enter a spec_id.";
		} else{
			$spec_id = $input_spec_id;
		}
		// Validate hours
		$input_hours = trim($_POST["hours"]);
		if(empty($input_hours)){
			$hours_err = "Please enter a hours.";
		} else{
			$hours = $input_hours;
		}
		// Validate online
		$input_online = trim($_POST["online"]);
		if(empty($input_online)){
			$online_err = "Please enter a online.";
		} else{
			$online = $input_online;
		}
		// Validate day
		$input_day = trim($_POST["day"]);
		if(empty($input_day)){
			$day_err = "Please enter a day.";
		} else{
			$day = date_format(date_create($input_day), 'd');
			$month = date_format(date_create($input_day), 'm');
		}
		// Проверяем, есть ли уже форма на этот месяц
		$forms = get_forms_by_order($order_id);
		$form = 0;
		foreach ($forms as $f) {
			if ($f['spec_id'] == $spec_id && $f['month'] == $month) {
				$form = $f;
				break;
			}
		}
		if (is_order_active($order_id) == 2) {
			alert('Заказ на этого клиента был отменен!', 'forms.php');
		}
		//Если форма на месяц не была создана
		if ($form == 0) {
			$sql = "INSERT INTO forms (order_id, client_id, spec_id, `".intval($day)."`, `online_".intval($day)."`, month) VALUES (?, ?, ?, ?, ?, ?)";
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
					header("location: forms.php");
					exit();
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}
				
				// Close statement
				mysqli_stmt_close($stmt);
			}
		} else if ($form != 0) {
			//Если форма на месяц была создана
			$sql = "UPDATE forms SET `".intval($day)."`=?, `online_".intval($day)."`=? WHERE id = ".intval($form['id']);
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "ss", $param_day, $param_online);
				// Set parameters
				$param_day = $hours;
				$param_online = $online;

				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Records created successfully. Redirect to landing page
					header("location: forms.php");
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
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Создать отчет</title>
		<!-- App favicon -->
		<link rel="shortcut icon" href="static/images/favicon.ico">

		<!-- Bootstrap Css -->
		<link href="static/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
		<!-- Icons Css -->
		<link href="static/css/icons.min.css" rel="stylesheet" type="text/css" />
		<!-- App Css-->
		<link href="static/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
		<style>
			.wrapper{
				width: 600px;
				margin: 0 auto;
			}
		</style>
		<!-- JAVASCRIPT -->
        <script src="static/libs/jquery/jquery.min.js"></script>
        <script src="static/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
	</head>
	<body>
		<div class="wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h2 class="mt-5">Создать запись в журнале </h2>
						<p>Введите и подтвердите данные для отправки</p>
						<form method="post">
							<div class="form-group">
								<label>ФИО клиента</label>
								<input type="text" class="form-control" value="<?php foreach($clients as $c){ if ($c['id'] == $client_order['client_id']) {echo $c['name'];}} ?>" disabled>
								<input type="text" name="client_id" class="form-control" value="60" hidden>
								<span class="invalid-feedback"><?php echo $client_id_err;?></span>
							</div>
							<div class="form-group">
								<label>Специалисты</label>
								<select class="form-select" name="spec_id" required>
									<option selected value="">Выберите специалиста</option>
									<?php
									
									foreach (explode(' ',$client_order['specialists']) as $co) {
										//Выводит ФИО специалиста из ID  в заказе
										echo '<option value="'.get_user($co)["id"].'">'. get_user($co)["name"] .'</option>';
									}
									?>
								</select>
								<span class="invalid-feedback"><?php echo $spec_id_err;?></span>
							</div>
							<div class="form-group">
								<label>Дата услуги</label>
								<input type="date" name="day" class="form-control" value="<?php echo date('Y-m-d');?>" min="<?php echo $client_order['order_date'];?>" max="<?php echo date('Y-m-d');?>">
								<span class="invalid-feedback"><?php echo $client_id_err;?></span>
							</div>
							<div class="form-group">
								<label>Количество часов</label>
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
							<div class="form-group">
								<label>Вид услуги</label>
							</div>
							<div class="form-group">
								<select class="form-select" name="online" id="online">
									<option value="1">Онлайн</option>
									<option value="2" selected>Оффлайн</option>
								</select>
							</div>
							<div class="form-group mt-3 mb-3">
							<input type="submit" class="btn btn-primary" value="Создать">
							<a href="forms.php" class="btn btn-secondary ml-2">Отмена</a>
							</div>
						</form>
					</div>
				</div>        
			</div>
		</div>

	</body>
	</html>
<?php } ?>