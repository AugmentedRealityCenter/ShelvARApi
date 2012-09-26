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
	echo "book_tag is " . $book_tag . " and book call is " . $book_call;
	// Create a prepared statement
		if($stmt = $con -> prepare("SELECT * FROM book_pings WHERE book_tag=? AND book_call=?")) {
			// Bind parameters
			 //s - string, b - blob, i - int, etc
			$stmt -> bind_param('ss', $book_tag, $book_call);

			//Execute it
			$stmt -> execute();

			// Bind results
			$stmt -> bind_result($result);

			// Fetch the value
			$stmt -> fetch();

			// Close statement
			$stmt -> close();
		}


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
	
?>