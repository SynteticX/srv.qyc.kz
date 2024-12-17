document.addEventListener('DOMContentLoaded', async function(){
    
    // Подгружаем все данные
    let items = await updateItemList();
    window.loading.show();
    let categories = await getFormAsync({"getTable": "warehouse_category"}).then(response => { try {return JSON.parse(response)} catch {return undefined}});
    let warehouses = await getFormAsync({"getTable": "warehouse_warehouses"}).then(response => { try {return JSON.parse(response)} catch {return undefined}});
    let transfers = await updateTransfersList();
    window.loading.hide();

    window.items = await items;
    window.categories = await categories;
    window.warehouses = await warehouses;
    window.transfers = await transfers;
    // Создаем статичные модальные окна
    document.body.insertAdjacentHTML('afterbegin', '<div id="modals"></div>');
    let modals = document.querySelector('#modals');

    if (!document.querySelector('#addCatModal')) {

    // Добавление товара
    let addItemModal = `<div class="modal" tabindex="-1" id="addItemModal">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Добавление нового товара</h5>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
            <div class="form-group">
                <label>Наименование</label>
                <input class="form-control" name="item_name" type="text" placeholder="Iphone 15 Pro Max" onchange=""></input>
            </div>
            <div class="form-group">
                <label>Категория</label>
                <select class="form-select" name="cat_id" required="">
                    <option selected="" value="">Выберите категорию</option>`;
                    if (categories) {
                        for (let cat of categories) {
                            addItemModal += `<option selected="" value="${cat['id']}">${cat['name']}</option>`
                        }
                    }
                    addItemModal += `</select>
            </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-ban"></i></button>
	        <button type="button" class="btn btn-primary" onclick="createItem(this)"><i class="fa fa-check"></i></button>
	      </div>
	    </div>
	  </div>
	</div>`;

    
    
    // Добавление категории
    let addCatModal = `<div class="modal" tabindex="-1" id="addCatModal">
    <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title">Добавление категории</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
    <div class="form-group">
    <label>Наименование</label>
    <input class="form-control" name="cat_name" type="text" placeholder="Смартфоны" onchange=""></input>
    </div>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-ban"></i></button>
    <button type="button" class="btn btn-primary" onclick="createCat(this);"><i class="fa fa-check"></i></button>
    </div>
    </div>
    </div>
	</div>`;

    // Удаление товара
    let delItemModal = `<div class="modal" tabindex="-1" id="delItemModal">
    <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title">Удаление товара</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
         Подтвердите удаление товара
         Также исчезнет история о перемещениях данного товара
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-ban"></i></button>
    <button type="button" class="btn btn-primary" onclick="deleteItem(window.itemToDelete);"><i class="fa fa-check"></i></button>
    </div>
    </div>
    </div>
	</div>`;
    
        modals.innerHTML += addItemModal;
        modals.innerHTML += addCatModal;
        modals.innerHTML += delItemModal;

        // Определение глобальных переменных для вызова модалок
        window.addItemModal = new bootstrap.Modal(document.getElementById('addItemModal'));
        window.addCatModal = new bootstrap.Modal(document.getElementById('addCatModal'));
        window.deleteItemModal = new bootstrap.Modal(document.getElementById('delItemModal'));
    }

    // Добавляем меню для вызова модальных окон
    document.querySelector('.page-title-box .btn-group').insertAdjacentHTML('beforeend', `
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#" onclick="addItemModal.show()"><i class="fa fa-plus"></i> Добавить товар</a></li>
          <li><a class="dropdown-item" href="#" onclick="addCatModal.show()"><i class="fa fa-plus"></i> Новая категория</a></li>
        </ul>`);
      

    // Вывод товаров
    updateItemsContent();

    // Делаем ячейки с классом editable редактируемыми
    let table = document.getElementById("warehouse");
    table.addEventListener("dblclick", function(event) {
        const target = event.target;

        // Проверяем, что клик был по ячейке с классом editable
        if (target.tagName.toLowerCase() === "td" && target.classList.contains("editable")) {
            // Сохраняем оригинальное содержимое ячейки
            const originalContent = target.textContent;
            let input = document.createElement("input");
            if (target.dataset.type != "category") {
                input.type = "text";
                input.value = originalContent;
                target.innerHTML = ""; // Очищаем содержимое ячейки
                target.appendChild(input);
                input.focus(); 
            } else {
                let originalCategory = categories.find((c) => c.name == originalContent);
                input = document.createElement("select");
                // Добавляем опции в select
                categories.forEach(optionData => {
                    const option = document.createElement("option");
                    option.value = optionData.id;
                    option.text = optionData.name;
                    input.appendChild(option);
                });
                
                input.value = originalCategory.id;
                input.classList.add("form-select");
                target.innerHTML = "";
                target.appendChild(input);
                input.focus();
            }

            

            // При потере фокуса сохраняем новое значение
            input.addEventListener("blur", function() {
                if (input.value) {
                    target.textContent = input.value || originalContent;
                
                    // Меняем количество в БД
                    if (target.dataset.type == "amount") {
                        // Защита от ввода текста
                        if (/^\d+$/.test(input.value) && (parseInt(input.value) || input.value == 0)) {
                            setItemAmount(target.parentNode.children[0].textContent, target.dataset.whId, items, input.value);

                            // Создаем перемещение товара в БД
                            // if (parseInt(input.value) != parseInt(originalContent)) {
                            //     createItemTransfer(target.parentNode.children[0].textContent, target.dataset.whId, parseInt(input.value) - parseInt(originalContent))
                            // }
                        } else {
                            // Если введен текст, вернем старое количество товара
                            target.textContent = originalContent;
                            popup('danger', 'Ошибка: Стоимость должна быть числом!');
                        }
                        
                    }

                    // Меняем наименование в БД
                    if (target.dataset.type == "name") {
                        updateItemName(target.parentNode.children[0].textContent, items, input.value);
                    }

                    // Меняем категорию в БД
                    if (target.dataset.type == "category") {
                        target.textContent = input.querySelector('option:checked').textContent || originalContent;
                        updateItemCategory(target.parentNode.children[0].textContent, items, input.value);
                    }
                } else {
                    target.textContent = originalContent;
                }
            });

            // Сохраняем при нажатии Enter
            input.addEventListener("keydown", function(event) {
                if (event.key === "Enter") {
                    input.blur();
                }
            });
        }
    });

});

