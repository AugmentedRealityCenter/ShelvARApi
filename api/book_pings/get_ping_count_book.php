<?php
$root = $_SERVER['DOCUMENT_ROOT']."/";
include_once $root."db_info.php";
include_once $root."database.php";
include_once $root."header_include.php";
include_once $root."api/api_ref_call.php";

$oauth_user = get_oauth();

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
	$cond = true;
	$numParams ++;
}

if (!$cond) {
	$db = new database();
	$db->query = $query;
	$db->params = $paramsList;
	$db->type = "";
    error_log($query);
}
else {
	$query = $query . " WHERE ";
	
	$query .= implode(" AND ", $qArray);
	
	$db = new database();
	$db->query = $query;
	$db->params = $paramsList;
	$db->type = $types[$numParams];
    error_log($query);
}

$result = $db->fetch();

$count = count($result);

print(json_encode(array('book_ping_count'=>$count,'result'=>"SUCCESS")));

?>
