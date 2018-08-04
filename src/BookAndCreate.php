<?php
// Include config file
require 'config.php';
session_start();

 
// Define variables and initialize with empty values
$first_name = $last_name = $IRS_Number = $SSN = $street = $street_number = $postal_code = $city  = "";
$first_name_err = $last_name_err = $IRS_Number_err = $SSN_err = $street_err = $street_number_err = $postal_code_err = $city_err  = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate name
    $input_first_name = trim($_POST["CFirst_Name"]);
    if(empty($input_first_name)){
        $first_name_err = "Please enter a first name.";
    } elseif(!filter_var(trim($_POST["CFirst_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $first_name_err = 'Please enter a valid first name.';
    } else{
        $first_name = $input_first_name;
    }
    
    $input_last_name = trim($_POST["CLast_Name"]);
    if(empty($input_last_name)){
        $last_name_err = "Please enter a last name.";
    } elseif(!filter_var(trim($_POST["CLast_Name"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $last_name_err = 'Please enter a valid last name.';
    } else{
        $last_name = $input_last_name;
    }
    

    $input_irs = trim($_POST["IRS_Number_C"]);
    if(empty($input_irs)){
        $IRS_Number_err = "Please enter the IRS number.";     
    } elseif(!ctype_digit($input_irs)){
        $IRS_Number_err = 'Please enter a positive integer value.';
    } else{
        $IRS_Number = $input_irs;
    }
	
    $input_ssn = trim($_POST["CSocial_Security_Number"]);
    if(empty($input_ssn)){
        $SSN_err = "Please enter the SSN number.";     
    } elseif(!ctype_digit($input_ssn)){
        $SSN_err = 'Please enter a positive integer value.';
    } else{
        $SSN = $input_ssn;
    }	
		
    $input_street = trim($_POST["CStreet"]);
    if(empty($input_street)){
        $street_err = "Please enter a street.";
    } elseif(!filter_var(trim($_POST["CStreet"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $street_err = 'Please enter a valid street name.';
    } else{
        $street = $input_street;
    } 
	$input_street_number = trim($_POST["CSt_Number"]);
    if(empty($input_street_number)){
        $street_number_err = "Please enter the street number.";     
    } elseif(!ctype_digit($input_street_number)){
        $street_number_err = 'Please enter a positive integer value.';
    } else{
        $street_number = $input_street_number;
    }
	$input_postal_code = trim($_POST["CPostal_Code"]);
    if(empty($input_postal_code)){
        $postal_code_err = "Please enter the postal code.";     
    } elseif(!ctype_digit($input_postal_code)){
        $postal_code_err = 'Please enter a positive integer value.';
    } else{
       	$postal_code = $input_postal_code;
    } 
	$input_city = trim($_POST["CCity"]);
    if(empty($input_city)){
        $city_err = "Please enter a city.";
    } elseif(!filter_var(trim($_POST["CCity"]), FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z'-.\s ]+$/")))){
        $city_err = 'Please enter a valid city name.';
    } else{
        $city = $input_city;
    }    
	
    // Check input errors before inserting in database
    if(empty($first_name_err) && empty($last_name_err) && empty($IRS_Number_err) && empty($SSN_err) && 
		empty($street_err) && empty($street_number_err) && empty($postal_code_err) && empty($city_err) ) {
			

      
       $sql = "REPLACE INTO Customer (IRS_Number_C, CLast_Name, CFirst_Name, CSocial_Security_Number, CStreet, CSt_Number,
        CPostal_Code, CCity, CFirst_Registration) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
         
         if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
             $stmt->bind_param("issisiiss",$param_irs, $param_last_name, $param_first_name, $param_ssn, $param_street, $param_street_number, $param_postal_code, $param_city, $param_first_registration);
            
            // Set parameters
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_irs = $IRS_Number;
            $param_ssn = $SSN;
			$param_street = $street;
			$param_street_number = $street_number;
			$param_postal_code = $postal_code;
			$param_city = $city;
			$param_first_registration = date('Y-m-d H:i:s');;
            // Attempt to execute the prepared statement
           if($stmt->execute()){
				$rooms = array();
				$rooms = $_SESSION["checked_rooms"];
				$i=0;
				foreach ($rooms as &$id) {
					$sql_room = "INSERT INTO Reserves (	ResStart_Date, 	ResFinish_Date, ResPaid, 	Customer_IRS_Number_C, 	Hotel_room_Room_ID)
					VALUES ( ?, ?, ?, ?, ?)"; 
					$stmt = $conn->prepare($sql_room);
						$stmt->bind_param("sssii", $param_start_date, $param_finish_date, $param_paid, $param_irs, $param_room_id);
            
						$param_start_date = $_SESSION["Start_Date"];
						$param_finish_date = $_SESSION["Finish_Date"];
						$param_paid = trim($_POST["Paid"]);
						$param_room_id = $id;
						$stmt->execute();
						$last_id[$i]=$conn->insert_id;
						$i=$i+1;
				}
				$_SESSION["First_Name"]= $first_name;
				$_SESSION["Last_Name"]= $last_name;
				$_SESSION["IRS"]= $IRS_Number;
				$_SESSION["SSN"]= $SSN;		
				$_SESSION["checked_reservations"]=$last_id;
				$_SESSION["Pay"]=$param_paid;		 

                 //Records created successfully. Redirect to landing page
               		header("location:bookinfo.php");
				exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        $stmt->close();
    }
    
    // Close connection
    $conn->close();
}
?>
 
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
 <meta charset="UTF-8">
<head>
<title>Create Record</title>

    <link rel="stylesheet"  href="styles/bgstyle.css"> 

</head>
<body >
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div  <meta charset="UTF-8">
                    <div class="page-header">
                        <h2>Create Record</h2>
                    </div>
                    <p>Please fill this form and submit to add customer record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($first_name_err)) ? 'has-error' : ''; ?>">
                            <label>First Name</label>
                            <input type="text" name="CFirst_Name" class="form-control" value="<?php echo $first_name; ?>">
                            <span class="help-block"><?php echo $first_name_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                            <label>Last Name</label>
                            <input type="text" name="CLast_Name" class="form-control" value="<?php echo $last_name; ?>">
                            <span class="help-block"><?php echo $last_name_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($IRS_Number_err)) ? 'has-error' : ''; ?>">
                            <label>IRS Number</label>
                            <input type="text" name="IRS_Number_C" class="form-control" value="<?php echo $IRS_Number; ?>">
                            <span class="help-block"><?php echo $IRS_Number_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($SSN_err)) ? 'has-error' : ''; ?>">
                            <label>SSN Number</label>
                            <input type="text" name="CSocial_Security_Number" class="form-control" value="<?php echo $SSN; ?>">
                            <span class="help-block"><?php echo $SSN_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($street_err)) ? 'has-error' : ''; ?>">
                            <label>Street</label>
                            <input type="text" name="CStreet" class="form-control" value="<?php echo $street; ?>">
                            <span class="help-block"><?php echo $street_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($street_number_err)) ? 'has-error' : ''; ?>">
                            <label>Street Number</label>
                            <input type="text" name="CSt_Number" class="form-control" value="<?php echo $street_number; ?>">
                            <span class="help-block"><?php echo $street_number_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($postal_code_err)) ? 'has-error' : ''; ?>">
                            <label>Postal Code</label>
                            <input type="text" name="CPostal_Code" class="form-control" value="<?php echo $postal_code; ?>">
                            <span class="help-block"><?php echo $postal_code_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($city_err)) ? 'has-error' : ''; ?>">
                            <label>City</label>
                            <input type="text" name="CCity" class="form-control" value="<?php echo $city; ?>">
                            <span class="help-block"><?php echo $city_err;?></span>
                        </div>
			<div class="form-group  dropdown">
				<label>Do you want to pay upfront?</label>
  				<select name="Paid" class=" w3-input w3-border w3-white" type="text">
				<option value="" disabled selected hidden> Please Choose...</option>
				<option value="Yes">Yes</option>
				<option value="No">No</option>
				</select>
                        </div>
				<?php

				$rooms = array();
				$rooms = $_SESSION["checked_rooms"];
				$format = 'Y-m-d';
				$f_date = DateTime::createFromFormat($format, $_SESSION["Finish_Date"]);
				$s_date = DateTime::createFromFormat($format, $_SESSION["Start_Date"]);
				$number_of_days = date_diff($s_date, $f_date)->format("%a days");
				$total_cost=0;
				$i=0;
				foreach ($rooms as &$id) {
					$temp=$rooms[$i];
					$sql = "SELECT * FROM RoomsView WHERE Room_ID= '$temp'";
					$result = $conn->query($sql);
					$row=$result->fetch_assoc();
					$total_cost=$total_cost + $number_of_days * $row["Price"] ;
					$i=$i+1;
				} 	?>

			<p><b><font color="red">Total cost for this reservation is: <?php echo $total_cost ?> &euro; </font></b></p>
			<p> <strong>Are you sure that you want to continue with the booking <br>
			    for the following dates? <br> <?php echo $_SESSION["Start_Date"]; echo " - "; echo $_SESSION["Finish_Date"]; ?> <br></strong></p>
                   <input type="submit"  class="btn btn-primary" value="Yes"></a>
			<a href="pageAdmin.php?filter=0" class="btn btn-default">No</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
