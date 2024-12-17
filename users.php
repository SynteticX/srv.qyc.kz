<?php
include('templates/header.php');
?>
<?php 
session_start(); 
// Include config file
require_once "config.php";
require_once "engine.php";

$users = get_all_from_table('users');
//Сортировка по алфавиту
$users = sort_by_name($users);
// Вывод страницы для админа и модератора
if ($_SESSION['group']  == 1 || $_SESSION['group']  == 3) {
?>
    <body data-sidebar="dark">

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

        <!-- Begin page -->
        <div id="layout-wrapper">

            <?php
			include('templates/top.php');
			include('templates/sidebar.php');
			?>

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Управление пользователями</h4>
                                    <a role="button" href="newform.php?newuser=1" class="btn btn-outline-info"><i class="fas fa-plus"></i> Создать нового пользователя</a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">              
                                        <div class="table-rep-plugin">
                                            <div class="table-wrapper">
                                            	<div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
                                                <table id="tech-companies-1" class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Id</th>
                                                        <th>Логин</th>
                                                        <th>E-mail</th>
                                                        <th>Имя</th>
                                                        <th>Группа</th>
                                                        <th>Дата прикрепления</th>
                                                        <th>Настройки</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
													<?php
													foreach ($users as &$u) {?>
                                                    <tr>
                                                        <td><?php echo $u['id']; ?></td>
                                                        <td><?php echo $u['username']; ?></td>
                                                        <td><?php echo $u['email']; ?></td>
                                                        <td><?php echo $u['name']; ?></td>
                                                        <td><?php if ($u['usergroup'] == 1) {echo 'Администратор';} else if ($u['usergroup'] == 2) {echo 'Специалист';} else if ($u['usergroup'] == 3) {echo 'Модератор';} else if (str_contains($u['usergroup'], "%")) {echo 'ГУ';} else if ($u['usergroup'] == 4) {echo 'Бухгалтер';} else if ($u['usergroup'] == 5) {echo 'Завсклад';} ?></td>
														<td><?php echo $u['reg_date']; ?></td>
														<td>
															<a href="building.php" title="Посмотреть" data-toggle="tooltip"><span class="fa fa-eye"></span></a>
															<a href="sendform.php?edituser=<?php echo $u['id'];?>" class="ms-3" title="Редактировать" data-toggle="tooltip"><span class="fa fa-pen"></span></i></a>
															<a href="deleteform.php?edituser=<?php echo $u['id'];?>" class="ms-3" title="Удалить" data-toggle="tooltip"><span class="fa fa-trash"></span></i></a></td>
                                                    </tr><?php } ?>
                                                    </tbody>
                                                </table>
                                            </div></div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div>
                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->
                
            </div>
            <!-- end main content-->
        </div>
        <!-- END layout-wrapper -->
        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

<?php
include('templates/footer.html');
}
?>