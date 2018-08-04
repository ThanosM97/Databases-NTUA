<?php

// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Include config file
    require'config.php';
    
    // Prepare a select statement
    $sql = "SELECT * FROM Customer WHERE IRS_Number_C = ?";
    
     if($stmt = $conn->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param( "i", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["id"]);
        
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
	
            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
                header("location: error.php");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    $stmt->close();
    
    // Close connection
    $conn->close();
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet"  href="styles/bgstyle.css"> 
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>View Record</h1>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <p class="form-control-static"><?php echo $row["CLast_Name"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <p class="form-control-static"><?php echo $row["CFirst_Name"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>IRS Number</label>
                        <p class="form-control-static"><?php echo $row["IRS_Number_C"]; ?></p>
                    </div>
			<div class="form-group">
                        <label>SSN</label>
                        <p class="form-control-static"><?php echo $row["CSocial_Security_Number"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Street </label>
                        <p class="form-control-static"><?php echo $row["CStreet"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Street Number</label>
                        <p class="form-control-static"><?php echo $row["CSt_Number"]; ?></p>
                    </div>
			<div class="form-group">
                        <label>Postal Code</label>
                        <p class="form-control-static"><?php echo $row["CPostal_Code"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <p class="form-control-static"><?php echo $row["CCity"]; ?></p>
                    </div>
		<div class="form-group">
                        <label>First Registration (date)</label>
                        <p class="form-control-static"><?php echo $row["CFirst_Registration"]; ?></p>
                    </div>

                    <p><a href="pageAdmin.php?filter=5" class="btn btn-primary">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
