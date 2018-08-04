<?php
// Process delete operation after confirmation
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Include config file
    require 'config.php';
    
    // Prepare a delete statement
    $sql = "DELETE FROM Reserves WHERE Reserves_ID = ?";
    
     
    if($stmt = $conn->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param( "i",  $reserve_id);
        
        // Set parameters
        $reserve_id = trim($_POST["Reserves_ID"]);
        
        // Attempt to execute the prepared statement
         if($stmt->execute()){
            // Records deleted successfully. Redirect to landing page
            header("location: pageAdmin.php?filter=6");
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
    if(empty(trim($_GET["res_id"]))){
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
                            <input type="hidden" name="Reserves_ID" value="<?php echo $_GET["res_id"]; ?>"/>
                            <p>Are you sure you want to delete this record?</p><br>
                            <p>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="pageAdmin.php?filter=6" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>

