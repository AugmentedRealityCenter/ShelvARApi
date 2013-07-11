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
		<link href="../bootstrap.css" rel="stylesheet">
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

			select,
			textarea,
			input[type="text"],
			input[type="password"],
			input[type="datetime"],
			input[type="datetime-local"],
			input[type="date"],
			input[type="month"],
			input[type="time"],
			input[type="week"],
			input[type="number"],
			input[type="email"],
			input[type="url"],
			input[type="search"],
			input[type="tel"],
			input[type="color"],
			.uneditable-input {
				display: inline-block;
				height: 20px;
				padding: 4px 6px;
				margin-bottom: 9px;
				font-size: 14px;
				line-height: 20px;
				color: #555555;
				-webkit-border-radius: 3px;
				-moz-border-radius: 3px;
				border-radius: 3px;
			}
			
			textarea:focus,
			input[type="text"]:focus,
			input[type="password"]:focus,
			input[type="datetime"]:focus,
			input[type="datetime-local"]:focus,
			input[type="date"]:focus,
			input[type="month"]:focus,
			input[type="time"]:focus,
			input[type="week"]:focus,
			input[type="number"]:focus,
			input[type="email"]:focus,
			input[type="url"]:focus,
			input[type="search"]:focus,
			input[type="tel"]:focus,
			input[type="color"]:focus,
			.uneditable-input:focus {
				border-color: rgba(82, 168, 236, 0.8);
				outline: 0;
				outline: thin dotted \9;
				/* IE6-9 */

				-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
				-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
				box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(82, 168, 236, 0.6);
			}
			
			.btn {
			  display: inline-block;
			  *display: inline;
			  padding: 4px 14px;
			  margin-bottom: 0;
			  *margin-left: .3em;
			  font-size: 14px;
			  line-height: 20px;
			  *line-height: 20px;
			  color: #333333;
			  text-align: center;
			  text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
			  vertical-align: middle;
			  cursor: pointer;
			  background-color: #f5f5f5;
			  *background-color: #e6e6e6;
			  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
			  background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6);
			  background-image: -o-linear-gradient(top, #ffffff, #e6e6e6);
			  background-image: linear-gradient(to bottom, #ffffff, #e6e6e6);
			  background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6);
			  background-repeat: repeat-x;
			  border: 1px solid #bbbbbb;
			  *border: 0;
			  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
			  border-color: #e6e6e6 #e6e6e6 #bfbfbf;
			  border-bottom-color: #a2a2a2;
			  -webkit-border-radius: 4px;
				 -moz-border-radius: 4px;
					  border-radius: 4px;
			  filter: progid:dximagetransform.microsoft.gradient(startColorstr="#ffffffff", endColorstr="#ffe6e6e6", GradientType=0);
			  filter: progid:dximagetransform.microsoft.gradient(enabled=false);
			  *zoom: 1;
			  -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
				 -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
					  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
			}
			
			.btn-primary {
			  color: #ffffff;
			  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
			  background-color: #006dcc;
			  *background-color: #0044cc;
			  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc));
			  background-image: -webkit-linear-gradient(top, #0088cc, #0044cc);
			  background-image: -o-linear-gradient(top, #0088cc, #0044cc);
			  background-image: linear-gradient(to bottom, #0088cc, #0044cc);
			  background-image: -moz-linear-gradient(top, #0088cc, #0044cc);
			  background-repeat: repeat-x;
			  border-color: #0044cc #0044cc #002a80;
			  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
			  filter: progid:dximagetransform.microsoft.gradient(startColorstr="#ff0088cc", endColorstr="#ff0044cc", GradientType=0);
			  filter: progid:dximagetransform.microsoft.gradient(enabled=false);
			}
			
			.control-group {
				margin-bottom: 10px;
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
				  <button class="btn-primary" type="submit">Sign in</button>
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
