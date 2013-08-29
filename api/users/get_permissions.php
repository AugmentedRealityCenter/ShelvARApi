<?php
	include_once "../../database.php";
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	$err = array();
	
	if(!$_GET['user_id']) {
		$err[] = "No user_id supplied";
	}
	if($_GET['user_id'] != $oauth_user['user_id']) {
		if($oauth_user['is_admin'] == 0) {
			if($oauth_user['is_superadmin'] == 0) {
				$err[] = "Invalid access to user account";
			}
		}
	}
	if(!count($err)) {
		$user_id = $_GET['user_id'];
		
		$db = new database();
		$db->query = "SELECT can_submit_inv, can_read_inv, can_shelf_read, is_admin FROM users WHERE user_id = ?";
		$db->params = array($user_id);
		$db->type = 's';
		
		$result = $db->fetch();
		if(!empty($result)) {
			echo json_encode(array('result'=>"SUCCESS", 'user'=>$user_id, 'permissions'=>$result, 'errors'=>""));
		}
		else $err[] = "SQL Error";
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'user'=>"", 'errors'=>$err)); 
	}
?>