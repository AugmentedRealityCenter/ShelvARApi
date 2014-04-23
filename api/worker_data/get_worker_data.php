<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/api_ref_call.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/database.php';

$inst_id    = 'forward';
/*$oauth_user = get_oauth();
$inst_id    = $oauth_user['inst_id'];
$user_id    = $oauth_user['user_id'];

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

// set the start date
if (isset($_GET['start_date'])) {
    $startDate = urldecode($_GET['start_date']);
} else {
    // set start date to one week before today by default
    $startDate = date("Y-m-d H:i:s", strtotime("-1 week"));
}

// set the end date
if (isset($_GET['end_date'])) {
    $endDate = urldecode($_GET['end_date']);
} else {
    // set end date to one week after start date
    $endDate = date("Y-m-d H:i:s", strtotime($startDate."+1 week"));
}

// max time between shelf reads in seconds
// default is 60 seconds
$maxTime = isset($_GET['max_time']) ? $_GET['max_time'] : 60;
// format is json by default
$format = isset($_GET['format']) ? $_GET['format'] : 'json';

$query = "SELECT DISTINCT user_id as worker,".
         " count(DISTINCT book_call) as books_scanned".
         " FROM book_pings WHERE inst_id = ?".
         " AND ping_time >= ? AND ping_time < ?".
         " GROUP BY user_id";
$paramsList = array($inst_id, $startDate, $endDate);

$db = new database();
$db->query = $query;
$db->params = $paramsList;
$db->type = 'sss';

$result = $db->fetch();

if (!empty($result)) {
    if ($format === 'json') {
        // set header so it outputs as a .json file
        header('Content-Type: application/json');
        echo json_encode(array("workers"=>$result,"result"=>"SUCCESS"));
    } else {
        echo print_r($result,1);
        echo "Feature not implemented yet";
    }
} else {
    if ($format === 'json') {
        // set header so it outputs as a .json file
        header('Content-Type: application/json');
        echo json_encode(array("workers"=>"No worker data found in specified".
                               "time period","result"=>"SUCCESS"));
    } else {
        echo "Feature not implemented yet";
    }
}
?>
