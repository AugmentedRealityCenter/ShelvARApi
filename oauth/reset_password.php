<?php

$err = array();	
	
if (!count($err) && $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['login'])) {

	if (isset($_POST['Username'])) // Handle the form.
	{
		if (empty($_POST['user_id'])) // Validate the user.
		{
			$err[] = 'No username supplied';
		}
			
		if(!count($err)) 
		{
			include_once("../db_info.php");
			include_once("../database.php");
			
			$db = new database();
			$db->query = "SELECT user_id From users WHERE user_id = ?";
			$db->params = array($user_id);
			$db->type = 's';
			
			$result = $db->fetch();
			
			//If there is a username that matches
			if(count($result) > 0){
				$p = substr ( md5(uniqid(rand(),1)), 3, 10);
				
				$db = new database();
				$db->query = "UPDATE users SET pass=SHA('$p') WHERE user_id = ?";
				$db->params = array($user_id);
				$db->type = 's';
				$res2 = $db->fetch();
				
				if (count($res2) > 0)  // If it ran ok
				{
					//Send an email
					$subject = "Your temporary password";
					$message = "<img src='".$api."ShelvARLogo_Big.png' /><br/><br/>Dear <br/>".$user_id."<br/>Your password to log into ShelvAR has been temporarily changed to ". $p. 
																										"Please log in using this password and your username. At that time you may change your password to something more familiar.". "<br/>";
					
					$headers = 'From: ShelvAR.com <noreply@shelvar.com>' . "\r\n" .
							   'Reply-To: noreply@shelvar.com' . "\r\n" .
							   'Content-type: text/html' . "\r\n" .
								'X-Mailer: PHP/' . phpversion();
					
					if(!mail ($_POST['email'], $subject, $message, $headers)){
						$err[] = "Error sending confirmation email";
					}
					
					echo '<h3>Your password has been changed. You will receive the new, temporary password at the email address with which you registered. Once you have logged in with this password, you may change it by clicking on the \“Accounts and then User\” link.</h3>';
				
					session_start();
					$_SESSION['user_id'] = $result[0]['user_id'];
				
					echo("<html><head><meta http-equiv=\"refresh\" content=\"0;post_login?oauth_token=" . $_GET['oauth_token'] . "\"></head></html>");
					exit(200);
				}
				else  //Failed the Validation test
				{
					$err[] = 'Please try again!';
				}
			}
		}
	}
}


echo(
	'<!DOCTYPE html>
	<html lang="en">
	  <head>
		<meta charset="utf-8">
		<title>ShelvAR Forgot Password</title>
		<link href="bootstrap.css" rel="stylesheet">
		<style type="text/css">
			html, body {
				background-color: #C60C30;
			}
			body {
				padding-top: 40px; 
			}
			
			.container {
				width: 600px;
			}

			.container > .content {
				background-color: #fff;
				padding: 20px;
				margin: 0 -20px; 
				-webkit-border-radius: 10px 10px 10px 10px;
				   -moz-border-radius: 10px 10px 10px 10px;
						border-radius: 10px 10px 10px 10px;
				-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.15);
				   -moz-box-shadow: 0 1px 2px rgba(0,0,0,.15);
						box-shadow: 0 1px 2px rgba(0,0,0,.15);
			}
			
			img {
				max-width: 100%;
				width: auto	9;
				height: auto;
				border: 0;
				-ms-interpolation-mode: bicubic;
				margin-left: -3%;
			}

			.password-form {
				margin-left: 25px;
			}

			legend {
				margin-right: -50px;
				font-weight: bold;
				color: #404040;
			}
		</style>
	</head>
	<body>
	  <div class="container">
		<img src="../ShelvARLogo_Big.png" width="200"/>
		<br/>
		<br/>
		<div class="content">
		  <div class="row">
			<div class="password-form">
			  <h3>Please enter user name and select forgot password</h3>
			  <form method="POST" action="">
				<fieldset>
				  <div class="control-group">
					<input type="text" class="input-xlarge" name="user_id" placeholder="Username">
				  </div>
				  <input type="hidden" id="login" name="login" value="login" />
				  <button class="btn btn-primary" type="submit">Forgot Password</button>
				</fieldset>
			  </form>
			</div> <!-- form -->
		  </div> <!-- row -->');

if(count($err)){
  echo('<div class="row"><div class="password-form">');
  echo('<h3>Errors</h3>');
  //print_r($err);
  foreach($err as $key => $value){
    echo("<p>" . $value . "</p>");
  }
  echo('</div></div>');
 }

echo ('
		</div> <!-- content -->
	  </div> <!-- container -->
	</body>
	</html>'
); 	
?>	