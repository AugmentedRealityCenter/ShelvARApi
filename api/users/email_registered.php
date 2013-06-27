<?php
	include_once "../../database.php";
	include_once "../../header_include.php";
	                                  
	if(isset($_GET['email'])) {                                                               
		$email = $_GET['email'];                                  
		$db = new database();
		$db->query = "SELECT user_id FROM users WHERE email = ?";
		$db->params = array($email);
		$db->type = 's';
		
		$result = $db->fetch();
		if(!empty($result)) {
			echo json_encode(array('result'=>"SUCCESS", 'email'=>"REGISTERED", 'message'=>"Email address registered with user", 'errors'=>""));
		}
		else echo json_encode(array('result'=>"SUCCESS", 'email'=>"NOT REGISTERED", 'message'=>"", 'errors'=>""));
	}
	else echo json_encode(array('result'=>"ERROR", 'errors'=>"No email address supplied"));
?>