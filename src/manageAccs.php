<?php 
session_start();

require 'loginconf.php';

$action = $_POST["action"];

if ($action == "Create"){
	$usrnm_input = test_input($_POST["username"]);
	$email_input = test_input($_POST["email"]);
	$pass_input1 = test_input($_POST["password"]);
	$pass_input2 = test_input($_POST["password2"]);
	$inputs[0] = $usrnm_input;
	$inputs[1] = $email_input;
	
	if (empty($usrnm_input)) {
		$err[0] = "Please enter a username.";
	} elseif (!preg_match('/^[a-zA-Z0-9]{5,}$/', $usrnm_input)) {
		$err[0] = "Username can only contain letters and numbers.";
	} elseif (!preg_match('/[a-zA-Z]/', $usrnm_input[0])) {
		$err[0] = "Username can only start with a letter.";
	} else {
		$sql = "SELECT * FROM admin WHERE username = '$usrnm_input'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0){
			$err[0] = "This username already exists.";
		} else {
			$usrnm = $usrnm_input;
		}
	}
	
	if (empty($email_input)){
		$err[1] = "Please enter an email.";
	} elseif (!filter_var($email_input,FILTER_VALIDATE_EMAIL)){
		$err[1] = "The correct email type is example@example.com";
	} else {
		$sql = "SELECT * FROM admin WHERE email = '$email_input'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0){
			$err[1] = "Email already used for another account.";
		} else {
			$email = $email_input;
		}
	}
	
	if (!empty($pass_input1) && ($pass_input1 == $pass_input2)){
		if (strlen($pass_input1) < 8){
			$err[2] = "Your password must contain at least 8 characters.<br>";
		}
		if (!preg_match("#[0-9]+#", $pass_input1)){
			$err[2] = $err[2] . "Your password must contain at least 1 number.<br>";
		}
		if (!preg_match("#[A-Z]+#", $pass_input1)){
			$err[2] = $err[2] . "Your password must contain at least 1 capital letter.<br>";
		}
		if (!preg_match("#[a-z]+#", $pass_input1)){
			$err[2] = $err[2] . "Your password must contain at least 1 lowercase letter.<br>";
		}
		$pass = $pass_input1;
	} elseif (!empty ($pass_input1)){
		$err[3] = "Password confirmation is not right.";
	} else {
		$err[2] = "Please enter a password.";
	}
	
	if (!isset($err)){
		$sql = "INSERT INTO admin (username, password, email) VALUES (? , ? , ?)";
		if ($stmt = $conn->prepare($sql)){
			$stmt->bind_param("sss", $usrnm, $pass , $email);
			if ($stmt->execute()){
				$msg = "New administrator account <br> has been succesfully created.";
			}else {
				header("location: error.php");
				exit();
			} 
		}else {
			echo "Oops! Something went wrong. Please try again later.";
		} 
	} else {
		$_SESSION['manage_errors'] = $err;
		$_SESSION['inputs'] = $inputs;
		header('Location: pageAdmin.php?filter=8'); 
	} 
}

if ($action == "ChangePass"){
	$usrnm_input = test_input($_POST["username"]);
	$pass_old_input = test_input($_POST["old_password"]);
	$pass_new_input1 = test_input($_POST["new_password"]);
	$pass_new_input2 = test_input($_POST["new_password2"]);
	$inputs[4] = $usrnm_input;
	
	if (!empty($pass_new_input1) && ($pass_new_input1 == $pass_new_input2)){
		if (strlen($pass_new_input1) < 8){
			$err[6] = "Your password must contain at least 8 characters.<br>";
		}
		if (!preg_match("#[0-9]+#", $pass_new_input1)){
			$err[6] = $err[6] . "Your password must contain at least 1 number.<br>";
		}
		if (!preg_match("#[A-Z]+#", $pass_new_input1)){
			$err[6] = $err[6] . "Your password must contain at least 1 capital letter.<br>";
		}
		if (!preg_match("#[a-z]+#", $pass_new_input1)){
			$err[6] = $err[6] . "Your password must contain at least 1 lowercase letter.<br>";
		}
		$pass = $pass_new_input1;
	} elseif (!empty ($pass_input1)){
		$err[7] = "Password confirmation is not right.";
	} else {
		$err[6] = "Please enter the new password.";
	}

	$sql = "SELECT * FROM admin WHERE username = '$usrnm_input'";
	$result = $conn->query($sql);
		if ($result->num_rows == 0){
			$err[4] = "There is no account with this username.";
			$_SESSION['manage_errors'] = $err; 
			$_SESSION['inputs'] = $inputs; 
			header("location:pageAdmin.php?filter=8");
		} else {
			$sql = "SELECT * FROM admin WHERE username = '$usrnm_input' AND password = '$pass_old_input'";
			$result = $conn->query($sql);
			if ($result->num_rows == 0){
				$err[5] = "Password is not correct.";
				$_SESSION['manage_errors'] = $err; 
				$_SESSION['inputs'] = $inputs; 
				header("location:pageAdmin.php?filter=8");
			} else {
				if (!isset($err)){
					$sql = "UPDATE admin SET password = '$pass' WHERE username = '$usrnm_input'";
					if (mysqli_query($conn, $sql)){
						$msg = 'Password has been <br> succesfully changed.';
					} else {
						$msg = 'Something went wrong!';
					}
				} else {
					$_SESSION['manage_errors'] = $err; 
					$_SESSION['inputs'] = $inputs; 
					header("location:pageAdmin.php?filter=8");
				}
			}
		}
}

