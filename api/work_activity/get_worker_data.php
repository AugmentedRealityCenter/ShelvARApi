<?php
/*
 * This php file returns a high level view of worker data.
 * It returns all those who have shelf read within the time
 * period along with the number of unique books they have
 * shelf read in either JSON or CSV format.
 */
include_once $_SERVER['DOCUMENT_ROOT'].'/api/api_ref_call.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/database.php';

$oauth_user = get_oauth();
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
}

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

// format is json by default
$format     = isset($_GET['format']) ? strtolower($_GET['format']) : 'json';
// type is raw output by default, user must specify if they want a file dl
$type       = isset($_GET['type']) ? strtolower($_GET['type']) : 'raw';

$query = "SELECT user_id as worker,".
    " count(*) as books_scanned".
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
    // format as JSON
    if ($format === 'json') {
        // user requests a file download
        if ($type === 'file') setFileHeaders('json');
        else header('Content-Type: application/json');
        echo json_encode(array("workers"=>$result,"result"=>"SUCCESS"));
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
    echo json_encode(array("workers"=>"No worker data found in specified".
        " time period","result"=>"SUCCESS"));
}

/**
 * Set the headers such that the user is prompted
 * to download the file, rather than see the contents
 * in the page.
 */
function setFileHeaders($fileType) {
    if ($fileType === 'json') {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="worker_data.json"');
    } else if ($fileType === 'csv') {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="worker_data.csv"');
    }
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
}
?>
