<!DOCTYPE html>
<html>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">


<head>
<link rel="stylesheet" href="styles/bgstyle.css">
<link rel="stylesheet" href="styles/BRstyle.css">
</head>

<body >

<div class="header">
  <h><img src="images/logo.png" alt="calligraphy-fonts" border="0"></h>
</div>
<?php
session_start();
$_SESSION["checked_rooms"] = $_POST["checked_rooms"];
?>
<button class="button" type="reset" onclick="location.href='BookAndCreateUser.php'">
   	This is my first booking at this site
</button>
<button class ="button"  onclick="location.href='BookAndExistsUser.php'">I have booked a room before</button>




</body>


 


</html>
