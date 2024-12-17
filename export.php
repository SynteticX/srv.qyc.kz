<?php

// Подключаем библиотеку PHPSpreadsheet
require_once 'vendor/autoload.php';

// Получаем данные из POST-запроса
$jsonData = json_decode(file_get_contents('php://input'), true);

// Получаем HTML-таблицу из данных
$htmlTable = $jsonData['table'];
$filename = $jsonData['filename'];

// Создаем новый объект Spreadsheet
$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();

// Устанавливаем индекс активного листа
$spreadsheet->setActiveSheetIndex(0);

// Импортируем HTML таблицу в Spreadsheet
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
$worksheet = $spreadsheet->getActiveSheet();
$reader->loadFromString($htmlTable, $spreadsheet);

// Получаем итератор листов таблицы
$worksheet = $spreadsheet->getActiveSheet();
$worksheetIterator = $worksheet->getRowIterator();
$freezeColumnLetter = 'A';
foreach ($worksheetIterator as $row) {
    $cellIterator = $row->getCellIterator();
    foreach ($cellIterator as $cell) {
        // Получаем заголовок колонки
		$columnLetter = $cell->getColumn(); // получаем буквенную обозначение колонки
		$columnIndex = PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnLetter); // получаем числовой индекс колонки
        $columnHeader = $worksheet->getCell($cell->getColumn() . '1')->getValue();
        // ID
        if ($columnHeader === '#') {
			//Меняем ширину
			$worksheet->getColumnDimensionByColumn($columnIndex)->setWidth(4);
        }
        // Номер заказа
        if ($columnHeader === 'Номер заказа') {
			//Меняем ширину
			$worksheet->getColumnDimensionByColumn($columnIndex)->setWidth(7);
        }
        // Дата заказа
        if ($columnHeader === 'Дата заказа') {
			//Меняем ширину
			$worksheet->getColumnDimensionByColumn($columnIndex)->setWidth(10);
        }
        // Клиент
        if ($columnHeader === 'Клиент') {
			//Меняем ширину
			$worksheet->getColumnDimensionByColumn($columnIndex)->setWidth(20);
			//Перенос текста
			$worksheet->getStyle($columnLetter . '1:' . $columnLetter . $worksheet->getHighestRow())->getAlignment()->setWrapText(true);
			//Выравнивание по верху
			$worksheet->getStyle($columnLetter . '1:' . $columnLetter . $worksheet->getHighestRow())->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
			//Делаем весь текст черным
            $worksheet->getStyle($columnLetter . '1:' . $columnLetter . $worksheet->getHighestRow())->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
        }
        // Специалист
        if ($columnHeader === 'Специалист') {
			//Меняем ширину
			$worksheet->getColumnDimensionByColumn($columnIndex)->setWidth(14);
			//Перенос текста
			$worksheet->getStyle($columnLetter . '1:' . $columnLetter . $worksheet->getHighestRow())->getAlignment()->setWrapText(true);
			//Выравнивание по верху
			$worksheet->getStyle($columnLetter . '1:' . $columnLetter . $worksheet->getHighestRow())->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
			
        }
        // ИИН
        if ($columnHeader === 'ИИН клиента') {
			//Меняем ширину
			$worksheet->getColumnDimensionByColumn($columnIndex)->setWidth(13);
            // Применяем стиль ко всей колонке
            for ($i = 2; $i <= $worksheet->getHighestRow(); $i++) {
                $cell = $worksheet->getCell($cell->getColumn() . $i);

                // Устанавливаем тип данных ячейки в целочисленный
                $cell->setValueExplicit(intval($cell->getValue()), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
        }
        // Телефон
        if ($columnHeader === 'Моб. телефон') {
			//Меняем ширину
			$worksheet->getColumnDimensionByColumn($columnIndex)->setWidth(15);
			$styleArray = [
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
				],
			];
			$worksheet->getStyle($columnLetter . '2:' . $columnLetter . $worksheet->getHighestRow())->applyFromArray($styleArray);
        }
        // Статус заказа
        if ($columnHeader === 'Статус заказа') {
			for ($i = 2; $i <= $worksheet->getHighestRow(); $i++) {
                $cell = $worksheet->getCell($cell->getColumn() . $i);

                // Устанавливаем тип данных ячейки в целочисленный
                $cell->setValueExplicit($cell->getValue(), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
        }
        // Статус заказа
        if ($columnHeader == 1) {
			$freezeColumnLetter = $columnLetter;
        }
        // Даты
		foreach (range(1,31) as $day) {
			if ($columnHeader == intval($day)) {
				//Меняем ширину
				$worksheet->getColumnDimensionByColumn($columnIndex)->setWidth(5);
				$worksheet->getStyle($columnLetter . '2:' . $columnLetter . $worksheet->getHighestRow())->getFont()->setBold(true);
				$worksheet->getStyle($columnLetter . '2:' . $columnLetter . $worksheet->getHighestRow())->getFont()->setSize(12);
			}
		}
    }
}
// Закрепление (после колонки "1")
$worksheet->freezePane($freezeColumnLetter . '2');

//Перенос текста для заголовков, выравнивание по верху, жирный текст
$worksheet->getStyle('A' . '1:' . $worksheet->getHighestColumn() . '1')->getAlignment()->setWrapText(true);
$worksheet->getStyle('A' . '1:' . $worksheet->getHighestColumn() . '1')->getFont()->setBold(true);
$worksheet->getStyle('A' . '1:' . $worksheet->getHighestColumn() . '1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

// Сохраняем результат в файл
$writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save($filename);

// Отправляем файл обратно на клиент
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Content-Length: ' . filesize($filename));
readfile($filename);

// Удаляем файл с сервера
sleep(5);
unlink($filename);