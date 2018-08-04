<?php
// Include config file
require 'config.php';
// Define variables and initialize with empty values
$hotel = $hotel_group = $price = $repairs = $expendable = $view = $capacity = "";
$hotel_err = $hotel_group_err = $price_err = $repairs_err = $expendable_err = $view_err = $capacity_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $input_hotel = trim($_POST["Hotel"]);
    if(empty($input_hotel)){
        $hotel_err = "Please enter a hotel name.";
    } elseif(!filter_var(trim($_POST["Hotel"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $hotel_err = 'Please enter a valid hotel name.';
	} else{
        $hotel = $input_hotel;
    }
    
    $input_hotel_group = trim($_POST["Hotel_Group"]);
    if(empty($input_hotel_group)){
        $hotel_group_err = "Please enter a hotel group.";     
    } elseif(!filter_var(trim($_POST["Hotel_Group"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $hotel_group_err = 'Please enter a valid hotel group name.';
    } else{
        $hotel_group = $input_hotel_group;
    }
    
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
    if(empty($hotel_err) && empty($hotel_group_err) && empty($price_err) && empty($capacity_err) && empty($view_err) && empty($expendable_err) && empty($repairs_err) ) {
		$sql = "SELECT Hotel_Group_ID FROM Hotel_Group WHERE Hotel_Group_Name = ?";
		$stmt= $conn->prepare($sql);
		$stmt->bind_param("s",$hotel_group );
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$hotel_group_id = $row["Hotel_Group_ID"];
		$sql_for_id = "SELECT DISTINCT Hotel_ID,Number_of_rooms FROM Hotel WHERE ((Hotel_Name = ?) AND (	Hotel_Group_Hotel_Group_ID = ?) )";
		if($stmt_for_id = $conn->prepare($sql_for_id)){
			$stmt_for_id->bind_param("si",$hotel,$hotel_group_id);
			if($stmt_for_id->execute()){
				$result = $stmt_for_id->get_result();
					if($result->num_rows == 1){
						$row = $result->fetch_array(MYSQLI_ASSOC);
                			$hotel_id = $row["Hotel_ID"];
						$room_number=$row["Number_of_rooms"];	
							// Prepare an insert statement
							$sql = "INSERT INTO Hotel_room (Price, Repairs_need, Expendable, RView, Capacity, Hotel_Hotel_ID, Hotel_Hotel_Group_Hotel_Group_ID) VALUES (?,?, ?, ?, ?, ?,?)";
							if($stmt = $conn->prepare($sql)){
							// Bind variables to the prepared statement as parameters
								$stmt->bind_param("isisiii",$param_price, $param_repairs, $param_expendable, $param_view, $param_capacity, 
													$param_hotel_id, $param_hotel_group_id);
							// Set parameters
								$param_price = $price;
								$param_repairs = $repairs;
								$param_expendable = $expendable;
								$param_view = $view;
								$param_capacity = $capacity;
								$param_hotel_id = $hotel_id;
								$param_hotel_group_id = $hotel_group_id;
								
							// Attempt to execute the prepared statement
								if($stmt->execute()){
									$room_id = $conn->insert_id;
									$next = 1;
									while (!empty($_POST["amenities".$next]) ){
										$sql = "INSERT INTO Hotel_room_Amenities (Amenities, Hotel_room_Room_ID) 
														VALUES (?,?)";
										$stmt = $conn->prepare($sql);
									// Bind variables to the prepared statement as parameters
										$stmt->bind_param("si",$param_amenities, $room_id);
										$param_amenities = trim($_POST["amenities".$next]);
										$stmt->execute();
										$next = $next +1;
									}
									$sql = "UPDATE Hotel SET  Number_of_rooms = Number_of_rooms+1 WHERE Hotel_ID = ?";
									$stmt = $conn->prepare($sql);
									$stmt->bind_param("i", $param_hotel_id);
									$param_hotel_id = $hotel_id;
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
			header("location: pageAdmin.php?filter=2");
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
        var addto = "#amenities" + next;
        var addRemove = "#amenities" + (next);
        next = next + 1;
        var newIn = '<label class="control-label" for="amenities1">Amenity ' + next +'</label> <input autocomplete="off" class="input form-control" id="amenities' + next + '" name="amenities' + next + '" type="text">';
        var newInput = $(newIn);
        var removeBtn = '<button id="remove' + (next - 1) + '" class="btn btn-danger remove-me" >-</button></div><div id="amenities">';
       // var removeButton = $(removeBtn);
        $(addto).after(newInput);
        $(addRemove).after(removeButton);
        $("#amenities" + next).attr('data-source',$(addto).attr('data-source'));
        $("#count1").val(next);  
        
            $('.remove-me').click(function(e){
                e.preventDefault();
                var fieldNum = this.id.charAt(this.id.length-1);
                var fieldID = "#amenities" + fieldNum;
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
                        <h2>Create Room Record</h2>
                    </div>
                    <p>Please fill this form and submit to add room record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($hotel_err)) ? 'has-error' : ''; ?>">
                            <label>Hotel</label>
                            <input type="text" name="Hotel" class="form-control" value="<?php echo $hotel; ?>">
                            <span class="help-block"><?php echo $hotel_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($hotel_group_err)) ? 'has-error' : ''; ?>">
                            <label>Hotel Group</label>
                            <input type="text" name="Hotel_Group" class="form-control" value="<?php echo $hotel_group; ?>">
                            <span class="help-block"><?php echo $hotel_group_err;?></span>
                        </div>
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
			<input type="hidden" name="count1" value="1" />
        		<div class="form-group" id="amenities">
				<label >Amenity 1</label>
            			<input autocomplete="off" class="input form-control" id="amenities1"  name="amenities1" type="text"  /><button id="b1" class="btn add-more" type="button">+</button>
          			<br>
           			<small>Press + to add another amenity</small>
       			</div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="pageAdmin.php?filter=2" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
