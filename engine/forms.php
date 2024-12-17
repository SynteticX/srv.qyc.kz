<?php
//Создает новую форму на специалиста по месяцу и клиенту
//ID специалиста, ID клиента, ID заказа на этот год, месяц отчета
function create_form($spec_id, $client_id, $order_id, $month) {
	$sql = "INSERT INTO forms (order_id, client_id, spec_id, month) VALUES (?, ?, ?, ?)";
	echo $sql;
	if($stmt = mysqli_prepare($link, $sql)){
		// Bind variables to the prepared statement as parameters
		mysqli_stmt_bind_param($stmt, "ssss", $param_order_id, $param_client_id, $param_spec_id, $param_day, $param_online, $param_month);
		
		// Set parameters
		$param_order_id = $order_id;
		$param_client_id = $client_id;
		$param_spec_id = $spec_id;
		$param_month = $month;
		// Attempt to execute the prepared statement
		if(mysqli_stmt_execute($stmt)){
			// Records created successfully. Redirect to landing page
			header("location: sendform.php?editform=".$client_id);
			exit();
		} else{
			echo "Oops! Something went wrong. Please try again later.";
		}
		
		// Close statement
		mysqli_stmt_close($stmt);
	}
}
// Сеттер формы (НЕ ИСПОЛЬЗОВАТЬ ДЛЯ СОЗДАНИЯ НОВОЙ! Для этого есть create_form)
// Меняет данные формы по ID
function set_form($params) {
	// $params = [
		// "id" => "",
		// "1" => "",
		// "online_1" => "",
		// "month" => "",
		// "checkbox1" => "",
		// "checkbox2" => "",
		// "comment" => "",
	// ];
	require "config.php";
	foreach ($params as $key => $value) {
        if ($key == 'id') {
            continue;
        }
        // Проверка на NULL
        if ($value === null) {
            $sql_params .= "`$key` = NULL,";
        } else {
            $sql_params .= "`$key` = '$value',";
        }
    }
	// Запрос
	$sql = "UPDATE forms SET ".substr_replace($sql_params,"",-1)." WHERE `id`=?";
	if($stmt = mysqli_prepare($link, $sql)){
		// Bind variables to the prepared statement as parameters	
		mysqli_stmt_bind_param($stmt, "i", $param_id);
		// Set parameters
		$param_id = $params['id'];
		// Attempt to execute the prepared statement
		if(mysqli_stmt_execute($stmt)){
			// Records created successfully. Redirect to landing page
			//header("location: sendform.php?editform=".$client_id);
			//exit();
			return true;
		} else{
			echo "Oops! Something went wrong. Please try again later.";
			return $sql;
		}
		
		// Close statement
		mysqli_stmt_close($stmt);
	}
	
	// Close connection
	mysqli_close($link);
}
//Получить форму по ID
function get_form($id) {
	require "config.php";
	// Получаем данные из БД
	$sql = "SELECT * FROM forms WHERE id = $id";
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				return $row;
			}
			// Free result set
			mysqli_free_result($result);
		} else{
			return null;
		}
	} else{
		return null;
	}
}
//Получить формы по ID заказа
function get_forms_by_order($id) {
	require "config.php";
	// Получаем данные из БД
	$sql = "SELECT * FROM forms WHERE order_id = $id";
	$forms = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				array_push($forms, $row);
			}
			return $forms;
			// Free result set
			mysqli_free_result($result);
		} else{
			return null;
		}
	} else{
		return null;
	}
}
//Поиск отчетов по параметрам
function get_forms_by_params($params) {
	require "config.php";
	// $params = [
		// "client_id" => "",
		// "spec_id" => "",
		// "gu_id" => "",
		// "psu_id" => "",
		// "year" => "",
		// "month" => "",
		// "accepted" => "true"
	// ];
	//print_r($params);
	$order_query = "";
	$order_params = array();
	$form_query = "";
	//Формирование запроса к заказам из параметров
	if ($params["client_id"] != null) {
		$order_query .= 'client_id = '.$params["client_id"].' AND ';
	}
	if ($params["gu_id"] != null) {
		$gu_query = '';
		if (gettype($params["gu_id"]) == 'array') {
			foreach ($params["gu_id"] as $gu_id) {
				$gu_query .= 'gu_id = '.$gu_id.' OR ';
			}
			$order_query .= '('.substr($gu_query,0,-3).') AND ';
		} else if (gettype($params["gu_id"]) == 'string') {
			$gu_query .= 'gu_id = '.$params["gu_id"];
			$order_query .= '('.$gu_query.') AND ';
		}
	}
	if ($params["psu_id"] != null) {
		$order_query .= 'psu_id = '.$params["psu_id"].' AND ';
	}
	if ($params["year"] != null) {
		$order_query .= 'EXTRACT(YEAR FROM `order_date`) = '.$params["year"].' AND ';
	}
	//Формирование запроса к формам из параметров
	if ($params["spec_id"] != null) {
		$form_query .= 'spec_id = '.$params["spec_id"].' AND ';
	}
	if ($params["month"] != null) {
		$form_query .= 'month = '.$params["month"].' AND ';
	}
	if (!empty($params["accepted"])) {
		$form_query .= '`accepted` = \''.$params["accepted"].'\' AND ';
	}
	//Проверяем, есть ли параметры для поиска заказа
	if (strlen($order_query) > 0) {
		//Убираем лишние AND
		$order_query = substr_replace($order_query,"",-4);
		//Получаем ID заказа по параметрам
		$sql = "SELECT * FROM orders WHERE $order_query;";
	} else {
		$sql = "SELECT * FROM orders";
	}
	if (strlen($form_query) > 0) {
		//Убираем лишние AND
		$form_query = substr_replace($form_query,"",-4);
	}
	
	//echo $sql;
	$orders = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				array_push($orders, $row["id"]);
			}
			mysqli_free_result($result);
		} else{
			return null;
		}
	} else{
		return null;
	}
	$form_query_final = "";
	foreach ($orders as $order) {
		if (strlen($form_query_final) > 0) {
			$form_query_final .= " OR ";
		}
		if (strlen($form_query) > 0) {
			$form_query_final .= "`order_id` = ".$order." AND ".$form_query;
		} else {
			$form_query_final .= "`order_id` = ".$order;
		}
	}
	//Получаем все отчеты по параметрам
	$sql = "SELECT * FROM forms WHERE $form_query_final";
	// echo $sql;
	$forms = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				array_push($forms, $row);
			}
			mysqli_free_result($result);
		} else{
			return null;
		}
	} else{
		return null;
	}
	return $forms;
}