<?php
		session_start();
		// Include config file
		require 'config.php';
		// Define variables and initialize with empty values
		$check_in = $check_out = $location = "";
		$adults = 1;
		$kids = 0;
		$manage_err= $_SESSION["manage_errors"];
		$old_inp= $_SESSION["inputs"];
		if($_SERVER["REQUEST_METHOD"] == "POST"){
			$adults = $_POST["NofAdults"];
			$kids = $_POST["NofKids"];
			$location = $_POST["Location"];
			$input_check_in = trim($_POST["CheckIn"]);
			$input_check_out = trim($_POST["CheckOut"]);
			$in_dt = new DateTime($input_check_in);
			$out_dt = new DateTime($input_check_out);
			if ($in_dt > $out_dt){
				$check_in_err = "Date of check in can t be after date of check out"; 
			} else{
				$check_in = $input_check_in;
				$check_out = $input_check_out;
				$_SESSION["Start_Date"] = $check_in;
				$_SESSION["Finish_Date"] = $check_out;
			}
		}
		?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">


<head>
 
 <link rel="stylesheet"  href="styles/style.css"> 
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
</head>

<body>

<div class="header">
  <h><img src="images/logo.png" alt="calligraphy-fonts" border="0"></h>
</div>


<div class="topnav" id="navbar">
	<div class="btn-group">
    <button class="button tablink" onclick="openLink(event, 'Search Hotel');"> Search Hotel</button>
    <button class="button tablink" onclick="openLink(event, 'Hotel Groups'); FilterFunc(0);">Hotel Groups</button>
	</div>
	<div class = "btn-group" style="float:right">
    <button class="button tablink" onclick="openLink(event, 'Rooms'); FilterFunc(0);">Rooms</button>
    <button class="button tablink" onclick="openLink(event, 'Hotels'); FilterFunc(0);">Hotels</button>
    <button class="button tablink" onclick="openLink(event, 'Employees'); FilterFunc(0);">Employees</button>
    <button class="button tablink" onclick="openLink(event, 'Customers'); FilterFunc(0);">Customers</button>
    <button class="button tablink" onclick="openLink(event, 'Reserves' ); FilterFunc(0);">Reserves</button>
    <button class="button tablink" onclick="openLink(event, 'Rents' ); FilterFunc(0);">Rents</button> 
     <button class="button tablink" onclick="openLink(event, 'Accs' ); FilterFunc(0);">Manage Accounts</button> 
  	</div>
</div>

