<?php 
	include "../../database.php";
	include_once "../../header_include.php";
	
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
	
	if(!count($err)) {
		$user_id = $_POST['user_id'];
		$inst_id = $_POST['inst_id'];
		$password = $_POST['password'];
		$name = $_POST['name'];
		$email = $_POST['email'];
		
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
			
			for($i = 0; $i < 1000; $i++) { 
				$password = hash('sha256', $password . $salt); 
			} 
			
			// check if email matches admin email in institutions to give admin rights
			$db = new database();
			$db->query = "SELECT admin_contact FROM institutions WHERE inst_id = ?";
			$db->params = array($inst_id);
			$db->type = 's';
		
			$result = $db->fetch();
			if($result[0]['admin_contact'] == $email) {
				$is_admin = 1;
			}
			else $is_admin = 0;
			
			$db = new database();
			$db->query = "INSERT INTO users(user_id,inst_id,password,salt,name,email,email_verified,is_admin,can_submit_data,can_read_data)
							VALUES(?,?,?,?,?,?,?,?,?,?)";
			$db->params = array($user_id, $inst_id, $password, $salt, $name, $email, "NO", $is_admin, 0, 0);
			$db->type = 'sssssssiii';

			if($db->insert()) {
				echo json_encode(array('result'=>"SUCCESS", 'user_id'=>$user_id)); 
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