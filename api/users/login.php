<?php 
	include '../../connect.php';
	include '../../header_include.php';
	
	$err = array(); // For storing any errors
	
	// should do this on client side as well
	if(!$_POST['user_id']) {
		$err[] = 'Null Username';
	}
	if(!$_POST['password']) {
		$err[] = 'Null Password';
	}
			
	if(!count($err)) {
		$user_id = $_POST['user_id'];
		$password = $_POST['password'];

		// TODO Use prepared statements
		$query = "SELECT user_id,inst_id,email,password,salt FROM users WHERE user_id = '$user_id';";
		$result = mysql_query($query);
		
		// If there is a username that matches
		if(mysql_num_rows($result) > 0) {
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			
			// Hash the password
			$check_password = hash('sha256', $password . $row['salt']); 
			for($i = 0; $i < 1000; $i++) { 
				$check_password = hash('sha256', $check_password . $row['salt']); 
			} 
				
			if($check_password === $row['password']) { 
				// remove private data
				unset($row['salt']); 
				unset($row['password']); 
				
				echo json_encode(array('result'=>"SUCCESS")); 
			} 
			else $err[]='Incorrect password';
		}
		else $err[]='No record of username';
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'errors'=>$err));
	}
?>