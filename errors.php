<?php
include('templates/header.php');
?>
<?php 
session_start(); 
// Include config file
require_once "config.php";
require_once "engine.php";

$forms = get_all_from_table("forms");
$orders = get_all_from_table("orders");
$clients = get_all_from_table("clients");
$specialists = get_all_specialists();
$error_types = explode(',', get_setting('error_type'));
?>

<?php 
// Вывод для админа и модератора
if ($_SESSION['group']  == 1 || $_SESSION['group']  == 3) {?>

<body data-sidebar="dark">
    <!-- Begin page -->
    <div id="layout-wrapper">

        <div id="alert-container"></div>

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
                                <h4 class="mb-sm-0">Ошибки</h4>
                                <!-- <a class="btn btn-outline-primary" href="newform.php?neworder=1" title="Создать заказ" data-toggle="tooltip"><span class="fa fa-plus"> Создать заказ</span></a> -->
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <?php 
                        if (isset($_GET["do"])) {
                            switch ($_GET["do"]) {
                                case "archive":
                                    include('templates/errors/errors_archive.php');
                                    break;
                                default:
                                    include('templates/errors/errors_admin.php');
                                    break;
                            }
                        } else {
                            include('templates/errors/errors_admin.php'); 
                        }?>
                    </div> <!-- end content -->
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

        </div>
        <!-- end main content-->

<!-- END layout-wrapper -->
<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

<?php
include('templates/footer.html');
}
?>