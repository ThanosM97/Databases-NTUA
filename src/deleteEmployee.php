<?php
// Process delete operation after confirmation
if(isset($_POST["irs"]) && !empty($_POST["irs"])){
    // Include config file
    require 'config.php';
    $sql = "DELETE FROM Works WHERE Work_ID = ?";
     
    if($stmt = $conn->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param( "i",  $work_id);
        // Set parameters
        $work_id = trim($_POST["Work_ID"]);
        // Attempt to execute the prepared statement
        if($stmt->execute()){
		// Prepare a delete statement
			$sql = "DELETE FROM  Employee WHERE  IRS_Number_E NOT IN (SELECT Employee_IRS_Number_E 
							FROM Works WHERE Employee_IRS_Number_E =IRS_Number_E)";
			if($stmt = $conn->prepare($sql)){
			// Attempt to execute the prepared statement
				if($stmt->execute()){
				// Records deleted successfully. Redirect to landing page
					header("location: pageAdmin.php?filter=4");
					exit();
			} else{
				echo "Oops! Something went wrong. Please try again later.";
			}
		}
	}
    // Close statement
    $stmt->close();
    
    // Close connection
    $conn->close();
}
} else{
	
    // Check existence of id parameter
    if(empty(trim($_GET["irs"]))){
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
                            <input type="hidden" name="irs" value="<?php echo $_GET["irs"]; ?>"/>
                            <input type="hidden" name="Work_ID" value="<?php echo $_GET["Work_ID"]; ?>"/>
                            <p>Are you sure you want to delete this record?</p><br>
                            <p>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="pageAdmin.php?filter=4" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
