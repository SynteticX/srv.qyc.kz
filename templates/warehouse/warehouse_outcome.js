document.addEventListener('DOMContentLoaded', async function(){

    // Подгружаем все данные
    window.loading.show();
    let items = await updateItemList();
    let categories = await getFormAsync({"getTable": "warehouse_category"}).then(response => { try {return JSON.parse(response)} catch {return undefined}});
    let warehouses = await getFormAsync({"getTable": "warehouse_warehouses"}).then(response => { try {return JSON.parse(response)} catch {return undefined}});
    let clients = await getFormAsync({"getTable": "clients"}).then(response => { try {return JSON.parse(response)} catch {return undefined}});
    let transfers = await updateTransfersList();
    let users = await getFormAsync({"getTable": "users"}).then(response => { try {return JSON.parse(response)} catch {return undefined}});
    window.loading.hide();

    // ============================
    // Создаем модальные окна
    // ============================
    let modals = document.querySelector('#modals');

    // Добавление поступления
    let addTransferOutcomeModal = `<div class="modal" tabindex="-1" id="addTransferOutcomeModal">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Добавить отправку</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
            <div class="form-group">
                <label>Наименование товара</label>
                <select class="form-select" name="item_id_outcome" required="">
                    <option selected="" value="">Выберите товар</option>`;
                    if (items) {
                        for (let i of items) {
                            addTransferOutcomeModal += `<option selected="" value="${i['id']}">${i['name']}</option>`
                        }
                    }
                    addTransferOutcomeModal += `</select>
            </div>
            <div class="form-group">
                <label>Местоположение</label>
                <select class="form-select" name="warehouse_id_outcome" required="">
                    <option selected="" value="">Выберите склад</option>`;
                    if (warehouses) {
                        for (let wh of warehouses) {
                            addTransferOutcomeModal += `<option selected="" value="${wh['id']}">${wh['name']}</option>`
                        }
                    }
                    addTransferOutcomeModal += `</select>
            </div>
            <div class="form-group">
                <label>Количество</label>
                <input class="form-control" name="amount_outcome" type="number" placeholder="0"></input>
            </div>
            <div class="form-group">
                <label>ФИО клиента</label>
                <select class="form-select" name="name_outcome" required="">
                    <option selected="" value="">Выберите клиента</option>`;
                    if (clients) {
                        for (let c of clients) {
                            addTransferOutcomeModal += `<option selected="" value="${c['id']}">${c['name']}</option>`
                        }
                    }
                    addTransferOutcomeModal += `</select>
            </div>
            <div class="form-group">
                <label>Номер заказа</label>
                <input class="form-control" name="order_id_outcome" type="number" placeholder="0"></input>
            </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-ban"></i></button>
	        <button type="button" class="btn btn-primary" onclick="createItemTransferOutcome()"><i class="fa fa-check"></i></button>
	      </div>
	    </div>
	  </div>
	</div>`;

    modals.innerHTML += addTransferOutcomeModal;

    // Определение глобальных переменных для вызова модалок
    window.addTransferOutcomeModal = new bootstrap.Modal(document.getElementById('addTransferOutcomeModal'));

    // Добавляем кнопку для вызова модалки
    document.querySelector('#warehouse_title_outcome').insertAdjacentHTML('beforeend', '<button class="btn btn-light btn-sm" onclick="addTransferOutcomeModal.show()"><i class="fa fa-plus"></i></button>')


    // ============================
    // Вывод товаров в таблицу
    // ============================

    // Собираем товары для вывода
    window.transfersOutcome = [];
    for (let tr of transfers) {
        if (tr.amount < 0) {
            window.transfersOutcome.push(tr);
        }
    }

    // Настройки пагинации
    const itemsPerPage = 10;
    let currentPage = 1;

    // Функция для отображения перемещений на текущей странице
    function displayProducts() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedProducts = window.transfersOutcome.slice(startIndex, endIndex);

        const productsContainer = document.getElementById('warehouse_content_outcome');
        productsContainer.innerHTML = '';

        paginatedProducts.forEach(product => {
            // Фильтруем только отправленные товары
            if (product.amount < 0 && getElementByID(product.item_id, items).name) {
                const productDiv = document.createElement('tr');
                productDiv.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.date}</td>
                    <td>${getElementByID(product.item_id, items).name}</td>
                    <td>${getElementByID(getElementByID(product.item_id, items).category, categories).name}</td>
                    <td>${Math.abs(product.amount)}</td>
                    <td>${getElementByID(product.warehouse_id, warehouses).name}</td>
                    <td>${(product.name) ? getElementByID(product.name, clients).name : ''}</td>
                    <td>${(product.order_id) ? product.order_id : ''}</td>
                    <td>${getElementByID(product.user_id, users).name}</td>
                `;
                productsContainer.appendChild(productDiv);
            }
        });
    }

    // Обработчик переключения страницы
    function handlePageChange(newPage) {
        if (newPage < 1 || newPage > Math.ceil(window.transfersOutcome.length / itemsPerPage)) {
            return; // Выход, если страница вне допустимого диапазона
        }
        currentPage = newPage;
        displayProducts();
        setupPaginationButtons();
    }

    // Генерация кнопок пагинации
    const createPageButton = (pageNumber, isCurrent = false) => {
        const listItem = document.createElement('li');
        listItem.className = 'page-item';

        const link = document.createElement('a');
        link.className = isCurrent ? 'page-link active' : 'page-link';
        link.href = '#'; // Можешь заменить на реальный URL, если нужно
        link.textContent = pageNumber;

        link.addEventListener('click', (event) => {
            event.preventDefault(); // Предотвращаем переход по ссылке
            if (!isCurrent) {
                handlePageChange(pageNumber);
            }
        });

        listItem.appendChild(link);
        return listItem;
    };

    // Функция для обработки кнопок пагинации
    function setupPaginationButtons() {
        const prevBtn = document.getElementById('prevBtnOutcome');
        const nextBtn = document.getElementById('nextBtnOutcome');
        const pageButtonsContainer = document.getElementById('page_buttons_outcome');

        // Очистка старых кнопок
        pageButtonsContainer.innerHTML = '';

        // Общее количество страниц
        const totalPages = Math.ceil(window.transfersOutcome.length / itemsPerPage);

        // Управление состоянием кнопок "Предыдущая" и "Следующая"
        prevBtn.parentElement.classList.toggle('disabled', currentPage === 1);
        nextBtn.parentElement.classList.toggle('disabled', currentPage === totalPages);

        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                handlePageChange(currentPage - 1);
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages) {
                handlePageChange(currentPage + 1);
            }
        });

        // Определяем диапазон страниц для показа
        let startPage, endPage;

        if (totalPages <= 5) {
            // Если страниц меньше или равно 5, показываем все
            startPage = 1;
            endPage = totalPages;
        } else {
            // Определяем диапазон страниц для показа
            if (currentPage <= 3) {
                startPage = 1;
                endPage = 5;
            } else if (currentPage + 2 >= totalPages) {
                startPage = totalPages - 4;
                endPage = totalPages;
            } else {
                startPage = currentPage - 2;
                endPage = currentPage + 2;
            }
        }

        // Создаем кнопки для диапазона страниц
        for (let i = startPage; i <= endPage; i++) {
            pageButtonsContainer.appendChild(createPageButton(i, i === currentPage));
        }
    }


    

    // Инициализация отображения и кнопок пагинации
    displayProducts();
    setupPaginationButtons();

});

// Создаем перемещение товара (отрицательное)
function createItemTransferOutcome() {
    let item_id = document.querySelector('select[name="item_id_outcome"]').value;
    let warehouse_id = document.querySelector('select[name="warehouse_id_outcome"]').value;
    let name = document.querySelector('select[name="name_outcome"]').value;
    let order = document.querySelector('input[name="order_id_outcome"]').value;
    let amount = parseInt(document.querySelector('input[name="amount_outcome"]').value) * -1;

    let item = getElementByID(item_id, window.items);

    if ((parseInt(getItemAmount(item)[warehouse_id]) + amount) >= 0 ) {
        postFormAsync({
            'warehouse_create_transfer': 1,
            item_id: item_id,
            warehouse_id: warehouse_id,
            amount: amount,
            user_id: window.user_id,
            name: name,
            order: order
        }).then(() => {
            addItemAmount(item_id, warehouse_id, window.items, amount);
            addTransferOutcomeModal.hide();
        });
    } else {
        popup("error", "Остаток не может быть меньше нуля!");
    }
}