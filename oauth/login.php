<?php	
	if(!isset($_GET['oauth_token'])) {
		echo "No token supplied";
		exit;
	}
	
	include($_SERVER['DOCUMENT_ROOT'] . '/oauth/exceptions/datastore/DataStoreReadException.php');
	include($_SERVER['DOCUMENT_ROOT'] . '/oauth/exceptions/datastore/DataStoreUpdateException.php');
	include($_SERVER['DOCUMENT_ROOT'] . '/oauth/exceptions/datastore/DataStoreDeleteException.php');
	
	require_once $_SERVER['DOCUMENT_ROOT'] . "/oauth/AutoLoader.php";
	new AutoLoader();
	
	try {
		// load REQUEST TOKEN from datastore
		$RequestToken = OAuthRequestTokenModel::loadFromToken($_GET['oauth_token'], Configuration::getDataStore());
		 
	} catch (DataStoreReadException $Exception) {
		echo $Exception->getMessage();
		exit;
	}
	$err = array();
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['allow'])) {
		if(!$_POST['user_id']) {
			$err[] = 'No username supplied';	
			exit;
		}/*
		if(!$_POST['password']) {
			echo 'No password supplied';
			exit;
		}*/
		if(!count($err)) {
			$user_id = $_POST['user_id'];
			$password = $_POST['password'];

			/******************* Prepared Statement ******************************/
			$db = new database();
			$db->query = "SELECT user_id, inst_id, password, encrip_salt, user_num
						  FROM users
						  WHERE user_id = ?";
			$db->params = array($user_id);
			$db->type = 's';
			/********************************************************************/
		
			$result = $db->fetch();
			
			print_r($result);
			
			// If there is a username that matches
			if(count($result) > 0) {
				//$row = mysql_fetch_array($result, MYSQL_ASSOC);
				$salt = $result[0]['encrip_salt'];
				// Hash the password
				$check_password = hash('sha256', trim($password) . $salt ); //$result['encrip_salt'] ); 
				//for($i = 0; $i < 1000; $i++) { 
				//$check_password = hash('sha256', $check_password . $salt);//$result['encrip_salt']); 
					//} 
				echo 'CHECK PASS: ' . $check_password;	
				if($check_password != $result[0]['password']) { 
					 echo 'Incorrect password';
					 exit;
				} 
			}
			else {
				echo 'No record of username';
				exit;
			}
		}
		
		// get verification code
		$verificationCode = OAuthProviderWrapper::generateToken();
		$RequestToken->setTokenVerificationCode($verificationCode);
		$RequestToken->setTokenUserId($result[0]['user_num']);
		
		try {
			$RequestToken->save();
		} catch (DataStoreUpdateException $Exception) {
			echo $Exception->getMessage();
			exit;
		}

	header( 'location: ' . $RequestToken->getTokenCallback() . '?oauth_token=' . $RequestToken->getToken() . '&oauth_verifier=' . $verificationCode );

	} 
	else if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['allow'])) {
		// User has denied access
		try {
			$RequestToken->delete();
		} catch (DataStoreDeleteException $Exception) {
			echo $Exception->getMessage();
			exit;
		}
	}

	echo "<form method='POST' action='?oauth_token=" .  $RequestToken->getToken(). "'>
			Username <input name='user_id' type='input'><br />
			Password <input name='password' type='password'><br />
			<input name='allow' type='submit' value='Allow'>
			<input name='deny' type='submit' value='Deny'>
		</form>";
?>
