<div class="col-12">
    <div class="card">
        <div class="card-body">              
            <div class="table-rep-plugin">
                <div class="table-wrapper">
                    <div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
                        <table id="warehouse" class="table table-striped table-bordered">
                            <style>
                                .editable {
                                    background-color: #f9f9f9;
                                    cursor: pointer;
                                }
                            </style>
                            <thead>
                                <tr>
                                    <td class="col-1"># <i class="fas fa-sort" onClick="sortTable('warehouse', 0);"></i></td>
                                    <td>Наименование <i class="fas fa-sort" onClick="sortTable('warehouse', 2);"></i></td>
                                    <td>Категория <i class="fas fa-sort" onClick="sortTable('warehouse', 3);"></i></td>
                                    <td>Кол-во <i class="fas fa-sort" onClick="sortTable('warehouse', 4);"></i></td>
                                    <td>Место отправки <i class="fas fa-sort" onClick="sortTable('warehouse', 5);"></i></td>
                                    <td>Пользователь <i class="fas fa-sort" onClick="sortTable('warehouse', 6);"></i></td>
                                </tr>
                            </thead>
                            <tbody id="warehouse_content_table">
                                
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

<script defer src="templates/warehouse/warehouse_outcome.js?v=<?php echo time(); ?>"></script>