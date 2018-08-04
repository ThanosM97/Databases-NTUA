<?php

// Check existence of id parameter before processing further
if(isset($_GET["irs"]) && !empty(trim($_GET["irs"]))){
    // Include config file
    require'config.php';
    
    // Prepare a select statement
    $sql = "SELECT * FROM Employee WHERE IRS_Number_E = ?";
    
     if($stmt = $conn->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param( "i", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["irs"]);
        
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
					Hotel_Hotel_Group_Hotel_Group_ID FROM Works WHERE	Work_ID = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param( "i", $work_id);
				$work_id = trim($_GET["Work_ID"]);
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
				}
				else{
					header("location: error.php");
					exit();
				}
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
                        <p class="form-control-static"><?php echo $last_name; ?></p>
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <p class="form-control-static"><?php echo $first_name; ?></p>
                    </div>
                    <div class="form-group">
                        <label>IRS Number</label>
                        <p class="form-control-static"><?php echo $irs_number; ?></p>
                    </div>
			<div class="form-group">
                        <label>SSN</label>
                        <p class="form-control-static"><?php echo $SSN; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Street </label>
                        <p class="form-control-static"><?php echo $street; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Street Number</label>
                        <p class="form-control-static"><?php echo $street_number; ?></p>
                    </div>
			<div class="form-group">
                        <label>Postal Code</label>
                        <p class="form-control-static"><?php echo $postal_code; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Hotel Group</label>
                        <p class="form-control-static"><?php echo $group_name; ?></p>
                    </div>
					<div class="form-group">
                        <label>Hotel</label>
                        <p class="form-control-static"><?php echo $hotel_name; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Start Date </label>
                        <p class="form-control-static"><?php echo $start_date; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Finish Date</label>
                        <p class="form-control-static"><?php echo $finish_date; ?></p>
                    </div>
					<div class="form-group">
                        <label>Position</label>
                        <p class="form-control-static"><?php echo $position; ?></p>
                    </div>

                    <p><a href="pageAdmin.php?filter=4" class="btn btn-primary">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
