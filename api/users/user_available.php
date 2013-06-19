<?php
	include_once "../../database.php";
	include_once "../../header_include.php";
	                                  
	if(isset($_GET['user_id'])) {                                                               
		$user_id = $_GET['user_id']);                                  
		$db = new database();
		$db->query = "SELECT user_id FROM users WHERE user_id = ?";
		$db->params = array($user_id);
		$db->type = 's';
		
		$result = $db->fetch();
		if(!empty($result)) {
			echo json_encode(array('result'=>"SUCCESS", 'user_id'=>"TAKEN", 'warnings'=>"user_id already in use", 'errors'=>"");
		}
		else echo json_encode(array('result'=>"SUCCESS", 'user_id'=>"AVAILABLE", 'errors'=>"");
	}
	else echo json_encode(array('result'=>"ERROR", 'errors'=>"No user_id supplied");
?>