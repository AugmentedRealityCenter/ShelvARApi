<?php
	include_once "../../database.php";
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	$err = array();
	
	if(stripos($oauth_user['scope'],"acctmod") === false) {
		exit(json_encode(array('result'=>'ERROR No permission to access account.')));
	}
	
	$inst_id = "";
	if(!$_POST['inst_id']) {
		if(!$_GET['inst_id']) {
			$err[] = "No inst_id supplied";
		}
		else $inst_id = $_GET['inst_id'];
	}
	else $inst_id = $_POST['inst_id'];
	
	if($inst_id != $oauth_user['inst_id'] || $oauth_user['is_superadmin'] == 0) {
		$err[] = "Invalid access to institution account";
	}
	
	// if all preliminary checks passed
	if (!count($err)) {
		$db = new database();
		
		$query = "SELECT notif_id,text,read,create_time
					FROM notifications
					WHERE inst_id = ?
					GROUP BY notif_id";
		$params = array($inst_id);
		$type = "isis";
		
		$db->query = $query;
		$db->params = $params;
		$db->type = $type;
		
		$result = $db->fetch();
		
		echo json_encode($result);
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'errors'=>$err)); 
	}
?>