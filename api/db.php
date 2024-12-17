<?php
// ==================================
//            SELECT
// ==================================
// Получить все строки из таблицы
function get_all_from_table($table) {
	require "../config.php";
	// Prepare a select statement
	$sql = "SELECT * FROM $table";
	$res = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				array_push($res, $row);
			}
			// Free result set
			mysqli_free_result($result);
		} else{
			echo "No records matching your query were found.";
		}
	} else{
		echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
	}
	return $res;
}
// Получить строку по параметрам
function get_row_from_table($table, $params) {
    require "../config.php";

    // Пример использования:
    // $row = get_row_from_table("forms", ["id" => 1, "name" => "John"]);
    
    // Подготовка параметров для SQL запроса
    $sql_conditions = [];
    $bind_types = '';
    $bind_values = [];
    
    foreach ($params as $key => $value) {
        $sql_conditions[] = "`$key` = ?";
        $bind_types .= is_integer($value) ? 'i' : 's'; // предположим, что значения только строки и целые числа
        $bind_values[] = $value;
    }

    // SQL запрос для поиска строки
    $sql = "SELECT * FROM `$table` WHERE " . implode(' AND ', $sql_conditions);

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Привязываем параметры
        mysqli_stmt_bind_param($stmt, $bind_types, ...$bind_values);

        // Попытка выполнить подготовленное выражение
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                return $row;  // Возвращает найденную строку
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
            return false;
        }
        
        // Закрываем выражение
        mysqli_stmt_close($stmt);
    }
    
    // Закрываем соединение
    mysqli_close($link);
    return false; // В случае, если строка не найдена
}
// ==================================
//            DELETE
// ==================================
//Удаление строки из БД по ID
function del_row_from_db($id, $table) {
	require "../config.php";
	// Запрос к БД
	$sql = "DELETE FROM $table WHERE id = ?";
	 
	if($stmt = mysqli_prepare($link, $sql)){
		// Bind variables to the prepared statement as parameters
		mysqli_stmt_bind_param($stmt, "i", $param_id);
		
		// Set parameters
		$param_id = $id;
		
		// Attempt to execute the prepared statement
		if(mysqli_stmt_execute($stmt)){
			// Records created successfully. Redirect to landing page
			//header("location: orders.php");
			return true;
			exit();
		} else{
			echo "Oops! Something went wrong. Please try again later. (function del_row_from_db($id, $table))";
		}
	}
	
	// Close statement
	mysqli_stmt_close($stmt);
	
	// Close connection
	mysqli_close($link);
}
// ==================================
//            INSERT
// ==================================
// Добавляет в таблицу новую строку с параметрами
function sendRowToTable($tableName, $data) {
    require "../config.php";

    // Пример использования:
    // sendRowToTable("forms", ["name" => "John", "email" => "john@example.com"]);
    
    // Подготовка параметров для SQL запроса
    $columns = [];
    $placeholders = [];
    $bind_types = ''; 
    $bind_values = [];
    
    foreach ($data as $key => $value) {
        $columns[] = "`$key`";
        $placeholders[] = '?';
        $bind_types .= is_integer($value) ? 'i' : 's'; // предположим, что только строки и целые числа
        $bind_values[] = $value;
    }

    // SQL запрос для добавления записи
    $sql = "INSERT INTO `$tableName` (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Привязываем параметры
        mysqli_stmt_bind_param($stmt, $bind_types, ...$bind_values);

        // Попытка выполнить подготовленное выражение
        if (mysqli_stmt_execute($stmt)) {
            return true;
        } else {
            echo "Oops! Something went wrong. Please try again later.";
            return false;
        }
        
        // Закрываем выражение
        mysqli_stmt_close($stmt);
    }
    
    // Закрываем соединение
    mysqli_close($link);
}

// ==================================
//            UPDATE
// ==================================
// Обновляет существующую строку в таблице на основе заданных параметров
function updateRowInTable($tableName, $data, $conditions) {
    require "../config.php";

    // Пример использования:
    // updateRowInTable("forms", ["name" => "Jane"], ["id" => 1]);
    
    // Подготовка параметров для SQL запроса
    $updates = [];
    $bind_types = ''; 
    $bind_values = [];

    // Данные для обновления
    foreach ($data as $key => $value) {
        $updates[] = "`$key` = ?";
        $bind_types .= is_integer($value) ? 'i' : 's'; // предположим, что только строки и целые числа
        $bind_values[] = $value;
    }

    // Условия для выбора строки
    $condition_statements = [];
    foreach ($conditions as $key => $value) {
        $condition_statements[] = "`$key` = ?";
        $bind_types .= is_integer($value) ? 'i' : 's';
        $bind_values[] = $value;
    }

    // SQL запрос для обновления записи
    $sql = "UPDATE `$tableName` SET " . implode(', ', $updates) . " WHERE " . implode(' AND ', $condition_statements);

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Привязываем параметры
        mysqli_stmt_bind_param($stmt, $bind_types, ...$bind_values);

        // Попытка выполнить подготовленное выражение
        if (mysqli_stmt_execute($stmt)) {
            return true;
        } else {
            echo "Oops! Something went wrong. Please try again later.";
            return false;
        }
        
        // Закрываем выражение
        mysqli_stmt_close($stmt);
    }
    
    // Закрываем соединение
    mysqli_close($link);
}
