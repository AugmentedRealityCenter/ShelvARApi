<?php 
	// returns every inst_id and name from the institutions table
	
	include_once $_SERVER['DOCUMENT_ROOT'] . "/database.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/header_include.php";

	$err = array();

	$db = new database();
	$db->query = "SELECT inst_id, name FROM institutions";
	$result = $db->fetch();
	
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'institutions'=>"", 'errors'=>$err)); 
	}
	else echo json_encode(array('result'=>"SUCCESS", 'institutions'=>$result, 'errors'=>"")); 
?>