<?php 
	include '../../connect.php';
	
	session_start();
	
	$err = array(); // For storing any errors
	
	// should do this on client side as well
	if(!$_POST['user_id'] || !$_POST['password']) {
		$err[] = 'Please fill in all fields';
	}
		
	if(!count($err)) {
		$user_id = $_POST['user_id'];
		$password = $_POST['password'];

		$query = "SELECT user_id,password,salt FROM users WHERE user_id = '$user_id';";
		$result = mysql_query($query);
		
		// If there is a username that matches
		if(mysql_num_rows($result) > 0) {
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			
			// Check the password
			$check_password = hash('sha256', $password . $row['salt']); 
			for($i = 0; $i < 1000; $i++) { 
				$check_password = hash('sha256', $check_password . $row['salt']); 
			} 
			
			// if the password matches			
			if($check_password === $row['password']) { 
				// remove password and salt
				unset($row['salt']); 
				unset($row['password']); 
				
				// login user by storing username and email into session
				$_SESSION['user'] = $row; 
	
				header("Location: ../");
				exit; 
			} 
			else $err[]='Incorrect password';
		}
		else $err[]='No record of username';
	}
	$_SESSION['msg']['login-err'] = implode('<br />',$err); // Save the error messages in the session
	
	header("Location: ../");
	exit;	
?>