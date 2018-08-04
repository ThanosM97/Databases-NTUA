<?php

// Check existence of id parameter before processing further
if(isset($_GET["Group_ID"]) && !empty($_GET["Group_ID"]) AND isset($_GET["Hotel_ID"]) && !empty($_GET["Hotel_ID"])){
    // Include config file
    require'config.php';
    
    // Prepare a select statement
    $sql = "SELECT * FROM Hotel WHERE  Hotel_ID = ? AND Hotel_Group_Hotel_Group_ID = ?";
    
     if($stmt = $conn->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param( "ii",  $hotel_id, $group_id );
        // Set parameters
        $hotel_id = trim($_GET["Hotel_ID"]);
        $group_id = trim($_GET["Group_ID"]);
        
        // Attempt to execute the prepared statement
         if($stmt->execute()){
            $result = $stmt->get_result();
            
            if($result->num_rows == 1){
                /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                  $row = $result->fetch_array(MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $hotel_name = $row["Hotel_Name"];
				$hotel_group = $row["Hotel_Group_Hotel_Group_ID"];
                $street = $row["HStreet"];
				$street_number = $row["HSt_Number"];
				$postal_code = $row["HPostal_Code"];
                $city = $row["HCity"];
                $stars = $row["Stars"];
                $rooms_number = $row["Number_of_rooms"];
                
				$sql = "SELECT * FROM Hotel_Phone_Number WHERE  Hotel_Hotel_ID = ? AND Hotel_Hotel_Group_Hotel_Group_ID = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param( "ii",  $hotel_id, $group_id );
				// Set parameters
				$hotel_id = trim($_GET["Hotel_ID"]);
				$group_id = trim($_GET["Group_ID"]);
				$stmt->execute();
				$phones = $stmt->get_result();
				$sql = "SELECT * FROM Hotel_Email_Address WHERE  Hotel_Hotel_ID = ? AND Hotel_Hotel_Group_Hotel_Group_ID = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param( "ii",  $hotel_id, $group_id );
				// Set parameters
				$hotel_id = trim($_GET["Hotel_ID"]);
				$group_id = trim($_GET["Group_ID"]);
				$stmt->execute();
				$emails = $stmt->get_result();          
				
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
                        <label>Hotel Name</label>
                        <p class="form-control-static"><?php echo $row["Hotel_Name"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Hotel Group</label>
                        <p class="form-control-static"><?php echo $row["Hotel_Group_Hotel_Group_ID"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Street </label>
                        <p class="form-control-static"><?php echo $row["HStreet"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Street Number</label>
                        <p class="form-control-static"><?php echo $row["HSt_Number"]; ?></p>
					</div>
					<div class="form-group">
                        <label>Postal Code</label>
                        <p class="form-control-static"><?php echo $row["HPostal_Code"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <p class="form-control-static"><?php echo $row["HCity"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Stars</label>
                        <p class="form-control-static"><?php echo $row["Stars"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Number of rooms</label>
                        <p class="form-control-static"><?php echo $row["Number_of_rooms"]; ?></p>
                    </div>
					<?php
					for ($x = 1; $x <= $phones->num_rows; $x++){
						$row_phones = $phones->fetch_array(MYSQLI_ASSOC);
					?>
						<div class="form-group">
							<label>Phone <?php echo $x; ?></label>
							<p class="form-control-static"><?php echo $row_phones["HPhone_Number"]; ?></p>
						</div>
					<?php } ?>
					<?php
					for ($x = 1; $x <= $emails->num_rows; $x++){
						$row_emails = $emails->fetch_array(MYSQLI_ASSOC);
					?>
						<div class="form-group">
							<label>Email <?php echo $x; ?></label>
							<p class="form-control-static"><?php echo $row_emails["HEmail"]; ?></p>
						</div>
					<?php } ?>
                    <p><a href="pageAdmin.php?filter=3" class="btn btn-primary">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
