<?php 
	session_start(); 
	require 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet"  href="styles/bgstyle.css"> 
<link rel="stylesheet"  href="styles/BIstyle.css">
</head>
<body>
<button class="button button1 " onclick="window.print();return false;"><i class="fa fa-print"></i> Print </button>
<a class="button" href="pageUser.php?filter=0"><i class="fa fa-back"></i> Home Page </a>
<div class="coupon">
  <div class="container">
    <h1 align="center">
    	<img src="images/logo.png" alt="calligraphy-fonts" border="0">
    </h1>
  </div>
  <div class="container" style="background-color:white">
    <h2 align="center"><b>You have successfully made a booking</b></h2> 
    <h3><u> Booking Informations </u></h3>
    <h4> The reservation ids for your room/rooms:
	    <?php 
				$reservations = array();
				$reservations = $_SESSION["checked_reservations"];
				$rooms = array();
				$rooms = $_SESSION["checked_rooms"];
				$i=1;
				$format = 'Y-m-d';
				$f_date = DateTime::createFromFormat($format, $_SESSION["Finish_Date"]);
				$s_date = DateTime::createFromFormat($format, $_SESSION["Start_Date"]);
				$number_of_days = date_diff($s_date, $f_date)->format("%a days");
				$total_cost=0;
				foreach ($reservations as &$id) {
					$temp=$rooms[$i-1];
					$sql = "SELECT * FROM RoomsView WHERE Room_ID= '$temp'";
					$result = $conn->query($sql);
					$row=$result->fetch_assoc();
					echo "<h4>Reservation id #$i: ". $id ; echo " at hotel " .$row["Hotel"]."</h4>";
					$total_cost=$total_cost + $number_of_days * $row["Price"];
					echo "<font color='blue'>Total cost for $number_of_days is: " .$row["Price"]."*".$number_of_days."= ".$row["Price"]*$number_of_days." &euro; </font>";
					$i=$i+1;
	} 
    ?>
    <p> The above room/rooms have been booked <br>for the following dates: <?php echo $_SESSION["Start_Date"]; echo "   until   "; echo $_SESSION["Finish_Date"]; ?> <p>
    <h3> <u> Customer's credentials </u> </h3>
    <p> <b>First Name:</b> <?php echo $_SESSION["First_Name"]; ?> <br>
	<b>Last Name:</b> <?php echo $_SESSION["Last_Name"]; ?> <br>
	<b>IRS Number:</b> <?php echo $_SESSION["IRS"]; ?> <br>
	<b>SSN Number:</b> <?php echo $_SESSION["SSN"]; ?></p>

    <p><font color="red" size='6'><u><b>Total cost is: <?php echo $total_cost; ?> &euro; </b></u></font></p>

    <?php if ($_SESSION["Pay"] == 'Yes') { echo "<p> You chose to pay in advance. <br> Please make a deposit at the following account. <br>
						IBAN: *********** ";} ?>

    

  </div>
  <div class="container">
    <p class="expire"><b>Keep this booking confirmation until your arrival at the hotel</b></p>
    <p class="expire"><b>If there is any problem, you can find us <a href="pageUser.php?filter=2"><font color="red"><u>here</u></font><a> </p>
  </div>
</div>



</body>
</html> 

