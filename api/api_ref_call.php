<?php

session_start();

error_log("GLOBALS: " . print_r($GLOBALS,TRUE));

require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/AutoLoader.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/OAuthProviderWrapper.php');

new AutoLoader();

unset($oauth_user);

error_log("Before checkOAuthRequest");

$Provider 	= new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_VERIFY	);
$response 	= $Provider->checkOAuthRequest();

foreach($_SERVER as $key => $value){
  if(strpos($key,"REDIRECT_") !== FALSE && strpos($key,"REDIRECT_STATUS") === FALSE){
    $newkey = substr($key,9);
    $_GET[$newkey] = $value;
    error_log("added $newkey");
  }
}
error_log("After checkOAuthRequest. " . print_r($response,TRUE));

if(is_bool($response) && $response == true){
  //Do nothing
 } else if(isset($_SERVER['HTTP_REFERER']) && stripos($_SERVER['HTTP_REFERER'],"api.shelvar.com") !== false){
  $user_id = "sandy";
  try {
    $db = new database();
    $db->query = "SELECT inst_id, name, user_id, is_admin, email_verified, can_submit_inv, can_read_inv, user_num FROM users WHERE user_id = ?";
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
    $exp_date = $Provider->getAccessTokenDate();

    $db = new database();
    $db->query = "SELECT inst_id, name, user_id, is_admin, email_verified, can_submit_inv, can_read_inv, user_num FROM users WHERE user_num = ?";
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

if(isset($oauth_user)){
  $db2 = new database();
  $db2->query = "SELECT exp_date, has_inv, is_activated, name FROM institutions WHERE inst_id = ?";
  $inst_id2 = $oauth_user['inst_id'];
  $db2->params = array($inst_id2);
  $db2->type = "s";

  $ret = $db2->fetch();
  $oauth_user['exp_date'] = "0";
  if(count($ret)>0){
    $date = new DateTime($ret[0]['exp_date'], new DateTimeZone("UTC"));
    $oauth_user['exp_date'] = "" . $date->getTimestamp();
    $oauth_user['inst_has_inv'] = $ret[0]['has_inv'];
    $oauth_user['inst_activated'] = $ret[0]['is_activated'];
    $oauth_user['inst_name'] = $ret[0]['name'];
  } else {
    $oauth_user['exp_date'] = "0";
    $oauth_user['inst_has_inv'] = "0";
    $oauth_user['inst_activated'] = "0";
    $oauth_user['inst_name'] = "ERROR";
    exit(json_encode(array("result"=>"ERROR Could not find your institution.")));
  }
 }
?>
