<?php
// Include config file
require'config.php';
 
// Define variables and initialize with empty values
$first_name = $last_name =  $SSN = $street = $street_number = $postal_code = $city = $start_date = $finish_date = $position = $hotel_name
	= $hotel_group_name = $hotel_id = $hotel_group_id =  "";
$first_name_err = $last_name_err =  $SSN_err = $street_err = $street_number_err = $postal_code_err = $city_err = $start_date_err
 = $finish_date_err = $position_err = $hotel_name_err = $group_name_err  =  "";
 
// Processing form data when form is submitted
if(isset($_POST["irs"]) && !empty($_POST["irs"]) && isset($_POST["Work_ID"]) && !empty($_POST["Work_ID"])){
    // Get hidden input value
	$IRS_Number = $_POST["irs"];
	$Work_id = $_POST["Work_ID"];
    // Validate name
    $input_first_name = trim($_POST["EFirst_Name"]);
    if(empty($input_first_name)){
		$first_name_err = "Please enter a first name.";
    } elseif(!filter_var(trim($_POST["EFirst_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
		$first_name_err = 'Please enter a valid first name.';
    } else{
        $first_name = $input_first_name;
    }
    
    $input_group_name = trim($_POST["Group_Name"]);
    if(empty($input_group_name)){
        $group_name_err = "Please enter a group name.";
    } elseif(!filter_var(trim($_POST["Group_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $group_name_err = 'Please enter a valid group name.';
    } else{
		$sql = "SELECT Hotel_Group_ID FROM Hotel_Group WHERE Hotel_Group_Name = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s", $param_group_name);
		$param_group_name = $input_group_name;
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows == 1){
			$group_name = $input_group_name;
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$hotel_group_id = $row["Hotel_Group_ID"];
			$input_hotel_name = trim($_POST["Hotel_Name"]);
			if(empty($input_hotel_name)){
				$hotel_name_err = "Please enter a hotel name.";
			}elseif(!filter_var(trim($_POST["Hotel_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
				$hotel_name_err = 'Please enter a valid hotel name.';
			} else{
				$sql = "SELECT Hotel_ID FROM Hotel WHERE Hotel_Group_Hotel_Group_ID = ? AND Hotel_Name = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("is", $hotel_group_id, $param_hotel_name);
				$param_hotel_name = $input_hotel_name;
				$stmt->execute();
				$result = $stmt->get_result();
			if($result->num_rows == 1){
				$hotel_name = $input_hotel_name;
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$hotel_id = $row["Hotel_ID"];
			}else{
				$hotel_name_err = 'Please enter a valid hotel name.';
			}
		}
		}else{
			$group_name_err = 'Please enter a valid group name.';
		}
	}
	
    
    $input_last_name = trim($_POST["ELast_Name"]);
    if(empty($input_last_name)){
        $last_name_err = "Please enter a last name.";
    } elseif(!filter_var(trim($_POST["ELast_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $last_name_err = 'Please enter a valid last name.';
    } else{
        $last_name = $input_last_name;
    }
    
    
	// Validate salary
    $input_ssn = trim($_POST["ESocial_Security_Number"]);
    if(empty($input_ssn)){
        $SSN_err = "Please enter the SSN number.";     
    } elseif(!ctype_digit($input_ssn)){
        $SSN_err = 'Please enter a positive integer value.';
    } else{
        $SSN = $input_ssn;
    }	
		
    $input_street = trim($_POST["EStreet"]);
    if(empty($input_street)){
        $street_err = "Please enter a street.";
    } elseif(!filter_var(trim($_POST["EStreet"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $street_err = 'Please enter a valid street name.';
    } else{
        $street = $input_street;
    } 
	$input_street_number = trim($_POST["ESt_Number"]);
    if(empty($input_street_number)){
        $street_number_err = "Please enter the street number.";     
    } elseif(!ctype_digit($input_street_number)){
        $street_number_err = 'Please enter a positive integer value.';
    } else{
        $street_number = $input_street_number;
    }
	
	$input_postal_code = trim($_POST["EPostal_Code"]);
    if(empty($input_postal_code)){
        $postal_code_err = "Please enter the postal code.";     
    } elseif(!ctype_digit($input_postal_code)){
        $postal_code_err = 'Please enter a positive integer value.';
    } else{
       	$postal_code = $input_postal_code;
    } 
	$input_city = trim($_POST["ECity"]);
    if(empty($input_city)){
        $city_err = "Please enter a city.";
    } elseif(!filter_var(trim($_POST["ECity"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $city_err = 'Please enter a valid city name.';
    } else{
        $city = $input_city;
    }    
    
    $input_start_date = $_POST["EStart_Date"];
	$format = 'Y-m-d';
	$d = DateTime::createFromFormat($format, $input_start_date);
    	if ($d && $d->format($format) == $input_start_date){
		$start_date = $input_start_date;
	} else{
		$start_date_err = 'Please enter a valid date and time.';
	}
	
	$input_finish_date = $_POST["EFinish_Date"];
	$format = 'Y-m-d';
	$d = DateTime::createFromFormat($format, $input_finish_date);
    	if ($d && $d->format($format) == $input_finish_date){
		$finish_date = $input_finish_date;
	} else{
		$finish_date_err = 'Please enter a valid date and time.';
	}
	
	$input_position = trim($_POST["EPosition"]);
    if(empty($input_position)){
        $position_err = "Please enter a position.";
    } elseif(!filter_var(trim($_POST["EPosition"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $position_err = 'Please enter a valid position.';
    } else{
        $position = $input_position;
    } 
   
    // Check input errors before inserting in database
   if(empty($first_name_err) && empty($last_name_err)  && empty($SSN_err) && empty($street_err) && empty($street_number_err) 
		&& empty($postal_code_err) && empty($city_err) && empty($start_date_err) && empty($finish_date_err) && empty($position_err) && empty($hotel_name_err) && empty($group_name_err)) {
        // Prepare an update statement
        
		
		$sql = "UPDATE Employee SET ELast_Name=? ,EFirst_Name=? ,ESocial_Security_Number=? ,EStreet=? ,ESt_Number=? ,EPostal_Code=?, ECity=? WHERE IRS_Number_E=?";
      
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param( "ssisiisi", $param_last_name, $param_first_name, $param_ssn, $param_street, $param_street_number, $param_postal_code, $param_city, $param_irs);
            
            // Set parameters
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_irs = $IRS_Number ;
            $param_ssn = $SSN;
			$param_street = $street;
			$param_street_number = $street_number;
			$param_postal_code = $postal_code;
			$param_city = $city;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
				$sql = "UPDATE Works SET WStart_Date=?, WPosition = ?, WFinish_Date = ?, 
							Hotel_Hotel_ID = ?, Hotel_Hotel_Group_Hotel_Group_ID = ?  WHERE Work_ID = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("sssiii", $param_start_date, $param_position, $param_finish_date, $param_hotel_id, $param_hotel_group_id , $param_work_id);
				$param_start_date = $start_date;
				$param_position = $position;
				$param_finish_date = $finish_date;
				$param_hotel_id = $hotel_id;
				$param_hotel_group_id = $hotel_group_id;
				$param_work_id = $Work_id;
				echo $start_date;
				echo $finish_date;
				echo $position;
				echo $hotel_id;
				echo $hotel_group_id;
				echo $Work_id;
				$stmt->execute();
                // Records updated successfully. Redirect to landing page
                header("location: pageAdmin.php?filter=4");
				exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        $stmt->close();
    }
    
    // Close connection
    $conn->close();
} else{
	
    // Check existence of id parameter before processing further
    if(isset($_GET["irs"]) && !empty(trim($_GET["irs"]) && isset($_GET["Work_ID"]) && !empty($_GET["Work_ID"]))){
        // Get URL parameter
        $IRS_Number_E =  trim($_GET["irs"]);
        $Work_ID = trim($_GET["Work_ID"]);
        // Prepare a select statement
        $sql = "SELECT * FROM Employee WHERE IRS_Number_E = ?";
      if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param( "i", $param_id);
            
            // Set parameters
            $param_id = $IRS_Number_E;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $result = $stmt->get_result();
                
                if($result->num_rows == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                     $row = $result->fetch_array(MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $first_name = $row["EFirst_Name"];
					$last_name = $row["ELast_Name"];
					$SSN = $row["ESocial_Security_Number"];
					$street = $row["EStreet"];
					$street_number = $row["ESt_Number"];
					$postal_code = $row["EPostal_Code"];
					$city = $row["ECity"];
					$irs_number = $row["IRS_Number_E"];
					
					$sql = "SELECT WStart_Date, WFinish_Date ,WPosition, Hotel_Hotel_ID,
						Hotel_Hotel_Group_Hotel_Group_ID FROM Works WHERE Work_ID = ?";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param( "i", $Work_ID);
					$stmt->execute();
					$result = $stmt->get_result();
					if($result->num_rows == 1){
						/* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
						$row = $result->fetch_array(MYSQLI_ASSOC);
						$start_date = $row["WStart_Date"];
						$finish_date = $row["WFinish_Date"];
						$position = $row["WPosition"];
						$hotel_id = $row["Hotel_Hotel_ID"];
						$group_id = $row["Hotel_Hotel_Group_Hotel_Group_ID"];
						
						$sql_for_hotel = "SELECT Hotel_Name FROM Hotel WHERE Hotel_ID = ? AND Hotel_Group_Hotel_Group_ID = ?";
						$stmt = $conn->prepare($sql_for_hotel);
						$stmt->bind_param( "ii", $hotel_id,$group_id );
						$stmt->execute();
						$result = $stmt->get_result();
						$row = $result->fetch_array(MYSQLI_ASSOC);
						$hotel_name = $row["Hotel_Name"];
						
						$sql_for_group = "SELECT Hotel_Group_Name FROM Hotel_Group WHERE Hotel_Group_ID = ?";
						$stmt = $conn->prepare($sql_for_group);
						$stmt->bind_param( "i", $group_id);
						$stmt->execute();
						$result = $stmt->get_result();
						$row = $result->fetch_array(MYSQLI_ASSOC);
						$group_name = $row["Hotel_Group_Name"];

                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
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
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
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
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                         <div class="form-group <?php echo (!empty($first_name_err)) ? 'has-error' : ''; ?>">
                            <label>First Name</label>
                            <input type="text" name="EFirst_Name" class="form-control" value="<?php echo $first_name; ?>">
                            <span class="help-block"><?php echo $first_name_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                            <label>Last Name</label>
                            <input type="text" name="ELast_Name" class="form-control" value="<?php echo $last_name; ?>">
                            <span class="help-block"><?php echo $last_name_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($SSN_err)) ? 'has-error' : ''; ?>">
                            <label>SSN Number</label>
                            <input type="text" name="ESocial_Security_Number" class="form-control" value="<?php echo $SSN; ?>">
                            <span class="help-block"><?php echo $SSN_Number_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($street_err)) ? 'has-error' : ''; ?>">
                            <label>Street</label>
                            <input type="text" name="EStreet" class="form-control" value="<?php echo $street; ?>">
                            <span class="help-block"><?php echo $street_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($street_number_err)) ? 'has-error' : ''; ?>">
                            <label>Street Number</label>
                            <input type="text" name="ESt_Number" class="form-control" value="<?php echo $street_number; ?>">
                            <span class="help-block"><?php echo $street_number_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($postal_code_err)) ? 'has-error' : ''; ?>">
                            <label>Postal Code</label>
                            <input type="text" name="EPostal_Code" class="form-control" value="<?php echo $postal_code; ?>">
                            <span class="help-block"><?php echo $postal_code_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($city_err)) ? 'has-error' : ''; ?>">
                            <label>City</label>
                            <input type="text" name="ECity" class="form-control" value="<?php echo $city; ?>">
                            <span class="help-block"><?php echo $city_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($group_name_err)) ? 'has-error' : ''; ?>">
                            <label>Hotel Group</label>
                            <input type="text" name="Group_Name" class="form-control" value="<?php echo $group_name; ?>">
                            <span class="help-block"><?php echo $group_name_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($hotel_name_err)) ? 'has-error' : ''; ?>">
                            <label>Hotel Name</label>
                            <input type="text" name="Hotel_Name" class="form-control" value="<?php echo $hotel_name; ?>">
                            <span class="help-block"><?php echo $hotel_name_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($start_date_err)) ? 'has-error' : ''; ?>">
                            <label>Start Date</label>
                            <input type="date" name="EStart_Date" class="form-control" value="<?php echo $start_date; ?>">
                            <span class="help-block"><?php echo $start_date_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($finish_date_err)) ? 'has-error' : ''; ?>">
                            <label>Finish Date</label>
                            <input type="date" name="EFinish_Date" class="form-control" value="<?php echo $finish_date; ?>">
                            <span class="help-block"><?php echo $finish_date_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($position_err)) ? 'has-error' : ''; ?>">
                            <label>Position</label>
                            <input type="text" name="EPosition" class="form-control" value="<?php echo $position; ?>">
                            <span class="help-block"><?php echo $position_err;?></span>
                        </div>
                        <input type="hidden" name="irs" value="<?php echo $_GET["irs"]; ?>"/>
                        <input type="hidden" name="Work_ID" value="<?php echo $_GET["Work_ID"]; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="pageAdmin.php?filter=4" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>			
    </div>
</body>
</html>