<!-- Tabs -->    
     <div id="Search Hotel" class="w3-container  w3-padding-16 myLink">     
	<div class="sidenav">
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <div class="w3-row-padding" style="margin:0px -8px;">
               <div  id="location" class="w3-margin-bottom">
                  <label><i class="fa fa-location-arrow" style="color:white"></i><font color="white"> Location</font></label>
                  <select  name="Location" class="w3-input w3-border w3-white" type="text" placeholder="Type the location"></div>
					  <option value="All_locations">Select Location...</option>
					<?php
					require 'config.php';
					$sql = "SELECT DISTINCT HCity FROM Hotel";
					if ($result = $conn->query($sql)){
						if($result->num_rows > 0){
							while($row = $result->fetch_array()){
								if(($_SERVER["REQUEST_METHOD"] == "POST") AND ($row['HCity'] == $location)){
									?><option selected value=<?php echo $row['HCity'];?> > <?php echo $row['HCity']; ?></option>
								<?php }
								else{
									 ?><option value=<?php echo $row['HCity'];?> > <?php echo $row['HCity']; ?></option>
								<?php }
							}
						}else{ 
							?> <option value="">No locations found</option> <?php
						}
					} else {
							?> <option value="">Error</option>; <?php
					}
					// Close connection
                    $conn->close();
					?> 
						
					</select>
               </div>
            <div class="w3-row-padding" style="margin:8 -16px;">
              <div id="Check-in" class="w3-half w3-margin-bottom form-group<?php echo (!empty($check_in_err)) ? 'has-error' : ''; ?>">
                <label><i class="fa fa-calendar-o" style="color:white"></i><font color="white"> Check In (*)</font> </label>
                <input name="CheckIn"min=<?php echo date('Y-m-d');?> class="w3-input w3-border" type="date"  required  value="<?php echo $check_in;?>">
                <span class="help-block"><?php echo $check_in_err;?></span>
              </div>
           
              <div  id="Check-out"class="w3-half form-group<?php echo (!empty($check_out_err)) ? 'has-error' : ''; ?>">
                <label><i class="fa fa-calendar-o" style="color:white"></i><font color="white"> Check Out (*)</font> </label>
                <input name="CheckOut" min=<?php echo date('Y-m-d');?> class="w3-input w3-border" type="date"  required  value="<?php echo $check_out;?>">
                <span class="help-block"><?php echo $check_out_err;?></span>
              </div>
            </div>
            <div class="w3-row-padding" style="margin:0px -8px;">
              <div  id="adults" class="w3-half w3-margin-bottom">
                <label><i class="fa fa-male" style="color:white"></i> <font color="white">Adults (*)</font></label>
                <input name="NofAdults" class="w3-input w3-border" type="number"  min="1"  required value="<?php echo $adults;?>">
              </div>
              <div  id="children" class="w3-half">
                <label><i class="fa fa-child" style="color:white"></i> <font color="white"> Kids</font></label>
              	<input name="NofKids" class="w3-input w3-border" type="number"  min="0"  value="<?php echo $kids;?>">
              </div>
            </div>
           
            <button id = "submit-btn" class="w3-button w3-dark-grey" style="margin-top:20px; margin-left:25%" type="submit" ><i class="fa fa-search w3-margin-right"></i> Search Availability</button>
          	<p><font color="white" style="margin-left:25%"> Fields with (*) are necessary</font></p>
	    <div id="toogleDIV">
		<div class="w3-row-padding" style="margin:0px -8px;">
		<div class=" w3-margin-bottom" id ="HG" >
                  <label><i class="fa fa-hotel" style="color:white"></i><font color="white"> Hotel Group </font></label>
               	  <select  id="HG_inp" onchange="FilterFunc(1)" name="HG" class=" w3-input w3-border w3-white" type="text" placeholder="Type wanted hotel group">
					  <option value="">Select Hotel Group...</option>
					<?php
					require 'config.php';
					$sql = "SELECT 	Hotel_Group_Name FROM Hotel_Group";
					if ($result = $conn->query($sql)){
						if($result->num_rows > 0){
							while($row = $result->fetch_array()){
								if(($_SERVER["REQUEST_METHOD"] == "POST") AND ($row['Hotel_Group_Name'] == $hotel_group)){
									?><option  value=<?php echo $row['Hotel_Group_Name'];?> selected> <?php echo $row['Hotel_Group_Name']; ?></option>
								<?php }
								else{
									 ?><option value=<?php echo $row['Hotel_Group_Name'];?> > <?php echo $row['Hotel_Group_Name']; ?></option>
								<?php }
						}
						}else{ 
							?> <option value="">No hotel groups found</option> <?php
						}
					} else {
							?> <option value="">Error</option>; <?php
					}
					// Close connection
                    $conn->close();
					?> 
						
					</select>
               </div>
		</div>
		 <div class="w3-row-padding" style="margin:0px -8px;">
		  <div class=" w3-margin-bottom" id="stars">
		        <label><i class="fa fa-star" style="color:white"></i><font color="white">Stars </font></label>
		        <input class="w3-input w3-border" id="stars_inp" onkeyup="FilterFunc(1)" name="stars_inp" type="number"  min="1" max="5" placeholder="Wanted number of stars">
			
		  </div>
		</div>
		<div class="w3-row-padding" style="margin:0px -8px;">
		<div class="w3-half slidercontainer form-group <?php echo (!empty($min_price_err)) ? 'has-error' : ''; ?>" id="min">
                <label><i class="fa fa-euro" style="color:white"></i><font color="white"> Price (min) per night :<span id="demo"></span></font></label>
              	<input name="MinPrice" onchange="FilterFunc(1)" class="w3-input w3-border slider" type="range" min="1" max="500" id = "min_inp" value="1">
				<span class="help-block"><?php echo $min_price_err;?></span>
			<script>
				var slider = document.getElementById("min_inp");
				var output = document.getElementById("demo");
				output.innerHTML = slider.value;

				slider.oninput = function() {
				  output.innerHTML = this.value;
				}
			</script>
		</div>
		<div class="w3-half slidercontainer " >
                <label><i class="fa fa-euro" style="color:white"></i><font color="white"> Price (max) per night :<span id="demo2"></span></font></label>
              	<input name="MaxPrice" onchange="FilterFunc(1)" class="w3-input w3-border slider" type="range" min="1" max="1000" id = "max_inp" value="999">
				<span class="help-block"><?php echo $min_price_err;?></span>
			<script>
				var slider2 = document.getElementById("max_inp");
				var output2 = document.getElementById("demo2");
				output2.innerHTML = slider.value;

				slider2.oninput = function() {
				  output2.innerHTML = this.value;
				}
			</script>
		</div>
		</div>
		<div class="w3-row-padding" style="margin:0px -8px;">
		<div class="w3-half slidercontainer form-group pull-left<?php echo (!empty($min_rooms_err)) ? 'has-error' : ''; ?>" id="min">
                <label><i class="fa fa-home" style="color:white"></i><font color="white">Minimum total number of rooms:<span id="demo3"></span></font></label>
              	<input name="MinN" onchange="FilterFunc(1)" class="w3-input w3-border slider" type="range" min="0" max="200" id = "min_N" value="0">
				<span class="help-block"><?php echo $min_rooms_err;?></span>
			<script>
				var slider3 = document.getElementById("min_N");
				var output3 = document.getElementById("demo3");
				output3.innerHTML = slider.value;

				slider3.oninput = function() {
				  output3.innerHTML = this.value;
				}
			</script>
		</div>
		<div class="w3-half slidercontainer " >
                <label><i class="fa fa-home" style="color:white"></i><font color="white"> Maximum total number of rooms:<span id="demo4"></span></font></label>
              	<input name="MaxN" onchange="FilterFunc(1)" class="w3-input w3-border slider" type="range" min="1" max="200" id = "max_N" value="200">
				<span class="help-block"><?php echo $min_rooms_err;?></span>
			<script>
				var slider4 = document.getElementById("max_N");
				var output4 = document.getElementById("demo4");
				output4.innerHTML = slider.value;

				slider4.oninput = function() {
				  output4.innerHTML = this.value;
				}
			</script>
		</div>
		</div>
             <div  id ="amenities">
                <label><i class="fa fa-tv" style="color:white; float:left"></i><font color="white">Amenities</font></label>
		<div class="newspaper">
         	<input id="ament1" type="checkbox" value="TV" onclick="FilterFunc(1);"/> TV <br>
		<input id="ament2" type="checkbox" value="AC"onclick="FilterFunc(1);"/> AC <br>
		<input id="ament3" type="checkbox" value="WiFi" onclick="FilterFunc(1);"/> WiFi <br>
		<input id="ament4" type="checkbox" value="Breakfast" onclick="FilterFunc(1);"/> Breakfast <br>
		<input id="ament5" type="checkbox" value="Mini Bar" onclick="FilterFunc(1);"/> Mini Bar <br>
		<input id="ament6" type="checkbox" value="Towels" onclick="FilterFunc(1);"/> Towels <br>
		<input id="ament7" type="checkbox" value="Refrigerator"onclick="FilterFunc(1);" /> Refrigerator <br>
		<input id="ament8" type="checkbox" value="Hair Dryer" onclick="FilterFunc(1);"/> Hair Dryer <br>
		<input id="ament9" type="checkbox" value="Kitchen" onclick="FilterFunc(1);"/> Kitchen <br>
		<input id="ament10"type="checkbox" value="Room Service" onclick="FilterFunc(1);"/> Room Service <br>
		<input id="ament11"type="checkbox" value="Pool" onclick="FilterFunc(1);"/> Pool <br>
		<input id="ament12"type="checkbox" value="Free Parking" onclick="FilterFunc(1);"/> Free Parking <br>
		</div>
             </div> 
           </div>
            </div>
	    </div>
          </form>
        </div>
      </div>
	<div class="main" id="cl">
	<?php 
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		 require'config.php';
		if(empty($check_in_err)){
			$people = $adults + $kids;
			if ($location!="All_locations"){
				$sql = "CREATE VIEW `TempView` AS
			SELECT 
				`T`.`Hotel` AS `Hotel`,
				`T`.`Hotel_Group` AS `Hotel_Group`,
				`T`.`Price` AS `Price`,
				`T`.`Capacity` AS `Capacity`,
				`T`.`RView` AS `RView`,
				`T`.`Expendable` AS `Expendable`,
				`T`.`Repairs` AS `Repairs`,
				`T`.`Stars` AS `Stars`,
				`T`.`City` AS `City`,
				`T`.`Street` AS `Street`,
				`T`.`St_Number` AS `St_Number`,
				`T`.`Postal_Code` AS `Postal_Code`,
				`T`.`Hotel_ID` AS `Hotel_ID`,
				`T`.`Number_of_rooms` AS `Number_of_rooms`,
				`T`.`Hotel_Group_ID` AS `Hotel_Group_ID`,
				`T`.`Room_ID` AS `Room_ID`
			FROM
				(SELECT 
					`RoomsView`.`Hotel` AS `Hotel`,
						`RoomsView`.`Hotel_Group` AS `Hotel_Group`,
						`RoomsView`.`Price` AS `Price`,
						`RoomsView`.`Capacity` AS `Capacity`,
						`RoomsView`.`RView` AS `RView`,
						`RoomsView`.`Expendable` AS `Expendable`,
						`RoomsView`.`Repairs` AS `Repairs`,
						`RoomsView`.`Stars` AS `Stars`,
						`RoomsView`.`City` AS `City`,
						`RoomsView`.`Street` AS `Street`,
						`RoomsView`.`St_Number` AS `St_Number`,
						`RoomsView`.`Postal_Code` AS `Postal_Code`,
						`RoomsView`.`Hotel_ID` AS `Hotel_ID`,
						`RoomsView`.`Number_of_rooms` AS `Number_of_rooms`,
						`RoomsView`.`Hotel_Group_ID` AS `Hotel_Group_ID`,
						`RoomsView`.`Room_ID` AS `Room_ID`
				FROM
					`RoomsView`
				WHERE
					(NOT (`RoomsView`.`Room_ID` IN (SELECT 
							`Reserves`.`Hotel_room_Room_ID`
						FROM
							`Reserves`
						WHERE
							((`Reserves`.`ResStart_Date` BETWEEN '$check_in' AND '$check_out')
								OR (`Reserves`.`ResFinish_Date` BETWEEN '$check_in' AND '$check_out')
								OR ('$check_in' BETWEEN `Reserves`.`ResStart_Date` AND `Reserves`.`ResFinish_Date`)))))) `T`
			WHERE
				((NOT (`T`.`Room_ID` IN (SELECT 
						`Rents`.`Hotel_room_Room_ID`
					FROM
						`Rents`
					WHERE
						((`Rents`.`RStart_Date` BETWEEN '$check_in' AND '$check_out')
							OR (`Rents`.`RFinish_Date` BETWEEN '$check_in' AND '$check_out')
							OR ('$check_in' BETWEEN `Rents`.`RStart_Date` AND `Rents`.`RFinish_Date`)))))
					AND (`T`.`City` = '$location')
					)";
					
			}else{
				$sql = "CREATE VIEW `TempView` AS
			SELECT 
				`T`.`Hotel` AS `Hotel`,
				`T`.`Hotel_Group` AS `Hotel_Group`,
				`T`.`Price` AS `Price`,
				`T`.`Capacity` AS `Capacity`,
				`T`.`RView` AS `RView`,
				`T`.`Expendable` AS `Expendable`,
				`T`.`Repairs` AS `Repairs`,
				`T`.`Stars` AS `Stars`,
				`T`.`City` AS `City`,
				`T`.`Street` AS `Street`,
				`T`.`St_Number` AS `St_Number`,
				`T`.`Postal_Code` AS `Postal_Code`,
				`T`.`Hotel_ID` AS `Hotel_ID`,
				`T`.`Number_of_rooms` AS `Number_of_rooms`,
				`T`.`Hotel_Group_ID` AS `Hotel_Group_ID`,
				`T`.`Room_ID` AS `Room_ID`
			FROM
				(SELECT 
					`RoomsView`.`Hotel` AS `Hotel`,
						`RoomsView`.`Hotel_Group` AS `Hotel_Group`,
						`RoomsView`.`Price` AS `Price`,
						`RoomsView`.`Capacity` AS `Capacity`,
						`RoomsView`.`RView` AS `RView`,
						`RoomsView`.`Expendable` AS `Expendable`,
						`RoomsView`.`Repairs` AS `Repairs`,
						`RoomsView`.`Stars` AS `Stars`,
						`RoomsView`.`City` AS `City`,
						`RoomsView`.`Street` AS `Street`,
						`RoomsView`.`St_Number` AS `St_Number`,
						`RoomsView`.`Postal_Code` AS `Postal_Code`,
						`RoomsView`.`Hotel_ID` AS `Hotel_ID`,
						`RoomsView`.`Number_of_rooms` AS `Number_of_rooms`,
						`RoomsView`.`Hotel_Group_ID` AS `Hotel_Group_ID`,
						`RoomsView`.`Room_ID` AS `Room_ID`
				FROM
					`RoomsView`
				WHERE
					(NOT (`RoomsView`.`Room_ID` IN (SELECT 
							`Reserves`.`Hotel_room_Room_ID`
						FROM
							`Reserves`
						WHERE
							((`Reserves`.`ResStart_Date` BETWEEN '$check_in' AND '$check_out')
								OR (`Reserves`.`ResFinish_Date` BETWEEN '$check_in' AND '$check_out')
								OR ('$check_in' BETWEEN `Reserves`.`ResStart_Date` AND `Reserves`.`ResFinish_Date`)))))) `T`
			WHERE
				((NOT (`T`.`Room_ID` IN (SELECT 
						`Rents`.`Hotel_room_Room_ID`
					FROM
						`Rents`
					WHERE
						((`Rents`.`RStart_Date` BETWEEN '$check_in' AND '$check_out')
							OR (`Rents`.`RFinish_Date` BETWEEN '$check_in' AND '$check_out')
							OR ('$check_in' BETWEEN `Rents`.`RStart_Date` AND `Rents`.`RFinish_Date`)))))
					)";
				}
				if(!$conn->query($sql)){ 
				header("location: error.php");
				exit(); 
			}
			$sql = "SELECT * FROM `TempView` WHERE (`Capacity` + `Expendable`) >= ?  ";
			if($stmt = $conn->prepare($sql)){
				$stmt->bind_param( "i",   $people );
				if($stmt->execute()){
					$available_hotels = $stmt->get_result();
					if ($available_hotels->num_rows>0){
						?> <div class="wrapper">
								<div id = "search-table" class="container-fluid">
								<div class="row">
									<div class="col-md-12">
										<div class="page-header clearfix">
										<h2 class="pull-left"><font color="white"><strong>Available Rooms</strong></font></h2>
										<button class="btn btn-success pull-right w3-red pull-right" onclick="tooglefunc()"><i class="fa fa-cogs"></i>Show filters</button>
										<form action="bookroom.php"  method="post">
										<button id = "book-btn"  type="submit" ><span class='glyphicon glyphicon-book'></span> Book now !</button>
										
									</div>
						<?php	echo "<table id='myTableSearch' class='table table-bordered table-striped'>";
				                echo "<thead>";
				                    echo "<tr>";                                        
				                        echo "<th>Hotel </th>";
				                        echo "<th>Price</th>";
										echo "<th>Capacity</th>";
				                        echo "<th>Room View</th>";
										echo "<th>Expendable</th>";
										echo "<th>Stars</th>";
										echo "<th>Hotel Group</th>";
										echo "<th>Amenities</th>";
										echo "<th>Total number <br> of rooms</th>";
										echo "<th>Book it now !</th>";
				                    echo "</tr>";
				                echo "</thead>";
				                echo "<tbody>";
								while ($row = $available_hotels->fetch_assoc()){
									$sql_amenities = "SELECT Amenities FROM Hotel_room_Amenities WHERE Hotel_room_Room_ID= ? ";
									$stmt_amenities = $conn->prepare($sql_amenities);
									$stmt_amenities->bind_param( "i", $row['Room_ID'] );
									$stmt_amenities->execute();
									$available_amenities = $stmt_amenities->get_result();
									echo "<tr>";
									echo "<td>" . $row['Hotel'] . "</td>";
									echo "<td>" . $row['Price'] . "</td>";
									echo "<td>" . $row['Capacity'] . "</td>";
									echo "<td>" . $row['RView'] . "</td>";
									echo "<td>" . $row['Expendable'] . "</td>";
									echo "<td>" . $row['Stars'] . "</td>";
									echo "<td>" . $row['Hotel_Group'] . "</td>";
									echo "<td>";
									echo "<ul class = 'two-col-special' >";
									while ($row_amenities = $available_amenities->fetch_assoc()){
										echo "<li>" . $row_amenities['Amenities'] . "</li>";
									}
									echo "</ul>";
									echo "</td>";
									echo "<td>" . $row['Number_of_rooms'] . "</td>";
									echo "<td>";
									?>
									<label class="amenity-container">
										<input type="checkbox" name="checked_rooms[]" value="<?php echo $row['Room_ID'];?>">
										<span class="amenity-checkmark"></span>
									</label>
									<?php
									echo "</td>";
				            echo "</tr>";
                          }
                            echo "</tbody>";                            
                         echo "</table>";
						
						}else{ 
							echo "<p class ='lead'><em>No records were found.</em></p>";
						}
						echo "</div>";
						echo "</div>";        
						echo "</div>";
						echo "</div>";
						echo 1;
						$sql = "DROP VIEW TempView";
						if(!$conn->query($sql)){ 
							header("location: error.php");
							exit();
						}
						} else{
							// URL doesn't contain valid id. Redirect to error page
							header("location: error.php");
							exit();
							} 
						
					}
				}
			}
			
		
     ?>      
      </form>
   </div>


    

    <div id="Hotel Groups" class="w3-container  w3-padding-16 myLink">
	<div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left"><font color="white"><b>Hotel Groups</b></font></h2>
			 <a href="createHotelGroup.php" class="btn btn-success pull-right w3-red" > <i class="fa fa-plus-circle"></i> Add New Hotel Group</a>
	  	    </div>
    	 <?php
	require 'config.php';
	$sql = "SELECT * FROM Hotel_Group";

		    if ($result = $conn->query($sql)){
                        if($result->num_rows > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";                                        
                                        echo "<th>Group Name </th>";
					echo "<th>Group ID </th>";
                                        echo "<th>Number of Hotels</th>";
					echo "<th>Address</th>";
                                        echo "<th>Contact info</th>";
					echo "<th>Hotels info</th>";
                                        echo "<th>Acion</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
                                        echo "<td>" . $row['Hotel_Group_Name'] . "</td>";
					echo "<td>" . $row['Hotel_Group_ID']   . "</td>";
                                        echo "<td>" . $row['Number_of_hotels'] . "</td>";
                                        echo "<td>" ; 
										echo "<p>" . $row['HGStreet'] . " " . $row['HGSt_Number'] . ", <br>" . $row['HGPostal_Code'] . "</p>" ;  
										echo "</td>"; 
										echo "<td>";
										echo "<a href='readHotelGroup.php?id=". $row['Hotel_Group_ID'] ."' title='View Info' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
										echo "</td>";
										echo "<td>";
										echo "<a href='readHotelGroup2.php?id=". $row['Hotel_Group_ID'] ."' title='View Info' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
										echo "</td>";
										echo "<td>";
										echo "<a href='updateHotelGroup.php?id=". $row['Hotel_Group_ID'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
										echo "<a href='deleteHotelGroup.php?id=". $row['Hotel_Group_ID'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
										echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
			    $result->free();
		}else{ echo "<p class ='lead'><em>No records were found.</em></p>";
		}
	} else {
	    echo "0 results";
	}
	 // Close connection
                    $conn->close();
	?> 
         </div>
            </div>        
        </div>
    </div>
    </div>


    <div id="Rooms" class="w3-container  w3-padding-16 myLink" >
      <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left"><font color="white"><b>Rooms Details</b></font></h2>
                        <a href="createRoom.php" class="btn btn-success pull-right w3-red" > <i class="fa fa-plus-circle"></i> Add New Room</a>
                    </div>
                    <?php
                    // Include config file
                    require 'config.php';
                    
                    // Attempt select query execution
                    $sql = "SELECT * FROM RoomsView";
                    if($result = $conn->query($sql)){
                        if($result->num_rows > 0){
			    echo "<input type='text' id='myInput1'  onkeyup='tabsfilterFunc(0,id,1)' placeholder='Search for hotel names..' title='Type in a name'>";
			    echo "<strong> <font color='white'> OR</font>  </strong>";
			    echo '<input type="text" id="myInput1.2"  onkeyup="tabsfilterFunc(1,id,1)" placeholder="Search for room ids.." title="Type in an id">';
                            echo "<table id='myTable1' class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";                                        
                                        echo "<th>Hotel</th>";
					echo "<th>Hotel Group</th>";
					echo "<th>Room ID</th>";
					echo "<th>Price</th>";
                                        echo "<th>Capacity</th>";
					echo "<th>View</th>";
                                        echo "<th>Expendability</th>";
                                        echo "<th>Needed Repairs</th>";
					echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
                                        echo "<td>" . $row['Hotel'] . "</td>";
					echo "<td>" . $row['Hotel_Group'] . "</td>";
					echo "<td>" . $row['Room_ID'] . "</td>";
                                        echo "<td>" . $row['Price'] . "</td>";
					echo "<td>" . $row['Capacity'] . "</td>";
                                        echo "<td>" . $row['RView'] . "</td>";
					echo "<td>" . $row['Expendable'] . "</td>";
                                        echo "<td>" . $row['Repairs'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='readRoom.php?id=". $row['Room_ID'] ."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                            echo "<a href='updateRoom.php?id=". $row['Room_ID'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='deleteRoom.php?id=". $row['Room_ID'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            $result->free();
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . $conn->error;
                    }
                    
                    // Close connection
                    $conn->close();
                    ?>
                </div>
            </div>        
        </div>
    </div>
    </div>


    <div id="Hotels" class="w3-container w3-padding-16 myLink">
      <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left"><font color="white"><b>Hotels Details</b></font></h2>
                        <a href="createHotel.php" class="btn btn-success pull-right w3-red" > <i class="fa fa-plus-circle"></i> Add New Hotel</a>
                    </div>
                    <?php
                    // Include config file
                    require 'config.php';
                    
                    // Attempt select query execution  
                    $sql = "SELECT * FROM HotelsView";  
                    if($result = $conn->query($sql)){
                        if($result->num_rows > 0){
				$tab="myTable2";
			    echo '<input type="text" id="myInput2"  onkeyup="tabsfilterFunc(0,id, 2 )" placeholder="Search for hotel names.." title="Type in a name">';
                            echo "<table id='myTable2' class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";                                        
                                        echo "<th>Name</th>";
					echo "<th>Hotel ID</th>";
					echo "<th>Hotel Group</th>";
                                        echo "<th>Address</th>";
					echo "<th>Stars</th>";
                                        echo "<th>Number of Rooms</th>";
					echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
                                        echo "<td>" . $row['Hotel_Name'] . "</td>";
					echo "<td>" . $row['Hotel_ID']   . "</td>";
					echo "<td>" . $row['Hotel_Group_Name'] . "</td>";
                                        echo "<td>" ; 
						echo "<p>" . $row['HCity'] . ", " . $row['HStreet'] . " " . $row['HSt_Number'] . ", <br>" . $row['HPostal_Code'] . "</p>" ;  
					echo "</td>"; 
					echo "<td>" . $row['Stars'] . "</td>";
                                        echo "<td>" . $row['Number_of_rooms'] . "</td>";
                                        echo "<td>"; echo "<a href='readHotel.php?Group_ID=". $row['Hotel_Group_ID'] . '&Hotel_ID=' . $row['Hotel_ID']."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                            echo "<a href='updateHotel.php?Group_ID=". $row['Hotel_Group_ID'] . '&Hotel_ID=' . $row['Hotel_ID']."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='deleteHotel.php?Group_ID=". $row['Hotel_Group_ID'] . '&Hotel_ID=' . $row['Hotel_ID']."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            $result->free();
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . $conn->error;
                    }
                    
                    // Close connection
                    $conn->close();
                    ?>
                </div>
            </div>        
        </div>
    </div>
    </div>


    <div id="Employees" class="w3-container  w3-padding-16 myLink">
      <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left"><font color="white"><b>Employees Details</b></font></h2>
                        <a href="createEmployee.php" class="btn btn-success pull-right w3-red" > <i class="fa fa-plus-circle"></i> Add New Employee</a>
                    </div>
                    <?php
                    // Include config file
                    require 'config.php';
                    
                    // Attempt select query execution
                    $sql = "SELECT * FROM EmpView";
                    if($result = $conn->query($sql)){
                        if($result->num_rows > 0){
                            echo "<input type='text' id='myInput3'  onkeyup='tabsfilterFunc(0,id,3)' placeholder='Search for last names..' title='Type in a name'>";
			    echo "<strong> <font color='white'> OR </font> </strong>";
			    echo '<input type="text" id="myInput3.2"  onkeyup="tabsfilterFunc(2,id,3)" placeholder="Search for IRS.." title="Type in an IRS">';
                            echo "<table id='myTable3' class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";                                        
                                        echo "<th>Last Name</th>";
                                        echo "<th>First Name</th>";
					echo "<th>IRS Number</th>";
                                        echo "<th>Position</th>";
                                        echo "<th>Hotel</th>";
					echo "<th>Hotel Group</th>";
					echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
                                        echo "<td>" . $row['Last Name'] . "</td>";
                                        echo "<td>" . $row['First Name'] . "</td>";
                                        echo "<td>" . $row['IRS'] . "</td>";
					echo "<td>" . $row['Position'] . "</td>";
                                        echo "<td>" . $row['Hotel_Name'] . "</td>";
					echo "<td>" . $row['Hotel_Group_Name'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='readEmployee.php?irs=". $row['IRS'] . '&Work_ID=' . $row['Work_ID']."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                            echo "<a href='updateEmployee.php?irs=". $row['IRS'] . '&Work_ID=' . $row['Work_ID']."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='deleteEmployee.php?irs=". $row['IRS'] . '&Work_ID=' . $row['Work_ID']."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            $result->free();
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . $conn->error;
                    }
                    
                    // Close connection
                    $conn->close();
                    ?>
                </div>
            </div>        
        </div>
    </div>
    </div>


    <div id="Customers" class="w3-container w3-padding-16 myLink">
	<div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left"><font color="white"><b>Customers Details</b></font></h2>
                        <a href="createCustomer.php" class="btn btn-success pull-right w3-red" > <i class="fa fa-plus-circle"></i> Add New Customer</a>
                    </div>
                    <?php
                    // Include config file
                    require 'config.php';
                    
                    // Attempt select query execution
                    $sql = "SELECT * FROM Customer";
                    if($result = $conn->query($sql)){
                        if($result->num_rows > 0){
                            echo "<input type='text' id='myInput4'  onkeyup='tabsfilterFunc(0,id,4)' placeholder='Search for last names..' title='Type in a name'>";
			    echo "<strong> <font color='white'> OR  </font></strong>";
			    echo '<input type="text" id="myInput4.2"  onkeyup="tabsfilterFunc(2,id,4)" placeholder="Search for IRS.." title="Type in an IRS">';
                            echo "<table id='myTable4' class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";                                        
                                        echo "<th>Last Name</th>";
                                        echo "<th>First Name</th>";
					echo "<th>IRS Number</th>";
                                        echo "<th>SSN</th>";
                                        echo "<th>First Registration</th>";
					echo "<th>Address</th>";
					echo "<th>Action</th>";
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
                                            echo "<a href='readCustomer.php?id=". $row['IRS_Number_C'] ."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                            echo "<a href='updateCustomer.php?id=". $row['IRS_Number_C'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='deleteCustomer.php?id=". $row['IRS_Number_C'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            $result->free();
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . $conn->error;
                    }
                    
                    // Close connection
                    $conn->close();
                    ?>
                </div>
            </div>        
        </div>
    </div>
    </div>

 <div id="Reserves" class="w3-container w3-padding-16 myLink">
	<div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left"><font color="white"><b>Reserves Details</b></font></h2>
                        <a href="createReserve.php" class="btn btn-success pull-right w3-red" > <i class="fa fa-plus-circle"></i> Add New Reserve</a>
                    </div>
                    <?php
                    // Include config file
                    require 'config.php';
                    
                    // Attempt select query execution
                    $sql = "SELECT * FROM ResView";
                    if($result = $conn->query($sql)){
                        if($result->num_rows > 0){
                            echo "<input type='text' id='myInput5'  onkeyup='tabsfilterFunc(0,id,5)' placeholder='Search for Reserves IDs..' title='Type in a Reserve ID'>";
							echo "<strong> <font color='white'> OR  </font></strong>";
							echo '<input type="text" id="myInput5.2"  onkeyup="tabsfilterFunc(5,id,5)" placeholder="Search for Customers Last Name.." title="Type in an IRS">';
                            echo "<table id='myTable5' class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";                                        
                                        echo "<th>Reserve ID</th>";
                                        echo "<th>Start Date</th>";
					echo "<th>Finish Date</th>";
                                        echo "<th>Paid</th>";
                                        echo "<th>Customer IRS</th>";
					echo "<th>Last Name</th>";
					echo "<th>First Name</th>";
					echo "<th>Room ID</th>";
					echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
                                        echo "<td>" . $row['Reserves_id'] . "</td>";
                                        echo "<td>" . $row['Start_Date'] . "</td>";
                                        echo "<td>" . $row['Finish_Date'] . "</td>";
					echo "<td>" . $row['Paid'] . "</td>";
                                        echo "<td>" . $row['C_IRS'] . "</td>";
					echo "<td>" . $row['CLast_Name'] . "</td>";
					echo "<td>" . $row['CFirst_Name'] . "</td>";					
					echo "<td>" . $row['Room_ID'] .  "</td>"; 
                                        echo "<td>";
					echo "<a href='updateReserve.php?res_id=". $row['Reserves_id'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='deleteReserve.php?res_id=". $row['Reserves_id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            $result->free();
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . $conn->error;
                    }
                    
                    // Close connection
                    $conn->close();
                    ?>
                </div>
            </div>        
        </div>
    </div>
    </div>
    
    <div id="Rents" class="w3-container w3-padding-16 myLink">
	<div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left"><font color="white"><b>Rents Details</b></font></h2>
                        <a href="createRent.php" class="btn btn-success pull-right w3-red" > <i class="fa fa-plus-circle"></i> Add New Rent</a>
                    </div>
                    <?php
                    // Include config file
                    require 'config.php';
                    
                    // Attempt select query execution
                    $sql = "SELECT * FROM RentsView";
                    if($result = $conn->query($sql)){
                        if($result->num_rows > 0){
                            echo "<input type='text' id='myInput6'  onkeyup='tabsfilterFunc(0,id,6)' placeholder='Search for Rents IDs..' title='Type in a Rent ID'>";
							echo "<strong> <font color='white'> OR  </font></strong>";
							echo '<input type="text" id="myInput6.2"  onkeyup="tabsfilterFunc(8,id,6)" placeholder="Search for Customers Last Name.." title="Type in an IRS">';
							echo "<strong> <font color='white'> OR  </font></strong>";
							echo '<input type="text" id="myInput6.3"  onkeyup="tabsfilterFunc(5,id,6)" placeholder="Search for Employees Last Name.." title="Type in an IRS">';
                            echo "<table id='myTable6' class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";                                        
                                        echo "<th>Rent ID</th>";
                                        echo "<th>Start Date</th>";
										echo "<th>Finish Date</th>";
                                        echo "<th>Employee IRS</th>";
                                        echo "<th>Employee First Name</th>";
                                        echo "<th>Employee Last Name</th>";
                                        echo "<th>Customer IRS</th>";
                                        echo "<th>Customer First Name</th>";
                                        echo "<th>Customer Last Name</th>";
										echo "<th>Room ID</th>";
										echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
                                        echo "<td>" . $row['Rents_id'] . "</td>";
                                        echo "<td>" . $row['Start_Date'] . "</td>";
                                        echo "<td>" . $row['Finish_Date'] . "</td>";
										echo "<td>" . $row['E_IRS'] . "</td>";
										if ($row['E_IRS'] != NULL){
											$sql_emp = "SELECT ELast_Name, EFirst_Name FROM Employee WHERE IRS_Number_E = ?";
											$stmt_emp = $conn->prepare($sql_emp);
											$stmt_emp->bind_param( "i", $row['E_IRS']);
											$stmt_emp->execute();
											$result_emp = $stmt_emp->get_result();
											$row_emp = $result_emp->fetch_assoc();
											echo "<td>" . $row_emp['EFirst_Name'] . "</td>";
											echo "<td>" . $row_emp['ELast_Name'] . "</td>";
										}
										else{
											echo "<td> DELETED </td>";
											echo "<td> DELETED </td>";
										}
                                        echo "<td>" . $row['C_IRS'] . "</td>";
                                        echo "<td>" . $row['CFirst_Name'] . "</td>";
                                        echo "<td>" . $row['CLast_Name'] . "</td>";
										echo "<td>" . $row['Room_ID'] .  "</td>"; 
                                        echo "<td>";
											echo "<a href='updateRent.php?rent_id=". $row['Rents_id'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='deleteRent.php?rent_id=". $row['Rents_id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            $result->free();
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . $conn->error;
                    }
                    
                    // Close connection
                    $conn->close();
                    ?>
                </div>
            </div>        
        </div>
    </div>
    </div>


<div id="Accs" class="w3-container w3-white  w3-padding-16 myLink">
	<div class="wrapper" style="text-align:center">
        <div class="container-fluid" style="width:80%">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Manage Administrator Accounts</h1>
                    </div>
                    <div class="w3-row-padding" style="margin:8 -16px;">
                    <div class="w3-half">
                    <h2>Create Account</h2>
                    <form method="post" action="manageAccs.php">
                        <div class="form-group <?php echo (!empty($manage_err[0])) ? 'has-error' : ''; ?>">
                            <label>Type username</label><br>
                            <input type="text" name="username" class="form-control" value="<?php echo $old_inp[0]; ?>">
                            <span class="help-block"><?php echo $manage_err[0];?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($manage_err[1])) ? 'has-error' : ''; ?>">
                            <label>Type email</label><br>
                            <input type="text" name="email" class="form-control" value="<?php echo $old_inp[1]; ?>">
                            <span class="help-block"><?php echo $manage_err[1];?></span>
                        </div>
						<div class="form-group <?php echo (!empty($manage_err[2])) ? 'has-error' : ''; ?>">
                            <label>Type password</label><br>
                            <input type="password" name="password" class="form-control">
                            <span class="help-block"><?php echo $manage_err[2];?></span>
                        </div>
                        <div style="padding-bottom:10px" class="form-group <?php echo (!empty($manage_err[3])) ? 'has-error' : ''; ?>">
                            <label>Type password again</label> <br>
                            <input type="password" name="password2" class="form-control">
                            <span class="help-block"><?php echo $manage_err[3];?></span>
                        </div>
                        <input type="hidden" name="action" value="Create">
                        <input type="submit" class="btn btn-primary w3-green" value="Create Account">
                    </form>
                    </div>
                    <div class="w3-half">
                    <h2>Change password</h2>
                    <form method="post" action="manageAccs.php">
                        <div class="form-group <?php echo (!empty($manage_err[4])) ? 'has-error' : ''; ?>">
                            <label>Type username</label><br>
                            <input type="text" name="username" class="form-control" value="<?php echo $old_inp[4]; ?>">
                            <span class="help-block"><?php echo $manage_err[4];?></span>
                        </div>
						<div class="form-group <?php echo (!empty($manage_err[5])) ? 'has-error' : ''; ?>">
                            <label>Type old password</label><br>
                            <input type="password" name="old_password" class="form-control">
                            <span class="help-block"><?php echo $manage_err[5];?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($manage_err[6])) ? 'has-error' : ''; ?>">
                            <label>Type new password</label><br>
                            <input type="password" name="new_password" class="form-control">
                            <span class="help-block"><?php echo $manage_err[6];?></span>
                        </div>
                        <div style="padding-bottom:10px" class="form-group <?php echo (!empty($manage_err[7])) ? 'has-error' : ''; ?>">
                            <label>Type new password again</label> <br>
                            <input type="password" name="new_password2" class="form-control">
                            <span class="help-block"><?php echo $manage_err[7];?></span>
                        </div>
                        <input type="hidden" name="action" value="ChangePass">
                        <input type="submit" class="btn btn-primary" value="Change Password">
                    </form>
                    </div>
                    </div>
                    <div class="w3-row-padding" style="margin:8 -16px;">
                    <div class="w3-half" style="padding-top:8%">
                    <h2>Delete Account</h2>
                    <form method="post" action="manageAccs.php">
                        <div class="form-group <?php echo (!empty($manage_err[8])) ? 'has-error' : ''; ?>">
                            <label>Type username</label><br>
                            <input type="text" name="username" class="form-control" value="<?php echo $old_inp[8]; ?>">
                            <span class="help-block"><?php echo $manage_err[8];?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($manage_err[9])) ? 'has-error' : ''; ?>">
                            <label>Type email</label><br>
                            <input type="text" name="email" class="form-control" value="<?php echo $old_inp[9]; ?>">
                            <span class="help-block"><?php echo $manage_err[9];?></span>
                        </div>
						<div style="padding-bottom:10px" class="form-group <?php echo (!empty($manage_err[10])) ? 'has-error' : ''; ?>">
                            <label>Type password</label><br>
                            <input type="password" name="password" class="form-control">
                            <span class="help-block"><?php echo $manage_err[10];?></span>
                        </div>
                        <input type="hidden" name="action" value="Delete">
                        <input type="submit" class="btn btn-primary w3-red" value="Delete Account">
                    </form>
                    </div>
                    <div class="w3-half" style="padding-top:8%">
                    <h2>Change email</h2>
                    <form method="post" action="manageAccs.php">
                        <div class="form-group <?php echo (!empty($manage_err[11])) ? 'has-error' : ''; ?>">
                            <label>Type username</label><br>
                            <input type="text" name="username" class="form-control" value="<?php echo $old_inp[11]; ?>">
                            <span class="help-block"><?php echo $manage_err[11];?></span>
                        </div>
						<div class="form-group <?php echo (!empty($manage_err[12])) ? 'has-error' : ''; ?>">
                            <label>Type password</label><br>
                            <input type="password" name="password" class="form-control">
                            <span class="help-block"><?php echo $manage_err[12];?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($manage_err[13])) ? 'has-error' : ''; ?>">
                            <label>Type old email</label><br>
                            <input type="text" name="old_email" class="form-control" value="<?php echo $old_inp[13]; ?>">
                            <span class="help-block"><?php echo $manage_err[13];?></span>
                        </div>
                        <div style="padding-bottom:10px" class="form-group <?php echo (!empty($manage_err[14])) ? 'has-error' : ''; ?>">
                            <label>Type new email</label> <br>
                            <input type="text" name="new_email" class="form-control" value="<?php echo $old_inp[14]; ?>">
                            <span class="help-block"><?php echo $manage_err[14];?></span>
                            <?php unset($_SESSION["manage_errors"]); ?>
                            <?php unset($_SESSION["inputs"]); ?>
                        </div>
                        <input type="hidden" name="action" value="ChangeEm">
                        <input type="submit" class="btn btn-primary" value="Change email">
                    	</form>
                    	<br>
                   	 	</div>
                    	</div>
					</div>
				</div>        
			</div>
		</div>
	</div>
  </body>
 

<script type="text/javascript" src="scripts/pageAdmin.js"></script>


</html>
