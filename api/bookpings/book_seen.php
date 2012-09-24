<?php

/**
 * @file
 * Get a list of the days on which this book was seen.
 *
 * Copyright 2011 by ShelvAR Team.
 *
 * @version September 17, 2012
 * @author Bo Brinkman and Raavian Rehman
 */

/**
 * Get a list of the dates when there was a shelf-read, and whether or not
 *    this particular book was seen on that day.
 *
 * @param $book_tag
 *    The base64 string for the book you are searching for.
 * @param $start_date
 *    Exclude all book pings before this date. "0000-00-00 00:00:00" format. If unset,
 *    returns all book pings that come on or before end_date
 * @param $end_date
 *    Exclude all book pings after this date. "0000-00-00 00:00:00" format. If unset,
 *    defaults to current day and time.
 *
 * @return
 *   An associative array of integers, keyed by date (no hours/minutes/seconds, year-month-day only).
 *   0 indicates that the book was not seen, but a neighbor was (indicating a shelf read on that day).
 *   1 indicates that the book was seen.
 */
$ret; //!< return value from function call that does most of the work
$ret = get_ping_count_since(stripslashes($_GET["book_tag"]), stripslashes($_GET["start_date"]), stripslashes($_GET["end_date"]));
Print $ret;

function book_seen($book_tag,$start_date,$end_date){
	include "../../db_info.php";

	/* Create a new mysqli object with database connection parameters */
	$con = new mysqli($server, $user, $password, $database);
	$query_or_not = true;
	$start_date_formatted = strtotime("Y-m-d H:i:s",$start_date);
	$end_date_formatted = strtotime("Y-m-d H:i:s",$end_date);
	if(mysqli_connect_errno()) {
		Print "Connection Failed: " . mysqli_connect_errno();
	}

	if (strlen($end_date) == 0){
		$end_date_formatted = date("Y-m-d H:i:s",time());//Current datetime
	}

	if ($start_date_formatted === false){
		Print "Bad start_date.";
		$query_or_not = false;
	}
	elseif ($end_date_formatted === false) {
		Print "Bad end_date.";
		$query_or_not = false;
	}
	/* Create a prepared statement */
	//First, build up a list of the neighbors of the book we are looking for
	//TODO: Increase performance, possibly by using COUNT and UNIQUE
	if($stmt = $con -> prepare("SELECT * FROM book_pings WHERE book_tag = ? AND ping_time >= ? AND ping_time <= ?") && query_or_not === true) {

		/* Bind parameters
		 s - string, b - blob, i - int, etc */
		$stmt -> bind_param("sss", $book_tag,$start_date_formatted,$end_date_formatted);

		/* Execute it */
		$stmt -> execute();

		/* Bind results */
		$stmt -> bind_result($result);

		/* Fetch the value */
		$stmt -> fetch();

		/* Close statement */
		$stmt -> close();
	}

	$seen_days = array();



	if($result == FALSE){
		//Print 'SQL Select failed' . mysqli_error();
	} else if(mysqli_num_rows($result) == 0) {
		//Print 'No rows seleted';
	} else {
		while ($row = mysqli_fetch_assoc($result)) {
			$arr = explode(" ",$row["ping_time"]);
			$days_seen[$arr[0]] = 1;
			$days_neighbor_seen[$arr[0]] = 1;
			$neighbors[$row["neighbor1_tag"]] = 1;
			$neighbors[$row["neighbor2_tag"]] = 1;
		}
		mysqli_free_result($result);


		/* Then calculate which days there was a shelf read
		 * based on the days when a neighbor was seen */
		/* TODO Improve performance by selecting just unique dates */
		foreach($neighbors as $key => $value){
			if($stmt = $con -> prepare("SELECT * FROM book_pings WHERE book_tag = ? AND ping_time >= ? AND ping_time <= ?") && (query_or_not === true)) {

				/* Bind parameters
				 s - string, b - blob, i - int, etc */
				$stmt -> bind_param("sss", $key,$start_date_formatted,$end_date_formatted);

				/* Execute it */
				$stmt -> execute();

				/* Bind results */
				$stmt -> bind_result($res2);

				/* Fetch the value */
				$stmt -> fetch();

				/* Close statement */
				$stmt -> close();
			}
			if($res2 == FALSE){
				//Print 'SQL Select failed' . mysqli_error();
			} else {
				while ($row = mysqli_fetch_assoc($res2)) {
	    $arr = explode(" ",$row["ping_time"]);
	    $days_neighbor_seen[$arr[0]] = 1;
				}
				mysqli_free_result($res2);
			}
		}

		/* Finally, check to see if the book in question was
		 * seen on all the shelf-read days */
		ksort($days_neighbor_seen);
		foreach($days_neighbor_seen as $key => $value){
			if($days_seen[$key] != 1){
				$seen_days[$key] = 0;
			} else {
				$seen_days[$key] = 1;
			}
		}
	}

	/* Close connection */
	$con -> close();
	return $seen_days;
}
//Very important to not have whitespace after the closing tag, since using
// in generating image files!
?>