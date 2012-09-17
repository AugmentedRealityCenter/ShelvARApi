<?php

/**
 * @file
 * Find where the book was most recently
 *
 * Copyright 2011 by ShelvAR Team.
 *
 * @version September 17, 2012
 * @author Eliot Fowler and Raavian Rehman
 */

//find_book("QA76.7 .A25 1991");
/**
 * Takes a string as input and (if connecting to the database is successful) queries the database
 * for all instances where that call number was seen. All of the tuples returned are stored in an
 * array. The last row in the array (the most recent ping) is returned as an associative array.
 *
 * @return An associative array with three values: last_time_seen, left_neighbor, right_neighbor.
 */
function find_book($lcNum)
{
	include_once "../../db_info.php";
	 
	/* Create a new mysqli object with database connection parameters */
	$con = new mysqli($server, $user, $password, $database);

	if(mysqli_connect_errno()) {
		echo "Connection Failed: " . mysqli_connect_errno();
		exit();
	}
	/* Create a prepared statement */
	if($stmt = $mysqli -> prepare("SELECT * FROM book_pings WHERE book_call =?")) {

		/* Bind parameters
		 s - string, b - blob, i - int, etc */
		$stmt -> bind_param("s", $lcNum);

		/* Execute it */
		$stmt -> execute();

		/* Bind results */
		$stmt -> bind_result($result);

		/* Fetch the value */
		$stmt -> fetch();

		/* Close statement */
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

		$return_arr["last_time_seen"] = $arr[count($arr)-1]["ping_time"];
		$return_arr["left_neighbor"] = $arr[count($arr)-1]["neighbor1_call"];
		$return_arr["right_neighbor"] = $arr[count($arr)-1]["neighbor2_call"];
		/* Close connection */
		$mysqli -> close();
		return $return_arr;
	}
}
?>