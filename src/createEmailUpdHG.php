<!DOCTYPE html>
<?php

require 'config.php';
if($_SERVER["REQUEST_METHOD"] == "POST"){
$group_id = $_POST["group_id"];
$next=1;


	while(isset($_POST["email".$next])){
		$sql = "INSERT INTO Hotel_Group_Email_Address (	HGEmail, Hotel_Group_Hotel_Group_ID) VALUES (?,? )";
		$stmt = $conn->prepare($sql);
		// Bind variables to the prepared statement as parameters
		$stmt->bind_param("si",$param_email, $group_id );
		$param_email = trim($_POST["email".$next]);
		$next = $next +1;
		if (!$stmt->execute()){
			header("location: error.php");
			exit();
		}
	}
header("location: updateHotelGroup.php?id=$group_id");
exit(); 

}


?>

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
        var addto = "#email" + next;
        var addRemove = "#email" + (next);
        next = next + 1;
        var newIn = '<label class="control-label" for="email1">Email ' + next +'</label> <input autocomplete="off" class="input form-control" id="email' + next + '" name="email' + next + '" type="text" placeholder="Ex. example@example.com">';
        var newInput = $(newIn);
        var removeBtn = '<button id="remove' + (next - 1) + '" class="btn btn-danger remove-me" ><span class="glyphicon glyphicon-remove-sign"></span></button></div><div id="email">';
       // var removeButton = $(removeBtn);
        $(addto).after(newInput);
        $(addRemove).after(removeButton);
        $("#email" + next).attr('data-source',$(addto).attr('data-source'));
        $("#count1").val(next);  
        
            $('.remove-me').click(function(e){
                e.preventDefault();
                var fieldNum = this.id.charAt(this.id.length-1);
                var fieldID = "#email" + fieldNum;
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
                        <h2>Add emails to hotel group: Group ID =<?php echo $_GET["groupid"];?> .</h2>
                    </div>
                    <p>Please fill this form to add more emails to the hotel group with id: <?php echo $_GET["groupid"]; ?>.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        		<div class="form-group" id="email">
				<label >Email 1</label>
            			<input autocomplete="off" class="input form-control" id="email1"  name="email1" type="text" placeholder="Ex. example@example.com" /><button id="b1" class="btn add-more" type="button"><span class="glyphicon glyphicon-plus"></span></button>
          			<br>
           			<small>Press + to add another email</small>
       			</div>
                        <input type="submit" class="btn btn-primary" value="Submit">
			<input type="hidden" name="group_id" value=<?php echo $_GET["groupid"]; ?> >
                        <a href="pageAdmin.php?filter=1" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>

