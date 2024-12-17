<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php
//Редактирование клиента
if (($_SESSION['group']  == 1 || $_SESSION['group']  == 3) and isset($_GET['editclient'])) {
	// Include config file
	require_once "config.php";
	require "engine.php";
	$client_id_once = $_GET['editclient'];
	// Prepare a select statement
	$sql = "SELECT * FROM clients WHERE id = $client_id_once";
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

	foreach ($clients as $client) {
		if ($client['id'] == $_GET['editclient']) {
			$clients = $client;
			break;
		}
	}
	// Define variables and initialize with empty values
	$name = $clients['name'];
	$iin = $clients['iin'];
	$phone = $clients['phone'];
	$portal_link = $clients['link'];
	// Фикс, чтобы ЭЦП можно было вернуть к null
	$ecp = null;
	if (!$clients['ecp'] == '') {
		$ecp = $clients['ecp'];
	}
	$ecp_online = $clients['ecp_online'];
	$name_err = $iin_err = $phone_err = $ecp_err = "";

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
		if(empty($input_ecp)){
			$ecp_err = "Please enter an phone."; 
			$ecp = null;
		} else{
			if ($ecp = '') {
				$ecp = null;
			} else {
				$ecp = $input_ecp;
			}
		}

		$ecp_online = trim($_POST["ecp_online"]);

		// Check input errors before inserting in database
		if(empty($name_err)){
			// Prepare an insert statement
			$sql = "UPDATE clients SET name=?, iin=?, phone=?, link=?, ecp=?, ecp_online=? WHERE id=?";
			 
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "ssssssi", $param_name, $param_iin, $param_phone, $param_portal_link, $param_ecp, $param_ecp_online, $param_id);
				
				// Set parameters
				$param_name = $name;
				$param_iin = $iin;
				$param_phone = $phone;
				$param_portal_link = $portal_link;
				$param_ecp = $ecp;
				$param_ecp_online = $ecp_online;
				$param_id = $client_id_once;
				
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
		<script src="static/libs/jquery/jquery.min.js"></script>
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
								<span class="invalid-feedback"><?php echo $iin_err;?></span>
							</div>
							<div class="form-group">
								<label>Моб. телефон</label>
								<input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>">
								<span class="invalid-feedback"><?php echo $phone_err;?></span>
							</div>
							<div class="form-group">
								<label>Ссылка на портал</label>
								<input name="portal_link" class="form-control <?php echo (!empty($portal_link_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $portal_link; ?>">
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
							// Кликаем чекбокс удаленно, если указано в БД
							$(document).ready(() => {
								let ecp_online = "<?php echo ($ecp_online) ? $ecp_online : 'undefined'; ?>";
								if (ecp_online == 'on') {
									$('input[name="ecp_online"]').prop('checked', true);
								}
							});
						</script>
					</div>
				</div>        
			</div>
		</div>
		<script src="./static/js/engine.js"></script>
<?php } ?>

