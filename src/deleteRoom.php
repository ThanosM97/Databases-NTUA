<?php
// Process delete operation after confirmation
if(isset($_POST["Room_ID"]) && !empty($_POST["Room_ID"])){
    // Include config file
    require 'config.php';
    
    // Prepare a delete statement
      $roomid = trim($_POST["Room_ID"]);
    
			 
			$sql = "SELECT Hotel_Hotel_ID, Hotel_Hotel_Group_Hotel_Group_ID FROM Hotel_room WHERE Room_ID= ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i",$param_room_id);
			$param_room_id = $roomid;
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_array();
			$hotel_id = $row["Hotel_Hotel_ID"];
			$group_id = $row["Hotel_Hotel_Group_Hotel_Group_ID"];
			$sql = "UPDATE Hotel SET Number_of_rooms = Number_of_rooms -1 
					WHERE Hotel_ID = ? AND Hotel_Group_Hotel_Group_ID =?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ii",$param_hotel_id,$group_id);
			$param_hotel_id = $hotel_id;
			$stmt->execute();
			
			$sql = "DELETE FROM Hotel_room WHERE Room_ID = ?";
			 $stmt = $conn->prepare($sql);
			 $stmt->bind_param( "i",  $roomid);
        
        // Set parameters
      
			 $stmt->execute();
            // Records deleted successfully. Redirect to landing page
            header("location: pageAdmin.php?filter=2");
            exit();
        
    
     
    // Close statement
    $stmt->close();
    
    // Close connection
    $conn->close();
} else{
	
    // Check existence of id parameter
    if(empty(trim($_GET["id"]))){
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
                            <input type="hidden" name="Room_ID" value="<?php echo $_GET["id"]; ?>"/>
                            <p>Are you sure you want to delete this record?</p><br>
                            <p>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="pageAdmin.php?filter=2" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
