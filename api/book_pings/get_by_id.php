<?php

/**
 * @file get_by_id.php
 * @author Jake Rego
 * Copyright 2012 by ShelvAR Team.
 * @version September 29, 2012
 * Retrieves a book_ping with the database id that is entered
 * The id entered, according to the db format, should be an 11 character integer
 */
 include "book_ping_lib.php"
 include ("../../db_info.php");
 
 // getting the entered ID and limiting by institution
 $ret = get_by_id(stripslashes($_GET["book_ping"],$_GET["institution"]); 
 
 // alternate query format
 $ret_two = get_by_id_1(stripslashes($_GET["book_ping"],$_GET["institution"]);
 
 
 /** 
 * Queries the database for requested book_ping by id
 */
 function get_by_id($book_ping, $institution="") {
 
	 if(isset($_GET["book_ping"]))
		$book_ping = $_GET['book_ping'];
	 
	 // Create a new mysqli object with database connection parameters
		$server = "localhost";
		$user ="mysql_username";
		$password = "mysql_pword";
		$database = "mysql_db";
		$con = new mysqli($server, $user, $password, $database);
		
		if(mysqli_connect_errno()) {
			echo "Connection Failed: " . mysqli_connect_errno();
			exit();
		}
		
		/* create a prepared statement */
		if($stmt - $con -> prepare("SELECT * from book_pings WHERE id = " + $book_ping) { //&& query_or_not ==true) {
		
			// Bind parameters
			$stmt -> bind_param("ssssssss",
			$book_info["book_tag"],
			$book_info["book_call"],
			$book_info["neighbor1_tag"],
			$book_info["neighbor1_call"],
			$book_info["neighbor2_tag"],
			$book_info["neighbor2_call"],
			$book_info["ping_time"],
			$institution);
		
			// Execute it
			$stmt -> execute();
		
			// Bind results
			$stmt -> bind_result($query_result);
			
			// Fetch value
			$stmt -> fetch();
			
			// Close statement
			$stmt -> close();		
		}

		if($query_result == FALSE) {
			Print 'SQL Select failed' . mysqli_error();
		} else {
			while ($row = mysqli_fetch_assoc($res2[$res_type = MYSQL_ASSOC ])) {
			
				
				/* If returned as array, should fill book_info array */
				$book_info[0] = $book_ping;
				$book_info[1] = $row["book_tag"];
				$book_info[2] = $row["book_call"];
				$book_info[3] = $row["neighbor1_tag"];
				$book_info[4] = $row["neighbor1_call"];
				$book_info[5] = $row["neighbor2_tag"];
				$book_info[6] = $row["neighbor2_call"];
				$book_info[7] = $row["ping_time"];
				$book_info[8] = $row["institution"];			
				
			}
			mysqli_free_result($res2);
		}
			
		/* Close connection */
		$con -> close();
		
	$book_info = json_encode(array('item' => $book_info_string), JSON_FORCE_OBJECT);	
	
	return $book_info;
} 
 
 
 /** 
 * Queries the database in a different format, not sure how to check institution here
 */
 function get_by_id_1($book_ping, $institution="") {
	
	if(isset($_GET["book_ping"]))
		$book_ping = $_GET['book_ping'];
	 
	 // Create a new mysqli object with database connection parameters
		$server = "localhost";
		$user ="mysql_username";
		$password = "mysql_pword";
		$database = "mysql_db";
		$con = new mysqli($server, $user, $password, $database);
		
		if(mysqli_connect_errno()) {
			echo "Connection Failed: " . mysqli_connect_errno();
			exit();
		}
		
		// creating query statement and executing
		$query = "SELECT * FROM book_pings WHERE id =" + $book_ping;
		if( $con -> mysqli_fetch_array($query) ) {
			$sql_output = mysqli_fetch_array($query); //should be a string of the row		
		}
		
		// closes connection
		$con -> close();
		
		$sql_output = json_encode($sql_output);
		return $sql_output;		
 }
 
 

return $ret; // or $ret_two
 ?>