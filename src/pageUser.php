<?php
		session_unset();
		session_destroy();
		session_start();
		// Include config file
		require 'config.php';
		// Define variables and initialize with empty values
		$check_in = $check_out = $location = "";
		$adults = 1;
		$kids = 0;
		if($_SERVER["REQUEST_METHOD"] == "POST"){
			$adults = $_POST["NofAdults"];
			$kids = $_POST["NofKids"];
			$location = $_POST["Location"];
			$input_check_in = trim($_POST["CheckIn"]);
			$input_check_out = trim($_POST["CheckOut"]);
			$number_of_rooms = trim($_POST["Rooms"]);
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
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css" type="text/css" media="all" /> 
<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.5.min.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">


<head>
<link rel="stylesheet" type="text/css" href="styles/style.css">
</head>

<body>

<div class="header">
  <h><img src="images/logo.png" alt="calligraphy-fonts" border="0"></h>
</div>


<div class="topnav" id="navbar">
	<div class="btn-group">
    <button class="button tablink enabled" style="cursor:pointer" href="javascript:void(0)" onclick="openLink(event, 'Search Hotel');"> Search Hotel</button>
    <button class="button tablink enabled" href="javascript:void(0)"onclick="openLink(event, 'Hotel Groups'); FilterFunc(0); ">Hotel Groups</button>
    <button class="button tablink enabled" href="javascript:void(0)"onclick="openLink(event, 'Hotels'); FilterFunc(0); ">Hotels</button>
    </div>
   <div class="btn-group" style="float:right">
    <button class="button tablink enabled" onclick="openLink(event, 'RoomsByLocation');FilterFunc(0);" href="javascript:void(0)">Rooms by Location</button>
    <button class="button tablink enabled" onclick="openLink(event, 'RoomsByCapacity');FilterFunc(0);" href="javascript:void(0)">Rooms by Capacity</button>
  </div>
</div>

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
                <input name="CheckIn" min=<?php echo date('Y-m-d');?> class="w3-input w3-border" type="date"  required  value="<?php echo $check_in;?>">
                <span class="help-block"><?php echo $check_in_err;?></span>
              </div>
           
              <div  id="Check-out"class="w3-half form-group<?php echo (!empty($check_out_err)) ? 'has-error' : ''; ?>">
                <label><i class="fa fa-calendar-o" style="color:white"></i><font color="white"> Check Out (*)</font> </label>
                <input name="CheckOut"min=<?php echo date('Y-m-d');?> class="w3-input w3-border" type="date"  required  value="<?php echo $check_out;?>">
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
					AND (`T`.`City` = '$location'))";
					
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
							OR ('$check_in' BETWEEN `Rents`.`RStart_Date` AND `Rents`.`RFinish_Date`))))))";
				}
				if(!$conn->query($sql)){ 
				header("location: errorUser.php");
				exit(); 
			}
			$sql = "SELECT * FROM TempView WHERE(Expendable + Capacity)>= ?  ";
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
										<form action="bookroomUser.php"  method="post">
										<button id = "book-btn"  type="submit" ><span class='glyphicon glyphicon-book'></span> Book now !</button>
										
									</div>
						<?php	echo "<table id='myTable1' class='table table-bordered table-striped'>";
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
							header("location: errorUser.php");
							exit();
						}
						} else{
							// URL doesn't contain valid id. Redirect to error page
							header("location: errorUser.php");
							exit();
							} 
						
					}
				}
			}
			
		
     ?>      
      </form>
   </div>


    

    <div id="Hotel Groups" class="w3-padding-16 myLink">
	<div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left"><font color="white"><strong>Hotel Groups</strong></font></h2>
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
                                        echo "<th>Number of Hotels</th>";
					echo "<th>Address</th>";
                                        echo "<th>Contact info</th>";
					echo "<th>Hotels info</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
                                        echo "<td>" . $row['Hotel_Group_Name'] . "</td>";
                                        echo "<td>" . $row['Number_of_hotels'] . "</td>";
                                        echo "<td>" ; 
										echo "<p>" . $row['HGStreet'] . " " . $row['HGSt_Number'] . ", <br>" . $row['HGPostal_Code'] . "</p>" ;  
										echo "</td>"; 
										echo "<td>";
										echo "<a href='readHotelGroupUser.php?id=". $row['Hotel_Group_ID'] ."' title='View Info' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
										echo "</td>";
										echo "<td>";
										echo "<a href='readHotelGroupUser2.php?id=". $row['Hotel_Group_ID'] ."' title='View Info' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
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

 <div id="Hotels" class="w3-container w3-padding-16 myLink">
      <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left"><font color="white"><b>Hotels Details</b></font></h2>
                    </div>
                    <?php
                    // Include config file
                    require 'config.php';
                    
                    // Attempt select query execution  
                    $sql = "SELECT * FROM HotelsView";  
                    if($result = $conn->query($sql)){
                        if($result->num_rows > 0){
				$tab="myTable2";
			    echo '<input type="text" id="hname_inp"  onkeyup="Filter2(2)" placeholder="Search for hotel names.." title="Type in a name">';
                            echo "<table id='Hotelstable' class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";                                        
                                        echo "<th>Name</th>";
					echo "<th>Hotel Group</th>";
                                        echo "<th>Address</th>";
					echo "<th>Stars</th>";
                                        echo "<th>Number of Rooms</th>";
					echo "<th>Contact Us</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
                                        echo "<td>" . $row['Hotel_Name'] . "</td>";
					echo "<td>" . $row['Hotel_Group_Name'] . "</td>";
                                        echo "<td>" ; 
						echo "<p>" . $row['HCity'] . ", " . $row['HStreet'] . " " . $row['HSt_Number'] . ", <br>" . $row['HPostal_Code'] . "</p>" ;  
					echo "</td>"; 
					echo "<td>" . $row['Stars'] . "</td>";
                                        echo "<td>" . $row['Number_of_rooms'] . "</td>";
                                        echo "<td>"; echo "<a href='readHotelUser.php?Group_ID=". $row['Hotel_Group_ID'] . '&Hotel_ID=' . $row['Hotel_ID']."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
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


<div id="RoomsByLocation" class=" w3-padding-16 myLink" >
      <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left"><font color="white"><strong>Rooms Details</strong></font></h2>
                        <select  id="city_inp" onchange="Filter2(0);" name="Location" class="w3-input w3-border w3-white" type="text" placeholder="Type the location"></div>
					  <option value="">Select Location...</option>
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
                    <?php
                    // Include config file
                    require 'config.php';
                    
                    // Attempt select query execution
                    $sql = "SELECT * FROM RoomsView";
                    if($result = $conn->query($sql)){
                        if($result->num_rows > 0){
                            echo "<table id='RoomsByLocation' class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";                                        
                                        echo "<th>Hotel</th>";
					echo "<th>Price</th>";
                                        echo "<th>Capacity</th>";
					echo "<th>View</th>";
                                        echo "<th>Expendability</th>";
                                        echo "<th>Needed Repairs</th>";
					echo "<th>City</th>";
					echo "<th>Amenities</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
					$sql_amenities = "SELECT Amenities FROM Hotel_room_Amenities WHERE Hotel_room_Room_ID= ? ";
					$stmt_amenities = $conn->prepare($sql_amenities);
					$stmt_amenities->bind_param( "i", $row['Room_ID'] );
					$stmt_amenities->execute();
					$available_amenities = $stmt_amenities->get_result();
                                        echo "<td>" . $row['Hotel'] . "</td>";
                                        echo "<td>" . $row['Price'] . "</td>";
					echo "<td>" . $row['Capacity'] . "</td>";
                                        echo "<td>" . $row['RView'] . "</td>";
					echo "<td>" . $row['Expendable'] . "</td>";
                                        echo "<td>" . $row['Repairs'] . "</td>";
					echo "<td>" . $row['City']. "</td>";
 					echo "<td>";
					echo "<ul class = 'two-col-special' >";
					while ($row_amenities = $available_amenities->fetch_assoc()){
						echo "<li>" . $row_amenities['Amenities'] . "</li>";
					}
					echo "</ul>";
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


<div id="RoomsByCapacity" class="w3-padding-16 myLink" >
      <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left"><font color="white"><strong>Rooms Details</strong></font></h2>
                        <select  id="capacity_inp" onchange="Filter2(1);" name="Capacity" class="w3-input w3-border w3-white" type="text" ></div>
					  <option value="">Select Capacity...</option>
					<?php
					require 'config.php';
					$sql = "SELECT DISTINCT Capacity FROM Hotel_room ORDER BY Capacity";
					if ($result = $conn->query($sql)){
						if($result->num_rows > 0){
							while($row = $result->fetch_array()){
								if(($_SERVER["REQUEST_METHOD"] == "POST") AND ($row['Capacity'] == $location)){
									?><option selected value=<?php echo $row['Capacity'];?> > <?php echo $row['Capacity']; ?></option>
								<?php }
								else{
									 ?><option value=<?php echo $row['Capacity'];?> > <?php echo $row['Capacity']; ?></option>
								<?php }
							}
						}else{ 
							?> <option value="">No capacities found</option> <?php
						}
					} else {
							?> <option value="">Error</option>; <?php
					}
					// Close connection
                    $conn->close();
					?> 
						
					</select>
                    </div>
                    <?php
                    // Include config file
                    require 'config.php';
                    
                    // Attempt select query execution
                    $sql = "SELECT * FROM RoomsView";
                    if($result = $conn->query($sql)){
                        if($result->num_rows > 0){
                            echo "<table id='RoomsByCapacity' class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";                                        
                                        echo "<th>Hotel</th>";
					echo "<th>Price</th>";
                                        echo "<th>Capacity</th>";
					echo "<th>View</th>";
                                        echo "<th>Expendability</th>";
                                        echo "<th>Needed Repairs</th>";
					echo "<th>City</th>";
					echo "<th>Amenities</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
					$sql_amenities = "SELECT Amenities FROM Hotel_room_Amenities WHERE Hotel_room_Room_ID= ? ";
					$stmt_amenities = $conn->prepare($sql_amenities);
					$stmt_amenities->bind_param( "i", $row['Room_ID'] );
					$stmt_amenities->execute();
					$available_amenities = $stmt_amenities->get_result();
                                        echo "<td>" . $row['Hotel'] . "</td>";
                                        echo "<td>" . $row['Price'] . "</td>";
					echo "<td>" . $row['Capacity'] . "</td>";
                                        echo "<td>" . $row['RView'] . "</td>";
					echo "<td>" . $row['Expendable'] . "</td>";
                                        echo "<td>" . $row['Repairs'] . "</td>";
					echo "<td>" . $row['City']. "</td>";
 					echo "<td>";
					echo "<ul class = 'two-col-special' >";
					while ($row_amenities = $available_amenities->fetch_assoc()){
						echo "<li>" . $row_amenities['Amenities'] . "</li>";
					}
					echo "</ul>";
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
  
  <script type="text/javascript" src="scripts/pageUser.js"></script>

  </body>
</html>
