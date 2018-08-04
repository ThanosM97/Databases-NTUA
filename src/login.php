
<?php
include("loginconf.php");

if($_SERVER["REQUEST_METHOD"] == "POST") {
      
      $myusername = $_POST['uname'];
      $mypassword = $_POST['psw'];
      
      $sql = "SELECT * FROM admin WHERE username = '$myusername' and password = '$mypassword'";
      $result = mysqli_query($conn,$sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $active = $row['active'];
      
      $count = mysqli_num_rows($result);
      
      // If result matched $myusername and $mypassword, table row must be 1 row
		
      if($count == 1) {
         header("location: pageAdmin.php");
      }else {
	 header("location: loginerror.php");
      }
}
?>




