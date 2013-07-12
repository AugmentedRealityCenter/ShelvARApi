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
  $RequestToken = OAuthRequestTokenModel::loadFromToken($_GET['oauth_token'], 
		  Configuration::getDataStore());
} catch (DataStoreReadException $Exception) {
  echo $Exception->getMessage();
  exit;
}

$err = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['allow'])) {
  session_start();
		
  // get verification code
  $verificationCode = OAuthProviderWrapper::generateToken();
  $RequestToken->setTokenVerificationCode($verificationCode);
  $RequestToken->setTokenUserId($_SESSION['user_num']);
		
  unset($_SESSION['user_num']);

  try {
    $RequestToken->save();
  } catch (DataStoreUpdateException $Exception) {
    echo $Exception->getMessage();
    exit;
  }

  $verification_url = $RequestToken->getTokenCallback() . '?oauth_token='
	  . $RequestToken->getToken() . '&oauth_verifier=' . 
    $verificationCode;

  echo("<html><head><meta http-equiv=\"refresh\" content=\"0;$verification_url\"></head></html>");
  exit(200);
 } 
 else if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['deny'])) {
   // User has denied access
   try {
     $RequestToken->delete();
     echo("<html><head><meta http-equiv=\"refresh\" content=\"0;$verification_url\"></head></html>");
     exit(200);
   } catch (DataStoreDeleteException $Exception) {
     echo $Exception->getMessage();
     exit;
   }
 }
 
if(stripos($RequestToken->getTokenScope(),"invread") !== false) {
	$scope .= '<dd><p>Read inventory on your behalf</p>';
}
if(stripos($RequestToken->getTokenScope(),"invsubmit") !== false) {
	$scope .= '<dd><p>Submit inventory on your behalf</p>';
}
if(stripos($RequestToken->getTokenScope(),"contactread") !== false) {
	$scope .= '<dd><p>Read your contact information</p>';
}
if(stripos($RequestToken->getTokenScope(),"acctmod") !== false) {
	$scope .= '<dd><p>Modify account information on your behalf</p>';
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
			  <h3>An application is requesting access to your ShelvAR account</h3>
			  <p>This application <b>will be able to</b>: </p>
			  '.$scope.'
			  <form method="POST" action="?oauth_token='.$RequestToken->getToken().'">
				<fieldset>
				  <button name="allow" class="btn" value="Allow" type="submit">Allow</button>
				  <button name="deny" class="btn btn-danger" value="Deny" type="submit">Deny</button>
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

echo("<html><body>"
     ."<img src=\"../ShelvARLogo_Big.png\" /><br/>"
     ."This app is requesting access to your ShelvAR account.<br/>"
     ."It will be allowed to <b>" . $RequestToken->getTokenScope() . "</b> on your behalf.<br/>"
     ."<form method='POST' action='?oauth_token=" .  
     $RequestToken->getToken(). "'>" .
	"<input name='allow' type='submit' value='Allow'>
	<input name='deny' type='submit' value='Deny'>
      </form></body></html>"); */

?>
