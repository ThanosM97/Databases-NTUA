<?php
// Include config file
require 'config.php';

$start_date = $finish_date = $paid = $customer_irs = $room_id = "";
$start_date_err = $finish_date_err  = $customer_irs_err = $room_id_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	$input_finish_date = $_POST["ResFinish_Date"];
	$format = 'Y-m-d';
	$d = DateTime::createFromFormat($format, $input_finish_date);
    if ($d && $d->format($format) == $input_finish_date){
		$finish_date = $input_finish_date;
	} else{
		$finish_date_err = 'Please enter a valid date.';
	}
	
	$input_start_date = $_POST["ResStart_Date"];
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
	
	

	$input_irs = trim($_POST["Customer_IRS_Number_C"]);
	if(empty($input_irs)){
        $customer_irs_err = "Please enter the IRS number.";     
    } elseif(!ctype_digit($input_irs)){
        $customer_irs_err = 'Please enter a positive integer value.';
    } else{
		$sql = "SELECT * FROM Customer WHERE IRS_Number_C = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $param_customer_irs);
		$param_customer_irs = $input_irs;
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows == 1){
			$customer_irs = $input_irs;
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
    $reserve_id = trim($_POST["Reserves_ID"]);
    if(empty($start_date_err) && empty($finish_date_err) && empty($customer_irs_err) && empty($room_id_err) ){
		
		$sql = "UPDATE Reserves SET	ResStart_Date = ?, ResFinish_Date = ? ,ResPaid = ?, Customer_IRS_Number_C = ?, Hotel_room_Room_ID = ? 
				WHERE Reserves_ID = ?";

		
         if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
             $stmt->bind_param("sssiii", $param_start_date, $param_finish_date, $param_paid, $param_customer_irs, $param_room_id, $param_reserve_id);
            
            // Set parameters
            $param_start_date = $start_date;
            $param_finish_date = $finish_date;
            $param_paid = $paid;
            $param_customer_irs = $customer_irs;
            $param_room_id = $room_id;
            $param_reserve_id = $reserve_id;
            if($stmt->execute()){
				header("location: pageAdmin.php?filter=6");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
            
            }
         
        $stmt->close();
    }
    
    // Close connection
    $conn->close();
} else{
	 // Check existence of id parameter before processing further
    if(isset($_GET["res_id"]) && !empty(trim($_GET["res_id"]))){
        // Get URL parameter
        $reserve_id =  trim($_GET["res_id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM Reserves WHERE Reserves_ID = ?";
      if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param( "i", $param_id);
            
            // Set parameters
            $param_id = $reserve_id;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $result = $stmt->get_result();
                
                if($result->num_rows == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                     $row = $result->fetch_array(MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $start_date = $row["ResStart_Date"];
					$finish_date = $row["ResFinish_Date"];
					$paid = $row["ResPaid"];
					$customer_irs = $row["Customer_IRS_Number_C"];
					$room_id = $row["Hotel_room_Room_ID"];
					
			
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    //header("location: error.php");
                    //exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
       
        $stmt->close();
        
        // Close connection
        $conn->close();
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
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
                        <h2>Update Record</h2>
                    </div>
                    <p>Please fill this form and submit to add reserve record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($start_date_err)) ? 'has-error' : ''; ?>">
                            <label>Start Date</label>
                            <input type="date" name="ResStart_Date" class="form-control" value="<?php echo $start_date; ?>">
                            <span class="help-block"><?php echo $start_date_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($finish_date_err)) ? 'has-error' : ''; ?>">
                            <label>Finish Date</label>
                            <input type="date" name="ResFinish_Date" class="form-control" value="<?php echo $finish_date; ?>">
                            <span class="help-block"><?php echo $finish_date_err;?></span>
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
                        <div class="form-group  dropdown">
							<label>Will he pay upfront?</label>
							<select  name="Paid" class=" w3-input w3-border w3-white" type="text" placeholder="Yes or No">
							<option value="Yes">Yes</option>
							<option value="No">No</option>
					</select>
                        </div>
						<input type="hidden" name="Reserves_ID" value="<?php echo $reserve_id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="pageAdmin.php?filter=6" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>


         
    
