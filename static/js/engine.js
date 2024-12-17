// Заполнить в месте отправки
// let params = {
//     "sendform": "",
//     "checkbox1": ""
// }

// Отправка множественных запросов
// let params = {
//   'sendform_list': forms,
//   {
//       'accepted': 'true'
//   }
// }

// Отправка POST-запроса на страницу sql.php с параметрами params
function postForm(params) {
    return new Promise((resolve, reject) => {
      var xhr = new XMLHttpRequest();
      var url = 'sql.php';
  
      xhr.open('POST', url, true);
      xhr.setRequestHeader('Content-Type', 'application/json');
  
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            resolve(xhr.responseText);
          } else {
            reject(new Error('Произошла ошибка при отправке запроса'));
          }
        }
      };
  
      xhr.send(JSON.stringify(params));
    });
}
// Отправка POST-запроса в фоновом режиме на страницу sql.php с параметрами params
async function postFormAsync(params) {
    let response = new Promise((resolve, reject) => {
      var xhr = new XMLHttpRequest();
      var url = 'sql.php';
  
      xhr.open('POST', url, true);
      xhr.setRequestHeader('Content-Type', 'application/json');
  
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) { 
          if (xhr.status === 200) {
            resolve(xhr.responseText);
          } else {
            reject(new Error('Произошла ошибка при отправке запроса'));
          }
        }
      };
  
      xhr.send(JSON.stringify(params));
    });

    return await response;

}
// Отправка GET-запроса в фоновом режиме на sql.php с параметрами params
async function getFormAsync(params) {
  let response = new Promise((resolve, reject) => {
    var xhr = new XMLHttpRequest();
    var url = 'sql.php';
    var queryParams = '?';

    // Преобразование параметров в строку запроса
    for (var key in params) {
      if (params.hasOwnProperty(key)) {
        queryParams += key + '=' + encodeURIComponent(params[key]) + '&';
      }
    }

    // Удаление последнего символа '&' из строки запроса
    queryParams = queryParams.slice(0, -1);

    xhr.open('GET', url + queryParams, true);

    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          resolve(xhr.responseText);
        } else {
          reject(new Error('Произошла ошибка при отправке запроса'));
        }
      }
    };

    xhr.send();
  });

  return await response;
}

// Считает часы клиента за месяц по форме
function countHoursByForm(form) {
  let hours = 0;
  if (form != null) {
    for (let day = 1; day <= 31; day++) {
      hours += Number(form[day]);
    }
  }
  return hours;
}

// Считает часы за месяц по формам в массиве
function countHoursByForms(forms) {
  let hours = 0;
  for (let form in forms) {
    hours += countHoursByForm(form);
  }
  return hours;
}

function get_month_name(month) {
  const months = {
      1: 'Январь',
      2: 'Февраль',
      3: 'Март',
      4: 'Апрель',
      5: 'Май',
      6: 'Июнь',
      7: 'Июль',
      8: 'Август',
      9: 'Сентябрь',
      10: 'Октябрь',
      11: 'Ноябрь',
      12: 'Декабрь'
  };

  return months[Number(month)];
}

async function getAllSettings() {
  return await getFormAsync({"get_settings": 1}).then((data) => {return JSON.parse(data)})
}

// Проверяем, подписан ли акт на месяц
async function isActSigned($spec_id) {
  let params = {
    "getTable": "acts"
  }
  getFormAsync(params).then(
    (data) => {console.log(data)}
  )
}

function sortTable(tableId, columnIndex) {
  var table = document.getElementById(tableId);
  var tbody = table.getElementsByTagName('tbody')[0];
  var rowsArray = Array.from(tbody.rows);
  var sortDirection = table.getAttribute('data-sort-direction') === 'desc' ? 'asc' : 'desc';

  // Функция для определения, является ли строка числовой
  function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
  }

  // Определяем, сортируем ли мы числа или строки
  var isNumericColumn = rowsArray.every(function(row) {
    var cell = row.cells[columnIndex].textContent.trim();
    return isNumeric(cell);
  });

  // Модифицированная функция сравнения для сортировки с учетом приоритета
  function compare(a, b) {
    // Извлекаем приоритет сортировки из dataset
    var priorityA = a.row.dataset.sortpriority ? parseInt(a.row.dataset.sortpriority, 10) : 0;
    var priorityB = b.row.dataset.sortpriority ? parseInt(b.row.dataset.sortpriority, 10) : 0;

    // Сравниваем приоритеты
    if (priorityA !== priorityB) {
      return priorityA - priorityB;
    }

    // Если приоритеты равны, переходим к сравнению по содержимому ячеек
    var aValue = isNumericColumn ? parseFloat(a.data) : a.data.toLowerCase();
    var bValue = isNumericColumn ? parseFloat(b.data) : b.data.toLowerCase();

    if (aValue < bValue) return sortDirection === 'desc' ? 1 : -1;
    if (aValue > bValue) return sortDirection === 'desc' ? -1 : 1;
    return 0;
  }

  // Создаем массив объектов содержащих данные и ссылку на соответствующую строку
  var cellDataArray = rowsArray.map(function(row) {
    return {
      data: row.cells[columnIndex].textContent.trim(),
      row: row // Сохраняем ссылку на строку для доступа к dataset
    };
  });

  // Сортируем массив данных с учетом приоритета
  cellDataArray.sort(compare);

  // Перемещаем строки в новом порядке в tbody
  cellDataArray.forEach(function(item) {
    tbody.appendChild(item.row);
  });

  // Обновляем направление сортировки в атрибуте таблицы
  table.setAttribute('data-sort-direction', sortDirection);
}


// ======================
//        Анимации
// ======================
document.querySelectorAll('.zoom').forEach(item => {
  item.addEventListener('mouseover', () => {
      // Увеличиваем объект
      item.style.transform = 'scale(1.1)'; // Увеличение на 10%
      item.style.transition = 'transform 0.5s ease'; // Плавное увеличение
  });

  item.addEventListener('mouseout', () => {
      // Возвращаем объект к исходному размеру
      item.style.transform = 'scale(1)'; // Возвращаем к исходному размеру
  });
});

// Добавляет на страницу всплывающее окно загрузки
document.addEventListener('DOMContentLoaded', function(){
  document.body.insertAdjacentHTML('afterbegin', '<div id="loading-container"></div>');
  let modal = document.querySelector('#loading-container');
  modal.innerHTML = `<div class="modal loading" tabindex="-1" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false">
	  <div class="modal-dialog" style="max-width:0px!important">
	    <div class="modal-content loading-body">
	      <div class="modal-body">
	        <p></p><center><i class="fa-2x fas fa-sync fa-spin"></i></center><p></p>
	      </div>
	    </div>
	  </div>
	</div>`;  

  window.modal = new bootstrap.Modal(document.getElementById('loadingModal'));
  window.loading = new bootstrap.Modal(document.getElementById('loadingModal'));
});