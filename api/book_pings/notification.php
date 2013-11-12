<?php

/**
 * Register notifications with a user or institution. 
 * Parameters must be set via POST.
 */

include_once "../../header_include.php";
include_once "../api_ref_call.php";

if($oauth_user['inst_activated'] != 1){
  exit(json_encode(array('result'=>'ERROR Your institution\'s account has not yet been activated.')));
}
if($oauth_user['inst_has_inv'] != 1){
  exit(json_encode(array('result'=>'ERROR Your institution does not subscribe to ShelvAR\'s inventory service.')));
}
if($oauth_user['exp_date'] < time()){
  exit(json_encode(array('result'=>'ERROR Your institution\'s account has expired. Please inform your administrator.')));
}
if($oauth_user['email_verified'] != 1){
  exit(json_encode(array('result'=>'ERROR You have not yet verified your email address.')));
}

echo "Successful call to notification API\n";