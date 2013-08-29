<?php
	include_once "../../database.php";
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	$err = array();
	
	if($oauth_user['is_admin'] == 0) {
		$err[] = "Invalid access to API call";
	}
	if(!isset($_GET['email'])) {
		$err[] = "No email address supplied";
	}
	                                  
	if(!count($err)) {                                                               
		$email = $_GET['email']; 
		$inst_id = $oauth_user['inst_id'];
		$db = new database();
		$db->query = "SELECT user_id FROM users WHERE email = ? AND inst_id = ?";
		$db->params = array($email, $inst_id);
		$db->type = 'ss';
		
		$result = $db->fetch();
		if(!empty($result)) {
			echo json_encode(array('result'=>"SUCCESS", 'email'=>"REGISTERED", 'errors'=>""));
		}
		else echo json_encode(array('result'=>"SUCCESS", 'email'=>"NOT REGISTERED", 'errors'=>""));
	}
	else echo json_encode(array('result'=>"ERROR", 'email'=>"", 'errors'=>$err));
?>