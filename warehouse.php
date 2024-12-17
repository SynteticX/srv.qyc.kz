<?php
include('templates/header.php');
?>
<?php 
session_start(); 
// Include config file
require_once "config.php";
require_once "engine.php";
require_once "engine/warehouse.php";

$warehouses = get_all_from_table("warehouse_warehouses");
$items = get_all_from_table("warehouse_items");
$cats = get_all_from_table("warehouse_category");
$transfers = get_all_from_table("warehouse_transfers");
?>

<?php 
// Вывод для админа и модератора
if ($_SESSION['group'] == 1 || $_SESSION['group']  == 3 || $_SESSION['group']  == 4 || $_SESSION['group']  == 5) {?>

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
                                <h4 class="mb-sm-0">Склад</h4>
                                <!-- <a class="btn btn-outline-primary" href="newform.php?neworder=1" title="Создать заказ" data-toggle="tooltip"><span class="fa fa-plus"> Создать заказ</span></a> -->
                                <!-- <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link" aria-current="page" href="?do=main">Склад</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="?do=income">Поступления</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="?do=outcome">Отправленные</a>
                                    </li>
                                </ul> -->
                                <div class="btn-group">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bars"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <?php 
                        if (!isset($_GET["do"]) || $_GET["do"] == "main") {
                            include('templates/warehouse/warehouse_all.php'); 
                            echo '<script>document.querySelector(".nav-tabs li:nth-child(1) > a").classList.add("active");</script>';
                            echo '<div class="d-flex row">';
                            include('templates/warehouse/warehouse_income.php');
                            include('templates/warehouse/warehouse_outcome.php');
                            echo '</div>';
                        }
                        if ($_GET["do"] == "income") {
                            include('templates/warehouse/warehouse_income.php');
                            echo '<script>document.querySelector(".nav-tabs li:nth-child(2) > a").classList.add("active");</script>';
                        }
                        if ($_GET["do"] == "outcome") {
                            include('templates/warehouse/warehouse_outcome.php');
                            echo '<script>document.querySelector(".nav-tabs li:nth-child(3) > a").classList.add("active");</script>';
                        }
                        ?>
                    </div> <!-- end content -->
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

        </div>
        <!-- end main content-->

<!-- END layout-wrapper -->
<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

<script>window.user_id = <?php echo $_SESSION["id"]; ?>;</script>

<?php
include('templates/footer.html');
}
?>