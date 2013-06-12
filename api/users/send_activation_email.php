<?php
	if(!isset($pending_email)) {
		$err[] = "No email address provided";
	}
	else $to = $pending_email;
	if(!isset($name)) {
		$name = "New ShelvAR User";
	}
	$api = "https://api.shelvar.com/";
	if($_SERVER['SERVER_NAME'] == "devapi.shelvar.com") {
		$api = "http://devapi.shelvar.com/";
	}
	
	if($editEmail) {
		$subject = "ShelvAR.com Email Change";
		$message = "<img src='".$api."ShelvARLogo_Big.png' /><br/><br/>Dear ShelvAR User,<br/><br/>This email is to confirm that you have changed your email address. You can confirm this email address by clicking the following link:<br/><br/>".$api."users/activate_email?key=$activation_key&edit=1<br/><br/>If this message was sent as a mistake you can safely ignore it.";
	}
	else {
		$subject = "ShelvAR.com Registration";
		$message = "<img src='".$api."ShelvARLogo_Big.png' /><br/><br/>Dear ".$name.",<br/><br/>Welcome to ShelvAR!<br/><br/>You, or someone using your email address, has registered at ShelvAR.com. You can complete registration by clicking the following link:<br/><br/>".$api."users/activate_email?key=$activation_key<br/><br/>If this message was sent as a mistake you can safely ignore it.";
	}
	
	$headers = 'From: ShelvAR.com <noreply@shelvar.com>' . "\r\n" .
    		   'Reply-To: noreply@shelvar.com' . "\r\n" .
    		   'Content-type: text/html' . "\r\n" .
    		   'X-Mailer: PHP/' . phpversion();

	if(!mail($to, $subject, $message, $headers)) {
		$err[] = "Error sending confirmation email";
	}

?>