<?php
//Редактирование пользователя
if (($_SESSION['group']  == 1 || $_SESSION['group']  == 3) and isset($_GET['edituser'])) {
	// Include config file
	require_once "config.php";
	require_once "engine.php";
	$user_id_once = $_GET['edituser'];
	// Prepare a select statement
	$sql = "SELECT * FROM users WHERE id = $user_id_once";
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

	foreach ($users as $user) {
		if ($user['id'] == $_GET['edituser']) {
			$users = $user;
			break;
		}
	}
	// Define variables and initialize with empty values
	$username = $user['username'];
	$email = $user['email'];
	$password = $user['password'];
	$reg_date = $user['reg_date'];
	$usergroup = $user['usergroup'];
	$name = $user['name'];
	$username_err = $email_err = $password_err = $reg_date_err = $name_err = "";

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
		// Validate reg_date
		$input_reg_date = trim($_POST["reg_date"]);
		if(empty($input_reg_date)){
			$reg_date_err = "Please enter an reg_date.";     
		} else{
			$reg_date = $input_reg_date;
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

		// Check input errors before inserting in database
		if(empty($name_err)){
			// Prepare an insert statement
			$sql = "UPDATE users SET username=?, email=?, password=?, reg_date=?, usergroup=?, name=? WHERE id=?";
			 
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "ssssssi", $param_username, $param_email, $param_password, $param_reg_date, $param_usergroup, $param_name, $param_id);
				
				// Set parameters
				$param_username = $username;
				$param_email = $email;
				if ($password != $user['password']) {
					$hash_password = password_hash($password, PASSWORD_DEFAULT);
					$param_password = $hash_password;
				} else {
					$param_password = $user['password'];
				}
				$param_reg_date = $reg_date;
				if ($usergroup != 4) {
					$param_usergroup = $usergroup;
				} else {
					$param_usergroup = "%" . getLastGUID()+1;
				}
				$param_name = $name;
				$param_id = $user_id_once;
				
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
		<title>Редактирование пользователя</title>
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
						<h2 class="mt-5">Создать запись в журнале <?php ?></h2>
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
								<label>Дата регистрации</label>
								<input type="date" name="reg_date" class="form-control <?php echo (!empty($reg_date_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $reg_date; ?>">
								<span class="invalid-feedback"><?php echo $reg_date_err;?></span>
							</div>
							<div class="form-group">
								<label>Группа</label>
								<select name="usergroup" id="usergroup" class="form-select">
									<option value="2" selected>Специалист</option>
									<option value="1">Администратор</option>
									<option value="3">Модератор</option>
									<option value="4">ГУ</option>
									<option value="5">Завсклад</option>
								</select>
								<span class="invalid-feedback"><?php echo $usergroup_err;?></span>
							</div>
							<div class="form-group">
								<label>ФИО</label>
								<input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
								<span class="invalid-feedback"><?php echo $name_err;?></span>
							</div>
							<div class="form-group mt-3 mb-3">
							<input type="submit" class="btn btn-primary" value="Создать">
							<a href="users.php" class="btn btn-secondary ml-2">Отмена</a>
							</div>
						<script>
							window.addEventListener('load', () => {
								$("#usergroup").val("<?php echo $usergroup; ?>").change();
							});
						</script>
						</form>
					</div>
				</div>        
			</div>
		</div>
	</body>
	</html>
<?php } ?>

<?php
//Редактирование заказа
if (($_SESSION['group']  == 1 || $_SESSION['group']  == 3) and isset($_GET['editorder'])) {
	// Include config file
	require_once "config.php";
	require_once "engine.php";
	// Получаем заказ
	$order_id_once = $_GET['editorder'];
	$order = get_order($order_id_once);
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
	$order_date = $order['order_date'];
	$client_id = $order['client_id'];
	// if (strpos($order['specialists'], ' ') !== false) {
		// $specs = explode(' ',$order['specialists']);
		// $specialists = $specs[0];
		// $specialists1 = $specs[1];
	// } else {
		// $specialists = $order['specialists'];
	// }
	$specs = parse_specialists($order['specialists']);
	if (gettype($specs) == "array") {
		$specialists = $specs[0];
		$specialists1 = $specs[1];
	} else {
		$specialists = $specs;
		$specialists1 = "";
	}
	$address = $order['address'];
	$order_status = $order['order_status'];
	$begin_hours = $order['begin_hours'];
	$cancel_date = $order['cancel_date'];
	$cancel_reason = $order['cancel_reason'];
	$gu_id = $order['gu_id'];
	$psu_id = $order['psu_id'];
	$order_date_err = $client_id_err = $specialists_err = $begin_hours_err = $order_status_err = $address_err = $cancel_date_err = $cancel_reason_err = $gu_id_err = $psu_id_err = "";
	
	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
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
				$specialists = ''.$input_specialists.' '.$input_specialists1.'';
			}
		}
		// Validate address
		$input_address = trim($_POST["address"]);
		if(empty($input_address)){
			$address_err = "Please enter an address.";     
		} else{
			$address = $input_address;
		}	
		// Validate order_status
		$input_order_status = trim($_POST["order_status"]);
		if(empty($input_order_status)){
			$order_status_err = "Please enter an order_status.";     
		} else{
			$order_status = $input_order_status;
		}		
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
		if($_POST['order_status'] == 1){
			// Prepare an insert statement
			$sql = "UPDATE orders SET order_date=?, client_id=?, specialists=?, address=?, order_status=?, begin_hours=?, cancel_date=?, cancel_reason=?, gu_id=?, psu_id=? WHERE id=?";
			
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "ssssssssssi", $param_order_date, $param_client_id, $param_specialists, $address, $param_order_status, $param_begin_hours, $param_cancel_date, $param_cancel_reason, $param_gu_id, $param_psu_id, $param_id);
				// Set parameters
				$param_order_date = $order_date;
				$param_client_id = $client_id;
				$param_specialists = $specialists;
				$param_address = $address;
				$param_order_status = $order_status;
				$param_begin_hours = $begin_hours;
				$param_cancel_date = "0000-00-00";
				$param_cancel_reason = "";
				$param_gu_id = $gu_id;
				$param_psu_id = $psu_id;
				$param_id = $order_id_once;
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
		if($_POST['order_status'] == 2){
			// Prepare an insert statement
			$sql = "UPDATE orders SET order_date=?, client_id=?, specialists=?, address=?, order_status=?, begin_hours=?, cancel_date=?, cancel_reason=?, gu_id=?, psu_id=? WHERE id=?";
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "ssssssssssi", $param_order_date, $param_client_id, $param_specialists, $param_address, $param_order_status, $param_begin_hours, $param_cancel_date, $param_cancel_reason, $param_gu_id, $param_psu_id, $param_id);
				// Set parameters
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
				$param_id = $order_id_once;
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
								<label>ФИО клиента</label>
								<input type="text" class="form-control" value="<?php foreach($clients as $c){if($c['id']==$client_id){echo $c['name'];}} ?>" disabled>
								<input type="text" name="client_id" class="form-control" value="<?php echo $client_id; ?>" hidden>
								<span class="invalid-feedback"><?php echo $client_id_err;?></span>
							</div>
							<div class="form-group">
								<label>Специалисты</label>
								<select class="form-select" name="specialists" id="specialists">
									<option value="" selected>Выберите специалиста</option>
									<?php
									foreach ($users as $s) {
										if ($s['usergroup'] == 2) {
											echo '<option value="'.$s['id'].'">'. $s['name'] .'</option>';
										}
									}
									?>
								</select><br>
								<select class="form-select" name="specialists1" id="specialists1">
									<option value="" selected>Выберите второго специалиста (если такой есть)</option>
									<?php
									foreach ($users as $s) {
										if ($s['usergroup'] == 2) {
											echo '<option value="'.$s['id'].'">'. $s['name'] .'</option>';
										}
									}
									?>
								</select>
								<span class="invalid-feedback"><?php echo $specialists_err;?></span>
							</div>
							<div class="form-group">
								<label>Адрес</label>
								<input name="address" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $address; ?>">
								<span class="invalid-feedback"><?php echo $address_err;?></span>
							</div>
							<div class="form-group">
								<label>Количество часов</label>
								<input type="text" name="begin_hours" class="form-control" value="<?php echo $begin_hours; ?>">
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
								<select class="form-select" name="gu_id" id="gu_id">
									<option selected>Выберите ГУ</option>
									<?php
									foreach ($gu_ids as $s) {
										echo '<option value="'.$s['id'].'">'. $s['name'] .'</option>';
									}
									?>
								</select>
								<span class="invalid-feedback"><?php echo $gu_id_err;?></span>
							</div>
							<div class="form-group">
								<label>ПСУ</label>
								<select class="form-select" name="psu_id" id="psu_id">
									<option selected>Выберите ПСУ</option>
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
							window.addEventListener('load', () => {
								$("#order_status_1").val("<?php echo $order_status; ?>").change();
								$("#specialists").val("<?php echo $specialists; ?>").change();
								var spec1 = '<?php echo ($specialists1) ? $specialists1 : 'undefined'; ?>';
								if (typeof spec1 != 'undefined') {
									$("#specialists1").val("<?php echo $specialists1; ?>").change();
								}
								$("#gu_id").val("<?php echo $gu_id; ?>").change();
								$("#psu_id").val("<?php echo $psu_id; ?>").change();
								var radio = $('#order_status_1').find(":selected").val();
								  if( radio == 1 ){
									$('#cancel_block').css("display", "none");
								  }
								  if( radio == 2 ){
									$('#cancel_block').css("display", "block");
								  }
							});
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
						<script>
							// Проверяем, существует ли введенный номер заказа и блокируем кнопку
							$(document).ready(() => {
								$('input[name="order_num"]').change(async (event) => {
									let params = {"getOrderByNum": event.target.value};
									let orders = getFormAsync(params).then(
										(data) => JSON.parse(data)
									).then((orders) => {
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
//Отправка формы от специалиста
if ($_SESSION['group']  == 2 and isset($_GET['editform'])) {
	include('templates/sendform/editform_specialist.php');
} ?>
<?php
//Выбор ГУ перед редактированием от бухгалтера
if ($_SESSION['group']  == 4 and isset($_GET['selectgu'])) {
	// Include config file
	require_once "engine.php";
	require_once "config.php";
	
	$gu_list = get_all_from_table('gu_ids');

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		// Перенаправление на страницу редактирования ГУ
		if (isset($_POST['gu_id']) && $_POST['gu_id'] != 'id') {
			header("location: sendform.php?editgu=".$_POST['gu_id']);
			exit();
		} else {
			alert('Выберите ГУ! ', 'sendform.php?selectgu=0');
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Выбор ГУ</title>
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
						<h2 class="mt-5 mb-3">Выберите ГУ</h2><hr>
						<form method="post">
							<div class="alert">
								<select class="form-select" name="gu_id">
									<option selected value="id">Выберите ГУ</option>
									<?php
									foreach ($gu_list as $gu) {
										echo '<option value="'.$gu['id'].'">'. $gu['name'] .'</option>';
									}
									?>
								</select>
								<p><br>
									<input type="submit" value="Выбрать" class="btn btn-danger">
									<a href="index.php" class="btn btn-secondary">Отмена</a>
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
//Редактирование ГУ от бухгалтера
if ($_SESSION['group']  == 4 and isset($_GET['editgu'])) {
	// Include config file
	require_once "config.php";
	require "engine.php";
	$gu = get_gu($_GET['editgu']);
?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Отправить данные</title>
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
								<label>Год</label>
								<select class="form-select" name="year">
									<option selected value="id">------</option>
									<?php
									foreach (range(2023,date('Y')) as $year) {
										echo '<option value="'.$year.'">'. $year .'</option>';
									}
									?>
								</select>
							</div>
							<div class="form-group">
								<label>Месяц</label>
								<select class="form-select" name="month">
									<option selected value="id">------</option>
									<?php
									foreach (range(1,12) as $month) {
										echo '<option value="'.$month.'">'. get_month_name($month) .'</option>';
									}
									?>
								</select>
							</div>
							<div class="form-group">
								<label>Статус оплаты</label>
								<select class="form-select" name="status">
									<option selected value="0">Не оплачен</option>
									<option value="1">Оплачен</option>
								</select>
							</div>
							<div class="form-group">
								<label>Комментарий к статусу оплаты</label>
								<input name="comment" class="form-control <?php echo (!empty($comment_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $comment; ?>">
								<span class="invalid-feedback"><?php echo $comment_err;?></span>
							</div>
							<div class="form-group mt-3 mb-3">
							<input type="submit" class="btn btn-primary" value="Создать">
							<a href="clients.php" class="btn btn-secondary ml-2">Отмена</a>
							</div>
						</form>
						<script>
							document.querySelector("select[name='month']").onchange = function() {
								let gu = <?php echo $gu['id']; ?>;
								let month = document.querySelector("select[name='month']").selectedOptions[0].value;
								if (document.querySelector("select[name='year']").selectedOptions[0].value != 'id') {
									let year = document.querySelector("select[name='year']").selectedOptions[0].value;
									let status = document.querySelector("select[name='status']");
									let comment = document.querySelector("input[name='comment']");

									let transaction = fetch(`sql.php?getTransaction=1&year=${year}&month=${month}&gu=${gu}`).then(
										(data) => data.json()
									)
									.then(
										(result) => {
											if (result == 'null') {
												
											}
										}
									);
									


								}
							};
						</script>
					</div>
				</div>        
			</div>
		</div>
	</body>
	</html>
<?php } ?>