<?php require 'config.php';
	$temp==0; 
	session_start();?>
				
				
				
<!DOCTYPE html>
<html>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="styles/BAEstyle.css">

<head>

<link rel="stylesheet"  href="styles/bgstyle.css"> 
	

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
<style type="text/css">
       body{
            width: 40%;
            margin: auto;
        }

</style>
</head>



<body >
<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
				$temp=2;
				$sql = "SELECT * FROM Customer WHERE CFirst_Name = ? AND CLast_Name = ? ";
				$stmt = $conn->prepare($sql);
				// Bind variables to the prepared statement as parameters
				$stmt->bind_param("ss", $param_first_name, $param_last_name);	
				$param_first_name = $_POST["First_Name"];
				$param_last_name = $_POST["Last_Name"];
				$paid=$_POST["Paid"];
				if($stmt->execute()){
					$result = $stmt->get_result();
					if($result->num_rows > 0){
						echo "<p>Please select your account and complete your booking.</p>";
						echo "<table class='table table-bordered table-striped'>";
						echo "<thead>";
						echo "<tr>";                                        
						echo "<th>Last Name</th>";
						echo "<th>First Name</th>";
						echo "<th>IRS Number</th>";
						echo "<th>SSN</th>";
						echo "<th>First Registration</th>";
						echo "<th>Address</th>";
						echo "<th>Confirm and Book</th>";
						echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						while($row = $result->fetch_array()){
							echo "<tr>";
							echo "<td>" . $row['CLast_Name'] . "</td>";
							echo "<td>" . $row['CFirst_Name'] . "</td>";
							echo "<td>" . $row['IRS_Number_C'] . "</td>";
							echo "<td>" . $row['CSocial_Security_Number'] . "</td>";
							echo "<td>" . $row['CFirst_Registration'] . "</td>";
							echo "<td>" ; 
								echo "<p>" . $row['CCity'] . ", " . $row['CStreet'] . " " . $row['CSt_Number'] . ", <br>" . $row['CPostal_Code'] . "</p>" ;  
							echo "</td>"; 
							echo "<td>";
							echo "<input type='hidden' name='Pay' value=".$_POST['Paid']." />";
							$irstemp=$row['IRS_Number_C'];
							echo "<a href='tempUser.php?IRS=$irstemp&Paid=$paid' class='button'>Go</a>";
							echo "</td>";
							echo "</tr>";
						}
						echo "</tbody>";                            
						echo "</table>";
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
					$total_cost=$total_cost + $number_of_days * $row["Price"];
					$i=$i+1;
				} 	

		 	echo "<p><b><font color='red'>Total cost for this reservation is: $total_cost &euro; </font></b></p>";
							
				 } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
	
                        }
			} else{
                        echo "ERROR: Could not able to execute $sql. " . $conn->error;
             }
                    
                    // Close connection
                    $conn->close();
				}
			
                 ?>
				 


<?php if ($temp ==0){ $temp=1; ?>
<div class="header">
  <h><img src="images/logo.png" alt="calligraphy-fonts" border="0"></h>
</div>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Find your record</h2>
                    </div>
                    <p>Please fill this form and submit to look for your registration in the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($first_name_err)) ? 'has-error' : ''; ?>">
                            <label>First Name</label>
                            <input type="text" name="First_Name" class="form-control" value="<?php echo $first_name; ?>">
                            <span class="help-block"><?php echo $first_name_err;?></span>
                        </div>
			<div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                            <label>Last Name</label>
                            <input type="text" name="Last_Name" class="form-control" value="<?php echo $last_name; ?>">
                            <span class="help-block"><?php echo $last_name_err;?></span>
                        </div>
				<label>Do you want to pay upfront?</label>
  				<select  name="Paid" class=" w3-input w3-border w3-white" type="text">
  				<option value="" disabled selected hidden> Please Choose...</option>
				<option value="Yes">Yes</option>
				<option value="No">No</option>
				</select>
				<input type="submit" class="btn btn-primary"  value="Submit">
                        <a href="pageUser.php?filter=0" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>        
        </div>
    </div>
</body>
<?php } ?>

</html>
