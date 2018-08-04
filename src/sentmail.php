<?php
require 'loginconf.php';

$myusername = $_POST["username"];
$mymail = $_POST["email"];

$sql = "SELECT password FROM admin WHERE username = '$myusername' and email = '$myemail'";
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
$active = $row['active'];


$msg = "You requested a password reminder for HotelFinder. \n Your password is: '".$row['email']."' "
$msg = wordwrap($msg,70);

mail($myemail, "Password Reminder", $msg);

header("location: starting_page.php");

?>
