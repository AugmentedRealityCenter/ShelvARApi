<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/database.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/header_include.php";
	
	$err = array();
	
	if(!$_GET['key']) {
		$err[] = 'No activation key supplied';
	}
	
	if(!count($err)) {
		$key = $_GET['key'];
		
		$db = new database();
		$db->query = "SELECT inst_id, pending_email, admin_contact, email_verified, activation_key FROM institutions WHERE activation_key = ?";
		$db->params = array($key);
		$db->type = 's';

		$result = $db->fetch();
		
		if(empty($result)) {
			$err[] = "No institution associated with supplied activation key";
		}
		else {
			$user_id = $result[0]['inst_id'];
			$pending_email = $result[0]['pending_email'];
		
			$admin_contact = $pending_email;
			$email_verified = 1;
			$pending_email = "";
			$activation_key = "";
			
			$db = new database();
			$db->query = "UPDATE institutions SET pending_email = ?, admin_contact = ?, email_verified = ?, activation_key = ? WHERE inst_id = ?";
			$db->params = array($pending_email, $admin_contact, $email_verified, $activation_key, $user_id);
			$db->type = 'ssiss';
			
			if($db->update()) {
				echo json_encode(array('result'=>"SUCCESS", 'inst_id'=>$inst_id, 'errors'=>""));
				$frontend = "http://shelvar.com/";
				if($_SERVER['SERVER_NAME'] == "devapi.shelvar.com") {
					$frontend = "http://dev.shelvar.com/";
				}
				header('Location: '.$frontend.'inst-registration-complete.php');
			}
			else $err[] = "SQL Error";
		}
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'errors'=>$err)); 
	}
?>