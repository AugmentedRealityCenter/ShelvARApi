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
			$db->query = "SELECT user_id, inst_id, password, encrip_salt
						  FROM 'users'
						  WHERE 'user_id' = ?";
			$db->params = array($user_id);
			$db->type = 's';
			/********************************************************************/
		
			$result = $db->fetch();
			
			echo $result;
			
			// If there is a username that matches
			if(mysql_num_rows($result) > 0) {
				$row = mysql_fetch_array($result, MYSQL_ASSOC);
				
				// Hash the password
				$check_password = hash('sha256', $password . $row['encrip_salt']); 
				for($i = 0; $i < 1000; $i++) { 
					$check_password = hash('sha256', $check_password . $row['encrip_salt']); 
				} 
					
				if($check_password != $row['password']) { 
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
		$RequestToken->setTokenUserId($row['user_id']);
		
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
