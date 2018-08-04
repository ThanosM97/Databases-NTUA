<?php
// Include config file
require'config.php';
 
// Define variables and initialize with empty values
$name = $street = $street_number = $postal_code = $hotels_number = "";
$name_err = $street_err = $street_number_err = $postal_code_err = $hotels_number_err = "";

// Processing form data when form is submitted
if(isset($_POST["Hotel_Group"]) && !empty($_POST["Hotel_Group"])){
    // Get hidden input value
    
    $group_id = trim($_POST["Hotel_Group"]);
    
    // Validate name
    $input_name = trim($_POST["Hotel_Group_Name"]);
    $sql = "SELECT * FROM Hotel_Group WHERE Hotel_Group_Name = '$input_name'";
    $result=$conn->query($sql);
    if(empty($input_name)){
        $name_err = "Please enter a hotel group name.";
    } elseif(!filter_var(trim($_POST["Hotel_Group_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $name_err = 'Please enter a valid hotel group name.';
    } else if ($result->num_rows>0){
	$name_err = 'There is already a hotel group with this name';
    } else{
        $name = $input_name;
    }
    
    
    $input_street = trim($_POST["HGStreet"]);
    if(empty($input_street)){
        $street_err = "Please enter a street.";
    } elseif(!filter_var(trim($_POST["HGStreet"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $street_err = 'Please enter a valid street name.';
    } else{
        $street = $input_street;
    } 
	$input_street_number = trim($_POST["HGSt_Number"]);
    if(empty($input_street_number)){
        $street_number_err = "Please enter the street number.";     
    } elseif(!ctype_digit($input_street_number)){
        $street_number_err = 'Please enter a positive integer value.';
    } else{
        $street_number = $input_street_number;
    }
	$input_postal_code = trim($_POST["HGPostal_Code"]);
    if(empty($input_postal_code)){
        $postal_code_err = "Please enter the postal code.";     
    } elseif(!ctype_digit($input_postal_code)){
        $postal_code_err = 'Please enter a positive integer value.';
    } else{
       	$postal_code = $input_postal_code;
    } 
	
	$input_hotels = trim($_POST["Number_of_hotels"]);
    if(!isset($input_hotels)){
        $hotels_number_err = "Please enter the number of hotels of the hotel group.";     
    } elseif(!ctype_digit($input_hotels)){
        $hotels_number_err = 'Please enter a positive integer value.';
    } else{
       	$hotels_number = $input_hotels;
    } 

    // Check input errors before inserting in database
   if(empty($name_err)  && empty($street_err) && empty($street_number_err) && empty($postal_code_err)  && empty($hotels_number_err)) {
        // Prepare an update statement
        $sql = "UPDATE Hotel_Group SET Hotel_Group_Name = ? , HGStreet = ? , HGSt_Number = ? , HGPostal_Code = ?,  Number_of_hotels = ?   WHERE 
				Hotel_Group_ID = ?";
       
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
           $stmt->bind_param("ssiiii",$param_name, $param_street,$param_street_number, $param_postal_code, $param_hotels, $param_group_id);

			$param_name = $name;
			$param_street = $street;
			$param_street_number = $street_number;
			$param_postal_code = $postal_code;
			$param_hotels = $hotels_number;
			$param_group_id = $group_id;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
				$x = 1;
				while (isset($_POST["phone".$x]) ){
					$sql = "UPDATE Hotel_Group_Phone_Number SET HGPhone_Number = ? 
						WHERE ((Hotel_Group_Hotel_Group_ID = ?)  AND (HGPhone_Number = ?))";
					$stmt = $conn->prepare($sql);
					// Bind variables to the prepared statement as parameters
					$stmt->bind_param("iii",$param_phone_input, $param_group_id,$param_phone_old);
					$param_phone_input = trim($_POST["phone".$x]);
					$param_phone_old = trim($_POST["oldphone".$x]);
					$stmt->execute();
					$x = $x +1;
				}
				$x = 1;
				while (isset($_POST["email".$x]) ){
					$sql = "UPDATE Hotel_Group_Email_Address SET HGEmail = ? 
						WHERE ((Hotel_Group_Hotel_Group_ID = ?)  AND (HGEmail = ?))";
					$stmt = $conn->prepare($sql);
					// Bind variables to the prepared statement as parameters
					$stmt->bind_param("sis",$param_email_input, $param_group_id,$param_email_old);
					$param_email_input= trim($_POST["email".$x]);
					$param_email_old= trim($_POST["oldemail".$x]);
					$stmt->execute();
					$x = $x +1;
				}
                // Records updated successfully. Redirect to landing page
				header("location: pageAdmin.php?filter=1");
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
    if(isset($_GET["id"]) && !empty($_GET["id"])){
        // Get URL parameter
        $group_id = trim($_GET["id"]);
       
		if(isset($_GET["phone"]) && !empty(trim($_GET["phone"]))){
			$phone =  trim($_GET["phone"]);
			$sql_delete = "DELETE FROM Hotel_Group_Phone_Number WHERE ((Hotel_Group_Hotel_Group_ID = $group_id) AND (HGPhone_Number = '".$phone."')) ";

			if(!$conn->query($sql_delete)){ 
				header("location: pageAdmin.php?filter=1");
				exit(); 
			}
		}
		if(isset($_GET["email"]) && !empty(trim($_GET["email"]))){
			$email =  trim($_GET["email"]);
			$sql_delete = "DELETE FROM Hotel_Group_Email_Address WHERE ((Hotel_Group_Hotel_Group_ID = $group_id) AND (HGEmail = '".$email."')) ";
			
			if(!$conn->query($sql_delete)){ 
				header("location: pageAdmin.php?filter=1");
				exit(); 
			}
		}
        // Prepare a select statement
        $sql = "SELECT * FROM Hotel_Group WHERE  Hotel_Group_ID = ?";
      if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param( "i",  $group_id ); 
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $result = $stmt->get_result();
                
                if($result->num_rows == 1){
                    // Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop 
                     $row = $result->fetch_array(MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                   			$name = $row["Hotel_Group_Name"];
					$street = $row["HGStreet"];
					$street_number = $row["HGSt_Number"];
					$postal_code = $row["HGPostal_Code"];
					$hotels_number = $row["Number_of_hotels"];

					$sql = "SELECT * FROM Hotel_Group_Phone_Number WHERE Hotel_Group_Hotel_Group_ID = ?";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param( "i", $group_id );
					// Set parameters
					$group_id = trim($_GET["id"]);
					$stmt->execute();
					$result = $stmt->get_result();
					$phone=array();
					if ($result->num_rows>0){						
						$i=1; 
						while ($row = $result->fetch_assoc()){
						
							$phone[$i]= $row["HGPhone_Number"];
							$i=$i+1;} } 
					
					
					$sql = "SELECT * FROM Hotel_Group_Email_Address WHERE Hotel_Group_Hotel_Group_ID = ?";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param( "i", $group_id );
					// Set parameters
					$group_id = trim($_GET["id"]);
					$stmt->execute();
					$email=array();
					$result = $stmt->get_result();  
					if ($result->num_rows>0){
						$i=1; 
						while ($row = $result->fetch_assoc()){
							$email[$i]= $row["HGEmail"];
							$i=$i+1;} } 

      
                } else{
                  //   URL doesn't contain valid id. Redirect to error page
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
		right: 5%;
 }
#cancel{
	color: white;
	margin-top: 10px;
	 position: relative;
	right: 65%;
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
                         <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>Hotel Group Name</label>
                            <input type="text" name="Hotel_Group_Name" class="form-control" value="<?php echo $name; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($street_err)) ? 'has-error' : ''; ?>">
                            <label>Street</label>
                            <input type="text" name="HGStreet" class="form-control" value="<?php echo $street; ?>">
                            <span class="help-block"><?php echo $street_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($street_number_err)) ? 'has-error' : ''; ?>">
                            <label>Street Number</label>
                            <input type="text" name="HGSt_Number" class="form-control" value="<?php echo $street_number; ?>">
                            <span class="help-block"><?php echo $street_number_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($postal_code_err)) ? 'has-error' : ''; ?>">
                            <label>Postal Code</label>
                            <input type="text" name="HGPostal_Code" class="form-control" value="<?php echo $postal_code; ?>">
                            <span class="help-block"><?php echo $postal_code_err;?></span>
			<div class="form-group <?php echo (!empty($hotels_number_err)) ? 'has-error' : ''; ?>">
                            <label>Number of hotels</label>
                            <input type="text" name="Number_of_hotels" class="form-control" value="<?php echo $hotels_number; ?>">
                            <span class="help-block"><?php echo $hotels_number_err;?></span>
                        </div>
			<?php 
					$x=1;
					while (isset($phone[$x])){ ?>
				    <div class="form-group" >
		                    <label >Phone Number <?php echo $x; ?></label>
		                     <div class="div-phone" ><input type="text" name="<?php echo "phone".$x?>" class="form-control"  value="<?php echo $phone[$x]; ?>">
				    <input type="hidden" name=<?php echo "oldphone".$x ?>  value=" <?php echo $phone[$x]; ?>"/>
				    <a href="updateHotelGroup.php?phone=<?php echo $phone[$x];?>&id=<?php echo $_GET["id"];?>" class="btn btn-default" style="float:right"><span class="glyphicon glyphicon-trash" id="image"></span></a></div>
		                    <span class="help-block" ></span>
                       		    </div>
					<?php  $x=$x+1;}
					$y=1;
					while (isset($email[$y])){ ?>
				    <div class="form-group">
		                    <label>Email <?php echo $y; ?></label>
		                    <div class="div-email" ><input type="text" name=<?php echo "email".$y?> class="form-control" value="<?php echo $email[$y]; ?>">
				    <input type="hidden" name=<?php echo "oldemail".$y ?>  value=" <?php echo $email[$y]; ?>"/>
		            <a href="updateHotelGroup.php?email=<?php echo $email[$y];?>&id=<?php echo $_GET["id"];?>" class="btn btn-default" style="float:right"><span class="glyphicon glyphicon-trash"></span></a></div>
		                    <span class="help-block"></span>
		                    </div>
					<?php $y=$y+1;} ?>
			<div class="container">
				<div class = "fixed">
			<input type="hidden" name="Hotel_Group" value="<?php echo $group_id; ?>"/>
                    <input type="submit" id = "submit" class="btn btn-primary"  value="Submit" ></div>
                <div class = "flex-item">  <a href="pageAdmin.php?filter=1"id = "cancel" class="btn btn-default">Cancel</a></div>
				<div class = "flex-item"><a href="createPhoneUpdHG.php?groupid=<?php echo $group_id; ?> " id = "phone-btn" class="btn btn-default" style="float:right"> Add more phone numbers</a></div>
				<div class = "flex-item"><a href="createEmailUpdHG.php?groupid=<?php echo $group_id; ?> " id = "email-btn" class="btn btn-default" style="float:right"> Add more email addresses</a></div>
			</div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
