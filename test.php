<?php
require_once "engine.php";
include('templates/header.php');

// set_moderator_report();
$month = date_format(date_create(get_current_period()[0]),'m');
$year = date_format(date_create(get_current_period()[0]),'Y');

echo $month;
echo $year;

?>

<?php
include('templates/footer.html');
?>