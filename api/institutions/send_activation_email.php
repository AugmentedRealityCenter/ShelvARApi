<?php
	if(!isset($pending_email)) {
		$err[] = "No email address provided";
	} else {
		$to = $pending_email;
	}
	
	if(!isset($name)) {
		$name = $previous_name;
	}
	$api = "https://api.shelvar.com/";
	if($_SERVER['SERVER_NAME'] == "devapi.shelvar.com") {
		$api = "http://devapi.shelvar.com/";
	}
	
	$headers = 'From: ShelvAR.com <noreply@shelvar.com>' . "\r\n" .
		   'Reply-To: noreply@shelvar.com' . "\r\n" .
		   'Content-type: text/html' . "\r\n" .
		   'X-Mailer: PHP/' . phpversion();
	
	if(isset($editAdmin)) 
	{
		//$inst_name = '';
		
		$root = $_SERVER['DOCUMENT_ROOT']."/";
		include_once($root."db_info.php");
		include_once($root."database.php");
			
		$db = new database();
		$db->query = "SELECT name FROM institutions WHERE inst_id = ?";
		$db->params = array($inst_id);
		$db->type = 's';	
		$result = $db->fetch();
		
		$username = getUsername($inst_id, $previous_admin);	
		$subject = "ShelvAR.com Admin Change";
		$message = "<img src='".$api."ShelvARLogo_Big.png' /><br/><br/>Dear " . $username . ",<br/><br/>This email is to confirm that you are no longer the administrator of the ShelvAR account for ".$result[0]['name'].".<br/><br/>If you did not intend to change the admin of this institution, please contact support@shelvar.com.";
		if(!mail($previous_admin, $subject, $message, $headers)) {
			$err[] = "Error sending confirmation email";
		}
		
		$username = getUsername($inst_id, $to);	
        $message = "<img src='".$api."ShelvARLogo_Big.png' /><br/><br/>Dear " . $username . ",<br/><br/>This email is to confirm that you are the new admin of this institution. You can confirm this email address by clicking the following link:<br/><br/>".$api."institutions/activate_inst?inst_key=$activation_key&edit=1<br/><br/>If this message was sent as a mistake you can safely ignore it.";
	}
	else {
					
		$subject = "ShelvAR.com Institution Registration";
		$message = "<img src='".$api."ShelvARLogo_Big.png' /><br/><br/>Dear ".$admin_contact.",<br/><br/>Welcome to ShelvAR!<br/><br/>You, or someone using your email address, has registered this institution at ShelvAR.com. <b>ShelvAR is still in its alpha testing phase meaning we are not and won't be ready for commercial use any time in the near future.</b> Use at your own discretion. <br/><br/>You can complete registration by clicking the following link:<br/><br/>".$api."institutions/activate_inst?inst_key=$activation_key<br/><br/>If this message was sent as a mistake you can safely ignore it.";
	}

	if(!mail($to, $subject, $message, $headers)) {
		$err[] = "Error sending confirmation email";
	}

	/******Functions defined below******/
	
	function getUsername($p_inst_id, $p_email_to){
		$db = new database();
		$db->query = "SELECT name FROM users WHERE inst_id = ? AND email = ?";
		$db->params = array($p_inst_id, $p_email_to);
		$db->type = 'ss';
		$username = $db->fetch();
		
		return $username[0]['name'];
	}
?>
