<?php	
if(!isset($_GET['oauth_token'])) {
  echo "No token supplied";
  exit;
 }
	
$err = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['login'])) {
  if(!$_POST['user_id']) {
    $err[] = 'No username supplied';	
    exit;
  }
  if(!count($err)) {
    $user_id = $_POST['user_id'];
    $user_id = strtolower($user_id);
    $password = $_POST['password'];

    include_once("../db_info.php");
    include_once("../database.php");

    /******************* Prepared Statement ******************************/
    $db = new database();
    $db->query = "SELECT user_id, inst_id, password, encrip_salt, user_num
						  FROM users
						  WHERE user_id = ?";
    $db->params = array($user_id);
    $db->type = 's';
    /********************************************************************/
		
    $result = $db->fetch();
			
    // If there is a username that matches
    if(count($result) > 0) {
      $salt = $result[0]['encrip_salt'];
      // Hash the password
      $check_password = hash('sha256', trim($password) . $salt );

      if($check_password != $result[0]['password']) { 
	echo 'Incorrect password';
	exit;
      } 
    }
    else {
      echo 'No record of username';
      exit;
    }

    $db = new database();
    $db->query = "SELECT exp_date FROM institutions WHERE inst_id = ?";
    $inst_id = $result[0]['inst_id'];
    $db->params = array($inst_id);
    $db->type = 's';
    $res2 = $db->fetch();

    session_start();
    $_SESSION['user_num'] = $result[0]['user_num'];

    echo("<html><head><meta http-equiv=\"refresh\" content=\"0;post_login?oauth_token=" . $_GET['oauth_token'] . "\"></head></html>");
    exit(200);
  }
 }

echo(
	'<!DOCTYPE html>
	<html lang="en">
	  <head>
		<meta charset="utf-8">
		<title>ShelvAR Login</title>
		<link href="bootstrap.css" rel="stylesheet">
		<style type="text/css">
			html, body {
				background-color: #eee;
			}
			body {
				padding-top: 40px; 
			}
			
			.container {
				width: 300px;
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

			.login-form {
				margin-left: 65px;
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
		<div class="content">
		  <div class="row">
			<div class="login-form">
			  <h2>Login</h2>
			  <form method="POST" action="?oauth_token='.$_GET["oauth_token"].'">
				<fieldset>
				  <div class="control-group">
					<input type="input" name="user_id" placeholder="Username">
				  </div>
				  <div class="control-group">
					<input type="password" name="password" placeholder="Password">
				  </div>
				  <button class="btn primary" type="submit">Sign in</button>
				</fieldset>
			  </form>
			</div>
		  </div>
		</div>
	  </div>
	</body>
	</html>'
); 

/*
echo(
		"<html>
			<body>
				<img src=\"../ShelvARLogo_Big.png\" />
				<br/>
				Please log in your ShelvAR account.
				<form method='POST' action='?oauth_token=".$_GET['oauth_token']."'>
					Username <input name='user_id' type='input'><br />
					Password <input name='password' type='password'><br />
					<input name='login' type='submit' value='Log in'>
				</form>
			</body>
		</html>"
	); */

?>
