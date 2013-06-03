<?php
	if(!isset($pending_email)) {
		if(!isset($_POST['email'])) {
			err[] = "No email address provided";
		}
		else $to = $_POST['email'];
	}
	else $to = $pending_email;
	
	$api = "https://api.shelvar.com/";
	if($_SERVER['HTTP_HOST'] == "dev.shelvar.com") {
		api = "http://devapi.shelvar.com/";
	}
	
	$subject = "ShelvAR.com Registration";

	$message = "Welcome to ShelvAR!\r\r
				You, or someone using your email address, has completed registration at ShelvAR.com. You can complete registration by clicking the following link:\r
				$api?$activationKey";

	$headers = 'From: noreply@shelvar.com' . "\r\n" .
    		   'Reply-To: noreply@shelvar.com' . "\r\n" .
    		   'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $message, $headers);

?>