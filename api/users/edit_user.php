<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/database.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/header_include.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/api/api_ref_call.php";
	
	if(stripos($oauth_user['scope'],"acctmod") === false) {
		exit(json_encode(array('result'=>'ERROR', 'message'=>'No permission to modify account.')));
	}
	
	$err = array();
	
	$user_id = "";
	if(!$_POST['user_id']) {
		if(!$_GET['user_id']) {
			$err[] = "No user_id supplied";
		}
		else $user_id = htmlspecialchars($_GET['user_id'], ENT_HTML401);
	}
	else $user_id = htmlspecialchars($_POST['user_id'], ENT_HTML401);
	
	if($user_id != $oauth_user['user_id']) {
		$err[] = "Invalid access to user account";
	}
	if(!count($err)) {
		$query = "UPDATE users SET ";
		$params = array();
		$type = "";
		
		$editName = false;
		$editEmail = false;
		$editPass = false;
		
		if(isset($_POST['name']) && $_POST['name'] != "") {
			$params[] = htmlspecialchars($_POST['name'], ENT_HTML401);
			$editName = true;
		}
		else if(isset($_GET['name']) && $_GET['name'] != "") {
			$params[] = htmlspecialchars($_GET['name'], ENT_HTML401);
			$editName = true;
		}
		
		if(isset($_POST['email']) && $_POST['email'] != "") {
			$params[] = htmlspecialchars($_POST['email'], ENT_HTML401);
			$pending_email = htmlspecialchars($_POST['email'], ENT_HTML401);
			$editEmail = true;
		}
		else if(isset($_GET['email']) && $_GET['email'] != "") {
			$params[] = htmlspecialchars($_GET['email'], ENT_HTML401);
			$pending_email = htmlspecialchars($_GET['email'], ENT_HTML401);
			$editEmail = true;
		}

		if(isset($_POST['password']) && $_POST['password'] != "") {
			$password = $_POST['password'];
			$editPass = true;
		}
		else if(isset($_GET['password']) && $_GET['password'] != "") {
			$password = $_GET['password'];
			$editPass = true;
		}
		
		if($editName) {
			$query .= "name = ?,";
			$type .= "s";
		}
		if($editEmail) {
			do {
				$activation_key = md5(uniqid(rand(), true));
				$activation_key = substr($activation_key, 0, 64);
		
				$db = new database();
				$db->query = "SELECT user_id FROM users WHERE activation_key = ?";
				$db->params = array($activation_key);
				$db->type = 's';
		
				$result = $db->fetch();
			} while(!empty($result));
			$params[] = $activation_key;
			$params[] = 0;
			$query .= "pending_email = ?, activation_key = ?, email_verified = ?,";
			$type .= "ssi";
		}
		if($editPass) {
			$db = new database();
			$db->query = "SELECT encrip_salt FROM users WHERE user_id = ?";
			$db->params = array($user_id);
			$db->type = 's';
			$result = $db->fetch();
			$salt = $result[0]['encrip_salt'];
			
			$query .= "password = ?,";
			$type .= "s";
			$password = hash('sha256', $password . $salt);
			$params[] = $password;
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
			if($editEmail) {
				include_once($_SERVER['DOCUMENT_ROOT'] . "/api/users/send_activation_email.php");
			}
			if(!$err) {
				echo json_encode(array('result'=>"SUCCESS", 'user_id'=>$user_id)); 
			}
		}
		else $err[] = "SQL Error";
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'message'=>$err)); 
	}
?>
