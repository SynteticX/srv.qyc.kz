            <!-- ========== Left Sidebar Start ========== -->
            <div class="vertical-menu">

                <div data-simplebar class="h-100">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->
                        <ul class="metismenu list-unstyled" id="side-menu">
                            <li class="menu-title">Меню</li>

                           <!-- <li>
                                <a href="/" class="waves-effect">
                                    <i class="mdi mdi-home-variant-outline"></i><spa>
                                    <span>Control Panel</span>
                                </a>
                            </li>-->
                                <?php // Администратор
								if ($_SESSION["group"] == 1) {
									echo '
								<li>
                                    <a href="index.php" class="waves-effect">
                                        <i class="mdi mdi-home-variant-outline"></i>
                                        <span>Панель управления</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="users.php" class="waves-effect">
                                        <i class="mdi mdi-account-edit"></i>
                                        <span>Пользователи</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="clients.php" class="waves-effect">
                                        <i class="mdi mdi-account"></i>
                                        <span>Клиенты</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="orders.php" class="waves-effect">
                                        <i class="mdi mdi-format-list-checks"></i>
                                        <span>Заказы</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="forms.php" class="waves-effect">
                                        <i class="mdi mdi-email"></i>
                                        <span>Отчеты</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="errors.php" class="waves-effect">
                                        <i class="mdi mdi-alert-circle"></i>
                                        <span>Ошибки</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="warehouse.php" class="waves-effect">
                                        <i class="mdi mdi-warehouse"></i>
                                        <span>Склад</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://srv.qyc.kz/phpmyadmin/index.php" class="waves-effect">
                                        <i class="mdi mdi-database-arrow-up-outline"></i>
                                        <span>Открыть базу данных</span>
                                    </a>
                                </li>';
								}
								?>
                                <?php // Специалист
								if ($_SESSION["group"] == 2) {
									echo '
								<li>
                                    <a href="index.php" class="waves-effect">
                                        <i class="mdi mdi-account"></i>
                                        <span>Мой профиль</span>
                                    </a>
                                </li>
								<li>
                                    <a href="clients.php" class="waves-effect">
                                        <i class="mdi mdi-account-edit"></i>
                                        <span>Клиенты</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="forms.php" class="waves-effect">
                                        <i class="mdi mdi-list-status"></i>
                                        <span>История актов</span>
                                    </a>
                                </li>';
								}
								?>
								<?php // Модератор
								if ($_SESSION["group"] == 3) {
									echo '
								<li>
                                    <a href="index.php" class="waves-effect">
                                        <i class="mdi mdi-account"></i>
                                        <span>Панель управления</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="users.php" class="waves-effect">
                                        <i class="mdi mdi-account-edit"></i>
                                        <span>Пользователи</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="clients.php" class="waves-effect">
                                        <i class="mdi mdi-account"></i>
                                        <span>Клиенты</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="orders.php" class="waves-effect">
                                        <i class="mdi mdi-format-list-checks"></i>
                                        <span>Заказы</span>
                                    </a>
                                </li>
								<li>
                                    <a href="forms.php" class="waves-effect">
                                        <i class="mdi mdi-email"></i>
                                        <span>Отчеты</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="forms.php" class="waves-effect">
                                        <span>&nbsp;&nbsp;&nbsp;&nbsp;Отчеты (вид 1)</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="forms.php?view=2" class="waves-effect">
                                        <span>&nbsp;&nbsp;&nbsp;&nbsp;Отчеты (вид 2)</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="errors.php" class="waves-effect">
                                        <i class="mdi mdi-alert-circle"></i>
                                        <span>Ошибки</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="warehouse.php" class="waves-effect">
                                        <i class="mdi mdi-warehouse"></i>
                                        <span>Склад</span>
                                    </a>
                                </li>';
								}
								?>
								<?php // Бухгалтер
								if ($_SESSION["group"] == 4) {
									echo '
								<li>
                                    <a href="index.php" class="waves-effect">
                                        <i class="mdi mdi-account"></i>
                                        <span>Панель управления</span>
                                    </a>
                                </li>
								<li>
                                    <a href="forms.php" class="waves-effect">
                                        <i class="mdi mdi-email"></i>
                                        <span>Отчеты</span>
                                    </a>
                                </li>
								<li>
                                    <a href="sendform.php?selectgu=0" class="waves-effect">
                                        <i class="mdi mdi-account-search"></i>
                                        <span>ГУ</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="warehouse.php" class="waves-effect">
                                        <i class="mdi mdi-warehouse"></i>
                                        <span>Склад</span>
                                    </a>
                                </li>';
								}
								?>
                                <?php // Бухгалтер
								if ($_SESSION["group"] == 5) {
									echo '
                                    <li>
                                        <a href="warehouse.php" class="waves-effect">
                                            <i class="mdi mdi-warehouse"></i>
                                            <span>Склад</span>
                                        </a>
                                    </li>
                                    ';
								}
								?>
								<?php // ГУ
								if ($_SESSION['group'][0] == '%') {
									echo '
								<li>
                                    <a href="index.php" class="waves-effect">
                                        <i class="mdi mdi-account"></i>
                                        <span>Главная</span>
                                    </a>
                                </li>
								<li>
                                    <a href="forms.php" class="waves-effect">
                                        <i class="mdi mdi-email"></i>
                                        <span>Отчеты</span>
                                    </a>
                                </li>';
								}
								?>

                        </ul>
                    </div>
                    <!-- Sidebar -->
                </div>
            </div>
            <!-- Left Sidebar End -->