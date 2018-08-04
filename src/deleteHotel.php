<?php
// Process delete operation after confirmation
if(isset($_POST["Hotel_Group"]) && !empty($_POST["Hotel_Group"]) AND isset($_POST["Hotel"]) && !empty($_POST["Hotel"])){
    // Include config file
    require 'config.php';
    
    // Prepare a delete statement
    $sql = "DELETE FROM Hotel WHERE Hotel_ID = ? AND Hotel_Group_Hotel_Group_ID = ?";
    
     
    if($stmt = $conn->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param( "ii",  $hotel_id, $group_id );
        
        // Set parameters
        $hotel_id = trim($_POST["Hotel"]);
        $group_id = trim($_POST["Hotel_Group"]);
        // Attempt to execute the prepared statement
        if($stmt->execute()){
			$sql = "UPDATE Hotel_Group SET Number_of_hotels = Number_of_hotels -1 
					WHERE Hotel_Group_ID = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i",$param_group_hotel_id);
			$param_group_hotel_id = $group_id;
			$stmt->execute();
            // Records deleted successfully. Redirect to landing page
            header("location: pageAdmin.php?filter=3");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    $stmt->close();
    
    // Close connection
    $conn->close();
} else{
	
    // Check existence of id parameter
    if(!isset($_GET['Group_ID'])){
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
                        <h1>Delete Record</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger fade in">
                            <input type="hidden" name="Hotel_Group" value="<?php echo $_GET['Group_ID']; ?>"/>
							<input type="hidden" name="Hotel" value="<?php echo $_GET['Hotel_ID']; ?>"/>
                            <p>Are you sure you want to delete this record?</p><br>
                            <p>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="pageAdmin.php?filter=3" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
