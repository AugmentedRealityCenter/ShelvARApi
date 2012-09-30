<?php

/**
 * @file get_by_id.php
 * 
 * Copyright 2011 by ShelvAR Team.
 * @version September 29, 2012
 * Retrieves a book_ping with the database id that is entered
 * The id entered, according to the db format, should be an 11 character integer
 */
 include "book_ping_lib.php"
 include ("../../db_info.php");
 
 $ret = get_by_id(stripslashes($_GET["book_ping"],$_GET["institution"]);
 
 if(isset($_GET["book_tag"]))
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
	if($stmt - $con -> prepare("SELECT * from book_pings WHERE id = " + book_ping) && query_or_not ==true) {
	
	
	}

	
	
	
	/* Close connection */
	$con -> close();
	
 return $ret
 
 ?>