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

// format is json by default
$format     = isset($_GET['format']) ? strtolower($_GET['format']) : 'json';
// type is raw output by default, user must specify if they want a file dl
$type       = isset($_GET['type']) ? strtolower($_GET['type']) : 'raw';

	$query = "SELECT DISTINCT book_call FROM book_pings WHERE inst_id = ?"
	         ." AND ping_time >= ? AND ping_time < ?";
	
	$book_call = array($inst_id, $start_date, $end_date);

	$db = new database();
	$db->query = $query;
	$db->params = $book_call;
	$db->type = 'sss';

	$result = $db->fetch();

if (!empty($result)) {
// format as JSON
    if ($format === 'json') {
        // user requests a file download
        if ($type === 'file') setFileHeaders('json');
        else header('Content-Type: application/json');
        echo json_encode(array("inventory"=>$result,"result"=>"SUCCESS"));
    // format as CSV
    } else if ($format === 'csv') {
        if ($type === 'file') setFileHeaders('csv');
        // use first result set keys as csv headings
        $keys = array_keys($result[0]);
        // echo csv headings
        for ($i = 0; ($i < count($keys)); $i++) {
            echo '"'.$keys[$i].'"';
            echo ($i <= (count($keys) - 1)) ? ',' : '';
        }
        echo "\n";
        // echo data
        for ($i = 0; ($i < count($result)); $i++) {
            foreach ($result[$i] as $key => $value) {
                echo $value . ",";
            }
            echo "\n";
        }
    } else {
        // invalid format specification, so throw error
        header('Content-Type: application/json');
        echo json_encode(array("ERROR invalid format specification"));
    }
} else {
	header('Content-Type: application/json');
	echo json_encode(array("inventory"=>"No inventory data found in specified"." time period","result"=>"SUCCESS"));
}

/**
 * Set the headers such that the user is prompted
 * to download the file, rather than see the contents
 * in the page.
 */
function setFileHeaders($fileType) {
    if ($fileType === 'json') {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="inventory.json"');
    } else if ($fileType === 'csv') {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="inventory.csv"');
    }
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
}

?>