// Функция для обновления контента склада
function updateItemsContent() {
    // ============================
    // Вывод товаров в таблицу
    // ============================

    // Настройки пагинации
    const itemsPerPage = 20;
    let currentPage = 1;

    // Функция для отображения товаров на текущей странице
    function displayProducts() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedProducts = items.slice(startIndex, endIndex);

        const productsContainer = document.getElementById('warehouse_content');
        productsContainer.innerHTML = ''; // Очистка контейнера перед добавлением новых элементов

        paginatedProducts.forEach(product => {
            let amount = getItemAmount(product);
            const productDiv = document.createElement('tr');
            productDiv.innerHTML = `
                <td>${product.id}</td>
                <td class="editable" data-type="name">${product.name}</td>
                <td class="editable" data-type="category">${getCat(product.category, categories).name}</td>`;
            warehouses.forEach(wh => {
                productDiv.innerHTML += `<td data-type="amount" data-wh-id="${wh.id}">${(amount[wh.id]) ? amount[wh.id] : '0'}</td>`;
            });
            productDiv.innerHTML += `<td><a class="btn" onclick="window.itemToDelete = ${product.id}; deleteItemModal.show();"><i class="fa fa-trash"></i></a></td>`;
            productsContainer.appendChild(productDiv);
        });
    }

    // Обработчик переключения страницы
    function handlePageChange(newPage) {
        if (newPage < 1 || newPage > Math.ceil(items.length / itemsPerPage)) {
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
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const pageButtonsContainer = document.getElementById('page_buttons');

        // Очистка старых кнопок
        pageButtonsContainer.innerHTML = '';

        // Общее количество страниц
        const totalPages = Math.ceil(items.length / itemsPerPage);

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
}

// Создание нового товара
function createItem(btn) {
    let itemName = btn.parentNode.parentNode.querySelector('input[name="item_name"]').value;
    let catName = btn.parentNode.parentNode.querySelector('select[name="cat_id"]').value;
    postFormAsync(
        {
            'warehouse_create_item': 1, 
            'name': itemName, 
            'cat': catName
        }
    ).then(async () => {
        popup('success', 'Товар успешно создан');
        addItemModal.hide();
        await updateItemList();
        updateItemsContent();
    })
}

// Создание нового товара
function deleteItem(id) {
    postFormAsync(
        {
            'warehouse_delete_item': id
        }
    ).then(async () => {
        popup('success', 'Товар успешно удален');
        window.deleteItemModal.hide();
        await updateItemList();
        updateItemsContent();
    });
}

// Обновление товаров на странице
async function updateItemList() {
    window.loading.show();
    window.items = await getFormAsync({"getTable": "warehouse_items"}).then(response => { try {return JSON.parse(response)} catch {return undefined}});
    window.loading.hide();
    return await window.items;
}

// Создание новой категории
function createCat(btn) {
    let catName = btn.parentNode.parentNode.querySelector('input').value;
    postFormAsync(
        {
            'warehouse_create_cat': 1, 
            'cat': catName
        }
    ).then(() => {
        popup('success', 'Категория успешно создана');
        addCatModal.hide();
    })
}

// Удаление категории
function deleteCat(id) {
    postFormAsync(
        {
            'warehouse_delete_cat': id
        }
    ).then(() => {
        popup('success', 'Категория успешно удалена');
        addItemModal.hide();
    })
}

// Получение категории по id
function getCat(id, categories) {
    return categories.find((c) => c.id == id);
}

// Получение количества товара
function getItemAmount(item) {
    if (item.amount) {
        let amount = (typeof item.amount === 'string') ? JSON.parse(item.amount) : item.amount;
        return amount;
    } else {
        return {};
    }
}

// Изменение количества товара на определенном складе
function setItemAmount(id, wh, items, item_amount_new) {
    let item = items.find((i) => i.id == id);
    if (item.amount) {
        item.amount = (typeof item.amount === 'string') ? JSON.parse(item.amount) : item.amount;
        item.amount[wh] = item_amount_new;
    } else {
        item.amount = {};
        item.amount[wh] = item_amount_new;
    }

    const warehouseChangeItem = {
        id: item.id,
        name: item.name,
        category: item.category,
        amount: item.amount
    };

    postFormAsync({'warehouse_change_item': JSON.stringify(warehouseChangeItem)})
        .then(popup('success', 'Количество успешно обновлено'))
        .then(updateItemsContent());
}

// Добавление количества товара на определенном складе
function addItemAmount(id, wh, items, item_amount_new) {
    let item = items.find((i) => i.id == id);
    if (item.amount) {
        item.amount = (typeof item.amount === 'string') ? JSON.parse(item.amount) : item.amount;
        console.log('INFO')
        console.log(item.amount);
        console.log(item_amount_new);
        if (item.amount[wh] !== null && !isNaN(item.amount[wh]) && Number.isInteger(item.amount[wh])) {
            item.amount[wh] = parseInt(item.amount[wh]) + parseInt(item_amount_new);
        } else {
            item.amount[wh] = item_amount_new;
        }
    } else {
        item.amount = {};
        item.amount[wh] = item_amount_new;
    }

    const warehouseChangeItem = {
        id: item.id,
        name: item.name,
        category: item.category,
        amount: item.amount
    };

    postFormAsync({'warehouse_change_item': JSON.stringify(warehouseChangeItem)})
        .then(popup('success', 'Количество успешно обновлено'))
        .then(updateItemsContent());
}

// Обновляем наименование в БД
function updateItemName(id, items, item_name_new) {
    let item = items.find((i) => i.id == id);

    item.name = item_name_new;

    const warehouseChangeItem = {
        id: item.id,
        name: item.name,
        category: item.category,
        amount: item.amount
    };

    postFormAsync({'warehouse_change_item': JSON.stringify(warehouseChangeItem)})
        .then(popup('success', 'Товар успешно обновлен'));
}

// Обновляем категорию товара в БД
function updateItemCategory(id, items, item_cat_new) {
    let item = items.find((i) => i.id == id);

    item.category = item_cat_new;

    const warehouseChangeItem = {
        id: item.id,
        name: item.name,
        category: item.category,
        amount: item.amount
    };

    postFormAsync({'warehouse_change_item': JSON.stringify(warehouseChangeItem)})
        .then(popup('success', 'Товар успешно обновлен'));
}

// Обновление отправок на странице
async function updateTransfersList() {
    window.loading.show();
    window.transfers = await getFormAsync({"getTable": "warehouse_transfers"}).then(response => { try {return JSON.parse(response)} catch {return undefined}});
    window.loading.hide();
    return await window.transfers;
}

// Получение элемента массива по ID
function getElementByID(id, array) {
    return array.find((i) => i.id == id);
}