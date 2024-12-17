document.addEventListener("DOMContentLoaded", async () => {
    let clients = await getFormAsync({"getTable": "clients"}).then(response => JSON.parse(response, true));
    window.clients = clients;
    window.client_data = clients.find((c) => c.id == window.client_id);

    let dossier = {};
    if (!client_data["dossier"]) {
        $('.list-group-item > span').each(function() {dossier[$(this).data().type] = ""});
    } else {
        dossier = JSON.parse(client_data["dossier"]);
    }
    
    window.dossier = dossier;

    for (let data in dossier) {
        $(`span[data-type="${data}"]`).text(dossier[data]);
    }
    
});

// Редактирование досье
async function editDossier() {
    
    $('#save_dossier').remove();
    $('#editDossier').addClass('disabled');
    $('#top_right').before('<a class="btn btn-dark" href="#" id="save_dossier"><i class="fa fa-save"></i> Сохранить данные</a>');



    // Превращаем все данные в input
    let process = await $('.list-group-item > span').each(async function() {
        let originalContent = $(this).text();
        
        let inputEl = '';
            inputEl = `<input class="form-control" data-type="${$(this).data().type}" value="${originalContent}"></input>`;
        $(this).empty().html(inputEl);

        if ($(this).data('input') == "date") {
            let inputEl = '';
            inputEl = `<input class="form-control" type="date" data-type="${$(this).data().type}" value="${originalContent}"></input>`;
            $(this).empty().html(inputEl);
        }

        if ($(this).data('type') == "photo") {
            let inputFile = `<strong>Фото профиля</strong><input type="file" class="form-control" data-type="photo">`;
            $(this).empty().html(inputFile);
            document.querySelector('input[data-type="photo"]').addEventListener('change', function(event) {
                const files = event.target.files;
                if (files.length > 0) {
                    const file = files[0]; // Получаем первый загруженный файл
                    console.log(file);
                    window.file = file;
                } else {
                    console.log('Файлы не загружены');
                }
            });            
        }

        if ($(this).find('input').data('type') == "friends") {
            var inputElement = $(this).find('input');
            
            // Создаем элемент select с множественным выбором и классами Bootstrap
            var selectElement = $('<select multiple="multiple" class="form-select" id="select-friends"></select>');
            
            // Данные для options (клиенты)
            var clientOptions = window.clients;
            // Добавляем опции в select
            clientOptions.forEach(function(option) {
                selectElement.append('<option value="' + option.id + '" data-user-type="client">' + option.name + '</option>');
            });
        
            // Данные для options (специалисты)
            var userOptions = await getFormAsync({"getTable": "users"}).then(response => JSON.parse(response, true));
            // Добавляем опции в select
            userOptions.forEach(function(option) {
                selectElement.append('<option value="' + option.id + '" data-user-type="user">' + option.name + '</option>');
            });
        
            // Заменяем input на select
            inputElement.replaceWith(selectElement);
        
            // Применяем плагин Select2 для добавления поиска и обработки тегов
            selectElement.select2({
                placeholder: 'Введите данные для поиска',
                allowClear: true, // Кнопка для очистки выбора
                tags: true,
                createTag: function (params) {
                    var term = $.trim(params.term);
                    
                    if (term === '') {
                        return null;
                    }
        
                    return {
                        id: term, // Уникальный ID для нового тега
                        text: term,
                        newTag: true // Пометка, что это новый тег
                    };
                },
                width: '100%' // Чтобы занимало всю ширину
            });
        }
        

        $('#save_dossier').click(() => {
            if ($(this).data('type') == "friends") {
                let friends = $('#select-friends').select2('data');
                let newContent = '';
                friends.forEach(el => {
                    if (el.element.dataset.userType == "client") {
                        newContent += `<p><a href="clients.php?dossier=${el.id}">${el.text}</a></p>`;
                    } else {
                        newContent += `<p>${el.text}</p>`;
                    }
                })
                $(this).html(newContent);
            } else {
                let newContent = $(this).find('input').val();
                $(this).html(newContent);
            }
        });
    });

    $('#save_dossier').click(async function () {
        await process;
        $('.list-group-item > span').each(function() {
            if ($(this).data().type != "photo") {
                dossier[$(this).data().type] = $(this).text();
            } else {

                
            }
        });
        updateDossier(dossier);
        $('#editDossier').removeClass('disabled');
        $(this).remove();
    });
}

// Отправляем обновленное досье
function updateDossier(dossier) {
    console.log(dossier);
    postFormAsync({"updateClientDossier": window.client_id, "dossier": dossier})
        .then(() => {
            popup('success', 'Данные успешно обновлены');
        });
}