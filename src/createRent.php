<?php
// Include config file
require 'config.php';

$start_date = $finish_date = $employee_irs = $customer_irs = $room_id = "";
$start_date_err = $finish_date_err  = $employee_irs_err = $customer_irs_err = $room_id_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	$input_finish_date = $_POST["RFinish_Date"];
	$format = 'Y-m-d';
	$d = DateTime::createFromFormat($format, $input_finish_date);
    if ($d && $d->format($format) == $input_finish_date){
		$finish_date = $input_finish_date;
	} else{
		$finish_date_err = 'Please enter a valid date.';
	}
	
	$input_start_date = $_POST["RStart_Date"];
	$format = 'Y-m-d';
	$d = DateTime::createFromFormat($format, $input_start_date);
    if ($d && $d->format($format) == $input_start_date){
		$start_date = $input_start_date;
	} else{
		$start_date_err = 'Please enter a valid date.';
	}
	$in_dt = new DateTime($input_start_date);
	$out_dt = new DateTime($input_finish_date);
	
	if ($in_dt > $out_dt){
		$start_date_err = "Start date can t be after finish date"; 
	} 
	
	
	$input_employee_irs = trim($_POST["Employee_IRS_Number_E"]);
	if(empty($input_employee_irs)){
        $employee_irs_err = "Please enter the IRS number of the employee.";     
    } elseif(!ctype_digit($input_employee_irs)){
        $employee_irs_err = 'Please enter a positive integer value.';
    } else{
		$sql = "SELECT * FROM Employee WHERE IRS_Number_E = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $param_employee_irs);
		$param_employee_irs = $input_employee_irs;
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows == 1){
			$employee_irs = $input_employee_irs;
		}
		else{
			$employee_irs_err = 'Please enter a valid employee irs.';
		}
	}
	
	$input_customer_irs = trim($_POST["Customer_IRS_Number_C"]);
	if(empty($input_customer_irs)){
        $customer_irs_err = "Please enter the IRS number of the customer.";     
    } elseif(!ctype_digit($input_customer_irs)){
        $customer_irs_err = 'Please enter a positive integer value.';
    } else{
		$sql = "SELECT * FROM Customer WHERE IRS_Number_C = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $param_customer_irs);
		$param_customer_irs = $input_customer_irs;
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows == 1){
			$customer_irs = $input_customer_irs;
		}
		else{
			$customer_irs_err = 'Please enter a valid customer irs.';
		}
	}
	
	$input_room_id = trim($_POST["Hotel_room_Room_ID"]);
	if(empty($input_room_id)){
        $room_id_err = "Please enter the Room ID.";     
    } elseif(!ctype_digit($input_room_id)){
        $room_id_err = 'Please enter a positive integer value.';
    } else{
		$sql = "SELECT * FROM Hotel_room WHERE Room_ID = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $param_room_id);
		$param_room_id = $input_room_id;
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows == 1){
			$room_id = $input_room_id;
		}
		else{
			$room_id_err = 'Please enter a valid room id.';
		}
	}
	
    $paid = trim($_POST["Paid"]);
    
    if(empty($start_date_err) && empty($finish_date_err) && empty($customer_irs_err) && empty($room_id_err) && empty($employee_irs_err)){
		
		$sql = "INSERT INTO Rents (	RStart_Date, RFinish_Date, 	Employee_IRS_Number_E, 	Customer_IRS_Number_C, 	Hotel_room_Room_ID) 
					VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE Customer_IRS_Number_C = Customer_IRS_Number_C";

		
         if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
             $stmt->bind_param("ssiii", $param_start_date, $param_finish_date, $param_employee_irs, $param_customer_irs, $param_room_id);
            
            // Set parameters
            $param_start_date = $start_date;
            $param_finish_date = $finish_date;
            $param_employee_irs = $employee_irs;
            $param_customer_irs = $customer_irs;
            $param_room_id = $room_id;
            
            if($stmt->execute()){
				header("location: pageAdmin.php?filter=7");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
            
            }
         
        $stmt->close();
    }
    
    // Close connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
   <link rel="stylesheet"  href="styles/bgstyle.css"> 
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Create Record</h2>
                    </div>
                    <p>Please fill this form and submit to add rent record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($start_date_err)) ? 'has-error' : ''; ?>">
                            <label>Start Date</label>
                            <input type="date" name="RStart_Date" class="form-control" value="<?php echo $start_date; ?>">
                            <span class="help-block"><?php echo $start_date_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($finish_date_err)) ? 'has-error' : ''; ?>">
                            <label>Finish Date</label>
                            <input type="date" name="RFinish_Date" class="form-control" value="<?php echo $finish_date; ?>">
                            <span class="help-block"><?php echo $finish_date_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($employee_irs_err)) ? 'has-error' : ''; ?>">
                            <label>Employee IRS</label>
                            <input type="text" name="Employee_IRS_Number_E" class="form-control" value="<?php echo $employee_irs; ?>">
                            <span class="help-block"><?php echo $employee_irs_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($customer_irs_err)) ? 'has-error' : ''; ?>">
                            <label>Customer IRS</label>
                            <input type="text" name="Customer_IRS_Number_C" class="form-control" value="<?php echo $customer_irs; ?>">
                            <span class="help-block"><?php echo $customer_irs_err;?></span>
                        </div>
                         <div class="form-group <?php echo (!empty($room_id_err)) ? 'has-error' : ''; ?>">
                            <label>Room ID</label>
                            <input type="text" name="Hotel_room_Room_ID" class="form-control" value="<?php echo $room_id; ?>">
                            <span class="help-block"><?php echo $room_id_err;?></span>
                        </div>
                        
		
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="pageAdmin.php?filter=7" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>


         
    
    
  

