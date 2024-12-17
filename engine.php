<?php
require('./engine/db.php');
require('./engine/order.php');
require('./engine/forms.php');
require('./engine/specialist.php');
require('./engine/user.php');
require('./engine/client.php');
require('./engine/gu.php');
require('./engine/errors.php');
// Декорируем строку с деньгами (20000.343265764 -> 20.000,34)
function money($money) {
	$result =  preg_replace('/(\d)(?=(\d{3})+(?!\d))/', '$1 ', round($money, 2));
	return $result;
}

/**
 * Поиск элемента во вложенном массиве по значению ключа.
 *
 * @param array $array Входной массив с вложенными массивами.
 * @param string $key Ключ для поиска.
 * @param mixed $value Искомое значение.
 * @return array|null Найденный элемент или null, если элемент не найден.
 */
function findInNestedArray(array $array, string $key, $value): ?array {
    foreach ($array as $item) {
        if (is_array($item) && isset($item[$key]) && $item[$key] == $value) {
            return $item; // Возвращаем элемент, если нашли совпадение
        }
    }
    return null; // Возвращаем null, если ничего не найдено
}

// Получаем год из даты
function get_year($date) {
	$year = date_format(date_create($date),'Y');
	return $year;
}
//Получить пользователя по ID
function get_user($id) {
	require "config.php";
	// Получаем данные из БД
	$sql = "SELECT * FROM users WHERE id = $id";
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
//Получить клиента по ID
function get_client($id) {
	require "config.php";
	// Получаем данные из БД
	$sql = "SELECT * FROM clients WHERE id = $id";
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

//Получить ГУ по ID
function get_gu($id) {
	require "config.php";
	// Получаем данные из БД
	$sql = "SELECT * FROM gu_ids WHERE id = $id";
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
//Получить ПСУ по ID
function get_psu($id) {
	require "config.php";
	// Получаем данные из БД
	$sql = "SELECT * FROM psu_ids WHERE id = $id";
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


//Получаем список специалистов
function parse_specialists($specialists) {
	if (strpos($specialists, ' ') !== false) {
		$specs = explode(' ',$specialists);
		return $specs;
	} else {
		return $specialists;
	}
}
//Список клиентов специалиста
function get_spec_clients($spec) {
	require "config.php";
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
	$result = array();
	foreach ($clients as $u) {
		//echo get_order_by_client($u['id'], date('Y'));
		$order_specialists = get_order_by_client($u['id'], date('Y'));
		if ($order_specialists != null) {	
			$specialists = parse_specialists($order_specialists['specialists']);
		} else {
			continue;
		}
		if ($specialists != null) {
			if (gettype($specialists) == "array") {
				foreach ($specialists as $s) {
					if ($spec == $s) {
						array_push($result, $u);
						break;
					}
				}
			} else if ($spec == $specialists) {
				array_push($result, $u);
			}
		}
	}
	return $result;
}
//Отправить на страницу сообщения
// alert(сообщение, ссылка для возврата)
function alert($msg, $url) {
	header("location: alert.php?msg=$msg&url=$url");
	exit();
}
// Отобразить pop-up уведомление на странице
function popup($logLvl, $text) {
	// Для версии Bootstrap 5.0.1
	echo "<script>
		let popup = document.createElement('div');

	</script>";
}
//Получить значение настройки из БД
function get_setting($name) {
	require "config.php";
	// Получаем данные из БД
	$sql = "SELECT * FROM settings";
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				if ($row['name'] == $name) {
					return $row['value'];
				}
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
//Установить значение настройки в БД
function set_setting($name, $value) {
	require "config.php";
	//Если форма на месяц была создана
	$sql = "UPDATE settings SET `value`=? WHERE `name`=?";
	if($stmt = mysqli_prepare($link, $sql)){
		// Bind variables to the prepared statement as parameters
		mysqli_stmt_bind_param($stmt, "ss", $param_value, $param_name);
		// Set parameters
		$param_name = $name;
		$param_value = $value;
		//echo $name;
		// Attempt to execute the prepared statement
		if(mysqli_stmt_execute($stmt)){
			// Records created successfully. Redirect to landing page
			//header("location: sendform.php?editform=".$client_id);
			//exit();
			return true;
		} else{
			echo "Oops! Something went wrong. Please try again later.";
		}
		
		// Close statement
		mysqli_stmt_close($stmt);
	}
		
	// Close connection
	mysqli_close($link);
}

//Считает часы клиента за год
function count_hours($client_id, $year) {
	require "config.php";
	//Получаем ID заказа на год
	$sql = "SELECT * FROM orders WHERE client_id = $client_id";
	$orders = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				//Проверяем год
				if (date_format(date_create($row['order_date']), 'Y') == date_format(date_create($year), 'Y')) {
					array_push($orders, $row);
				}
			}
			mysqli_free_result($result);
		} else{
			return null;
		}
	} else{
		return null;
	}
	//Получаем все отчеты за год
	foreach ($orders as $o) {
		$sql = "SELECT * FROM forms WHERE order_id = ".$o['id'];
		$hours = array();
		if($result = mysqli_query($link, $sql)){
			if(mysqli_num_rows($result) > 0){
				while($row = mysqli_fetch_array($result)){
					//Записываем все часы в массив
					foreach (range(1,31) as $i) {
						array_push($hours, $row[$i]);
					}
				}
				mysqli_free_result($result);
			} else{
				return null;
			}
		} else{
			return null;
		}
	}
	//Считаем часы в массиве и выводим
	$result = 0;
	foreach ($hours as $h) {
		$result += $h;
	}
	return $result;
}
//Выдаем выходные по месяцу массивом дней
function get_vacations($month, $year) {
	//Получаем настройку
	$month = intval($month-1);
	$vacations = get_setting('vacations_' . $year);
	$vacations = explode(':', $vacations);
	return explode(',', $vacations[$month]);
}
//Проверяем является ли день выходным, выходные на месяц должны подаваться массивом из дней
function is_vacation($vacations_array_for_month, $month_day) {
	foreach ($vacations_array_for_month as $vacation_day) {
		if ($month_day == $vacation_day) {
			return true;
		}
	}
	return false;
}
//Получаем статус онлайн-оффлайн
function get_online_status($form_id, $day) {
	$form = get_form($form_id);
	if ($form[$day] != null) {
		return $form['online_'.$day];
	}
	return 'No hours';
}
//Получаем стиль для статуса онлайн-оффлайн
function get_online_status_as_style($form_id, $day) {
	$status = get_online_status($form_id, $day);
	if ($status == 1) {
		return 'color:orange';
	} else {
		return '';
	}
}
//Получаем класс для div для статуса онлайн-оффлайн
function get_online_status_as_div_class($form_id, $day) {
	$status = get_online_status($form_id, $day);
	if ($status == 1) {
		return 'td-div-online';
	} else if ($status != 'No hours') {
		return 'td-div-offline';
	}
}

// Вывод месяца на русском
function get_month_name($month) {
	$monthes = array(
		1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
		5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
		9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
	);
	return $monthes[$month];
}
//Сортировка по именам
function sort_by_names_method($a,$b) {
	return strcmp($a["name"], $b["name"]);
}
function sort_by_name($array_for_sort) {
	$result = $array_for_sort;
	usort($result, "sort_by_names_method");
	return $result;
}
//Сортировка по month
function sort_by_month_method($a,$b) {
	return $a['month'] > $b['month'];
}
function sort_by_month($array_for_sort) {
	$result = $array_for_sort;
	usort($result, "sort_by_month_method");
	return $result;
}
//Сортировка по id
function sort_by_id_method($a,$b) {
	return $a['id'] > $b['id'];
}
function sort_by_id($array_for_sort) {
	$result = $array_for_sort;
	usort($result, "sort_by_id_method");
	return $result;
}
//Считает часы клиента за год
function count_hours_for_spec($client_id, $spec_id, $year) {
	require "config.php";
	//Получаем ID заказа на год
	$sql = "SELECT * FROM orders WHERE client_id = $client_id";
	$orders = array();
	if($result = mysqli_query($link, $sql)){
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				//Проверяем год
				if (date_format(date_create($row['order_date']), 'Y') == date_format(date_create($year), 'Y')) {
					array_push($orders, $row);
				}
			}
			mysqli_free_result($result);
		} else{
			return null;
		}
	} else{
		return null;
	}
	//Получаем все отчеты за год
	foreach ($orders as $o) {
		$sql = "SELECT * FROM forms WHERE order_id = ".$o['id']." AND spec_id=".$spec_id;
		$hours = array();
		if($result = mysqli_query($link, $sql)){
			if(mysqli_num_rows($result) > 0){
				while($row = mysqli_fetch_array($result)){
					//Записываем все часы в массив
					foreach (range(1,31) as $i) {
						array_push($hours, $row[$i]);
					}
				}
				mysqli_free_result($result);
			} else{
				return null;
			}
		} else{
			return null;
		}
	}
	//Считаем часы в массиве и выводим
	$result = 0;
	foreach ($hours as $h) {
		$result += $h;
	}
	return $result;
}
// Считает часы клиента за месяц по форме
function count_hours_by_form($form) {
	$hours = 0;
	if ($form != null) {
		foreach (range(1,31) as $day) {
			$hours += $form[$day];
		}
	}
	return $hours;
}
// Считает часы по массиву форм
function count_hours_by_forms($forms) {
	$hours = 0;
	foreach ($forms as $form) {
		$hours += count_hours_by_form($form);
	}
	return $hours;
}
// Подсчет часов для ГУ
function count_hours_by_gu_for_month($gu_id, $year, $month) {
	$params = [
		"client_id" => "",
		"spec_id" => "",
		"gu_id" => $gu_id,
		"psu_id" => "",
		"year" => $year,
		"month" => $month,
	];
	$forms = get_forms_by_params($params);
	$hours = count_hours_by_forms($forms);
	return $hours;
}
//Остаток часов
function remaining_hours($client_id, $year) {
	$begin_hours = get_order_by_client($client_id, $year)['begin_hours'];
	$count_hours = count_hours($client_id, $year);
	if ($begin_hours < 0 || $begin_hours == null) {
		return 0;
	}
	return $begin_hours - $count_hours;
}
//Остаток часов на определенный период (ДО месяца)
// UPD 01.24: В начале года не находил формы и ломал выдачу форм, добавлены исключения при null
function count_hours_for_month($client_id,$year,$month) {
	$order = get_order_by_client($client_id, $year);
	if (!is_array($order)) {
		return 0;
	} else {
		$unsorted_forms = get_forms_by_order($order['id']);
		if (!is_array($unsorted_forms)) {
			return 0;
		} else {
			$forms = sort_by_month($unsorted_forms);
		}
	}
	$hours = 0;
	foreach ($forms as $f) {
		//Фикс для января (так как в range не отнять 1 у month)
		if (intval($f['month']) != $month && intval($f['month']) < $month) {
			foreach (range(1,31) as $day) {
				$hours += $f[$day];
			}
		}
	}
	return $hours;
}
//Проверка даты на включение в период сдачи
function get_period($dateToCheck, $client_id) {
	$startDay = date_format(date_create('1970-01-'.get_setting('start_day')), 'd');
	$endDay = date_format(date_create('1970-01-'.intval(get_setting('start_day'))-1), 'd');
	// Нынешняя дата
	$currentMonth = date('m');
	$currentYear = date('Y');
	// Следующий месяц
	$nextMonth = date('m', strtotime('+1 month'));
	$nextYear = date('Y', strtotime('+1 month'));
	// Предыдущий месяц
	$lastMonth = date('m', strtotime('-1 months'));
	$lastYear = date('Y', strtotime('-1 months'));
	// Старт и конец периода сдачи этого месяца
	$startDate = $currentYear . '-' . $currentMonth . '-' . $startDay;
	$endDate = $nextYear . '-' . $nextMonth . '-' . $endDay;
	// Переменные для возврата. Нынешний или предыдущий период
	$currentPeriod = $currentYear . '-' . $currentMonth . '-01';
	$lastPeriod = $lastYear . '-' . $lastMonth . '-01';

	$dateToCheck = strtotime($dateToCheck); // преобразовать введенную дату в timestamp
	
	//echo date_format(date_create($currentPeriod), 'Y');

	if (date("Y-m-d", $dateToCheck) >= $startDate && date("Y-m-d", $dateToCheck) <= $endDate) {
	  // Отправка запроса разрешена. Проверяем, что было раньше - оформление заказа или начало периода
	  // Дата создания заказа на клиента
	  $order_date = get_order_by_client($client_id, date_format(date_create($currentPeriod), 'Y'));
	  if ($order_date['order_date'] > $currentPeriod) {
		  return $order_date['order_date'];
	  } else {
		  return $currentPeriod;
	  }
	} else if (date('d', $dateToCheck) <= $endDay) {
	  // Отправка запроса на прошлый месяц. Проверяем, что было раньше - оформление заказа или начало периода
	  $order_date = get_order_by_client($client_id, date_format(date_create($currentPeriod), 'Y'));
	  if ($order_date['order_date'] > $lastPeriod) {
		  return $order_date['order_date'];
	  } else {
		  return $lastPeriod;
	  }
	} else {
		return null;
	}
}
// Получаем даты текущего периода
function get_current_period() {
    $startDay = date_format(date_create('1970-01-'.get_setting('start_day')), 'd');
    $endDay = date_format(date_create('1970-01-'.intval(get_setting('start_day'))-1), 'd');
    
    // Текущий месяц и год
    $currentMonth = date('m');
    $currentYear = date('Y');
    
    // Дата начала текущего периода
    $startDate = $currentYear . '-' . $currentMonth . '-' . $startDay;
    
    // Следующий месяц
    $nextMonth = date('m', strtotime('+1 month'));
    $nextYear = date('Y', strtotime('+1 month'));
    
    // Дата конца текущего периода
    $endDate = $nextYear . '-' . $nextMonth . '-' . $endDay;
    
    // Возвращаем дату начала и конца текущего периода
    return array($startDate, $endDate);
}
// Получаем даты предыдущего периода
function get_previous_period() {
    $startDay = date_format(date_create('1970-01-'.get_setting('start_day')), 'd');
    $endDay = date_format(date_create('1970-01-'.intval(get_setting('start_day'))-1), 'd');
    
    // Текущий месяц и год
    $currentMonth = date('m');
    $currentYear = date('Y');
    
    // Предыдущий месяц
    $lastMonth = date('m', strtotime('-1 months'));
    $lastYear = date('Y', strtotime('-1 months'));
    
    // Дата начала предыдущего периода
    $startDate = $lastYear . '-' . $lastMonth . '-' . $startDay;
    
    // Дата конца предыдущего периода
    $endDate = $currentYear . '-' . $currentMonth . '-' . $endDay;
    
    // Возвращаем дату начала и конца предыдущего периода
    return array($startDate, $endDate);
}

