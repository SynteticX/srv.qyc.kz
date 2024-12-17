<?php 
session_start(); 
?>
<?php
//Удаление клиента
if (($_SESSION['group']  == 1 || $_SESSION['group']  == 3) and isset($_GET['editclient'])) {
	// Include config file
	require_once "config.php";

	$name_err = "";

	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		// Prepare an insert statement
		$sql = "DELETE FROM clients WHERE id = ?";
		 
		if($stmt = mysqli_prepare($link, $sql)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "i", $param_id);
			
			// Set parameters
			$param_id = trim($_GET['editclient']);
			
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Records created successfully. Redirect to landing page
				header("location: clients.php");
				exit();
			} else{
				echo "Oops! Something went wrong. Please try again later.";
			}
		}
		
		// Close statement
		mysqli_stmt_close($stmt);
		
		// Close connection
		mysqli_close($link);
	}
} 
?>
<?php
//Удаление пользователя
if (($_SESSION['group']  == 1 || $_SESSION['group']  == 3) and isset($_GET['edituser'])) {
	// Include config file
	require_once "config.php";

	$name_err = "";

	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		// Prepare an insert statement
		$sql = "DELETE FROM users WHERE id = ?";
		 
		if($stmt = mysqli_prepare($link, $sql)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "i", $param_id);
			
			// Set parameters
			$param_id = trim($_GET['edituser']);
			
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Records created successfully. Redirect to landing page
				header("location: users.php");
				exit();
			} else{
				echo "Oops! Something went wrong. Please try again later.";
			}
		}
		
		// Close statement
		mysqli_stmt_close($stmt);
		
		// Close connection
		mysqli_close($link);
	}
} 
?>
<?php
//Удаление заказа
if (($_SESSION['group']  == 1 || $_SESSION['group']  == 3) and isset($_GET['editorder'])) {
	// Include config file
	require_once "config.php";

	$name_err = "";

	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		// Prepare an insert statement
		$sql = "DELETE FROM orders WHERE id = ?";
		 
		if($stmt = mysqli_prepare($link, $sql)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "i", $param_id);
			
			// Set parameters
			$param_id = trim($_GET['editorder']);
			
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Records created successfully. Redirect to landing page
				header("location: orders.php");
				exit();
			} else{
				echo "Oops! Something went wrong. Please try again later.";
			}
		}
		
		// Close statement
		mysqli_stmt_close($stmt);
		
		// Close connection
		mysqli_close($link);
	}
} 
?>
<?php
//Удаление формы
if (($_SESSION['group']  == 1 || $_SESSION['group']  == 3) and isset($_GET['editform'])) {
	// Include config file
	require_once "config.php";

	$name_err = "";

	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		// Prepare an insert statement
		$sql = "DELETE FROM forms WHERE id = ?";
		 
		if($stmt = mysqli_prepare($link, $sql)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "i", $param_id);
			
			// Set parameters
			$param_id = trim($_GET['editform']);
			
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Records created successfully. Redirect to landing page
				header("location: forms.php");
				exit();
			} else{
				echo "Oops! Something went wrong. Please try again later.";
			}
		}
		
		// Close statement
		mysqli_stmt_close($stmt);
		
		// Close connection
		mysqli_close($link);
	}
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Удаление записи</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5 mb-3">Удаление записи</h2>
                    <form method="post">
                        <div class="alert alert-danger">
                            <input type="hidden" name="editclient" value=""/>
                            <p>Вы уверены, что хотите удалить эту запись?</p>
                            <p>
                                <input type="submit" value="Да" class="btn btn-danger">
                                <a href="orders.php" class="btn btn-secondary">Нет</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>