<?php
$root = $_SERVER['DOCUMENT_ROOT']."/";
include_once $root."db_info.php";
include_once $root."database.php";
include_once $root."header_include.php";
include_once $root."api/api_ref_call.php";

/*
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
}*/

$inst_id = '';
if (isset($_GET['inst_id'])) {
	$inst_id = urldecode($_GET['inst_id']);
} else {
	// test default for now, TODO get rid later
	$inst_id = 'sandbox';
}

$book_call_start = "";
if (isset($_GET['book_call_start'])) {
    $book_call_start = urldecode($_GET['book_call_start']);
} else {
    // test default for now, TODO get rid later
    $book_call_start = "BA";
}

$book_call_end = ""
if (isset($_GET['book_call_end'])) {
    $book_call_end = urldecode($_GET['book_call_end']);
} else {
    // test default for now, TODO get rid later
    $book_call_end = "BZ";
}

$startDate = "";
if (isset($_GET['start_date'])) {
    $startDate = urldecode($_GET['start_date']);
} else {
	// test default for now, TODO get rid later
    // set start date to one week before today by default
    $startDate = date("Y-m-d H:i:s", strtotime("-1 year"));
}

if (isset($_GET['end_date'])) {
    $endDate = urldecode($_GET['end_date']);
} else {
	// test default for now, TODO get rid later
    // set end date to one week after start date
    $endDate = date("Y-m-d H:i:s", strtotime($startDate."+1 year"));
}

$pattern = '/^[A-Z]+[0-9]+$/';
if(preg_match($pattern, $book_call)){
	$book_call .= ' ';
}

$query = "SELECT COUNT(*) FROM book_pings WHERE inst_id = ? AND book_call LIKE ?"
		  ." AND ping_time >= ? AND ping_time < ? ";
$book_call .= '%';


//$query = "SELECT DISTINCT book_call FROM book_pings WHERE inst_id = ?"
//        ." AND ping_time >= ? AND ping_time < ?";
$book_count = array($inst_id, $book_call, $startDate, $endDate);

$db = new database();
$db->query = $query;
$db->params = $book_count;
$db->type = 'ssss';

$result = $db->fetch();

echo json_encode(array("Call Numbers"=>$result,"result"=>"SUCCESS"));

?>