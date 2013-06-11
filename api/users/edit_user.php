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
	
	if($user_id != $oauth_user['user_id']) {
		$err[] = "Invalid access to user account";
	}
	if(!count($err)) {
		$query = "UPDATE users SET ";
		$params = array();
		
		if(isset($_POST['name']) && $_POST['name'] != "") {
			$query += "name = ?,";
			$type += "s";
			$params[] = $_POST['name'];
		}
		else if(isset($_GET['name']) && $_GET['name'] != "") {
			$query += "name = ?,";
			$type += "s";
			$params[] = $_GET['name'];
		}
		
		if(isset($_POST['email']) && $_POST['email'] != "") {
			$query += "email = ?,";
			$type += "s";
			$params[] = $_POST['email'];
		}
		else if(isset($_GET['email']) && $_GET['email'] != "") {
			$query += "email = ?,";
			$type += "s";
			$params[] = $_GET['email'];
		}
		
		$editPass = false;
		if(isset($_POST['password']) && $_POST['password'] != "") {
			$password = $_POST['password'];
			$editPass = true;
		}
		else if(isset($_GET['password']) && $_GET['password'] != "") {
			$password = $_GET['password'];
			$editPass = true;
		}
		
		if($editPass) {
			$db = new database();
			$db->query = "SELECT encrip_salt FROM users WHERE user_id = ?";
			$db->params = array($user_id);
			$db->type = 's';
			$result = $db->fetch();
			$salt = $result[0]['salt'];
			
			// TODO error handling
			
			$query += "password = ?,";
			$type += "s";
			$password = hash('sha256', $password . $salt);
			$params[] = $password;
		}	
		
		$query = substr($query,0,-1); // removing trailing comma
		$query += "WHERE user_id = ?";
		$type += "s";
		$params[] = $user_id;
		
		$db = new database();
		$db->query = "UPDATE users SET name = ?, email = ?, password = ? WHERE user_id = ?";
		$db->params = $params;
		$db->type = $type;
		
		if($db->update()) {
			echo json_encode(array('result'=>"SUCCESS", 'user_id'=>$params, 'errors'=>""));
		}
		else $err[] = "SQL Error";
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'user'=>"", 'errors'=>$err)); 
	}
?>