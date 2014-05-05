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

$book_call = "";
if (isset($_GET['book_call'])) {
    $book_call = urldecode($_GET['book_call']);
} else {
    // test default for now, TODO get rid later
    $book_call = "BH";
}

$start_date = "";
if (isset($_GET['start_date'])) {
    $start_date = urldecode($_GET['start_date']);
} else {
	// test default for now, TODO get rid later
    // set start date to one week before today by default
    $start_date = date("Y-m-d H:i:s", strtotime("-1 year"));
}

if (isset($_GET['end_date'])) {
    $end_date = urldecode($_GET['end_date']);
} else {
	// test default for now, TODO get rid later
    // set end date to one week after start date
    $end_date = date("Y-m-d H:i:s", strtotime($start_date."+1 year"));
}

getClassCount($inst_id, $book_call, $start_date, $end_date);

/************Functions below****************/

function getClassCount($p_inst_id, $p_book_call, $p_start_date, $p_end_date){
	$book_call_reg = '';
	$pattern = '/^[A-Z]+_$/';
	//See if we're just have letters (class/subclass)
	if(preg_match($pattern, $p_book_call)){
		//Make sure we have no letters following (options are spaces, dots and numbers)
		//substr function used to get rid of '_' character
		$book_call_reg = '^' . substr($p_book_call,0,strlen($p_book_call-1)) . '[ .0-9]';
		
		$query = "SELECT COUNT(*) FROM book_pings WHERE inst_id = ?"
				  ." AND ping_time >= ? AND ping_time < ? AND book_call REGEXP ?";

		$book_count = array($p_inst_id, $p_start_date, $p_end_date, $book_call_reg);
		fetchFromDB($query, $book_count, 'ssss');
	} else {
		//Otherwise we know we want all subclasses
		countSubclasses($p_inst_id, $p_book_call, $p_start_date, $p_end_date);
	}
}

function countSubclasses($p_inst_id, $p_book_call, $p_start_date, $p_end_date){
	$pattern = '/^[A-Z]+[0-9]+$/';
	$book_call_reg = '';
	//if p_book_call is exactly letters followed by numbers
	if(preg_match($pattern, $p_book_call)){
		//make sure that we don't grab extra numbers
		//eg) calling BH102 and not getting back BH1023 in the results
		$book_call_reg = '^' . $p_book_call . '[ .]';
	} else {
		//Normal
		$book_call_reg = '^' . $p_book_call;
	}

	$query = "SELECT COUNT(*) FROM book_pings WHERE inst_id = ? AND book_call REGEXP ?"
			  ." AND ping_time >= ? AND ping_time < ? ";

	$book_count = array($p_inst_id, $book_call_reg, $p_start_date, $p_end_date);
	fetchFromDB($query, $book_count, 'ssss');
}

function fetchFromDB($query, $book_count, $type){
	$db = new database();
	$db->query = $query;
	$db->params = $book_count;
	$db->type = $type;

	$result = $db->fetch();

	echo json_encode(array("Count"=>$result,"result"=>"SUCCESS"));
}

?>