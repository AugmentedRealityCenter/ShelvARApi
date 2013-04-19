<?php
include_once ("../../db_info.php");
include_once "../../header_include.php";
//include_once "../api_ref_call.php";
 
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
if(isset($_GET["institution"])){ 
    $qArray[] = "institution = '" . urldecode($_GET["institution"]) . "'"; 
	$cond = true;
} 

if($cond)
	$sql = $sql . " WHERE ";

$sql .= implode(" AND ", $qArray); 

	
$con = mysql_connect($server,$user,$password);

if (!$con)
	die('Could not connect: ' . mysql_error());
	
mysql_select_db($database, $con);

$result = mysql_query($sql);
$count = 0;
while($row = mysql_fetch_array($result))
{
	$count++;
}

print(json_encode(array('book_ping_count'=>$count)));
	
?>