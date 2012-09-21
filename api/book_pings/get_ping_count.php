<?php

/**
 * @file
 * Get count of number of book_pings since a particular date
 *
 * Copyright 2011 by ShelvAR Team.
 *
 * @version September 17, 2012
 * @author Bo Brinkman and Raavian Rehman
 */

/**
 * Get number of book pings since a certain date
 *
 * @param[in] $date
 *   The date at which we want to start counting. This should be a datetime in "0000-00-00 00:00:00" format.
 *   The function will count ping times that are >= the specified date.
 *
 * @return
 *   An integer, which is the number of book pings since the given date.
 *
 */
$ret; //!< return value from function call that does most of the work
$ret = get_ping_count_since(stripslashes($_GET["date"]));
Print $ret;

function get_ping_count_since($date){
	include_once "../../db_info.php";
	$query_or_not = true;
	$date_formatted = strtotime("Y-m-d H:i:s",$date);
	/* Create a new mysqli object with database connection parameters */
	$con = new mysqli($server, $user, $password, $database);

	if(mysqli_connect_errno()) {
		Print "Connection Failed: " . mysqli_connect_errno();
	}

	if ($date_formatted === false){
		Print "Bad start_date.";
		$query_or_not = false;
	}
	/* Create a prepared statement */
	if($stmt = $con -> prepare("SELECT COUNT(*) as TOTALFOUND FROM book_pings WHERE ping_time>=?")) {

		/* Bind parameters
		 s - string, b - blob, i - int, etc */
		$stmt -> bind_param("s", $date_formatted);

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
		Print '<pre>SQL select failed' . mysqli_error();
		Print '<br />';
		/* Close connection */
		$con -> close();
	} else {
		$row = 0;
		$field = "TOTALFOUND";
		$result->data_seek($row);
		$datarow = $res->fetch_array();
    	/* Close connection */
   		$con -> close();
    	return $datarow[$field]; 
	} 
}
?>