<?php
include_once ("../../db_info.php");

if(isset($_GET["book_tag"]))
	$book_tag = $_GET['book_tag'];
else $book_tag = "*";

if(isset($_GET["call_number"]))
	$call_number = $_GET['call_number'];
else $call_number = "*";

if(isset($_GET["neighbor_tag"]))
	$neighbor_tag = $_GET['neighbor_tag'];
else $neighbor_tag = "*";

if(isset($_GET["neighbor_call"]))
	$neighbor_call = $_GET['neighbor_call'];
else $neighbor_call = "*";

if(isset($_GET["start_date"]))
	$start_date = $_GET['start_date'];
else $start_date = "*";

if(isset($_GET["end_date"]))
	$end_date = $_GET['end_date'];
else $end_date = "*";

$count = -1;
$where = "";

	// Create a new mysqli object with database connection parameters
	$con = new mysqli($server, $user, $password, $database);

	if(mysqli_connect_errno()) {
		echo "Connection Failed: " . mysqli_connect_errno();
		exit();
	}
	// Create a prepared statement
		if($stmt = $con -> prepare("SELECT * FROM book_pings WHERE book_tag = 1 AND book_call = 2 AND neighbor_tag = 3 AND neighbor_call = 4 AND start_date = 5 AND end_date = 6")) {

			// Bind parameters
			 //s - string, b - blob, i - int, etc
			$stmt -> bind_param(1, $book_tag);
			$stmt -> bind_param(2, $book_call);
			$stmt -> bind_param(3, $neighbor_tag);
			$stmt -> bind_param(4, $neighbor_call);
			$stmt -> bind_param(5, $start_date);
			$stmt -> bind_param(6, $end_date);

			//Execute it
			$stmt -> execute();

			// Bind results
			$stmt -> bind_result($result);

			// Fetch the value
			$stmt -> fetch();

			// Close statement
			$stmt -> close();
		}

/*
	if($result == FALSE){
		Print "FAILED 1";
		//Print 'SQL Select failed' . mysqli_error();
	} else if(mysqli_num_rows($result) == 0) {
		Print "NO ROWS";
		//Print 'No rows seleted';
	} else {
		//Print $resource;
		while ($row = mysqli_fetch_assoc($result)) {
			$arr[] = $row;
		}
		$count = count($arr);
		// Close connection
		$con -> close();
		return $count;
	}
	/*
?>