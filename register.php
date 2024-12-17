<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $hash_password = $name = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Это имя уже занято.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Что-то пошло не так, попробуйте позднее.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Введите пароль.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Пароль должен содержать больще 6 символов.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Подтвердите пароль.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Пароли не совпадают.";
        }
    }
	
	// Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Введите e-mail.";     
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)){
        $email_err = "Неправильный e-mail.";
    } else{
        $email = trim($_POST["email"]);
    }	
	// Validate name
    $name = trim($_POST["fio"]);

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, email, name) VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_username, $param_password, $email, $name);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "ERROR";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
<?php
include('templates/header.php');
?>
	<body class="bg-pattern">
		<div class="bg-overlay"></div><br><br>
		<div class="account-pages my-5 pt-5">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-xl-4 col-lg-6 col-md-8">
						<div class="card">
							<div class="card-body p-4">
								<div class="">
									<div class="text-center">
										<a href="index.html" class="">
											<img src="static/images/logo-dark.png" alt="" height="24" class="auth-logo logo-dark mx-auto">
										</a>
									</div>
									<!-- end row -->
									<h4 class="font-size-18 text-muted mt-2 text-center">Регистрация</h4>
								   <br><br>
									<form class="form-horizontal" method="post">
										<div class="row">
										  <div class="col-md-12">
											<div class="mb-4">
											  <label class="form-label" for="login">Логин</label>
											  <input type="text" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" name="username" placeholder="Введите имя пользователя" required="required" value="<?php echo $username; ?>">
											  <span class="invalid-feedback"><?php echo $username_err; ?></span>
											</div>
											<div class="mb-4">
											  <label class="form-label" for="password">Пароль</label>
											  <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" required="required" placeholder="Введите пароль"/>
											  <span class="invalid-feedback"><?php echo $password_err; ?></span>
											</div>
											<div class="mb-4">
											  <label class="form-label" for="cpassword">Подтвердите пароль</label>
											  <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>" id="cpassword" required="required" placeholder="Подтвердите ваш пароль"/>
											  <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
											</div>
											<div class="mb-4">
											  <label class="form-label" for="email">E-mail</label>
											  <input type="email" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" required="required" placeholder="Например name@gmail.com"/>
											  <span class="invalid-feedback"><?php echo $email_err; ?></span>
											</div>
											<div class="mb-4">
											  <label class="form-label" for="name">Ваше ФИО</label>
											  <input type="text" class="form-control" name="fio" value="<?php echo $name; ?>" required="required" placeholder="Введите ФИО"/>
											</div>
											<div class="mb-4">
											  <input type="submit" class="btn btn-primary" value="Подтвердить">
											  <input type="reset" class="btn btn-secondary ml-2" value="Сбросить">
											</div>
										  </div>
										</div>
									  </div>
									</form>
								</div>
							</div>
						</div>
						<div class="mt-5 text-center">
							<p class="text-white-50">Уже зарегистрированы? <a href="login.php" class="fw-medium text-primary"> Войти </a> </p>
						</div>
					</div>
				</div>
				<!-- end row -->
			</div>
		</div>
		<!-- end Account pages -->
<?php
include('templates/footer.html');
?>