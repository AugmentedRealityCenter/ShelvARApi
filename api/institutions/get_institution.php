<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/database.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/header_include.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/api/api_ref_call.php";
	
	$err = array();
	$contactread = true;
	if(!isset($_GET['inst_id'])) {
		$err[] = "No inst_id supplied";
	}
	if(!count($err) && $_GET['inst_id'] != $oauth_user['inst_id']) {
		$err[] = "Invalid access to institution information";
	}
	if(stripos($oauth_user['scope'],"contactread") === false) {
		$contactread = false;
	}
	if(!count($err)) {
		$inst_id = $_GET['inst_id'];
		
		$db = new database();
		if(!$contactread) {
			$db->query = "SELECT inst_id, name, inst_url, inst_type, inst_size FROM institutions WHERE inst_id = ?";
		}
		else $db->query = "SELECT inst_id, name, admin_contact, alt_contact, inst_url, inst_type, inst_size FROM institutions WHERE inst_id = ?";
		$db->params = array($inst_id);
		$db->type = 's';
		
		$result = $db->fetch();
		if(!empty($result)) {
			echo json_encode(array('result'=>"SUCCESS", 'institution'=>$result));//'errors'=>""));
		}
		else $err[] = "SQL Error";
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'message'=>$err)); 
	}
?>