// Получить количество сданных часов переводчиком в день
function hours_per_day($spec_id, $date) {
	$day = date_format(date_create($date),'d');
	$month = date_format(date_create($date),'m');
	$year = date_format(date_create($date),'Y');
	$params = [
		"client_id" => "",
		"spec_id" => $spec_id,
		"gu_id" => "",
		"psu_id" => "",
		"year" => $year,
		"month" => $month,
	];
	$forms = get_forms_by_params($params);
	$hours = 0;
	foreach ($forms as $f) {
		$hours += $f[intval($day)];
	}
	return $hours;
}
//Получить часы специалиста за конкретный день у клиента
function get_hours_in_day_by_client_form($spec_id, $date, $client_id) {
	$day = date_format(date_create($date),'d');
	$month = date_format(date_create($date),'m');
	$year = date_format(date_create($date),'Y');
	$params = [
		"client_id" => $client_id,
		"spec_id" => $spec_id,
		"gu_id" => "",
		"psu_id" => "",
		"year" => $year,
		"month" => $month,
	];
	$forms = get_forms_by_params($params);
	$hours = 0;
	foreach ($forms as $f) {
		$hours += $f[intval($day)];
	}
	return $hours;
}
// Достигнут ли лимит часов на день
function is_spec_day_limit_reached($spec_id, $date, $client_id, $add_hours) {
	$hours = hours_per_day($spec_id, $date);
	$current_hours = get_hours_in_day_by_client_form($spec_id, $date, $client_id);
	if ($hours - $current_hours + $add_hours > 8) {
		alert("Количество часов на день было превышено. Вы не можете работать больше 8 часов в день!", $_SERVER['REQUEST_URI']);
	} else {
		return false;
	}
}
// Генерируем Excel
function generateExcelFromHTML($htmlTable, $filename) {
    // Подключаем библиотеку PHPSpreadsheet
    require_once 'vendor/autoload.php';

    // Создаем новый объект Spreadsheet
    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();

    // Устанавливаем индекс активного листа
    $spreadsheet->setActiveSheetIndex(0);

    // Получаем данные из HTML элемента table
    $htmlTable = str_replace("\n", '', $htmlTable);
    $htmlTable = str_replace("\r", '', $htmlTable);
    $htmlTable = str_replace("\t", '', $htmlTable);

    // Импортируем HTML таблицу в Spreadsheet
    $worksheet = $spreadsheet->getActiveSheet();
    $worksheet->fromHtml($htmlTable);

    // Получаем итератор ячеек таблицы
    $cellIterator = $worksheet->getCellIterator();
    foreach ($cellIterator as $cell) {
        // Получаем стили ячейки из HTML атрибута style
        $style = $cell->getStyle();
        if ($style !== null) {
            // Устанавливаем стили ячейки в Excel
            $worksheet->getStyle($cell->getCoordinate())->applyFromArray($style->toArray());
        }
    }

    // Сохраняем результат в файл
    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($filename);
}
// Чекбоксы модератора
// Вставляет функцию замены цвета ячейки
function get_cell_color_script() {
	echo <<<'JS'
	<script>
		// Отображение цвета ячеек в зависимости от чекбоксов
		async function set_cell_color(cell, form) {
			setTimeout(() => {
				console.log(form);
				// Если выбран только первый чекбокс
				if (form.checkbox1 == 'on' && (form.checkbox2 == 'off' || form.checkbox2 == 'undefined')) {
					cell.css({
						'background': '#BD8124',
						'color': 'white'
					});
					cell.find('a').css({
						'color': 'white'
					});
				}
				// Если выбраны оба чекбокса
				else if (form.checkbox1 == 'on' && form.checkbox2 == 'on') {
					cell.css({
						'background': '#9700C7',
						'color': 'white'
					});
					cell.find('a').css({
						'color': 'white'
					});
				}
				else {
					cell.css({
						'background': 'white',
						'color': 'black'
					});
					cell.find('a').css({
						'color': '#0bb197'
					});
				}
			}, 500);
		}
	</script>
JS;
}
// Возвращает цвет подкрашивания ячейки в зависимости от отмеченного чекбокса
function get_checkbox_color($form) {
	// Если выбран только первый чекбокс
	if ($form['checkbox1'] == 'on' && ($form['checkbox2'] == 'off' || $form['checkbox2'] == 'undefined')) {
		return '#BD8124';
	} else 
	if ($form['checkbox1'] == 'on' && $form['checkbox2'] == 'on') {
		return '#9700C7';
	}
	return 'white';
}
// Возвращает транзакцию
function getTransaction($year, $month, $gu) {
	require "config.php";
	// Получаем данные из БД
	$sql = "SELECT * FROM transactions WHERE `year` = $year AND `month` = $month AND `gu_id` = $gu";
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

//Выдает последний ID GU (для создания аккаунтов ГУ от админа)
function getLastGUID() {
	$array = get_all_from_table('users');
	$maxWithPercent = null;

    foreach ($array as $value) {
		$value = $value["usergroup"];
        if (!is_numeric($value) && strpos($value, '%') !== false) {
            // Извлекаем числовое значение, убирая символ '%'
            $number = (int) str_replace('%', '', $value);

            // Проверяем, является ли текущее число максимальным
            if ($maxWithPercent === null || $number > $maxWithPercent) {
                $maxWithPercent = $number;
            }
        }
    }

    return $maxWithPercent;
}

// Формирование модального окна.
function get_modal($id, $title, $msg, $onsubmit = '', $btn = 'Подтвердить') {
	return '
	<div class="modal" tabindex="-1" id="'.$id.'">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">' . $title . '</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        ' . $msg . '
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
	        <button type="button" class="btn btn-primary" onclick="' . $onsubmit . '" data-bs-dismiss="modal">' . $btn . '</button>
	      </div>
	    </div>
	  </div>
	</div>
	';
}

// Создаем отчет от модератора
function set_moderator_report() {
	$month = date_format(date_create(get_current_period()[0]),'m');
	$year = date_format(date_create(get_current_period()[0]),'Y');
	// Меняем период на предыдущий
	if (intval($month) != 1) {
		$month = intval($month) - 1;
	} else {
		$month = 12;
		$year = $year - 1;
	}

	sendRowToTable("moderator_reports", ["year" => $year, "month" => $month]);
}

// Получаем отчет от модератора
function get_moderator_report($year, $month) {
    require "config.php";
	// Получаем данные из БД
	$sql = "SELECT * FROM moderator_reports WHERE `year` = $year AND `month` = $month";
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

// Проверяет, готов ли отчет от модератора на данный период
function check_moderator_report() {
    $month = date_format(date_create(get_current_period()[0]),'m');
	$year = date_format(date_create(get_current_period()[0]),'Y');

	if (get_moderator_report($year, $month)) {
		return true;
	} else {
		return false;
	}
}