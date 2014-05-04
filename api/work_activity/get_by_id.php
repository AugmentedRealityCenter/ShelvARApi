<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/api_ref_call.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/database.php';

$inst_id = 'forward';
/*
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

// format is json by default
$format     = isset($_GET['format']) ? strtolower($_GET['format']) : 'json';
// type is raw output by default, user must specify if they want a file dl
$type       = isset($_GET['type']) ? strtolower($_GET['type']) : 'raw';
// min time between shelf read activity is 60 seconds by default
$timeDiff   = isset($_GET['time_diff']) ? $_GET['time_diff'] : 60;
// user id for which to grab shelf reading activity data
$user       = $_GET['user_id'];

$query = "SELECT book_call AS book, ".
    "ping_time AS time FROM book_pings WHERE inst_id = ? AND ".
    "user_id = ? AND ping_time >= ? AND ping_time < ? ".
    "ORDER BY ping_time";
$paramsList = array($inst_id, $user, $startDate, $endDate);

$db = new database();
$db->query = $query;
$db->params = $paramsList;
$db->type = 'ssss';

$result = $db->fetch();

if (!empty($result)) {
    $newResult          = array();
    $activityCount      = 1;
    $lastDate           = $result[0]["time"];
    $lastActivityEnd    = 0;
    for ($i = 1; ($i < count($result)); $i++) {
        $diff = abs(strtotime($lastDate) - strtotime($result[$i]["time"]));
        if ($diff >= $timeDiff) { 
            $actString = "Activity " . $activityCount;
            for ($j = $lastActivityEnd+1; ($j < $i); $j++) {
                $newResult[$actString][] = $result[$j];
            }
            $activityCount++;
            $lastActivityEnd = $i - 1;
        }
        if (($i === count($result) - 1) && $lastActivityEnd < $i) {
            $actString = "Activity " . $activityCount;
            for ($j = $lastActivityEnd+1; ($j <= $i); $j++) {
                $newResult[$actString][] = $result[$j];
            }
        }
        $lastDate = $result[$i]["time"];
        error_log("i: " . $i . " count result: " . (count($result) - 1) . " lastActEnd: " . $lastActivityEnd);
    }
    // format as JSON
    if ($format === 'json') {
        // user requests a file download
        if ($type === 'file') setFileHeaders('json', $user);
        else header('Content-Type: application/json');
        echo json_encode(array($user=>$newResult,"result"=>"SUCCESS"));
        // format as CSV
    } else if ($format === 'csv') {
        if ($type === 'file') setFileHeaders('csv', $user);
        // get the keys to the associative array
        // they define the csv file headings
        $keys       = array_keys($newResult);
        $subKeys    = array_keys($newResult[$keys[0]][0]);
        // echo data
        foreach ($newResult as $key => $value) {
            // print activity heading
            echo "\"".$key."\"\n";
            // print subheadings under activity
            for ($i = 0; ($i < count($subKeys)); $i++) {
                echo "\"".$subKeys[$i]."\"";
                echo ($i < count($subKeys) - 1) ? ',' : '';
            }
            echo "\n";
            for ($i = 0; ($i < count($value)); $i++) {
                foreach ($value[$i] as $key1 => $value1) {
                    echo $value1.",";
                }
                echo "\n";
            }
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
function setFileHeaders($fileType, $fileName) {
    if ($fileType === 'json') {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="'.$fileName.'.json"');
    } else if ($fileType === 'csv') {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$fileName.'.csv"');
    }
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
}
?>
