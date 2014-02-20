<?php 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/database.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/header_include.php";

	$err = array();

	if(!$_POST['user_id'] || !$_POST['password'] || !$_POST['name'] || !$_POST['email'] || !$_POST['inst_id']) {
		$err[] = 'Please fill in all fields';
	}
	if(strlen($_POST['user_id']) < 4 || strlen($_POST['user_id']) > 45) {
		$err[] = 'Your username must be between 5 and 45 characters';
	}
	if(preg_match('/[^a-z0-9\-\_\.]+/i',$_POST['user_id'])) {
		$err[] = 'Your username contains invalid characters';
	}
	if(($_POST['password']) !== ($_POST['password2'])) {
		$err[] = 'Your passwords do not match';
	}

	if(!count($err)) {
		$user_id = $_POST['user_id'];
		$user_id = htmlspecialchars(strtolower($user_id), ENT_HTML401);
		$inst_id = htmlspecialchars($_POST['inst_id'], ENT_HTML401);
		$password = $_POST['password'];
		$name = htmlspecialchars($_POST['name'], ENT_HTML401);
		$email = htmlspecialchars($_POST['email'], ENT_HTML401);

		$db = new database();
		$db->query = "SELECT * FROM users WHERE user_id = ?";
		$db->params = array($user_id);
		$db->type = 's';

		$result = $db->fetch();

		if(count($result) > 0) {  
			$err[]='Username already taken';
		}
		else {
			// Generate random salt
			$salt = md5(uniqid(rand(), true));
			$salt = substr($salt, 0, 10);

			// Hash the password with the salt
			$password = hash('sha256', $password . $salt); 
			
			// check if email matches admin email in institutions to give admin rights
			$db = new database();
			$db->query = "SELECT admin_contact FROM institutions WHERE inst_id = ?";
			$db->params = array($inst_id);
			$db->type = 's';

			$result = $db->fetch();
			
			// TODO error handling
			if($result[0]['admin_contact'] === $email) {
				$is_admin = 1;
	
			}
			else $is_admin = 0;
			
			if(!isset($_POST['withhold_email'])) {
				// Generate random activation key
				// Check if key has already been generated
				do {
					$activation_key = md5(uniqid(rand(), true));
					$activation_key = substr($activation_key, 0, 64);
				
					$db = new database();
					$db->query = "SELECT user_id FROM users WHERE activation_key = ?";
					$db->params = array($activation_key);
					$db->type = 's';
				
					$result = $db->fetch();
				} while(!empty($result));
			}
			else $activation_key = "";
			
			$pending_email = $email;

			$db = new database();
			$db->query = "INSERT INTO users(inst_id,password,name,user_id,is_admin,email,email_verified,pending_email,activation_key,encrip_salt,can_submit_inv,can_read_inv,can_shelf_read)
							VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$db->params = array($inst_id, $password, $name, $user_id, $is_admin, "", 0, $pending_email, $activation_key, $salt, 0, 0, 0);
			$db->type = 'ssssisisssiii';
			/*
			$db->query = "INSERT INTO users(inst_id,password,name,user_id,is_admin,email,email_verified,encrip_salt,can_submit_inv,can_read_inv)
							VALUES(?,?,?,?,?,?,?,?,?,?)";
			$db->params = array($inst_id, $password, $name, $user_id, $is_admin, $email, 0, $salt, 0, 0);
			$db->type = 'ssssisisii';
			*/

			if($db->insert()) {
				if(!isset($_POST['withhold_email'])) {
					include_once($_SERVER['DOCUMENT_ROOT'] . "/api/users/send_activation_email.php");
				}
				if(!$err) {
					echo json_encode(array('result'=>"SUCCESS", 'user_id'=>$user_id, 'errors'=>"")); 
				}	
			}
			else {
				$err[] = 'MySQL Error';
			}
		}
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'user_id'=>"", 'errors'=>$err)); 
	}
?>