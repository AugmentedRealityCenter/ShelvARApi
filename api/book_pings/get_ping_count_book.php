<?php
echo "hi";
include_once "../../db_info.php";
//comment
$book_tag = $_GET('book_tag');
$call_number = $_GET('call_number');
$neighbor_tag = $_GET('neighbor_tag');
$neighbor_call = $_GET('neighbor_call');
$start_date = $_GET('start_date');
$end_date = $_GET('end_date');
$count = -1;
$where = "";
if(isset($book_tag)) {
	$where = "book_tag = " . $book_tag;
}
if(isset($call_number)) {
	if($where != "") {
		$where += " AND book_call = " . $call_number;
	}
	else $where = "book_call = " . $call_number;
}

	// Create a new mysqli object with database connection parameters
	$con = new mysqli($server, $user, $password, $database);

	if(mysqli_connect_errno()) {
		echo "Connection Failed: " . mysqli_connect_errno();
		exit();
	}
	// Create a prepared statement
	if($where != "") {
		if($stmt = $con -> prepare("SELECT * FROM book_pings WHERE " . $where . "?")) {

			// Bind parameters
			 //s - string, b - blob, i - int, etc
			$stmt -> bind_param("s", $lcNum);

			//Execute it
			$stmt -> execute();

			// Bind results
			$stmt -> bind_result($result);

			// Fetch the value
			$stmt -> fetch();

			// Close statement
			$stmt -> close();
		}
	} /*else {
		if($stmt = $con -> prepare("SELECT * FROM book_pings LIMIT 20") {

			// Bind parameters
			 //s - string, b - blob, i - int, etc
			$stmt -> bind_param("s", $lcNum);

			//Execute it
			$stmt -> execute();

			// Bind results
			$stmt -> bind_result($result);

			// Fetch the value
			$stmt -> fetch();

			// Close statement
			$stmt -> close();
		}
	}*/

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
		$con -> close();*/
		//return $count;ds
	//}d
?>