if ($action == "Delete"){
	$usrnm_input = test_input($_POST["username"]);
	$email_input = test_input($_POST["email"]);
	$pass_input = test_input($_POST["password"]);
	$inputs[8] = $usrnm_input; 
	$inputs[9] = $email_input;

	$sql = "SELECT * FROM admin WHERE username = '$usrnm_input'";
	$result = $conn->query($sql);
		if ($result->num_rows == 0){
			$err[8] = "There is no account with this username.";
			$_SESSION['manage_errors'] = $err; 
			$_SESSION['inputs'] = $inputs;
			header("location:pageAdmin.php?filter=8");
		} else {
			$sql = "SELECT * FROM admin WHERE username = '$usrnm_input' AND password = '$pass_input'";
			$result = $conn->query($sql);
			if ($result->num_rows == 0){
				$err[10] = "Password is not correct.";
			}
			
			$sql = "SELECT * FROM admin WHERE username = '$usrnm_input' AND email = '$email_input'";
			$result = $conn->query($sql);
			if ($result-> num_rows == 0){
				$err[9] = "Email is not correct.";
			}
			
			if (!isset($err)){
				$sql = "DELETE FROM admin WHERE username = '$usrnm_input'";
				if ($conn->query($sql)){
					$msg = "The account has been <br> deleted succesfully";
				} else {
					$msg = "Something went wrong!";
				}
			} else {
				$_SESSION['manage_errors'] = $err; 
				$_SESSION['inputs'] = $inputs;
				header("location:pageAdmin.php?filter=8");
			}
		}
}

if ($action == "ChangeEm"){
	$usrnm_input = test_input($_POST["username"]);
	$pass_input = test_input($_POST["password"]);
	$email_old_input = test_input($_POST["old_email"]);
	$email_new_input = test_input($_POST["new_email"]);
	$inputs[11] = $usrnm_input;
	$inputs[13] = $email_old_input;
	$inputs[14] = $email_new_input;

	if (empty($email_new_input)){
		$err[14] = "Please enter the new email.";
	} elseif (!filter_var($email_new_input,FILTER_VALIDATE_EMAIL)){
		$err[14] = "The correct email type is example@example.com";
	} else {
		$sql = "SELECT * FROM admin WHERE email = '$email_new_input'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0){
			$err[14] = "This email is already used for another account.";
		} else {
			$email = $email_new_input;
		}
	}
	
	$sql = "SELECT * FROM admin WHERE username = '$usrnm_input'";
	$result = $conn->query($sql);
		if ($result->num_rows == 0){
			$err[11] = "There is no account with this username.";
			$_SESSION['manage_errors'] = $err; 
			$_SESSION['inputs'] = $inputs;
			header("location:pageAdmin.php?filter=8");
		} else {
			$sql = "SELECT * FROM admin WHERE username = '$usrnm_input' AND password = '$pass_input'";
			$result = $conn->query($sql);
			if ($result->num_rows == 0){
				$err[12] = "Password is not correct.";
			}
			
			$sql = "SELECT * FROM admin WHERE username = '$usrnm_input' AND email = '$email_old_input'";
			$result = $conn->query($sql);
			if ($result-> num_rows == 0){
				$err[13] = "Email is not correct. $email_old_input";
			}
			
			if (!isset($err)){
				$sql = "UPDATE admin SET email = '$email' WHERE username = '$usrnm_input'";
				if ($conn->query($sql)){
					$msg = "The email has been <br> changed succesfully";
				} else {
					$msg = "Something went wrong!";
				}
			} else {
				$_SESSION['manage_errors'] = $err;
				$_SESSION['inputs'] = $inputs; 
				header("location:pageAdmin.php?filter=8");
			}
		}
}

function test_input($data){
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet"  href="styles/bgstyle.css"> 
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                <div class="page-header">
 					<h><img src="images/logo.png" alt="calligraphy-fonts" border="0"></h>
 				</div>
                    <div class="alert alert-danger fade in">
                        <p><font size="5"><?php echo $msg; ?> </font></p>
                        <p><font size="4" color="red"> Redirecting to previous page after <span id="countdown">5</span> seconds...</p>
                    </div>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>

<script type ="text/javascript">
	var seconds = 5;
	
	function countdown() {
		seconds = seconds - 1;
		if (seconds < 0){
			window.location = "pageAdmin.php?filter=8";
		} else {
			document.getElementById("countdown").innerHTML = seconds;
			window.setTimeout("countdown()" , 1000);
		}
	}
	
	countdown();

</script>

