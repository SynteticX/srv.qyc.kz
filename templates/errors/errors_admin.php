<div class="col-12">
    <div class="card">
        <div class="card-body">              
            <div class="table-rep-plugin">
                <div class="table-wrapper">
                    <div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
                        <table id="errors" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <td class="col-1"># <i class="fas fa-sort" onClick="sortTable('errors', 0);"></i></td>
                                    <td>Год <i class="fas fa-sort" onClick="sortTable('errors', 1);"></i></td>
                                    <td>Месяц</td>
                                    <td>Клиент <i class="fas fa-sort" onClick="sortTable('errors', 3);"></i></td>
                                    <td>Специалист <i class="fas fa-sort" onClick="sortTable('errors', 4);"></i></td>
                                    <td>ИИН</td>
                                    <td>Тип ошибки</td>
                                    <td>Дата создания</td>
                                    <td>Комментарий к ошибке</td>
                                    <td>Срок исполнения</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody id="error_forms_content">
                                <?php 
                                    // Генерируем модальное окно
                                    echo get_modal(
                                        "addError", 
                                        'Добавить ошибку', 
                                        '', 
                                        "postFormAsync(
                                            {
                                                'create_error': {
                                                    'text': document.querySelector('#add_error_textarea').value,
                                                    'type': document.querySelector('#add_error_type').value,
                                                    'deadline': document.querySelector('#add_error_deadline').value,
                                                    'form_id': document.querySelector('#add_error_form') ? (document.querySelector('#add_error_form').value || 'NULL') : 'NULL'
                                                }
                                            }
                                        ).then(() => {popup('success', 'Ошибка добавлена'); setTimeout(() => {window.location.reload()}, 2000)});"
                                    );
                                    echo get_modal(
                                        "editError", 
                                        'Редактировать ошибку', 
                                        '', 
                                        "postFormAsync(
                                            {
                                                'update_error': {
                                                    'id': document.querySelector('#editError').dataset.id,
                                                    'text': document.querySelector('#upd_error_textarea').value,
                                                    'type': document.querySelector('#upd_error_type').value,
                                                    'deadline': document.querySelector('#upd_error_deadline').value,
                                                    'form_id': Number.isInteger(parseInt(document.querySelector('#editError').dataset.formId)) ? (document.querySelector('#editError').dataset.formId || 'NULL') : 'NULL'
                                                }
                                            }
                                        ).then(() => {popup('success', 'Ошибка обновлена'); setTimeout(() => {window.location.reload()}, 2000)});"
                                    );
                                    
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
             <!-- Navigation start -->
             <nav aria-label="Навигация">
                <ul class="pagination justify-content-center">
                    <li class="page-item">
                    <a class="page-link" href="#" aria-label="Previous" id="prevBtn">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                    </li>
                    <div id="page_buttons" class="pagination justify-content-center mx-2"></div>
                    <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next" id="nextBtn">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                    </li>
                </ul>
            </nav>
            <!-- Navigation end -->
        </div>
    </div>
</div>

<script defer src="templates/errors/errors_admin.js?v=<?php echo time(); ?>"></script>