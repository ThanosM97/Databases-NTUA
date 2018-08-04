<?php
// Include config file
require'config.php';
 
// Define variables and initialize with empty values
$hotel_name = $street = $street_number = $postal_code = $city = $stars = $rooms_number = "";
$hotel_name_err = $street_err = $street_number_err = $postal_code_err = $city_err = $stars_err = $rooms_number_err = "";

// Processing form data when form is submitted
if(isset($_POST["Hotel_Group"]) && !empty($_POST["Hotel_Group"]) AND isset($_POST["Hotel"]) && !empty($_POST["Hotel"])){
    // Get hidden input value
    $hotel_id = trim($_POST["Hotel"]);
    
    $group_id = trim($_POST["Hotel_Group"]);
    
    // Validate name
    $input_name = trim($_POST["Hotel_Name"]);
    if(empty($input_name)){
        $hotel_name_err = "Please enter a hotel name.";
    } elseif(!filter_var(trim($_POST["Hotel_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $hotel_name_err = 'Please enter a valid hotel name.';
	} else{
        $hotel_name = $input_name;
    }
    
    
    $input_street = trim($_POST["HStreet"]);
    if(empty($input_street)){
        $street_err = "Please enter a street.";
    } elseif(!filter_var(trim($_POST["HStreet"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $street_err = 'Please enter a valid street name.';
    } else{
        $street = $input_street;
    } 
	$input_street_number = trim($_POST["HSt_Number"]);
    if(empty($input_street_number)){
        $street_number_err = "Please enter the street number.";     
    } elseif(!ctype_digit($input_street_number)){
        $street_number_err = 'Please enter a positive integer value.';
    } else{
        $street_number = $input_street_number;
    }
	$input_postal_code = trim($_POST["HPostal_Code"]);
    if(empty($input_postal_code)){
        $postal_code_err = "Please enter the postal code.";     
    } elseif(!ctype_digit($input_postal_code)){
        $postal_code_err = 'Please enter a positive integer value.';
    } else{
       	$postal_code = $input_postal_code;
    } 
	$input_city = trim($_POST["HCity"]);
    if(empty($input_city)){
        $city_err = "Please enter a city.";
    } elseif(!filter_var(trim($_POST["HCity"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $city_err = 'Please enter a valid city name.';
    } else{
        $city = $input_city;
    }    
	
	$input_rooms = trim($_POST["Number_of_rooms"]);
    if(empty($input_rooms)){
        $rooms_number_err = "Please enter the number of rooms of the hotel.";     
    } elseif(!ctype_digit($input_rooms)){
        $rooms_number_err = 'Please enter a positive integer value.';
    } else{
       	$rooms_number = $input_rooms;
    } 
	$input_stars = trim($_POST["Stars"]);
    if(empty($input_stars)){
        $stars_err = "Please enter the stars of the hotel.";     
    } elseif(!ctype_digit($input_stars)){
        $stars_err = 'Please enter a positive integer value.';
    } else{
       	$stars = $input_stars;
    } 
    // Check input errors before inserting in database
   if(empty($hotel_name_err) && empty($stars_err)  && empty($street_err) && empty($street_number_err) && empty($postal_code_err) && empty($city_err) && empty($rooms_number_err)) {
        // Prepare an update statement
        $sql = "UPDATE Hotel SET Hotel_Name =? ,HStreet=? ,HSt_Number=? ,HPostal_Code=?, HCity=?, Stars=?,  Number_of_rooms=?  WHERE 
				Hotel_Group_Hotel_Group_ID = ? AND Hotel_ID = ?";
       
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
           $stmt->bind_param("ssiisiiii",$param_name, $param_street,$param_street_number, $param_postal_code, $param_city,
										$param_stars, $param_rooms, $param_group_id, $param_hotel_id);
							// Set parameters
			$param_hotel_id = $hotel_id;
			$param_name = $hotel_name;
			$param_street = $street;
			$param_street_number = $street_number;
			$param_postal_code = $postal_code;
			$param_city = $city;
			$param_stars = $stars;
			$param_rooms = $rooms_number;
			$param_group_id = $group_id;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
				$x = 1;
				while (isset($_POST["phone".$x]) ){
					$sql = "UPDATE Hotel_Phone_Number SET HPhone_Number = ? 
						WHERE ((Hotel_Hotel_Group_Hotel_Group_ID = ?) AND (Hotel_Hotel_ID = ?) AND (HPhone_Number = ?))";
					$stmt = $conn->prepare($sql);
					// Bind variables to the prepared statement as parameters
					$stmt->bind_param("iiii",$param_phone_input, $param_group_id, $param_hotel_id,$param_phone_old);
					$param_phone_input = trim($_POST["phone".$x]);
					$param_phone_old = trim($_POST["oldphone".$x]);
					$stmt->execute();
					$x = $x +1;
				}
				$x = 1;
				while (isset($_POST["email".$x]) ){
					$sql = "UPDATE Hotel_Email_Address SET HEmail = ? 
						WHERE ((Hotel_Hotel_Group_Hotel_Group_ID = ?) AND (Hotel_Hotel_ID = ?) AND (HEmail = ?))";
					$stmt = $conn->prepare($sql);
					// Bind variables to the prepared statement as parameters
					$stmt->bind_param("siis",$param_email_input, $param_group_id, $param_hotel_id,$param_email_old);
					$param_email_input= trim($_POST["email".$x]);
					$param_email_old= trim($_POST["oldemail".$x]);
					$stmt->execute();
					$x = $x +1;
				}
                // Records updated successfully. Redirect to landing page
				header("location: pageAdmin.php?filter=3");
				exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        $stmt->close();
    }
    
    // Close connection
    $conn->close();
} else{
	
    // Check existence of id parameter before processing further
    if(isset($_GET["Group_ID"]) && !empty($_GET["Group_ID"]) AND isset($_GET["Hotel_ID"]) && !empty($_GET["Hotel_ID"])){
        // Get URL parameter
        $hotel_id = trim($_GET["Hotel_ID"]);
        $group_id = trim($_GET["Group_ID"]);
       
		if(isset($_GET["phone"]) && !empty(trim($_GET["phone"]))){
			$phone =  trim($_GET["phone"]);
			$sql_delete = "DELETE FROM Hotel_Phone_Number WHERE ((Hotel_Hotel_Group_Hotel_Group_ID = $group_id) AND (Hotel_Hotel_ID = $hotel_id)
			AND (HPhone_Number = '".$phone."')) ";

			if(!$conn->query($sql_delete)){ 
				header("location: pageAdmin.php");
				exit(); 
			}
		}
		if(isset($_GET["email"]) && !empty(trim($_GET["email"]))){
			$email =  trim($_GET["email"]);
			$sql_delete = "DELETE FROM Hotel_Email_Address WHERE ((	Hotel_Hotel_Group_Hotel_Group_ID = $group_id) AND (	Hotel_Hotel_ID = $hotel_id)
			AND (HEmail = '".$email."')) ";
			
			if(!$conn->query($sql_delete)){ 
				header("location: pageAdmin.php");
				exit(); 
			}
		}
        // Prepare a select statement
        $sql = "SELECT * FROM Hotel WHERE  Hotel_ID = ? AND Hotel_Group_Hotel_Group_ID = ?";
      if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param( "ii",  $hotel_id, $group_id ); 
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $result = $stmt->get_result();
                
                if($result->num_rows == 1){
                    // Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop 
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
					$result = $stmt->get_result();
					$phone=array();
					if ($result->num_rows>0){						
						$i=1; 
						while ($row = $result->fetch_assoc()){
						
							$phone[$i]= $row["HPhone_Number"];
							$i=$i+1;} } 
					
					
					$sql = "SELECT * FROM Hotel_Email_Address WHERE  Hotel_Hotel_ID = ? AND Hotel_Hotel_Group_Hotel_Group_ID = ?";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param( "ii",  $hotel_id, $group_id );
					// Set parameters
					$hotel_id = trim($_GET["Hotel_ID"]);
					$group_id = trim($_GET["Group_ID"]);
					$stmt->execute();
					$email=array();
					$result = $stmt->get_result();  
					if ($result->num_rows>0){
						$i=1; 
						while ($row = $result->fetch_assoc()){
							$email[$i]= $row["HEmail"];
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
    }  else{
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
    <style type="text/css">
    

.div-phone, .div-email{
  display: flex;
  margin-bottom: 10px;
}

.glyphicon-trash{
  display: inline-block;
}

.container{
    display: flex;
}
.fixed{
    width: 17%;
}
.flex-item{
    flex-grow: 1;
}
#email-btn{
	margin-top: 10px;
	 position: relative;
		right: 150%;
}

#phone-btn{
	margin-top: 10px;
	 position: relative;
		right: 108%;
}
#submit{
	margin-top: 10px;
	 position: relative;
		right: 20%;
 }
#cancel{
	color: white;
	margin-top: 10px;
	 position: relative;
	right: 70%;
	background-color: #e60000;
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
                         <div class="form-group <?php echo (!empty($hotel_name_err)) ? 'has-error' : ''; ?>">
                            <label>Hotel Name</label>
                            <input type="text" name="Hotel_Name" class="form-control" value="<?php echo $hotel_name; ?>">
                            <span class="help-block"><?php echo $hotel_name_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($street_err)) ? 'has-error' : ''; ?>">
                            <label>Street</label>
                            <input type="text" name="HStreet" class="form-control" value="<?php echo $street; ?>">
                            <span class="help-block"><?php echo $street_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($street_number_err)) ? 'has-error' : ''; ?>">
                            <label>Street Number</label>
                            <input type="text" name="HSt_Number" class="form-control" value="<?php echo $street_number; ?>">
                            <span class="help-block"><?php echo $street_number_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($postal_code_err)) ? 'has-error' : ''; ?>">
                            <label>Postal Code</label>
                            <input type="text" name="HPostal_Code" class="form-control" value="<?php echo $postal_code; ?>">
                            <span class="help-block"><?php echo $postal_code_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($city_err)) ? 'has-error' : ''; ?>">
                            <label>City</label>
                            <input type="text" name="HCity" class="form-control" value="<?php echo $city; ?>">
                            <span class="help-block"><?php echo $city_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($stars_err)) ? 'has-error' : ''; ?>">
                            <label>Stars</label>
                            <input type="text" name="Stars" class="form-control" value="<?php echo $stars; ?>">
                            <span class="help-block"><?php echo $stars_err;?></span>
                        </div>
						<div class="form-group <?php echo (!empty($rooms_err)) ? 'has-error' : ''; ?>">
                            <label>Number of rooms</label>
                            <input type="text" name="Number_of_rooms" class="form-control" value="<?php echo $rooms_number; ?>">
                            <span class="help-block"><?php echo $rooms_err;?></span>
                        </div>
			<?php 
					$x=1;
					while (isset($phone[$x])){ ?>
				    <div class="form-group" >
		                    <label >Phone Number <?php echo $x; ?></label>
		                     <div class="div-phone" ><input type="text" name="<?php echo "phone".$x?>" class="form-control"  value="<?php echo $phone[$x]; ?>">
				    <input type="hidden" name=<?php echo "oldphone".$x ?>  value=" <?php echo $phone[$x]; ?>"/>
				    <a href="updateHotel.php?phone=<?php echo $phone[$x];?>&Group_ID=<?php echo $_GET["Group_ID"];?>&Hotel_ID=<?php echo $_GET["Hotel_ID"];?>" class="btn btn-default" style="float:right"><span class="glyphicon glyphicon-trash" id="image"></span></a></div>
		                    <span class="help-block" ></span>
                       		    </div>
					<?php  $x=$x+1;}
					$y=1;
					while (isset($email[$y])){ ?>
				    <div class="form-group">
		                    <label>Email <?php echo $y; ?></label>
		                    <div class="div-email" ><input type="text" name=<?php echo "email".$y?> class="form-control" value="<?php echo $email[$y]; ?>">
				    <input type="hidden" name=<?php echo "oldemail".$y ?>  value=" <?php echo $email[$y]; ?>"/>
		            <a href="updateHotel.php?email=<?php echo $email[$y];?>&Group_ID=<?php echo $_GET["Group_ID"];?>&Hotel_ID=<?php echo $_GET["Hotel_ID"];?>" class="btn btn-default" style="float:right"><span class="glyphicon glyphicon-trash"></span></a></div>
		                    <span class="help-block"></span>
		                    </div>
					<?php $y=$y+1;} ?>
			<div class="container">
				<div class = "fixed">
					<input type="hidden" name="Hotel_Group" value="<?php echo $group_id; ?>"/>
                    <input type="hidden" name="Hotel" value="<?php echo $hotel_id; ?>"/>
                    <input type="submit" id = "submit" class="btn btn-primary"  value="Submit" ></div>
                <div class = "flex-item">  <a href="pageAdmin.php?filter=3"id = "cancel" class="btn btn-default">Cancel</a></div>
				<div class = "flex-item"><a href="createPhoneUpd.php?hotelid= <?php echo $hotel_id; ?>&groupid=<?php echo $group_id; ?> " id = "phone-btn" class="btn btn-default" style="float:right"> Add more phone numbers</a></div>
				<div class = "flex-item"><a href="createEmailUpd.php?hotelid= <?php echo $hotel_id; ?>&groupid=<?php echo $group_id; ?> " id = "email-btn" class="btn btn-default" style="float:right"> Add more email addresses</a></div>
			</div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
