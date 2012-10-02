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
$ret = book_seen(stripslashes($_GET["book_tag"]), stripslashes($_GET["start_date"]), stripslashes($_GET["end_date"]));
//Currently unsure if this is what is wanted..?
print_r ($ret);

function book_seen($book_tag,$start_date,$end_date){
	include "../../database.php";

	/* Create a new mysqli object with database connection parameters */
	date_default_timezone_set("UTC");
	$s_date = new DateTime($start_date);
	$e_date = new DateTime($end_date);
	$start_date_formatted = $s_date->format('Y-m-d H:i:s');
	$end_date_formatted = $e_date->format('Y-m-d H:i:s');
	if(mysqli_connect_errno()) {
		Print "Connection Failed: " . mysqli_connect_errno();
	}

	if (strlen($end_date) == 0){
		$end_date_formatted = date("Y-m-d H:i:s",time());//Current datetime
	}

	//First, build up a list of the neighbors of the book we are looking for
	//TODO: Increase performance, possibly by using COUNT and UNIQUE
	
	/* Create a prepared statement */
	$array = array();
    $db = new database();
	$book_tag = stripslashes($_GET['book_tag']);
	$date = stripslashes($_GET["start_date"]);
    $db->query = "SELECT * FROM book_pings WHERE book_tag = ? AND ping_time >= ? AND ping_time <= ?";
    $db->params = array($book_tag,$start_date_formatted,$end_date_formatted);
    $db->type = 'sss';
    $r = $db->fetch();
	
	
	
	$seen_days = array();


	foreach ($r as $result){
			$arr = explode(" ",$result["ping_time"]);
			$days_seen[$arr[0]] = 1;
			$days_neighbor_seen[$arr[0]] = 1;
			$neighbors[$result["neighbor1_tag"]] = 1;
			$neighbors[$result["neighbor2_tag"]] = 1;
	}
	


		/* Then calculate which days there was a shelf read
		 * based on the days when a neighbor was seen */
		/* TODO Improve performance by selecting just unique dates */
			/* Create a prepared statement */
		foreach($neighbors as $key => $value){
			$array = array();
			$db = new database();
			$db->query = "SELECT * FROM book_pings WHERE book_tag = ? AND ping_time >= ? AND ping_time <= ?";
			$db->params = array($key,$start_date_formatted,$end_date_formatted);
			$db->type = 'sss';
			$r2 = $db->fetch();
			foreach ($r2 as $result2){
				$arr = explode(" ",$result2["ping_time"]);
				$days_neighbor_seen[$arr[0]] = 1;
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

	/* Close connection */
	return $seen_days;
}
//Very important to not have whitespace after the closing tag, since using
// in generating image files!
?>