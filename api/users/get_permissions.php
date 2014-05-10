<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/database.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/header_include.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/api/api_ref_call.php";

    $oauth_user = get_oauth();
    $inst_id    = $oauth_user['inst_id'];
    $user_id    = $oauth_user['user_id'];
	
	$err = array();
	
	if(!isset($_GET['user_id'])) {
		$err[] = "No user_id supplied";
	}
	if(!count($err) && ($_GET['user_id'] != $oauth_user['user_id'])) {
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
			echo json_encode(array('result'=>"SUCCESS", 'user'=>$user_id, 'permissions'=>$result));
		}
		else $err[] = "SQL Error";
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'message'=>$err)); 
	}
?>
