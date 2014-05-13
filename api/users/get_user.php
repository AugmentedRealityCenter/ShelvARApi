<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/database.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/header_include.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/api/api_ref_call.php";
	
    $oauth_user = get_oauth();
    $inst_id    = $oauth_user['inst_id'];
    $user_id    = $oauth_user['user_id'];

	$err = array();
	$contactread = true;
	if(!isset($_GET['user_id'])) {
		$err[] = "No user_id supplied";
	}
	if(!count($err) && ($_GET['user_id'] != $oauth_user['user_id'])) {
		if($oauth_user['is_admin'] == 0) {
			if($oauth_user['is_superadmin'] == 0) {
                http_response_code(403);
				$err[] = "Invalid access to user account";
			}
		}
	}
	if(stripos($oauth_user['scope'],"contactread") === false) {
		$contactread = false;
	}
	if(!count($err)) {
		$user_id = $_GET['user_id'];
		
		$db = new database();
		if(!$contactread) {
			$db->query = "SELECT user_id, name FROM users WHERE user_id = ?";
		}
		else $db->query = "SELECT user_id, name, email FROM users WHERE user_id = ?";
		$db->params = array($user_id);
		$db->type = 's';
		
		$result = $db->fetch();
		if(!empty($result)) {
			echo json_encode(array('result'=>"SUCCESS", 'user'=>$result));
		}
		else $err[] = "User ID not found";
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'message'=>$err)); 
	}
?>
