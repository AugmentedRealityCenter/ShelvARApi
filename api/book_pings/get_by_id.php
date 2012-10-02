<?php

/**
 * @file get_by_id.php
 * @author Jake Rego
 * Copyright 2012 by ShelvAR Team.
 * @version September 29, 2012
 * Retrieves a book_ping with the database id that is entered
 * The id entered, according to the db format, should be an 11 character integer
 */

 include "../../database.php";
 
 // getting the entered ID and limiting by institution
 $ret = get_by_id(stripslashes($_GET["book_ping_id"]),(stripslashes($_GET["institution"]))); 
 echo ($ret);
 /** alternate query format
 $ret_two = get_by_id_1(stripslashes($_GET["book_ping_id"],$_GET["institution"]);
 */
 
 /** 
 * Queries the database for the requested book_ping_id 
 */
 function get_by_id($book_ping_id) {
	
	if(isset($_GET["book_ping_id"])){
		$book_ping_id = $_GET['book_ping_id'];
	}
	
	if(isset($_GET["institution"])){
		$institution = $_GET['institution'];
	}
	/* Create a prepared statement */
	$array = array();
    $db = new database();
    $db->query = "SELECT * FROM book_pings WHERE id =? AND institution=?";
    $db->params = array($book_ping_id,$institution);
    $db->type = 'ss';
    $r = $db->fetch();
	 
	$book_info = json_encode($r);	
	
	return $book_info;
} 
 
 
 /** 
 * Queries the database in a different format, not sure how to check institution here
 */
 function get_by_id_1($book_ping_id, $institution="") {
	
	if(isset($_GET["book_ping_id"]))
		$book_ping_id = $_GET['book_ping_id'];
	 
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
		$query = "SELECT * FROM book_pings WHERE id =" + $book_ping_id;
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