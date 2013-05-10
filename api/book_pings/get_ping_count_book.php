<?php
include_once ("../../db_info.php");
include_once "../../header_include.php";

include_once "../api_ref_call.php";
if($oauth_user['can_read_data'] != 1){
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
    $qArray[] = "institution = '" . urldecode($inst_id) . "'"; 
	$cond = true;
} 

if($cond)
	$sql = $sql . " WHERE ";

$sql .= implode(" AND ", $qArray); 

	
$con = mysql_connect($sql_server,$sql_user,$sql_password);

if (!$con)
	die('Could not connect: ' . mysql_error());
	
mysql_select_db($sql_database, $con);

$result = mysql_query($sql);
$count = 0;
while($row = mysql_fetch_array($result))
{
	$count++;
}

print(json_encode(array('book_ping_count'=>$count,'result'=>"SUCCESS")));
	
?>