<?php
// Include config file
require 'config.php';
 
// Define variables and initialize with empty values
$name = $street = $street_number = $postal_code  = "";
$name_err = $street_err = $street_number_err = $postal_code_err  = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $input_name = trim($_POST["Hotel_Group_Name"]);

    $sql="SELECT * FROM Hotel_Group WHERE Hotel_Group_Name = '$input_name'";
    $result=$conn->query($sql);
    if(empty($input_name)){
        $name_err = "Please enter a hotel group name.";
    } elseif(!filter_var(trim($_POST["Hotel_Group_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $name_err = 'Please enter a valid hotel group name.';
    }else if ($result->num_rows>0) {
	$name_err = 'There is already a hotel group with this name';
    }else{
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
	
    // Check input errors before inserting in database
    if(empty($name_err) && empty($street_err) && empty($street_number_err) && empty($postal_code_err) && empty($city_err) ) {
			$sql = "INSERT INTO Hotel_Group (Hotel_Group_Name,Number_of_hotels, HGPostal_Code, HGStreet, HGSt_Number) VALUES ( ?, ?, ?, ?, ?)";
			if($stmt = $conn->prepare($sql)){
			// Bind variables to the prepared statement as parameters
			$stmt->bind_param("siisi", $param_name, $param_hotels,$param_postal_code,$param_street, $param_street_number );
			// Set parameters
				$param_name = $name;
				$param_street = $street;
				$param_street_number = $street_number;
				$param_postal_code = $postal_code;
				$param_hotels=0;
				// Attempt to execute the prepared statement
				if($stmt->execute()){
					$param_group_id=$conn->insert_id;
					$next = 1;
					while (!empty($_POST["phone".$next]) ){
							$sql = "INSERT INTO Hotel_Group_Phone_Number (HGPhone_Number, Hotel_Group_Hotel_Group_ID) VALUES (?,?)";
							$stmt = $conn->prepare($sql);
									// Bind variables to the prepared statement as parameters
										$stmt->bind_param("ii",$param_phone, $param_group_id);
										$param_phone = trim($_POST["phone".$next]);
										$stmt->execute();
										$next = $next +1;
					}   
									$next = 1;
					while (!empty($_POST["email".$next]) ){
										$sql = "INSERT INTO Hotel_Group_Email_Address (HGEmail, Hotel_Group_Hotel_Group_ID) 
														VALUES (?,?)";
										$stmt = $conn->prepare($sql);
									// Bind variables to the prepared statement as parameters
										$stmt->bind_param("si",$param_email, $param_group_id);
										$param_email= trim($_POST["email".$next]);
										$stmt->execute();
										$next = $next +1;
					}   

				}else{
					// URL doesn't contain valid id parameter. Redirect to error page
					header("location: error.php");
					exit();
				}  

			header("location: pageAdmin.php?filter=1");
			exit();

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
                    <p>Please fill this form and submit to add hotel group record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                        <a href="pageAdmin.php?filter=1" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
