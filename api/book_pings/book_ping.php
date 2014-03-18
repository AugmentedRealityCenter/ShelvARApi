<?php

/**
 * @file
 * Submit a list of "book pings" to the database. Data should be provided by POST, and conform with the
 * requirements listed in book_ping_lib.php.
 *
 * @sa do_book_ping
 *
 * Copyright 2011 by ShelvAR Team. 
 * 
 * @version Sept 13, 2011
 * @author Bo Brinkman
 */

include_once "book_ping_lib.php";
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
if($oauth_user['can_submit_inv'] != 1){
  exit(json_encode(array('result'=>'ERROR No permission to submit data.')));
 }
if(stripos($oauth_user['scope'],"invsubmit") === false) {
	exit(json_encode(array('result'=>'ERROR No permission to submit data.')));
}


/** @cond */
$ret; //!< return value from function call that does most of the work
$input="[]";
if(isset($_POST["book_pings"])){
  $input = stripslashes($_POST["book_pings"]);
 }
$ret = do_book_ping($input,$inst_id,$user_id);
Print json_encode(array('result'=>$ret)); 
//var_dump($_POST);
//var_dump($_GET);
/** @endcond */  
?>
