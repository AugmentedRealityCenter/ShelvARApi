<?php
$root = $_SERVER['DOCUMENT_ROOT']."/";
include_once $root."db_info.php";
include_once $root."database.php";
include_once $root."header_include.php";
include_once $root."api/api_ref_call.php";

//Begin comment out for debugging
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
}
*/
//end comment out for debugging

//Uncomment below for easy debugging
$inst_id = '';
if (isset($_GET['inst_id'])) {
	$inst_id = urldecode($_GET['inst_id']);
} else {
	// test default for now, TODO get rid later
	$inst_id = 'sandbox';
}

$book_call = "";
if (isset($_GET['book_call'])) {
    $book_call = strtoupper(urldecode($_GET['book_call']));
} else {
	//Empty on purpose
    $book_call = "";
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

if (isset($_GET['book_call_end'])) {
	$book_call_end = strtoupper(urldecode($_GET['book_call_end']));
	getSubclassRange($inst_id, $book_call, $book_call_end, $start_date, $end_date);
} else {
	getClassCount($inst_id, $book_call, $start_date, $end_date);
}
/************Functions below****************/

/**
* Sends a json encoded message back containing all subclasses in the given range.
* @throws error if book_call and book_call_end not the same length or
*				if book_call and book_call_end do not start with the same letter
*/
function getSubclassRange($p_inst_id, $p_book_call_start, $p_book_call_end, $p_start_date, $p_end_date){
	//Return an error if the call start and call end are not the same length
	if(strlen($p_book_call_start) !== strlen($p_book_call_end)){
		echo json_encode(array("result"=>"ERROR", "message"=>"book_call and book_call_end must be the same length"));
		return;
	}

	$start_arr = str_split($p_book_call_start);
	$end_arr = str_split($p_book_call_end);	
	
	if($start_arr[0] !== $end_arr[0]){
		echo json_encode(array("result"=>"ERROR", "message"=>"book_call and book_call_end must be in the same LCC class"));
		return;
	}
	
	$i=0;
	while($i<count($start_arr) && $i<count($end_arr) && $start_arr[$i] === $end_arr[$i]) {
		$i++;
	}
	
	//If the while loop broke from the last condition, use the range.
	if($i<count($start_arr) && $i<count($end_arr)){
		$data_arr = array();
		foreach(range($start_arr[$i], $end_arr[$i]) as $letter){
			$call = substr($p_book_call_start, 0, $i) . $letter . '_';
			$result = countClass($p_inst_id, $call, $p_start_date, $p_end_date);
			if($result['count'] !== 0){
				$data_arr[] = $result;
			}
		}
		echo json_encode(array("result"=>"SUCCESS", "count_data"=>$data_arr));
	} else {
		//Else the characters are all the same
		getClassCount($p_inst_id, $p_book_call_start . '_', $p_start_date, $p_end_date);
	}
}

/**
*	Returns a json encoded message for the given call number prefix.
*
*	If p_book_call ends in the character '_' then it is treated as a special
*	character that only returns the count for that LCC subclass.
*
*	Otherwise p_book_call is treated as an LCC class and a breakdown is given
*	for the counts of all subclasses below it.
*/
function getClassCount($p_inst_id, $p_book_call, $p_start_date, $p_end_date){
	//See if we're just have letters (class/subclass)
	if(hasSubclassOnlyChar($p_book_call)){
		$count_data = countClass($p_inst_id, $p_book_call, $p_start_date, $p_end_date);
		echo json_encode(array("result"=>"SUCCESS", "count_data"=>$count_data));
	} else {
		//Otherwise we know we want all subclasses
		$count_data = countSubclasses($p_inst_id, $p_book_call, $p_start_date, $p_end_date);
		$count_data["subclasses"] = findSubclasses($p_inst_id, $p_book_call, $p_start_date, $p_end_date);
		echo json_encode(array("result"=>"SUCCESS", "count_data"=>$count_data));
	}
}

/**
*	Returns true if specifying the subclass only char: '_'
*/
function hasSubclassOnlyChar($p_book_call){
	$pattern = '/^[A-Z]+_$/';
	return preg_match($pattern, $p_book_call);
}

/**
*	Returns a list of all subclasses for the given call number prefix that
*	have a count > 0 within that time window.
*/
function findSubclasses($p_inst_id, $p_book_call, $p_start_date, $p_end_date){
	$book_search = "";
	$resultArr = array();
	foreach (range('A', 'Z') as $letter) {
		$book_search = $p_book_call . $letter;
		$result = countSubclasses($p_inst_id, $book_search, $p_start_date, $p_end_date);
		if($result['count'] !== 0){
			$resultArr[] = $result;
		}
	}
	//Include . after
	$book_search = $p_book_call . '[.]';
	$result = countSubclasses($p_inst_id, $book_search, $p_start_date, $p_end_date);
	if($result['count'] !== 0){
		$resultArr[] = $result;
	}
		
	//Include just the subclass
	$book_search = $p_book_call . '_';
	$result = countClass($p_inst_id, $book_search, $p_start_date, $p_end_date);
	if($result['count'] !== 0){
		$resultArr[] = $result;
	}
	return $resultArr;
}

/**
*	Counts the number of book_pings seen for a given LCC class in the given time window.
* 	@precondition: $p_book_call ends in the appended character '_'. 
*/
function countClass($p_inst_id, $p_book_call, $p_start_date, $p_end_date){
	//Make sure we have no letters following (options are spaces, dots and numbers)
	//substr function used to get rid of '_' character
	$book_call_reg = '^' . substr($p_book_call,0,strlen($p_book_call-1)) . '[ .0-9]';
	
	$query = "SELECT COUNT(*) FROM book_pings WHERE inst_id = ?"
			  ." AND ping_time >= ? AND ping_time < ? AND book_call REGEXP ?";

	$book_count = array($p_inst_id, $p_start_date, $p_end_date, $book_call_reg);
	$count_data = fetchFromDB($p_book_call, $query, $book_count, 'ssss');
	return $count_data;
}

/**
*	Counts the number of book_pings seen for an LCC subclass in a given time window
*/
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
	$count_data = fetchFromDB($p_book_call, $query, $book_count, 'ssss');
	return $count_data;
}

/**
*	Returns an array containing the call number prefix and the count seen
*/
function fetchFromDB($call_num, $query, $book_count, $type){
	$db = new database();
	$db->query = $query;
	$db->params = $book_count;
	$db->type = $type;

	$resultArr = $db->fetch();
	$result = $resultArr[0];
	return array("call_prefix"=>$call_num, "count"=>$result["COUNT(*)"]);
}

?>