<?php

/**
 * @file
 * Find where the book was most recently
 *
 * Copyright 2011 by ShelvAR Team.
 * 
 * @version Nov 6, 2011
 * @author Eliot Fowler
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
   include_once "../db_info.php";
   $con = mysql_connect($server,$user,$password);
	if (!$con){
		Print "COULDNT CONNECT";
		die('Could not connect: ' . mysql_error());
	}
  
	mysql_select_db($database, $con);
	
	$resource = mysql_query("SELECT * FROM book_pings " . 
	          "WHERE book_call = '" . $lcNum . "'");
			  
	if($resource == FALSE){
		Print "FAILED 1";
      //Print 'SQL Select failed' . mysql_error();
    } else if(mysql_num_rows($resource) == 0) {
		Print "NO ROWS";
      //Print 'No rows seleted';
    } else { 
		//Print $resource;
		while ($row = mysql_fetch_assoc($resource)) {
			$arr[] = $row;
		}
		
		$return_arr["last_time_seen"] = $arr[count($arr)-1]["ping_time"];
		$return_arr["left_neighbor"] = $arr[count($arr)-1]["neighbor1_call"];
		$return_arr["right_neighbor"] = $arr[count($arr)-1]["neighbor2_call"];
		return $return_arr;
	}
 }
?>