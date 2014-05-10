<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/database.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/header_include.php";
	                                  
	if(isset($_GET['user_id'])) {                                                               
		$user_id = $_GET['user_id'];                                  
		$db = new database();
		$db->query = "SELECT user_id FROM users WHERE user_id = ?";
		$db->params = array($user_id);
		$db->type = 's';
		
		$result = $db->fetch();
		if(!empty($result)) {
			echo json_encode(array('result'=>"SUCCESS", 'user_id'=>"TAKEN"));
		}
		else echo json_encode(array('result'=>"SUCCESS", 'user_id'=>"AVAILABLE"));
	}
	else echo json_encode(array('result'=>"ERROR", 'message'=>"No user ID supplied"));
?>
