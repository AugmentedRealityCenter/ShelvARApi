<?php
	include_once "../../database.php";
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	$err = array();
	
	$user_id = "";
	if(!$_POST['user_id']) {
		if(!$_GET['user_id']) {
			$err[] = "No user_id supplied";
		}
		else $user_id = $_GET['user_id'];
	}
	else $user_id = $_POST['user_id'];
	
	if($oauth_user['is_admin'] == 0) {
		$err[] = "Invalid access to user account";
	}
	if(!count($err)) {
		$query = "UPDATE users SET ";
		$params = array();
		$type = "";
		
		if(isset($_POST['can_submit_inv']) && $_POST['can_submit_inv'] != "") {
			$query .= "can_submit_inv = ?,";
			$type .= "i";
			$params[] = $_POST['can_submit_inv'];
		}
		else if(isset($_GET['can_submit_inv']) && $_GET['can_submit_inv'] != "") {
			$query .= "can_submit_inv = ?,";
			$type .= "i";
			$params[] = $_GET['can_submit_inv'];
		}
		
		if(isset($_POST['can_read_inv']) && $_POST['can_read_inv'] != "") {
			$query .= "can_read_inv = ?,";
			$type .= "i";
			$params[] = $_POST['can_read_inv'];
		}
		else if(isset($_GET['can_read_inv']) && $_GET['can_read_inv'] != "") {
			$query .= "can_read_inv = ?,";
			$type .= "i";
			$params[] = $_GET['can_read_inv'];
		}
		
		if(isset($_POST['can_shelf_read']) && $_POST['can_shelf_read'] != "") {
			$query .= "can_shelf_read = ?,";
			$type .= "i";
			$params[] = $_POST['can_shelf_read'];
		}
		else if(isset($_GET['can_shelf_read']) && $_GET['can_shelf_read'] != "") {
			$query .= "can_shelf_read = ?,";
			$type .= "i";
			$params[] = $_GET['can_shelf_read'];
		}
		
		if(isset($_POST['is_admin']) && $_POST['is_admin'] != "") {
			$query .= "is_admin = ?,";
			$type .= "i";
			$params[] = $_POST['is_admin'];
		}
		else if(isset($_GET['is_admin']) && $_GET['is_admin'] != "") {
			$query .= "is_admin = ?,";
			$type .= "i";
			$params[] = $_GET['is_admin'];
		}
		
		$query = substr($query,0,-1); // removing trailing comma
		$query .= " WHERE user_id = ?";
		$type .= "s";
		$params[] = $user_id;
		
		$db = new database();
		$db->query = $query;
		$db->params = $params;
		$db->type = $type;
		
		if($db->update()) {
			echo json_encode(array('result'=>"SUCCESS", 'user_id'=>$user_id)); 
		}
		else $err[] = "SQL Error";
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR $err", 'user_id'=>"")); 
	}
?>