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
   } catch (DataStoreDeleteException $Exception) {
     echo $Exception->getMessage();
     exit;
   }
 }

echo("<html><body>"
     ."<img src=\"../ShelvARLogo_Big.png\" /><br/>"
     ."This app is requesting access to your ShelvAR account.<br/>"
     ."It will be allowed to <b>" . $RequestToken->getTokenScope() . "</b> on your behalf.<br/>"
     ."<form method='POST' action='?oauth_token=" .  
     $RequestToken->getToken(). "'>" .
	"<input name='allow' type='submit' value='Allow'>
	<input name='deny' type='submit' value='Deny'>
      </form></body></html>");

?>
