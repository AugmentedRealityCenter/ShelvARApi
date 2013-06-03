<?php
	if(!isset($pending_email)) {
		$err[] = "No email address provided";
	}
	else $to = $pending_email;
	
	$api = "https://api.shelvar.com/";
	if($_SERVER['SERVER_NAME'] == "devapi.shelvar.com") {
		$api = "http://devapi.shelvar.com/";
	}
	
	$subject = "ShelvAR.com Registration";

	$message = "<img src='ShelvARLogo_Big.png' />\r\rWelcome to ShelvAR!\r\rYou, or someone using your email address, has completed registration at ShelvAR.com. You can complete registration by clicking the following link:\r$apiactivate_email.php?$activation_key";

	$headers = 'From: noreply@shelvar.com' . "\r\n" .
    		   'Reply-To: noreply@shelvar.com' . "\r\n" .
    		   'X-Mailer: PHP/' . phpversion();

	if(!mail($to, $subject, $message, $headers)) {
		$err[] = "Error sending confirmation email";
	}

?>