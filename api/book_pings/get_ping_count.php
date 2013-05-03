<?php

  //include_once "../api_ref_call.php";

/**
 * DEPRECATED. TODO: Delete this file
 **/

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

/*
$ret; //!< return value from function call that does most of the work
$ret = get_ping_count_since(stripslashes($_GET["date"]));
echo $ret;

function get_ping_count_since($date){
	include_once "../../database.php";
	/* Create a new mysqli object with database connection parameters */
	date_default_timezone_set("UTC");
	$s_date = new DateTime($date);
	$start_date_formatted = $s_date->format('Y-m-d H:i:s');
	/* Create a prepared statement */
	$array = array();
    $db = new database();
    $db->query = "SELECT COUNT(*) as TOTALFOUND FROM book_pings WHERE ping_time>=?";
    $db->params = array($start_date_formatted);
    $db->type = 's';
    $r = $db->fetch();
		
	if($r === FALSE){
		Print '<pre>SQL select failed' . mysqli_error();
		Print '<br />';
		/* Close connection */
	} else {
		return $r[0]['TOTALFOUND'];
	} 
}*/
?>
