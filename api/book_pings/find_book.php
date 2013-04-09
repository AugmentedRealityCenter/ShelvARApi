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
 
 include_once "../api_ref_call.php";
 
function find_book($lcNum,$institution)
{
	include_once "../../database.php";
	/* Create a prepared statement */
	$array = array();
    $db = new database();
    $db->query = "SELECT * FROM book_pings WHERE book_call =? AND institution=?";
    $db->params = array($lcNum,$institution);
    $db->type = 'ss';
    $r = $db->fetch();
	 
		
	if($r === FALSE){
		Print "FAILED 1";
		//Print 'SQL Select failed' . mysqli_error();
	} else {
		$arr = array();
		//Print $resource;
		foreach($r as $row) {
			$arr[] = $row;
		}
		$return_arr = array();
		$return_arr["last_time_seen"] = $arr[count($arr)-1]["ping_time"];
		$return_arr["left_neighbor"] = $arr[count($arr)-1]["neighbor1_call"];
		$return_arr["right_neighbor"] = $arr[count($arr)-1]["neighbor2_call"];
		return $return_arr;
	}
}
?>