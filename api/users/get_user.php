<?php
	include_once "../../database.php";
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	$err = array();
	
	if(!$_GET['user_id']) {
		$err[] = "No user_id supplied";
	}
	if($_GET['user_id'] != $oauth_user['user_id']) {
		$err[] = "Invalid access to user account";
	}
	if(!count($err)) {
		$user_id = $_GET['user_id'];
		
		$db = new database();
		$db->query = "SELECT user_id, name, email FROM users WHERE user_id = ?";
		$db->params = array($user_id);
		$db->type = 's';
		
		$result = $db->fetch();
		if(!empty($result)) {
			echo json_encode(array('result'=>"SUCCESS", 'user'=>$result, 'errors'=>""));
		}
		else $err[] = "SQL Error";
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'user'=>"", 'errors'=>$err)); 
	}
?>