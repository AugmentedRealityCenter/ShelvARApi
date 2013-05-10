<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/AutoLoader.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/OAuthProviderWrapper.php');

new AutoLoader();

$Provider 	= new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_VERIFY	);
$response 	= $Provider->checkOAuthRequest();
if (!is_bool($response) || $response != true) {
  exit(json_encode(array("result"=>"ERROR. OAuth token missing or invalid.")));
}

try {
  $user_num = $Provider->getUserId();
  $db = new database();
  $db->query = "SELECT * FROM users WHERE user_num = ?";
  $db->params = array($user_num);
  $db->type = "i";
  $the_rec = $db->fetch();

  if(count($the_rec) > 0){
    $user = $the_rect[0];
  } else {
    $arr = array('result' => "ERROR User not found.");
    exit(json_encode($arr));
  }

} catch (Exception $Exception) {
  exit(json_encode(array("result"=>"ERROR OAuth token missing or invalid.")));
}

?>
