<?php
$DB_SERVER = '127.0.0.1';
$DB_USERNAME = 'root';
$DB_PASSWORD = 'EjXxwst2wCYacDm0';
$DB_NAME = 'qyc_crud';

$link = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
