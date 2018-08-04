<?php 
// Include config file
require'config.php';

 
// Define variables and initialize with empty values
 $price = $repairs = $expendable = $view = $capacity = "";
$price_err = $repairs_err = $expendable_err = $view_err = $capacity_err = "";


// Processing form data when form is submitted
if(isset($_POST["pros"]) && !empty($_POST["pros"])){
    // Get hidden input value
    $room_id = $_GET["id"]; 
    // Validate name

    
    
    $input_price = trim($_POST["Price"]);
    if(empty($input_price)){
        $price_err = "Please enter a price.";
    } elseif(!ctype_digit($input_price)){
        $price_err = 'Please enter a valid price value.';
    } else{
        $price = $input_price;
    } 
	$input_capacity = trim($_POST["Capacity"]);
    if(empty($input_capacity)){
        $capacity_err = "Please enter the room capacity.";     
    } elseif(!ctype_digit($input_capacity)){
        $capacity_err = 'Please enter a positive integer value.';
    } else{
        $capacity = $input_capacity;
    }
	$input_view = trim($_POST["RView"]);
    if(empty($input_view)){
        $view_err = "Please enter the view.";     
    } elseif(!filter_var(trim($_POST["RView"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $view_err = 'Please enter a valid view value.';
    } else{
       	$view = $input_view;
    } 
	$input_expendable= trim($_POST["Expendable"]);
    if(!isset($input_expendable)){
        $expendable_err = "Please enter the expendability.";
    } elseif(!ctype_digit($input_expendable)){
        $expendable_err = 'Please enter a valid expendability value.';
    } else{
        $expendable = $input_expendable;
    }    
	
	$input_repairs = trim($_POST["Repairs"]);
    if(empty($input_repairs)){
        $repairs_err = "Please enter the needed repairs.";     
    } elseif(!filter_var(trim($_POST["Repairs"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $repairs_err = 'Please enter a valid repairs value.';
    } else{
       	$repairs = $input_repairs;
    } 


    // Check input errors before inserting in database
    if(empty($price_err) && empty($capacity_err) && empty($view_err) && empty($expendable_err) && empty($repairs_err) ) {
        // Prepare an update statement

				$sql = "UPDATE Hotel_room SET Price=? ,Repairs_need=? ,Expendable=? ,RView=? ,Capacity=?   WHERE Room_ID= ? ";      
				if($stmt = $conn->prepare($sql)){
				    // Bind variables to the prepared statement as parameters
				    $stmt->bind_param( "isisii", $param_price, $param_repairs, $param_expendable, $param_view, $param_capacity, $param_room_id);
				    
				    // Set parameters
				    $param_price = $price;
				    $param_repairs = $repairs;
				    $param_expendable = $expendable;
				    $param_view = $view;
					$param_capacity = $capacity;
					$param_room_id = $room_id; 
	
						    
				    // Attempt to execute the prepared statement
				    if($stmt->execute()){
					$x =1;
					while (isset($_POST["amenities".$x]) ){
						$sql = "UPDATE Hotel_room_Amenities SET Amenities = ? 
							WHERE ((Hotel_room_Room_ID = ?) AND (Amenities= ?)) LIMIT 1";
						$stmt = $conn->prepare($sql);
						// Bind variables to the prepared statement as parameters
						$stmt->bind_param("sis",$param_amenities_input, $param_room_id, $param_amenities_old);
						$param_amenities_input = (trim($_POST["amenities".$x]));
						$param_amenities_old = (trim($_POST["old".$x]));
						$stmt->execute();
						$x = $x +1;
					}				
	
					// Records updated successfully. Redirect to landing page
					header("location: pageAdmin.php?filter=2");
					exit(); 

				    } else{
					echo "Something went wrong. Please try again later.";
				    }
				}
				$stmt->close();
			    }
			    // Close connection
			    $conn->close(); 


} else{
	include 'config.php';
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){ 
        // Get URL parameter
        	$room_id =  trim($_GET["id"]); 
		if(isset($_GET["amenity"]) && !empty(trim($_GET["amenity"]))){
			$amenity_name =  trim($_GET["amenity"]);
			$sql_delete = "DELETE FROM Hotel_room_Amenities WHERE ((Hotel_room_Room_ID = $room_id) AND (`Amenities` LIKE '".$amenity_name."')) ";

			if(!$conn->query($sql_delete)){ 
				header("location: pageAdmin.php");
				exit(); 
			}
		}
        // Prepare a select statement
        $sql = "SELECT * FROM Hotel_room WHERE Room_ID = ?";
      if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param( "i", $room_id);


            // Attempt to execute the prepared statement
           if($stmt->execute()){
                $result = $stmt->get_result();


                if($result->num_rows == 1){
                    // Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop 
                   $row = $result->fetch_array(MYSQLI_ASSOC); 
                    
                    // Retrieve individual field value
                    	$room_id = $row["Room_ID"];
			$hotel_id = $row["Hotel_Hotel_ID"];
		        $price = $row["Price"];
		        $view = $row["RView"];
			$repairs = $row["Repairs_need"];
			$expendable = $row["Expendable"];
		        $capacity = $row["Capacity"]; 

		$sql = "SELECT * FROM Hotel_room_Amenities WHERE  Hotel_room_Room_ID = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param( "i",  $room_id);
		// Set parameters
		$stmt->execute();
		$result = $stmt->get_result(); 
		if ($result->num_rows>0){
			$amenities=array();
			$i=1; 

			while ($row = $result->fetch_assoc()){
				$amenities[$i]= $row["Amenities"];
				$i=$i+1;} } 

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

    } 	else{
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
    <style>
        
.container{
    display: flex;
}
.fixed{
    width: 17%;
}
.flex-item{
    flex-grow: 1;
}
#amenities{
	margin-top: 10px;
	 position: relative;
		right: 120%;
}

#submit{
	margin-top: 10px;
	position: relative;
	right: 5px;
 }
#cancel{
	margin-top: 10px;
	 position: relative;
	right: 55%;
	color: white;
	background-color: #e60000;
 }
.div-amenity{
  display: flex;
  margin-bottom: 10px;
}

.glyphicon-trash{
  display: inline-block;
}

    </style>
   
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
			<div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
                            <label>Price</label>
                            <input type="text" name="Price" class="form-control" value="<?php echo $price; ?>">
                            <span class="help-block"><?php echo $price_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($capacity_err)) ? 'has-error' : ''; ?>">
                            <label>Capacity</label>
                            <input type="text" name="Capacity" class="form-control" value="<?php echo $capacity; ?>">
                            <span class="help-block"><?php echo $capacity_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($view_err)) ? 'has-error' : ''; ?>">
                            <label>View</label>
                            <input type="text" name="RView" class="form-control" value="<?php echo $view; ?>">
                            <span class="help-block"><?php echo $view_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($expendable_err)) ? 'has-error' : ''; ?>">
                            <label>Expendability</label>
                            <input type="text" name="Expendable" class="form-control" value="<?php echo $expendable; ?>">
                            <span class="help-block"><?php echo $expendable_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($repairs_err)) ? 'has-error' : ''; ?>">
				<label>Needed Repairs</label>
  				<input type="text" name="Repairs" class="form-control" value="<?php echo $repairs; ?>">
  				<span class="help-block"><?php echo $repairs_err;?></span>
                        </div>
			<?php  
					$x=1;
			   while (isset($amenities[$x])){  ?>
					
				<div class="form-group">
		       		<label>Amenity <?php echo $x; ?> </label>
		        	<div class="div-amenity" ><input type="text" name= "<?php echo "amenities".$x ?>"  class="form-control" value=" <?php echo $amenities[$x]; ?>">
					<input type="hidden" name=<?php echo "old".$x ?>  value=" <?php echo $amenities[$x]; ?>"/>
		        	<a href="updateRoom.php?amenity=<?php echo $amenities[$x];?>&id=<?php echo $_GET["id"];?>" class="btn btn-default" style="float:right"><span class="glyphicon glyphicon-trash"></span></a></div></a>
		        	<span class="help-block"></span>
		        	</div> 
				<?php  $x=$x+1;} ?>
			<div class="container">
				<div class = "fixed"><input type="hidden" name="pros" value="<?php echo $price; ?>"/>
                        <input type="submit" id="submit" class="btn btn-primary" value="Submit"></div>
                        <div class = "flex-item"><a href="pageAdmin.php?filter=2" id="cancel" class="btn btn-default">Cancel</a></div>
			<div class = "flex-item"><a href="createAmenUpd.php?id= <?php echo $room_id; ?> " id="amenities" class="btn btn-default" style="float:right"> Create more amenities</a>
				</div>
				</div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
