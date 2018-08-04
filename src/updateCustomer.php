<?php
// Include config file
require'config.php';
 
// Define variables and initialize with empty values
$first_name = $last_name = $IRS_Number = $SSN = $street = $street_number = $postal_code = $city = $first_registration = "";
$first_name_err = $last_name_err = $IRS_Number_err = $SSN_err = $street_err = $street_number_err = $postal_code_err = $city_err = $first_registration_err = "";

// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $IRS_Number = $_POST["id"];
    
    // Validate name
    $input_first_name = trim($_POST["CFirst_Name"]);
    if(empty($input_first_name)){
        $first_name_err = "Please enter a first name.";
    } elseif(!filter_var(trim($_POST["CFirst_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $first_name_err = 'Please enter a valid first name.';
    } else{
        $first_name = $input_first_name;
    }
    
    $input_last_name = trim($_POST["CLast_Name"]);
    if(empty($input_last_name)){
        $last_name_err = "Please enter a last name.";
    } elseif(!filter_var(trim($_POST["CLast_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $last_name_err = 'Please enter a valid last name.';
    } else{
        $last_name = $input_last_name;
    }
    
    // Validate salary
    $input_irs = trim($_POST["IRS_Number_C"]);
    if(empty($input_irs)){
        $IRS_Number_err = "Please enter the IRS number.";     
    } elseif(!ctype_digit($input_irs)){
        $IRS_Number_err = 'Please enter a positive integer value.';
    } else{
        $irs_number = $input_irs;
    }
	// Validate salary
    $input_ssn = trim($_POST["CSocial_Security_Number"]);
    if(empty($input_ssn)){
        $SSN_err = "Please enter the SSN number.";     
    } elseif(!ctype_digit($input_ssn)){
        $SSN_err = 'Please enter a positive integer value.';
    } else{
        $SSN = $input_ssn;
    }	
		
    $input_street = trim($_POST["CStreet"]);
    if(empty($input_street)){
        $street_err = "Please enter a street.";
    } elseif(!filter_var(trim($_POST["CStreet"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $street_err = 'Please enter a valid street name.';
    } else{
        $street = $input_street;
    } 
	$input_street_number = trim($_POST["CSt_Number"]);
    if(empty($input_street_number)){
        $street_number_err = "Please enter the street number.";     
    } elseif(!ctype_digit($input_street_number)){
        $street_number_err = 'Please enter a positive integer value.';
    } else{
        $street_number = $input_street_number;
    }
	
	$input_postal_code = trim($_POST["CPostal_Code"]);
    if(empty($input_postal_code)){
        $postal_code_err = "Please enter the postal code.";     
    } elseif(!ctype_digit($input_postal_code)){
        $postal_code_err = 'Please enter a positive integer value.';
    } else{
       	$postal_code = $input_postal_code;
    } 
	$input_city = trim($_POST["CCity"]);
    if(empty($input_city)){
        $city_err = "Please enter a city.";
    } elseif(!filter_var(trim($_POST["CCity"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $city_err = 'Please enter a valid city name.';
    } else{
        $city = $input_city;
    } 
   	$first_registration = trim($_POST["CFirst_Registration"]);
	$first_registration_date=substr($first_registration, 0, 10);
    if(empty($input_city)){
        $first_registration_err = "Please enter a date.";
	}
    // Check input errors before inserting in database
    if(empty($first_name_err) && empty($last_name_err) && empty($IRS_Number_err) && empty($SSN_err) && empty($street_err) && empty($street_number_err) && empty($postal_code_err) && empty($city_err) && empty($first_registration_err)) {
        // Prepare an update statement
        $sql = "UPDATE Customer SET CLast_Name=? ,CFirst_Name=? ,CSocial_Security_Number=? ,CStreet=? ,CSt_Number=? ,CPostal_Code=?, CCity=?, CFirst_Registration=?  WHERE IRS_Number_C=?";
       
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param( "ssisiissi", $param_last_name, $param_first_name, $param_ssn, $param_street, $param_street_number, $param_postal_code, $param_city, $param_first_registration,$param_irs);
            
            // Set parameters
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_irs = $irs_number;
            $param_ssn = $SSN;
		$param_street = $street;
		$param_street_number = $street_number;
		$param_postal_code = $postal_code;
		$param_city = $city;
		$param_first_registration = $first_registration;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records updated successfully. Redirect to landing page
                header("location: pageAdmin.php?filter=5");
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
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $IRS_Number_C =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM Customer WHERE IRS_Number_C = ?";
      if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param( "i", $param_id);
            
            // Set parameters
            $param_id = $IRS_Number_C;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $result = $stmt->get_result();
                
                if($result->num_rows == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                     $row = $result->fetch_array(MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $first_name = $row["CFirst_Name"];
		$last_name = $row["CLast_Name"];
                $SSN = $row["CSocial_Security_Number"];
                $street = $row["CStreet"];
		$street_number = $row["CSt_Number"];
		$postal_code = $row["CPostal_Code"];
                $city = $row["CCity"];
                $irs_number = $row["IRS_Number_C"];
		$first_registration = $row["CFirst_Registration"];

		$first_registration_date=substr($first_registration, 0, 10);
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
                            <input type="text" name="CFirst_Name" class="form-control" value="<?php echo $first_name; ?>">
                            <span class="help-block"><?php echo $first_name_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                            <label>Last Name</label>
                            <input type="text" name="CLast_Name" class="form-control" value="<?php echo $last_name; ?>">
                            <span class="help-block"><?php echo $last_name_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($IRS_Number_err)) ? 'has-error' : ''; ?>">
                            <label>IRS Number</label>
                            <input type="text" name="IRS_Number_C" class="form-control" value="<?php echo $irs_number; ?>">
                            <span class="help-block"><?php echo $IRS_Number_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($SSN_err)) ? 'has-error' : ''; ?>">
                            <label>SSN Number</label>
                            <input type="text" name="CSocial_Security_Number" class="form-control" value="<?php echo $SSN; ?>">
                            <span class="help-block"><?php echo $SSN_Number_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($street_err)) ? 'has-error' : ''; ?>">
                            <label>Street</label>
                            <input type="text" name="CStreet" class="form-control" value="<?php echo $street; ?>">
                            <span class="help-block"><?php echo $street_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($street_number_err)) ? 'has-error' : ''; ?>">
                            <label>Street Number</label>
                            <input type="text" name="CSt_Number" class="form-control" value="<?php echo $street_number; ?>">
                            <span class="help-block"><?php echo $street_number_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($postal_code_err)) ? 'has-error' : ''; ?>">
                            <label>Postal Code</label>
                            <input type="text" name="CPostal_Code" class="form-control" value="<?php echo $postal_code; ?>">
                            <span class="help-block"><?php echo $postal_code_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($city_err)) ? 'has-error' : ''; ?>">
                            <label>City</label>
                            <input type="text" name="CCity" class="form-control" value="<?php echo $city; ?>">
                            <span class="help-block"><?php echo $city_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($first_registration_err)) ? 'has-error' : ''; ?>">
				<label>First Registration (date)</label>
  				<input type="date" name="CFirst_Registration" class="form-control" value="<?php echo $first_registration_date; ?>">
  				<span class="help-block"><?php echo $first_registration_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $irs_number; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="pageAdmin.php?filter=5" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
