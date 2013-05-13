<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/AutoLoader.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/OAuthProviderWrapper.php');

new AutoLoader();

unset($oauth_user);

$Provider 	= new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_VERIFY	);
$response 	= $Provider->checkOAuthRequest();

if(is_bool($response) && $response == true){
  //Do nothing
 } else if(isset($_SERVER['HTTP_REFERER']) && stripos($_SERVER['HTTP_REFERER'],"api.shelvar.com") !== false){
  $user_id = "sandy";
  try {
    $db = new database();
    $db->query = "SELECT * FROM users WHERE user_id = ?";
    $db->params = array($user_id);
    $db->type = "s";
    $the_rec = $db->fetch();

    if(count($the_rec) > 0){
      $oauth_user = $the_rec[0];
      $user_id = $oauth_user['user_id'];
      $inst_id = $oauth_user['inst_id'];
      http_response_code(200);
    } else {
      $arr = array('result' => "ERROR Sandbox user not found.");
      exit(json_encode($arr));
    }

  } catch (Exception $Exception) {
    exit(json_encode(array("result"=>"ERROR Could not find Sandbox user.")));
  }
  
 } else if (!is_bool($response) || $response != true) {
  exit(json_encode(array("result"=>"ERROR. OAuth token missing or invalid.")));
}

if(!isset($oauth_user)){
  try {
    $user_num = $Provider->getUserId();
    $db = new database();
    $db->query = "SELECT * FROM users WHERE user_num = ?";
    $db->params = array($user_num);
    $db->type = "i";
    $the_rec = $db->fetch();

    if(count($the_rec) > 0){
      $oauth_user = $the_rec[0];
      $user_id = $oauth_user['user_id'];
      $inst_id = $oauth_user['inst_id'];
    } else {
      $arr = array('result' => "ERROR User not found.");
      exit(json_encode($arr));
    }

  } catch (Exception $Exception) {
    exit(json_encode(array("result"=>"ERROR OAuth token missing or invalid.")));
  }
 }

?>
