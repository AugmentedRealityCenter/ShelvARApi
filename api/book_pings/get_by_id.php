<?php

  /**
   * @file get_by_id.php
   * @author Jake Rego and Bo Brinkman
   * Copyright 2012 by ShelvAR Team.
   * @version September 29, 2012
   * Retrieves a book_ping with the database id that is entered
   * The id entered, according to the db format, should be an 11 character integer
   */
$root = $_SERVER['DOCUMENT_ROOT']."/";
include_once $root."database.php";
include_once $root."header_include.php";
include_once $root."api/api_ref_call.php";

$oauth_user = get_oauth();
$inst_id = $oauth_user['inst_id'];
$user_id = $oauth_user['user_id'];

if($oauth_user['inst_activated'] != 1){
  exit(json_encode(array('result'=>'ERROR', 'message'=>'Your institution\'s account has not yet been activated.')));
 }
if($oauth_user['inst_has_inv'] != 1){
  exit(json_encode(array('result'=>'ERROR', 'message'=>'Your institution does not subscribe to ShelvAR\'s inventory service.')));
 }
if($oauth_user['exp_date'] < time()){
  exit(json_encode(array('result'=>'ERROR', 'message'=>'Your institution\'s account has expired. Please inform your administrator.')));
 }
if($oauth_user['email_verified'] != 1){
  exit(json_encode(array('result'=>'ERROR', 'message'=>'You have not yet verified your email address.')));
 }
if($oauth_user['can_read_inv'] != 1){
  exit(json_encode(array('result'=>'ERROR', 'message'=>'No permission to read data.')));
 }
if(stripos($oauth_user['scope'],"invread") === false) {
  exit(json_encode(array('result'=>'ERROR', 'message'=>'No permission to read data.')));
}

$array = array();
$db = new database();
print($db->query . "\n\n");
$db->query = "SELECT * FROM book_pings WHERE id = ? and inst_id = ?";
$_GET['institution']=$inst_id;
if (!isset($_GET['book_ping_id'])) {
    exit(json_encode(array('result'=>'ERROR',
       'message'=>'Please specify book ping id.')));
}
$db->params = array($_GET['book_ping_id'],$_GET['institution']);
$db->type = 'is';
$the_rec = $db->fetch();

if(count($the_rec)>0){
  unset($the_rec[0]['institution']);
  $arr = array('book_ping' => $the_rec[0], 'result'=>"SUCCESS");
  print json_encode($arr);
} else {
  $arr = array('book_ping' => "", 'result'=>'SUCCESS');//"ERROR No such book ping id.");
  print json_encode($arr);

 }

 ?>
