<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/database.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/header_include.php";
	
	$err = array();
	
	if(!$_GET['inst_key']) {
		$err[] = 'No activation key supplied';
	}
	
	if(!count($err)) {
		$key = $_GET['inst_key'];
		
		$db = new database();
		$db->query = "SELECT inst_id, pending_email, admin_contact, email_verified, activation_key FROM institutions WHERE activation_key = ?";
		$db->params = array($key);
		$db->type = 's';

		$result = $db->fetch();
		
		if(empty($result)) {
			$err[] = "No institution associated with supplied activation key";
		}
		if($result[0]['email_verified'] == 1) {
			$err[] = "Institution already verified";
		}
		else {
			$inst_id = $result[0]['inst_id'];
			$pending_email = $result[0]['pending_email'];
		
			$admin_contact = $pending_email;
			$email_verified = 1;
			$pending_email = "";
						
			$db = new database();
			$db->query = "UPDATE institutions SET pending_email = ?, admin_contact = ?, email_verified = ? WHERE inst_id = ?";
			$db->params = array($pending_email, $admin_contact, $email_verified, $inst_id);
			$db->type = 'ssis';
			
			if($db->update()) {
				// attempt to activate admin account
				$db = new database();
				$db->query = "SELECT user_id FROM users WHERE (email = ? OR pending_email = ?) AND inst_id = ?";
				$db->params = array($admin_contact, $admin_contact, $inst_id);
				$db->type = 'sss';
				$result = $db->fetch();
				
				$frontend = "http://shelvar.com/";
				if($_SERVER['SERVER_NAME'] == "devapi.shelvar.com") {
					$frontend = "http://dev.shelvar.com/";
				}
				
				if(count($result) < 1) {  
					echo json_encode(array('result'=>"SUCCESS", 'inst_id'=>$inst_id, 'user_id'=>"", 'errors'=>"", 'warnings'=>'No admin registered for this institution'));
					header('Location: '.$frontend.'register-user.php');
				}
				else {
					$email = $admin_contact;
					$is_admin = 1;
					$user_id = $result[0]['user_id'];
					$pending_email = "";
					$db = new database();
					$db->query = "UPDATE users SET pending_email = ?, email = ?, email_verified = ?, is_admin = ? WHERE user_id = ?";
					$db->params = array($pending_email, $email, $email_verified, $is_admin, $user_id);
					$db->type = 'ssiis';
					if($db->update()) {
						if(isset($_GET['edit'])) {
							header('Location: '.$frontend.'logout.php?edit-email-complete.php');
						}
						else header('Location: '.$frontend.'registration-complete.php');
					}
				}
			}
			else $err[] = "SQL Error";
		}
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'errors'=>$err)); 
	}
?>