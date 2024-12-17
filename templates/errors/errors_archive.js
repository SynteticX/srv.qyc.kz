$('document').ready(() => {
    init_error_page();
    update_forms_with_errors();
});

async function init_error_page() {
    document.querySelector('.page-title-box').insertAdjacentHTML('beforeend', `<a class="mx-2 btn btn-outline-info" title="Назад" href="errors.php" data-toggle="tooltip">Назад</a>`);
    // Заполняем типы ошибок
    let errorTypes = await getFormAsync({'get_setting': 'error_type'}).then((response) => JSON.parse(response));
    errorTypes.split(',').forEach((type, index) => {
        document.querySelector('#add_error_type').insertAdjacentHTML(`beforeend`, `<option value="${index}">${type}</option>`);
    })
    document.querySelector('#add_error_is_form').addEventListener('change', async (e) => {
        if (e.target.checked) {
            document.querySelector('#errorIsForm').innerHTML = '<i class="fa fa-spinner-third fa-spin fa-3x"></i>';
            let clients = await getFormAsync({"getTable": "clients"}).then((data) => JSON.parse(data));
            let formInput = `
            <label>Клиент</label>
            <select class="form-select" id="add_error_client">`;
            clients.forEach((c) => {
                formInput += `<option value="${c['id']}">${c['name']}</option>`;
            })
            formInput += `</select>`;
            document.querySelector('#errorIsForm').innerHTML = formInput;

            document.querySelector('#add_error_client').addEventListener('change', async (e) => {
                console.log(e.target.value)
            });
        } else {
            document.querySelector('#errorIsForm').innerHTML = '';
        }
    })
}

// Очищаем ошибки в форме
function archive_error(id) {
    postFormAsync({"archive_error": id}).then(popup("success", "Ошибка архивирована"));
    update_forms_with_errors();
}

// Функция для обновления контента
async function update_forms_with_errors() {
    // ============================
    // Вывод  в таблицу
    // ============================

    window.modal.show();

    let orders = await getFormAsync({'getTable': 'orders'}).then((response) => JSON.parse(response));
    let forms = await getFormAsync({"getTable": "forms"}).then((response) => JSON.parse(response));
    let errors = await getFormAsync({'getTable': 'errors'}).then((response) => JSON.parse(response));
    let errorTypes = await getFormAsync({'get_setting': 'error_type'}).then((response) => JSON.parse(response));
    errorTypes = errorTypes.split(',');
    let clients = await getFormAsync({'getTable': 'clients'}).then((response) => JSON.parse(response));
    let specialists = await getFormAsync({'getTable': 'users'}).then((response) => JSON.parse(response));

    window.modal.hide();

    // Настройки пагинации
    const itemsPerPage = 20;
    let currentPage = 1;

    // Функция для отображения товаров на текущей странице
    function displayProducts() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedProducts = errors.slice(startIndex, endIndex);

        const productsContainer = document.getElementById('error_forms_content');
        productsContainer.innerHTML = ''; // Очистка контейнера перед добавлением новых элементов

        paginatedProducts.forEach(product => {
            if (product.status == 1) {
                const errorDiv = document.createElement('tr');
            let isForm = product.form_id != 'NULL';
            errorDiv.innerHTML = `
                <td>${product.id}</td>
                <td>${isForm ? new Date(orders.find((o) => o.id == forms.find((f) => f.id == product.form_id)['order_id'])["order_date"]).getFullYear() : "----"}</td>
                <td>${isForm ? get_month_name(forms.find((f) => f.id == product.form_id)['month']) : "----"}</td>
                <td>${isForm ? clients.find((c) => c.id == forms.find((f) => f.id == product.form_id)['client_id'])["name"] : "----"}</td>
                <td>${isForm ? specialists.find((s) => s.id == forms.find((f) => f.id == product.form_id)['spec_id'])["name"] : "----"}</td>
                <td>${isForm ? clients.find((c) => c.id == forms.find((f) => f.id == product.form_id)['client_id'])['iin'] : "----"}</td>
                <td>${errorTypes[product.type]}</td>
                <td>${product.date}</td>
                <td>${product.text}</td>
                <td>${product.deadline}</td>
                <td></td>`;
                productsContainer.appendChild(errorDiv);
            }
        });
    }

    // Обработчик переключения страницы
    function handlePageChange(newPage) {
        if (newPage < 1 || newPage > Math.ceil(errors.length / itemsPerPage)) {
            return; // Выход, если страница вне допустимого диапазона
        }
        currentPage = newPage;
        displayProducts(errors);
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
        const totalPages = Math.ceil(errors.length / itemsPerPage);

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