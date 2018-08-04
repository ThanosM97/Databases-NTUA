<?php

// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Include config file
    require'config.php';
    
    // Prepare a select statement
    $sql = "SELECT * FROM Hotel_Group_Email_Address WHERE Hotel_Group_Hotel_Group_ID = ?";
    
     if($stmt = $conn->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param( "i", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["id"]);
        
        // Attempt to execute the prepared statement
         if($stmt->execute()){
            $result = $stmt->get_result();
            
            if($result->num_rows >= 0){
		$mail=array();
		$j=1;
		while ($row = $result->fetch_assoc()){
		   $mail[$j]= $row['HGEmail'] ;
		   $j=$j+1;
		   }
	
            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
                header("location: errorUser.php");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    $stmt->close();

	//Second Statement

	$sql = "SELECT * FROM Hotel_Group_Phone_Number WHERE Hotel_Group_Hotel_Group_ID = ?";
    
     if($stmt = $conn->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param( "i", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["id"]);
        
        // Attempt to execute the prepared statement
         if($stmt->execute()){
            $result = $stmt->get_result();
            
            if($result->num_rows >= 0){
		$phone=array();
		$j=1;
		while ($row = $result->fetch_assoc()){
		   $phone[$j]= $row['HGPhone_Number'] ;
		   $j=$j+1;
		   }
	
            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
                header("location: errorUser.php");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    $stmt->close();
    
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: errorUser.php");
    exit();
}

$conn->close();


?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Contact Informations</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet"  href="styles/bgstyle.css"> 
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Contact Info</h1> 
                    </div>
                    <div class="form-group">
		    <label>Emails</label>
			<?php 
				echo "<br>";
				$i=1;
				while (!empty($mail[$i])) {
					echo "Email $i: $mail[$i] <br>";
					$i=$i+1;}
			?>
                        <p class="form-control-static"><?php echo ""; ?></p>
                    </div>
		    <div class="form-group">
		    <label>Phone Numbers</label>
			<?php 
				echo "<br>";
				$i=1;
				while (!empty($phone[$i])) {
					echo "Number $i: $phone[$i] <br>";
					$i=$i+1;}
			?>
                        <p class="form-control-static"><?php echo ""; ?></p>
                    </div>


                    <p><a href="pageUser.php?filter=1" class="btn btn-primary">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
</body>
</html> 
