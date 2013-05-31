<?php 
	// returns every inst_id and name from the institutions table
	
	include_once $_SERVER['DOCUMENT_ROOT'] . "/database.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/header_include.php";

	$err = array();

	$db = new database();
	$db->query = "SELECT inst_id, name FROM institution";
	$result = $db->fetch();
	
	if(!$result) {
		$err[] = "SQL Error"; 
	}
	else if(!count($result)) {
		$err[] = "No institutions in database";
	}
	
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'institutions'=>"", 'errors'=>$err)); 
	}
	else echo json_encode(array('result'=>"SUCCESS", 'institutions'=>$result, 'errors'=>"")); 
?>