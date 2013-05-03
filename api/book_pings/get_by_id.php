<?php

  /**
   * @file get_by_id.php
   * @author Jake Rego and Bo Brinkman
   * Copyright 2012 by ShelvAR Team.
   * @version September 29, 2012
   * Retrieves a book_ping with the database id that is entered
   * The id entered, according to the db format, should be an 11 character integer
   */

include "../../database.php";
include_once "../../header_include.php";
//include_once "../api_ref_call.php";

$array = array();
$db = new database();
print($db->query . "\n\n");
$db->query = "SELECT * FROM book_pings WHERE id = ? and inst_id = ?";
$_GET['institution']="miamioh";
$db->params = array($_GET['book_ping_id'],$_GET['institution']);
$db->type = 'is';
$the_rec = $db->fetch();

if(count($the_rec)>0){
  unset($the_rec[0]['institution']);
  $arr = array('book_ping' => $the_rec[0], 'result'=>"SUCCESS");
  print json_encode($arr);
} else {
  $arr = array('book_ping' => "", 'result'=>"ERROR No such book ping id.");
  print json_encode($arr);

 }

 ?>