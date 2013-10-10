<?php
include_once ("../../db_info.php");
include_once "../../database.php";
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
if($oauth_user['can_read_inv'] != 1){
  exit(json_encode(array('result'=>'ERROR No permission to read data.')));
 }
 
if(stripos($oauth_user['scope'],"invread") === false) {
	exit(json_encode(array('result'=>'ERROR No permission to read data.')));
}

$cond = false;
$query = "SELECT * FROM book_pings";
$qArray = array();
$paramsList = array();
$types = array(
		0 => "s",
		1 => "ss",
		2 => "sss",
		3 => "ssss",
		4 => "sssss");
$numParams = -1;

if(isset($_GET["book_tag"])){
	$qArray[] = "book_tag = ?";
	$paramsList[] = urldecode($_GET["book_tag"]);
	$cond = true;
	$numParams ++;
}
if(isset($_GET["call_number"])){
	$qArray[] = "book_call = ?";
	$paramsList[] = urldecode($_GET["call_number"]);
	$cond = true;
	$numParams ++;
}
if(isset($_GET["start_date"])){
	$qArray[] = "ping_time >= ?";
	$paramsList[] = urldecode($_GET["start_date"]); 
	$cond = true;
	$numParams ++;
}
if(isset($_GET["end_date"])){
	$qArray[] = "ping_time < ?";
	$paramsList[] = urldecode($_GET["end_date"]);
	$cond = true;
	$numParams ++;
}
if(isset($inst_id)){
	$qArray[] = "inst_id = ?";
	$paramsList[] = urldecode($inst_id);
	$cond = false;
	$numParams ++;
}

if (!$cond) {
	$db = new database();
	$db->query = $query;
	$db->params = $paramsList;
	$db->type = "";
}
else {
	$query = $query . " WHERE ";
	
	$query .= implode(" AND ", $qArray);
	
	$db = new database();
	$db->query = $query;
	$db->params = $paramsList;
	$db->type = $types[$numParams];
}

/*
$db = new database();
$db->query = "SELECT * FROM book_pings ".
		"WHERE book_tag = ? AND book_call = ? AND ping_time >= ? ".
		"AND ping_time < ? AND inst_id = ? ";
$db->params = array(urldecode($_GET["book_tag"]), urldecode($_GET["call_number"]), urldecode($_GET["start_date"]),
				urldecode($_GET["end_date"]), urldecode($inst_id));
$db->type = "sssss";
*/

$result = $db->fetch();

$count = count($result);

print(json_encode(array('book_ping_count'=>$count,'result'=>"SUCCESS")));

//////////////////////////////////////////////////////
/*
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

	
$con = mysql_connect($sql_server,$sql_user,$sql_password);

if (!$con)
	die('Could not connect: ' . mysql_error());
	
mysql_select_db($sql_database, $con);

error_log(print_r($sql,TRUE));

$result = mysql_query($sql);
$count = 0;
while($row = mysql_fetch_array($result))
{
	$count++;
}

print(json_encode(array('book_ping_count'=>$count,'result'=>"SUCCESS")));
*/
	
?>