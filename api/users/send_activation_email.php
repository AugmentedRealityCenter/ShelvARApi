<?php
	if(!isset($pending_email)) {
		$err[] = "No email address provided";
	}
	if(!isset($name)) {
		$name = "New ShelvAR User";
	}
	else $to = $pending_email;
	
	$api = "https://api.shelvar.com/";
	if($_SERVER['SERVER_NAME'] == "devapi.shelvar.com") {
		$api = "http://devapi.shelvar.com/";
	}
	
	$subject = "ShelvAR.com Registration";

	$message = "<img src='".$api."ShelvARLogo_Big.png' /><br/><br/>Dear ".$name.",<br/>Welcome to ShelvAR!<br/><br/>You, or someone using your email address, has registered at ShelvAR.com. You can complete registration by clicking the following link:<br/><br/>".$api."users/activate_email?$activation_key<br/><br/>If this message was sent as a mistake you can safely ignore it.";

	$headers = 'From: ShelvAR.com <noreply@shelvar.com>' . "\r\n" .
    		   'Reply-To: noreply@shelvar.com' . "\r\n" .
    		   'Content-type: text/html' . "\r\n" .
    		   'X-Mailer: PHP/' . phpversion();

	if(!mail($to, $subject, $message, $headers)) {
		$err[] = "Error sending confirmation email";
	}

?>