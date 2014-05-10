<?php
$root = $_SERVER['DOCUMENT_ROOT']."/";
include_once $root."db_info.php";
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

$cond = false;
$limSet = false;
$query = "SELECT * FROM book_pings ";
$qArray = array();
$paramsList = array();
$types = array(
		0 => "s",
		1 => "ss",
		2 => "sss",
		3 => "ssss",
		4 => "sssss",
		5 => "ssssss");
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
	$cond = true;
	$numParams ++;
}
if(isset($_GET["num_limit"]) && (is_int($_GET["num_limit"]) || ctype_digit($_GET["num_limit"]))){
	$paramsList[] = $_GET["num_limit"];
	$cond = true;
	$limSet = true;
	$numParams++;
}

if (!$cond) {
	$query = $query . " LIMIT 0,20";
	
	$db = new database();
	$db->query = $query;
	$db->params = $paramsList;
	$db->type = "";
}
else {
	$query = $query . " WHERE ";
	
	$query .= implode(" AND ", $qArray);
	if ($limSet)
		$query = $query . " LIMIT 0,?";
	else 
		$query = $query . " LIMIT 0,20";
	
	
	$db = new database();
	$db->query = $query;
	$db->params = $paramsList;
	$db->type = $types[$numParams];
}


$result = $db->fetch();

if (!empty($result)) 
	print(json_encode(array("book_pings"=>$result,"result"=>"SUCCESS")));
else 
	print json_encode(array("book_pings"=>array(),'result'=>'SUCCESS'));//"result"=>'ERROR Could not connect: ' . mysql_error()));

?>
