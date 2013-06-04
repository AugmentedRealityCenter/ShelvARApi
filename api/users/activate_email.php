<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/database.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/header_include.php";
	
	$err = array();
	
	if(!$_SERVER['QUERY_STRING']) {
		$err[] = 'No activation key supplied';
	}
	
	if(!count($err)) {
		$key = $_SERVER['QUERY_STRING'];
		
		$db = new database();
		$db->query = "SELECT user_id, pending_email, email, email_verified, activation_key FROM users WHERE activation_key = ?";
		$db->params = array($key);
		$db->type = 's';

		$result = $db->fetch();
		
		if(empty($result)) {
			$err[] = "No user associated with supplied activation key";
		}
		else {
			$user_id = $result[0]['user_id'];
			$pending_email = $result[0]['pending_email'];
		
			$email = $pending_email;
			$email_verified = 1;
			$pending_email = "";
			$activation_key = "";
			
			$db = new database();
			$db->query = "UPDATE users SET pending_email = ?, email = ?, email_verified = ?, activation_key = ? WHERE user_id = ?";
			$db->params = array($pending_email, $email, $email_verified, $activation_key, $user_id);
			$db->type = 'ssiss';
			
			if($db->update()) {
				echo json_encode(array('result'=>"SUCCESS", 'user_id'=>$user_id, 'errors'=>"")); 
			}
		}
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'errors'=>$err)); 
	}
?>