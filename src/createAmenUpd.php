<!DOCTYPE html>
<?php

require 'config.php';
if($_SERVER["REQUEST_METHOD"] == "POST"){
$room_id = $_POST["roomid"];
$next=1;

	while(isset($_POST["amenities".$next])){
		$sql = "INSERT INTO Hotel_room_Amenities (Amenities, Hotel_room_Room_ID) VALUES (?,?)";
		$stmt = $conn->prepare($sql);
		// Bind variables to the prepared statement as parameters
		$stmt->bind_param("si",$param_amenities, $room_id);
		$param_amenities = trim($_POST["amenities".$next]);
		$next = $next +1;
		if (!$stmt->execute()){
			header("location: error.php");
			exit();
		}
	}
header("location: updateRoom.php?id=$room_id");
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
        var addto = "#amenities" + next;
        var addRemove = "#amenities" + (next);
        next = next + 1;
        var newIn = '<label class="control-label" for="amenities1">Amenity ' + next +'</label> <input autocomplete="off" class="input form-control" id="amenities' + next + '" name="amenities' + next + '" type="text" placeholder="Ex. (TV,AC,...)">';
        var newInput = $(newIn);
        var removeBtn = '<button id="remove' + (next - 1) + '" class="btn btn-danger remove-me" ><span class="glyphicon glyphicon-remove-sign"></span></button></div><div id="amenities">';
        //var removeButton = $(removeBtn);
        $(addto).after(newInput);
        $(addRemove).after(removeButton);
        $("#amenities" + next).attr('data-source',$(addto).attr('data-source'));
        $("#count1").val(next);  
        
            $('.remove-me').click(function(e){
                e.preventDefault();
                var fieldNum = this.id.charAt(this.id.length-1);
                var fieldID = "#amenities" + fieldNum;
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
                        <h2>Add amenities to room:<?php echo $_GET["id"]; ?> </h2>
                    </div>
                    <p>Please fill this form to add more amenities to the room with id: <?php echo $_GET["id"]; ?>.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        		<div class="form-group" id="amenities">
				<label >Amenity 1</label>
            			<input autocomplete="off" class="input form-control" id="amenities1"  name="amenities1" type="text" placeholder="Ex. (TV,AC,...)" /><button id="b1" class="btn add-more" type="button"><span class="glyphicon glyphicon-plus"></span></button>
          			<br>
           			<small>Press + to add another amenity</small>
       			</div>
                        <input type="submit" class="btn btn-primary" value="Submit">
			<input type="hidden" name="roomid" value="<?php echo $_GET["id"]; ?>" >
                        <a href="pageAdmin.php?filter=2" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
