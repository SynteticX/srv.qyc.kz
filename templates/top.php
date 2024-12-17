<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if($_SESSION["loggedin"] != true){
    echo 'not logged in';
    header("Location: login.php");
    exit;
}
?>
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box text-center">
                <a href="index.php" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="static/images/logo-sm.png" alt="logo-sm-dark" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="static/images/logo-dark.png" alt="logo-dark" height="24">
                    </span>
                </a>

                <a href="index.php" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="static/images/logo-sm.png" alt="logo-sm-light" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="static/images/logo-light.png" alt="logo-light" height="24">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                <i class="ri-menu-2-line align-middle"></i>
            </button>

        </div>

        <div class="d-flex" id="top_right">
            <div class="dropdown d-inline-block user-dropdown">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="static/images/users/avatar-2.jpg"
                        alt=<?php echo $_SESSION["username"];?>>
                    <span class="d-none d-xl-inline-block ms-1"><?php echo $_SESSION["username"];?></span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->

                    <?php
					if ($_SESSION["group"] == 1) {
						echo '<a class="dropdown-item" href="index.php"><i class="ri-settings-fill align-middle me-1"></i> Панель управления</a>';
					}
                    ?>
                    <!-- <a class="dropdown-item" href="/"><i class="ri-shield-user-line align-middle me-1"></i> Profile</a> -->
                    <a class="dropdown-item text-danger" href="logout.php"><i class="ri-shut-down-line align-middle me-1 text-danger"></i> Выход</a>
                </div>
            </div>
        </div>
    </div>
    <?php
        //Вывод для администратора
        if ($_SESSION['group']  == 1) {
    ?>
    <script defer>
        $(document).ready(() => {
            let params = {"getTable": "clients"};
            let clients = getFormAsync(params).then(
                (data) => JSON.parse(data)
            ).then((clients) => {
                let client_counter_expired = 0;
                let client_counter_7days = 0;
                let date = Date.now();
                for (let client of clients) {
                    let client_date = Date.parse(client['ecp']);
                    if (client_date - date <= 0) {
                        client_counter_expired++;
                    }
                    if (client_date - date <= 604800000 && client_date - date > 0) {
                        client_counter_7days++;
                    }
                }

                if (client_counter_expired > 0) {
                    let alert = `
                    <div class="alert alert-danger my-2 p-3 h-100" role="alert">
                    ЭЦП истек: ${client_counter_expired}
                    </div>
                    `;
                    $('#top_right').prepend(alert);
                }
                if (client_counter_7days > 0) {
                    let alert = `
                    <div class="alert alert-warning my-2 p-3 h-100 mx-1" role="alert">
                    ЭЦП скоро закончится: ${client_counter_7days}
                    </div>
                    `;
                    $('#top_right').prepend(alert);
                }
            })
        });
    </script>
    <?php
        }
    ?>
</header>