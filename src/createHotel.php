<?php
// Include config file
require 'config.php';
 
// Define variables and initialize with empty values
$hotel_group_name = $name = $street = $street_number = $postal_code = $city = $stars  = "";
$hotel_group_name_err = $name_err = $street_err = $street_number_err = $postal_code_err = $city_err = $stars_err  = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $input_name = trim($_POST["Hotel_Name"]);
    if(empty($input_name)){
        $name_err = "Please enter a hotel name.";
    } elseif(!filter_var(trim($_POST["Hotel_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $name_err = 'Please enter a valid hotel name.';
	} else{
        $name = $input_name;
    }
    
    $input_group_name = trim($_POST["Hotel_Group_Hotel_Group_Name"]);
    if(empty($input_group_name)){
        $hotel_group_name_err = "Please enter the hotel group name.";     
    } elseif(!filter_var(trim($_POST["Hotel_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $hotel_group_name_err = 'Please enter a valid hotel group name.';
    } else{
        $hotel_group_name = $input_group_name;
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
	

	$input_stars = trim($_POST["Stars"]);
    if(empty($input_stars)){
        $stars_err = "Please enter the stars of the hotel.";     
    } elseif(!ctype_digit($input_stars)){
        $stars_err = 'Please enter a positive integer value.';
    } else{
       	$stars = $input_stars;
    } 
	
    // Check input errors before inserting in database
    if(empty($name_err) && empty($hotel_group_name_err) && empty($stars_err)  && empty($street_err) && empty($street_number_err) && empty($postal_code_err) && empty($city_err)) {
		$sql_for_group_id="SELECT Hotel_Group_ID FROM Hotel_Group WHERE Hotel_Group_Name=?";
		$stmt_for_group_id= $conn->prepare($sql_for_group_id);
		$stmt_for_group_id->bind_param("s",$hotel_group_name );
		$stmt_for_group_id->execute();
		$result = $stmt_for_group_id->get_result();
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$hotel_group=$row["Hotel_Group_ID"];
		$sql_for_id = "SELECT Number_of_hotels FROM Hotel_Group WHERE Hotel_Group_ID = ?";
		if($stmt_for_id = $conn->prepare($sql_for_id)){
			$stmt_for_id->bind_param("i",$hotel_group);
			if($stmt_for_id->execute()){
				$result = $stmt_for_id->get_result();
					if($result->num_rows == 1){
						$row = $result->fetch_array(MYSQLI_ASSOC);
                			$hotel_numbers = $row["Number_of_hotels"];
							$hotel_id = $hotel_numbers +1;
							// Prepare an insert statement
							$sql = "INSERT INTO Hotel (Hotel_ID, Hotel_Name, Number_of_rooms, Stars, HPostal_Code, HStreet, HSt_Number, HCity, Hotel_Group_Hotel_Group_ID) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?)";
							if($stmt = $conn->prepare($sql)){
							// Bind variables to the prepared statement as parameters
								$stmt->bind_param("isiiisisi",$param_hotel_id, $param_name, $param_rooms, $param_stars, $param_postal_code, 
													$param_street, $param_street_number, $param_city,$param_group_id );
							// Set parameters
								$param_hotel_id = $hotel_id;
								$param_name = $name;
								$param_street = $street;
								$param_street_number = $street_number;
								$param_postal_code = $postal_code;
								$param_city = $city;
								$param_stars = $stars;
								$param_rooms = 0;
								$param_group_id = $hotel_group;
							// Attempt to execute the prepared statement
								if($stmt->execute()){
									$next = 1;
									while (!empty($_POST["phone".$next]) ){
										$sql = "INSERT INTO Hotel_Phone_Number (HPhone_Number, Hotel_Hotel_ID, Hotel_Hotel_Group_Hotel_Group_ID) 
														VALUES (?,?,?)";
										$stmt = $conn->prepare($sql);
									// Bind variables to the prepared statement as parameters
										$stmt->bind_param("iii",$param_phone, $hotel_id, $param_group_id);
										$param_phone = trim($_POST["phone".$next]);
										$stmt->execute();
										$next = $next +1;
									}
									$next = 1;
									while (!empty($_POST["email".$next]) ){
										$sql = "INSERT INTO Hotel_Email_Address (HEmail, Hotel_Hotel_ID, Hotel_Hotel_Group_Hotel_Group_ID) 
														VALUES (?,?,?)";
										$stmt = $conn->prepare($sql);
									// Bind variables to the prepared statement as parameters
										$stmt->bind_param("sii",$param_email, $hotel_id, $param_group_id);
										$param_email= trim($_POST["email".$next]);
										$stmt->execute();
										$next = $next +1;
									}
									$sql = "UPDATE Hotel_Group SET  Number_of_hotels = ? WHERE Hotel_Group_ID = ?";
									$stmt = $conn->prepare($sql);
									$stmt->bind_param("ii",$param_number_of_hotels, $param_group_hotel_id);
									$param_number_of_hotels = $hotel_numbers + 1;
									$param_group_hotel_id = $hotel_group;
									$stmt->execute();
								
								}else{
								// URL doesn't contain valid id parameter. Redirect to error page
									header("location: error.php");
									exit();
								}
						}else{
							echo "Oops! Something went wrong. Please try again later.";
						}
				}
			}
			header("location: pageAdmin.php?filter=3");
			exit();
        } else{
                echo "Something went wrong. Please try again later.";
        }
        
         
        $stmt->close();
    }
    
    // Close connection
    $conn->close();
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet"  href="styles/bgstyle.css"> 
	<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
	<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
</head>
<body>
  <script>
	$(document).ready(function(){
    var next = 1;
    $(".add-more").click(function(e){
        e.preventDefault();
        var addto = "#phone" + next;
        var addRemove = "#phone" + (next);
        next = next + 1;
        var newIn = '<label class="control-label" for="phone1">Phone Number ' + next +'</label> <input autocomplete="off" class="input form-control" id="phone' + next + '" name="phone' + next + '" type="text">';
        var newInput = $(newIn);
        var removeBtn = '<button id="remove' + (next - 1) + '" class="btn btn-danger remove-me" >-</button></div><div id="phone">';
      //  var removeButton = $(removeBtn);
        $(addto).after(newInput);
        $(addRemove).after(removeButton);
        $("#phone" + next).attr('data-source',$(addto).attr('data-source'));
        $("#count1").val(next);  
        
            $('.remove-me').click(function(e){
                e.preventDefault();
                var fieldNum = this.id.charAt(this.id.length-1);
                var fieldID = "#phone" + fieldNum;
                $(this).remove();
                $(fieldID).remove();
            });
    });
});
    </script>
    <script>
	$(document).ready(function(){
    var next = 1;
    $(".add-more2").click(function(e){
        e.preventDefault();
        var addto = "#email" + next;
        var addRemove = "#email" + (next);
        next = next + 1;
        var newIn = '<label class="control-label" for="email1">Email ' + next +'</label> <input autocomplete="off" class="input form-control" id="email' + next + '" name="email' + next + '" type="text">';
        var newInput = $(newIn);
        var removeBtn = '<button id="remove' + (next - 1) + '" class="btn btn-danger remove-me" >-</button></div><div id="email">';
       // var removeButton = $(removeBtn);
        $(addto).after(newInput);
        $(addRemove).after(removeButton);
        $("#email" + next).attr('data-source',$(addto).attr('data-source'));
        $("#count2").val(next);  
        
            $('.remove-me').click(function(e){
                e.preventDefault();
                var fieldNum = this.id.charAt(this.id.length-1);
                var fieldID = "email" + fieldNum;
                $(this).remove();
                $(fieldID).remove();
            });
    });
});
    </script>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Create Record</h2>
                    </div>
                    <p>Please fill this form and submit to add hotel record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($hotel_group_name_err)) ? 'has-error' : ''; ?>">
                            <label>Hotel Group Name</label>
                            <input type="text" name="Hotel_Group_Hotel_Group_Name" class="form-control" value="<?php echo $hotel_group_name; ?>">
                            <span class="help-block"><?php echo $hotel_group_name_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>Hotel Name</label>
                            <input type="text" name="Hotel_Name" class="form-control" value="<?php echo $name; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
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
			<input type="hidden" name="count1" value="1" />
        		<div class="form-group" id="phones">
				<label >Phone Number 1</label>
            			<input autocomplete="off" class="input form-control" id="phone1"  name="phone1" type="text"  /><button id="b1" class="btn add-more" type="button">+</button>
          			<br>
           			<small>Press + to add another phone number</small>
       			</div>
			<input type="hidden" name="count2" value="1" />
        		<div class="form-group" id="emails">
				<label >Email 1</label>
            			<input autocomplete="off" class="input form-control" id="email1"  name="email1" type="text"  /><button id="b2" class="btn add-more2" type="button">+</button>
          			<br>
           			<small>Press + to add another email</small>
       			</div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="pageAdmin.php?filter=3" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
