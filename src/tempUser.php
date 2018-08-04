<?php 
require 'config.php';
session_start();
$IRS=$_GET["IRS"];


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
						$param_paid =trim( $_POST["Paid"]);
						$param_room_id = $id;
						$param_irs = $IRS;
						$stmt->execute();
						$last_id[$i]=$conn->insert_id;
						$i=$i+1;
				}

				$sql = "SELECT * FROM Customer WHERE IRS_Number_C = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("i", $IRS);
				$stmt->execute();
				$user_info = $stmt->get_result();
				$row = $user_info->fetch_assoc();
				$_SESSION["First_Name"]= $row["CFirst_Name"];
				$_SESSION["Last_Name"]= $row["CLast_Name"];
				$_SESSION["IRS"]= $IRS;
				$_SESSION["SSN"]= $row["CSocial_Security_Number"];
				$_SESSION["checked_reservations"]=$last_id;		 
				$_SESSION["Pay"]=$param_paid;		
              		header("location:bookinfoUser.php");
			exit();

			


?>

