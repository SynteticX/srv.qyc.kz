// Функция для запроса на создание Excel таблицы
function getExcel(table_id) {
	// Загрузочный экран
	let loadingModal = window.modal;
	loadingModal.show();
	// Получаем HTML-таблицу
	let htmlTable = document.getElementById('table-forms');
	// Фиксим ссылки на клиентов
	let a = htmlTable.querySelectorAll('a[name="client"]');
	// Удаляем JS
	let js = htmlTable.querySelectorAll('script');
	htmlTable = htmlTable.outerHTML;
	for (let el of a) {
		// el.style.color = "black";
		htmlTable = htmlTable.replace(el.outerHTML, el.innerHTML);
	}
	for (let el of js) {
		htmlTable = htmlTable.replace(el.outerHTML, '');
	}
	let filename = 'qyc_' + Date.now() + '.xlsx';
	// Создаем JSON-объект для отправки на сервер
	let data = {
		table: htmlTable,
		filename: filename
	};

	// Создаем новый объект XMLHttpRequest
	let xhr = new XMLHttpRequest();

	// Устанавливаем метод и адрес для отправки запроса
	xhr.open('POST', 'export.php');

	// Устанавливаем заголовок для отправки JSON-данных
	xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');

	// Устанавливаем тип ответа сервера
	xhr.responseType = 'arraybuffer';

	// Устанавливаем обработчик события изменения состояния запроса
	xhr.onreadystatechange = function() {
		if (xhr.readyState === 4 && xhr.status === 200) {
			loadingModal.hide();
			// Создаем объект Blob из полученных данных
			let blob = new Blob([xhr.response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

			// Создаем ссылку на скачивание файла
			let url = window.URL.createObjectURL(blob);

			// Создаем ссылку на элемент <a> и устанавливаем настройки скачивания файла
			let a = document.createElement('a');
			a.href = url;
			a.download = filename;

			// Добавляем ссылку в документ и кликаем по ней, чтобы скачать файл
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
		}
		if (xhr.status === 500) {
			let alertContainer = document.querySelector('#alert-container');
			// создаем объект FormData
			let formData = new FormData();
			formData.append('id', 'alertModal');
			formData.append('title', 'Ошибка');
			formData.append('msg', 'Что-то пошло не так. Обратитесь к системному администратору.');
			formData.append('btn', 'Закрыть');

			// отправляем POST запрос
			fetch('modal.php', {
				method: 'POST',
				body: formData
			})
			.then(response => response.text())
			.then(html => {
				// добавляем полученный HTML-код на страницу
				alertContainer.innerHTML = html;
			});
			let alertModal = new bootstrap.Modal('#alertModal', {
			  keyboard: false
			});
			alertModal.show();
		}
		if (xhr.status === 504) {
			let alertContainer = document.querySelector('#alert-container');
			// создаем объект FormData
			let formData = new FormData();
			formData.append('id', 'alertModal');
			formData.append('title', 'Ошибка');
			formData.append('msg', 'Превышено время ожидания');
			formData.append('btn', 'Закрыть');

			// отправляем POST запрос
			fetch('modal.php', {
				method: 'POST',
				body: formData
			})
			.then(response => response.text())
			.then(html => {
				// добавляем полученный HTML-код на страницу
				alertContainer.innerHTML = html;
			});
			let alertModal = new bootstrap.Modal('#alertModal', {
			  keyboard: false
			});
			alertModal.show();
		}
	};

	// Отправляем данные на сервер
	xhr.send(JSON.stringify(data));
}

// Отслеживание кнопки после загрузки страницы
document.addEventListener("DOMContentLoaded", function() {
    let btn = document.getElementById('excel');
    btn.addEventListener("click", function(){
        getExcel('table-forms');
    });
});