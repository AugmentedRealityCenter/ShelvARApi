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
		$db->query = "SELECT user_id, pending_email, email, email_verified, activation_key, inst_id FROM users WHERE activation_key = ?";
		$db->params = array($key);
		$db->type = 's';

		$result = $db->fetch();
		
		if(empty($result)) {
			$err[] = "No user associated with supplied activation key";
		}
		else {
			$user_id = $result[0]['user_id'];
			$pending_email = $result[0]['pending_email'];
			$inst_id = $result[0]['inst_id']
		
			$email = $pending_email;
			$email_verified = 1;
			$pending_email = "";
			$activation_key = "";
			$is_admin = 0;
			
			// check if email matches admin email in institutions to give admin rights
			$db = new database();
			$db->query = "SELECT admin_contact FROM institutions WHERE inst_id = ?";
			$db->params = array($inst_id);
			$db->type = 's';

			$result = $db->fetch();
			
			// TODO error handling
			if($result[0]['admin_contact'] == $email) {
				$is_admin = 1;
			}
			else $is_admin = 0;
			
			$db = new database();
			$db->query = "UPDATE users SET pending_email = ?, email = ?, email_verified = ?, is_admin = ?, activation_key = ? WHERE user_id = ?";
			$db->params = array($pending_email, $email, $email_verified, $is_admin, $activation_key, $user_id);
			$db->type = 'ssiiss';
			
			if($db->update()) {
				echo json_encode(array('result'=>"SUCCESS", 'user_id'=>$user_id, 'errors'=>""));
				$frontend = "http://shelvar.com/";
				if($_SERVER['SERVER_NAME'] == "devapi.shelvar.com") {
					$frontend = "http://dev.shelvar.com/";
				}
				if(isset($_GET['edit'])) {
					header('Location: '.$frontend.'edit-email-complete.php');
				}
				else header('Location: '.$frontend.'registration-complete.php');
			}
			else $err[] = "SQL Error";
		}
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'errors'=>$err)); 
	}
?>