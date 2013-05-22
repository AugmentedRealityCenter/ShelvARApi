<?php
include_once ("../../db_info.php");
include_once "../../header_include.php";

include_once "../api_ref_call.php";
if($oauth_user['can_read_inv'] != 1){
  exit(json_encode(array('result'=>'ERROR No permission to read data.')));
 }
 
$qArray = array();


$sql = "SELECT * FROM book_pings";
$result;
$cond = false;

if(isset($_GET["book_tag"])){ 
	$qArray[] = "book_tag = '" . urldecode($_GET["book_tag"]) . "'"; 
	$cond = true;
} 
if(isset($_GET["call_number"])){ 
    $qArray[] = "book_call = '" . urldecode($_GET["call_number"]) . "'"; 
	$cond = true;
} 
if(isset($_GET["start_date"])){ 
	$qArray[] = "ping_time >= '" . urldecode($_GET["start_date"]) . "'"; 
	$cond = true;
} 
if(isset($_GET["end_date"])){ 
    $qArray[] = "ping_time < '" . urldecode($_GET["end_date"]) . "'"; 
	$cond = true;
} 
if(isset($inst_id)){ 
    $qArray[] = "inst_id = '" . urldecode($inst_id) . "'"; 
	$cond = true;
} 

if($cond)
	$sql = $sql . " WHERE ";

$sql .= implode(" AND ", $qArray); 

$sql .= " ORDER BY id DESC";

$lim = "20";
if(isset($_GET["num_limit"]) && (is_int($_GET["num_limit"]) || ctype_digit($_GET["num_limit"]))){
  $lim = $_GET["num_limit"];
 }
$sql .= " LIMIT 0,".$lim;
	
$con = mysql_connect($sql_server,$sql_user,$sql_password);

if (!$con){
  print json_encode(array("book_pings"=>array(),"result"=>'ERROR Could not connect: ' . mysql_error()));
 } else {
	
  mysql_select_db($sql_database, $con);
  
  $result = mysql_query($sql);
  $count = 0;
  
  $ret = array();
  
  while($row = mysql_fetch_array($result))
    {
      $row['book_ping_id'] = $row['id'];
      unset($row['id']);
      //unset($row['institution']);
      unset($row[0]);
      unset($row[1]);
      unset($row[2]);
      unset($row[3]);
      unset($row[4]);
      unset($row[5]);
      unset($row[6]);
      unset($row[7]);
      unset($row[8]);
      unset($row[9]);
      $ret[] = $row;
    }
  
  print(json_encode(array("book_pings"=>$ret,"result"=>"SUCCESS")));
 }
?>
