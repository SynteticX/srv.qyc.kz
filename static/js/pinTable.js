function pinTable(selector, row, col) {
    let tables = document.querySelectorAll(selector);
    for (let table of tables) {
        let tableWrapper = table.parentNode;
        tableWrapper.style.maxHeight = window.innerHeight - window.innerHeight / 6 + 'px';
        let firstRow = table.querySelector('tr:nth-child(' + row + ')');
        let columns = [];
        let lastCol = 0;
        for (let i = 1; i <= col; i++) {
            columns = columns.concat(...table.querySelectorAll('tbody > tr > td:nth-child(' + i + ')'));
            if (i == col) {
                lastCol = table.querySelector('tbody > tr > td:nth-child(' + i + ')');
            }
        }
        firstRow.style.position = 'sticky';
        firstRow.style.top = 0;
        firstRow.style.background = '#ffffff';
        firstRow.style.zIndex = 100;

        //Закрепление ячеек первой строки по горизонтали
        let cornerColumns = firstRow.querySelectorAll('th');
        for (let i = 0; i < col; i++) {
            cornerColumns[i].style.position = 'sticky';
            cornerColumns[i].style.left = cornerColumns[i].offsetLeft + 'px';
            cornerColumns[i].style.background = '#ffffff';
        }

        for (let col of columns) {
            col.style.position = 'sticky';
            col.style.left = col.offsetLeft-table.offsetLeft + 'px';
            col.style.background = '#ffffff';
            col.style.zIndex = 98;

        }

        // Отрисовка таблицы с границами
        tableWrapper.innerHTML = `<style>.border-top {
            position: sticky;
            position: -webkit-sticky; /* for Safari */
            top: ${firstRow.offsetHeight}px;
            background-color: white;
            height: 1px; /* Высота верхней границы */
            z-index: 101; /* Поднять элемент над таблицей */
            border-top: 2px solid #bcbfc5!important;
            width: ${table.offsetWidth}px; 
        }
        
        .border-left {
            position: absolute;
            position: -webkit-absolute; /* for Safari */
            left: ${lastCol.offsetLeft + lastCol.offsetWidth - 2}px;
            background-color: #bcbfc5!important;
            width: 3px; /* Ширина левой границы */
            height: ${Number(tableWrapper.style.maxHeight.slice(0,-2))-20}px; /* Высота должна быть 100% для прокрутки */
            z-index: 99; /* Поднять элемент над таблицей */
        }
        </style>
        <div class="border-top"></div> 
        <!-- Элемент для верхней границы -->
        <div class="border-left"></div> 
        <!-- Элемент для левой границы -->`;

        let leftBorder = document.createElement('div');
        leftBorder.classList.add('border-left');

        // Добавляем таблицу
        tableWrapper.appendChild(table);
    }
}

window.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.pin_table_admin_forms') != null) {
        pinTable('.pin_table_admin_forms', 1, 6);
    }
    if (document.querySelector('.pin_table_moderator_forms') != null) {
        pinTable('.pin_table_moderator_forms', 1, 7);
    }
    if (document.querySelector('.pin_table_gu_forms') != null) {
        pinTable('.pin_table_gu_forms', 1, 0);
    }
    if (document.querySelector('.pin_table_buh_forms') != null) {
        pinTable('.pin_table_buh_forms', 1, 0);
    }
    if (document.querySelector('.pin_table_buh_forms') != null) {
        pinTable('.pin_table_spec_forms', 1, 0);
    }
    if (document.querySelector('.pin_table_psu_forms') != null) {
        pinTable('.pin_table_psu_forms', 1, 0);
    }
});