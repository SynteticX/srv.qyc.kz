<?php
// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editclient'])){
		$url = $_GET['url'];
		header("location: $url");
		exit();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>QYC | Сообщение</title>
	<!-- App favicon -->
	<link rel="shortcut icon" href="static/images/favicon.ico">

	<!-- Bootstrap Css -->
	<link href="static/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
	<!-- Icons Css -->
	<link href="static/css/icons.min.css" rel="stylesheet" type="text/css" />
	<!-- App Css-->
	<link href="static/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
	<style>
		.wrapper{
			width: 600px;
			margin: 0 auto;
		}
	</style>
	<!-- JAVASCRIPT -->
	<script src="static/libs/jquery/jquery.min.js"></script>
	<script src="static/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5 mb-3"><?php echo $_GET['msg']; ?></h2><hr>
                    <form method="post">
                        <div class="alert">
                            <input type="hidden" name="editclient" value="1"/>
                            <p>Нажмите вернуться, чтобы продолжить работу</p>
                            <p>
                                <input type="submit" value="Вернуться" class="btn btn-primary">
                                <a href="index.php" class="btn btn-secondary">На главную</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>