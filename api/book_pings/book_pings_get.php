<?php

/**
 * @file
 * Find where the book was most recently
 *
 * Copyright 2011 by ShelvAR Team.
 *
 * @version September 30, 2012
 * @author Drew Ritcher
 */

//find_book("QA76.7 .A25 1991");
/**
 * Retrieves a list of the most recent book_pings. By default, returns the 20 most recent. 
 * Search can be limited using the optional parameters: Will return the 20 most recent pings 
 * that match all parameters.
 *
 * @return
 */
 
 $ret = find_book(stripslashes($_GET["book_ping_id"],$_GET["institution"]); 
  
  
function find_book($book_ping_id, $institution="")
{
	include_once "../../db_info.php";
	
	if(isset($_GET["book_ping_id"]))
		$book_ping_id = $_GET['book_ping_id'];
	
/**	if(isset($_GET["book_tag"]))
*		
*	if(isset($_GET["call_number"]))
*		$call_number = $_GET['call_number'];
*	
*	if(isset($_GET["start_date"]))
*		$start_date = $_GET['start_date'];
*		
*	if(isset($_GET["end_date"]))
*		$end_date = $_GET['end_date'];
*		
*	if(isset($_GET["num_limit"]))
*		$num_limit = $_GET['num_limit'];
*/	
	
	
	 
	/* Create a new mysqli object with database connection parameters */
	$con = new mysqli($server, $user, $password, $database);

	if(mysqli_connect_errno()) {
		echo "Connection Failed: " . mysqli_connect_errno();
		exit();
	}
	/* Create a prepared statement */
	if($stmt = $con -> prepare("SELECT * FROM book_pings WHERE id =" + $book_ping_id)) {

		/* Bind parameters
		 s - string, b - blob, i - int, etc */
		$stmt -> bind_param("ssssssss",
			$book_info["book_tag"],
			$book_info["book_call"],
			$book_info["neighbor1_tag"],
			$book_info["neighbor1_call"],
			$book_info["neighbor2_tag"],
			$book_info["neighbor2_call"],
			$book_info["ping_time"],
			$institution);

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
		$con -> close();
		return $return_arr;
	}
}

return $ret;
?>