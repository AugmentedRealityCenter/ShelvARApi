<?php
	include '../../database.php';
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	$err = array();
	
	if($oauth_user['is_admin'] == 1 || $oauth_user['is_superadmin'] == 1) {
		$query = "SELECT inst_id, name, user_id, is_admin, email, email_verified, can_submit_inv, can_read_inv, can_shelf_read FROM users WHERE inst_id = ?";
		$param = array($oauth_user['inst_id']);
	}
	else {
		if(stripos($oauth_user['scope'],"contactread") === false) {
			$query = "SELECT inst_id, name, user_id, is_admin, email_verified, can_submit_inv, can_read_inv, can_shelf_read FROM users WHERE user_id = ?";
		}
		else $query = "SELECT inst_id, name, user_id, is_admin, email, email_verified, can_submit_inv, can_read_inv, can_shelf_read FROM users WHERE user_id = ?";
		$param = array($oauth_user['user_id']);
	}
	
	if(!count($err)) {
		$db = new database();
		$db->query = $query;
		$db->params = $param;
		$db->type = 's';
		$result = $db->fetch();
		
		if(!empty($result)) {
			echo json_encode(array('result'=>"SUCCESS", 'users'=>$result));
		}
		else $err[] = "SQL Error";
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR ".$err, 'users'=>"")); 
	}
?>