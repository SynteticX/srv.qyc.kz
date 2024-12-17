<?php 
//Получить все заказы
function get_all_orders() {
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
    return $orders;
}
//Получить заказ по ID
function get_order($id) {
	require "config.php";
	// Получаем данные из БД
	$sql = "SELECT * FROM orders WHERE id = $id";
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
//Получить заказ по ID клиента и году (получает только активный заказ)
function get_order_by_client($id, $year) {
	require "config.php";
	// Получаем данные из БД
	$sql = "SELECT * FROM orders WHERE client_id=$id";
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				if (date_format(date_create($row['order_date']), 'Y') == date_format(date_create($year), 'Y')) {
					if ($row['order_status'] == 1) {
						return $row;
					}
				}
			}
			// Free result set
			mysqli_free_result($result);
		} else{
			return null;
		}
	} else{
		return "Error";
	}
}
// Получить номер заказа по его ID
function get_order_num_by_id($id) {
	return get_order($id)['order_num'];
}
//Проверяем наличие заказа на клиента на год
function exist_order($client_id, $year) {
	require "config.php";
	//Получаем ID заказа на год
	$sql = "SELECT * FROM orders WHERE client_id = $client_id";
	$orders = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				//Проверяем год
				if (date_format(date_create($row['order_date']), 'Y') == date_format(date_create($year), 'Y')) {
					//Заказ существует
					return '1';
				}
			}
			mysqli_free_result($result);
		} else{
			//Заказа нет
			return '2';
		}
	} else{
		//Ошибка при запросе
		return '3';
	}
}
//Проверяем статус заказа по ID
function is_order_active($id) {
	require "config.php";
	//Получаем ID заказа на год
	$sql = "SELECT * FROM orders WHERE id = $id";
	$orders = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				return $row['order_status'];
			}
			mysqli_free_result($result);
		} else{
			//Заказа нет
			return null;
		}
	} else{
		//Ошибка при запросе
		return null;
	}
}