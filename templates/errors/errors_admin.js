$('document').ready(() => {
    init_error_page();
    update_forms_with_errors();
});

async function init_error_page() {
    
    $('.page-title-box').append(`
        <a class="mx-2 btn btn-outline-info" title="Архив" href="errors.php?do=archive" data-toggle="tooltip">Архив</a>
        <a class="mx-2 btn btn-outline-danger" id="addErrorBtn" title="Добавить ошибку" data-toggle="tooltip">
            <i class="fas fa-plus"></i>
        </a>
    `).find('#addErrorBtn').click(function () {
        let modal = new bootstrap.Modal($('#addError'));
        modal.show();
    });

    let clients = await getFormAsync({ "getTable": "clients" }).then((data) => JSON.parse(data));
    let users = await getFormAsync({ "getTable": "users" }).then((data) => JSON.parse(data));
    let orders = await getFormAsync({ "getTable": "orders" }).then((data) => JSON.parse(data));
    let forms = await getFormAsync({ "getTable": "forms" }).then((data) => JSON.parse(data));

    $('#addError .modal-body').prepend(`
        <label>Тип ошибки</label>
        <select class="form-select" id="add_error_type"></select>
        <label>Комментарий</label>
        <textarea class="form-control" id="add_error_textarea"></textarea>
        <label>Срок исполнения</label>
        <input class="form-control" type="date" id="add_error_deadline">
        <br>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="add_error_is_form">
            <label class="form-check-label" for="add_error_is_form">Привязать к отчету</label>
        </div>
        <div id="errorIsForm"></div>
    `);

    // Заполняем типы ошибок
    let errorTypes = await getFormAsync({ 'get_setting': 'error_type' }).then((response) => JSON.parse(response));
    errorTypes.split(',').forEach((type, index) => {
        $('#add_error_type').append(`<option value="${index}">${type}</option>`);
    });

    $('#add_error_is_form').change(async function () {
        if ($(this).is(':checked')) {
            $('#errorIsForm').html('<i class="fa fa-spinner-third fa-spin fa-3x"></i>');

            let formInput = `
                <label>Клиент</label>
                <select class="form-select" id="add_error_client">`;

            clients.forEach((c) => {
                formInput += `<option value="${c['id']}">${c['name']}</option>`;
            });

            formInput += `</select>
                <a class="btn btn-primary" id="add_error_client_btn"><i class="fa fa-check"></i></a>`;

            $('#errorIsForm').html(formInput);
            $('#add_error_client option:first').prop('selected', true);

            $('#add_error_client_btn').click(function () {
                let formInput = `
                    <label>Год заказа</label>
                    <select class="form-select" id="add_error_order">`;

                orders.forEach((c) => {
                    if (c.client_id == $('#add_error_client').val()) {
                        formInput += `<option value="${c['id']}">${c['order_date']}</option>`;
                    }
                });

                formInput += `</select>
                    <a class="btn btn-primary" id="add_error_order_btn"><i class="fa fa-check"></i></a>`;

                $(this).remove();
                $('#errorIsForm').append(formInput);

                $('#add_error_order_btn').click(function () {
                    let formInput = `
                        <label>Отчет</label>
                        <select class="form-select" id="add_error_form">`;

                    forms.forEach((c) => {
                        if (c.order_id == $('#add_error_order').val()) {
                            formInput += `<option value="${c['id']}">${c['month']} - ${users.find((u) => u.id == c["spec_id"])["name"]}</option>`;
                        }
                    });

                    formInput += `</select>`;

                    $(this).remove();
                    $('#errorIsForm').append(formInput);
                });
            });
        } else {
            $('#errorIsForm').empty();
        }
    });

    $('#editError .modal-body').prepend(`
        <label>Тип ошибки</label>
        <select class="form-select" id="upd_error_type"></select>
        <label>Комментарий</label>
        <textarea class="form-control" id="upd_error_textarea"></textarea>
        <label>Срок исполнения</label>
        <input class="form-control" type="date" id="upd_error_deadline">
        <input class="form-control" id="upd_error_form" hidden>
        <input class="form-control" id="upd_error_id" hidden>
    `);
    errorTypes.split(',').forEach((type, index) => {
        $('#upd_error_type').append(`<option value="${index}">${type}</option>`);
    });
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

        window.modal.show();
        
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedProducts = errors.slice(startIndex, endIndex);

        const productsContainer = document.getElementById('error_forms_content');
        productsContainer.innerHTML = ''; // Очистка контейнера перед добавлением новых элементов

        paginatedProducts.forEach(product => {
            if (product.status != 1) {
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
                <td><i class="fa fa-reply" onclick="archive_error(${product.id})"></i><i class="fa fa-pen" data-error="${product.id}" onclick="let modal = new bootstrap.Modal($('#editError')); modal.show();"></i></td>`;
                productsContainer.appendChild(errorDiv);
            }
            $('i.fa-pen').click(async function (e) {
                let error_id = $(this).data('error');  // Теперь $(this) будет ссылаться на текущий элемент
                let error = errors.find((e) => e.id == error_id);
                if (error) {
                    $('#upd_error_type').val(error.type);  // Исправил sel на val
                    $('#upd_error_textarea').text(error.text);
                    $('#upd_error_deadline').val(error.deadline);
                    $('#editError').attr("data-id", error_id);
                    $('#editError').attr("data-form-id", error.form_id);

                }
            });
            
        });
        window.modal.hide();
    }

    // Обработчик переключения страницы
    function handlePageChange(newPage) {
        if (newPage < 1 || newPage > Math.ceil(errors.length / itemsPerPage)) {
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