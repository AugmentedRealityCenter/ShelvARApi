<?php

$err = array();	
if(!isset($_GET['oauth_token'])) {
  $err[] = "Application is broken: No token supplied";
 }
	
if (!count($err) && $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['login'])) {
  if(!$_POST['user_id']) {
    $err[] = 'No username supplied';	
  }
  if(!count($err)) {
    $user_id = $_POST['user_id'];
    $user_id = strtolower($user_id);
    $password = $_POST['password'];

    $root = $_SERVER['DOCUMENT_ROOT']."/";
    include_once($root."db_info.php");
    include_once($root."database.php");

    $db = new database();
    $db->query = "SELECT user_id, inst_id, password, encrip_salt, user_num
						  FROM users
						  WHERE user_id = ?";
    $db->params = array($user_id);
    $db->type = 's';
		
    $result = $db->fetch();
			
    // If there is a username that matches
    if(count($result) > 0) {
      $salt = $result[0]['encrip_salt'];
      // Hash the password
      $check_password = hash('sha256', trim($password) . $salt );

      if($check_password != $result[0]['password']) { 
	$err[] = 'Incorrect username or password';
      } 
    }
    else {
      $err[] = 'Incorrect username or password';
    }

    if(!count($err)){
      $db = new database();
      $db->query = "SELECT exp_date FROM institutions WHERE inst_id = ?";
      $inst_id = $result[0]['inst_id'];
      $db->params = array($inst_id);
      $db->type = 's';
      $res2 = $db->fetch();

      session_start();
      $_SESSION['user_num'] = $result[0]['user_num'];

      error_log('before manual redirect');
      echo("<html><head><meta http-equiv=\"refresh\" content=\"0;post_login?oauth_token=" . $_GET['oauth_token'] . "\"></head></html>");
      exit(200);
    }
  }
 }

echo(
	'<!DOCTYPE html>
	<html lang="en">
	  <head>
		<meta charset="utf-8">
		<title>ShelvAR Log in</title>
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

			.login-form {
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
			<div class="login-form">
			  <h3>An application would like to connect to your ShelvAR account</h3>
			  <p>Please log in to access this application</p>
			  <form method="POST" action="?oauth_token='.$_GET["oauth_token"].'">
				<fieldset>
				  <div class="control-group">
					<input type="text" class="input-xlarge" name="user_id" placeholder="Username">
				  </div>
				  <div class="control-group">
					<input type="password" class="input-xlarge" name="password" placeholder="Password">
				  </div>
				  <input type="hidden" id="login" name="login" value="login" />
				  <button class="btn btn-primary" type="submit">Log in</button>
				  <a href="reset_password.php" target="_blank">Forgot Password?</a>
				</fieldset>
			  </form>
			</div> <!-- form -->
		  </div> <!-- row -->');

if(count($err)){
  echo('<div class="row"><div class="login-form">');
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
