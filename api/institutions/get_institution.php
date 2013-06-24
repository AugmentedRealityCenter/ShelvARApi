<?php
	include_once "../../database.php";
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	$err = array();
	
	if(!$_GET['inst_id']) {
		$err[] = "No inst_id supplied";
	}
	if($_GET['inst_id'] != $oauth_user['inst_id']) {
		$err[] = "Invalid access to institution information";
	}
	if(!count($err)) {
		$inst_id = $_GET['inst_id'];
		
		$db = new database();
		$db->query = "SELECT inst_id, name, admin_contact, alt_contact, inst_url, inst_type, inst_size FROM institutions WHERE inst_id = ?";
		$db->params = array($inst_id);
		$db->type = 's';
		
		$result = $db->fetch();
		if(!empty($result)) {
			echo json_encode(array('result'=>"SUCCESS", 'institution'=>$result, 'errors'=>""));
		}
		else $err[] = "SQL Error";
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'institution'=>"", 'errors'=>$err)); 
	}
?>