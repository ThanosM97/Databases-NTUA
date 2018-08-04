<?php

// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Include config file
    require'config.php';
    
    // Prepare a select statement
    $sql = "SELECT * FROM RoomsView WHERE Room_ID = ?";
    
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
                $room_id = $row["Room_ID"];
		$price = $row["Price"];
                $repairs = $row["Repairs"];
                $expendable = $row["Expendable"];
		$view = $row["RView"];
		$capacity = $row["Capacity"];
                $hotel_id = $row["Hotel_ID"];
		$hotel_name=$row["Hotel"]; 
	
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

    //Second Statement
	$sql = "SELECT * FROM Hotel_room_Amenities WHERE Hotel_room_Room_ID = ?";
    
     if($stmt = $conn->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param( "i", $param_id);
        
        // Set parameters
        $param_id = $room_id;
        
        // Attempt to execute the prepared statement
         if($stmt->execute()){
            $result = $stmt->get_result();
            
            if($result->num_rows > 0){
		$amenities=array();
		$j=1;
		while ($row = $result->fetch_assoc()){
		   $amenities[$j]= $row['Amenities'] ;
		   $j=$j+1;
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
                        <label>Room ID</label>
                        <p class="form-control-static"><?php echo $room_id; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <p class="form-control-static"><?php echo $price; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Repairs needed</label>
                        <p class="form-control-static"><?php echo $repairs; ?></p>
                    </div>
			<div class="form-group">
                        <label>Expendable</label>
                        <p class="form-control-static"><?php echo $expendable; ?></p>
                    </div>
                    <div class="form-group">
                        <label>View </label>
                        <p class="form-control-static"><?php echo $view; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Capacity</label>
                        <p class="form-control-static"><?php echo $capacity; ?></p>
		    </div>
		    <div class="form-group">
                        <label>Hotel Name</label>
                        <p class="form-control-static"><?php echo $hotel_name; ?></p>
                    </div>
		    <div class="form-group">
                        <label>Hotel ID</label>
                        <p class="form-control-static"><?php echo $hotel_id; ?></p>
                    </div>
		    <div class="form-group">
		    <label>Amenities</label>
			<?php 
				echo "<ul>\n";
				$i=1;
				while (!empty($amenities[$i])) {
					echo "<li>  $amenities[$i]  </li>\n";
					$i=$i+1;}
				echo "</ul>";
			?>
			<p class="form-control-static"><?php echo ""; ?></p>
                    </div>

                    <p><a href="pageAdmin.php?filter=2" class="btn btn-primary">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
