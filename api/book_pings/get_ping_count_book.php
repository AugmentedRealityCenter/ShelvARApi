<?php
include_once ("../../db_info.php");
 
$qArray = array();
$count = 0;

$sql = "SELECT * FROM book_pings";
$result;
bool $cond = false;

if(isset($_GET["book_tag"])){ 
	$qArray[] = 'book_tag = ' . $_GET["book_tag"]; 
	$cond = true;
} 
if(isset($_GET["call_number"])){ 
    $qArray[] = 'book_call = ' . $_GET["call_number"]; 
	$cond = true;
} 
if(isset($_GET["start_date"])){ 
	$qArray[] = 'start_date = ' . $_GET["start_date"]; 
	$cond = true;
} 
if(isset($_GET["end_date"])){ 
    $qArray[] = 'end_date = ' . $_GET["end_date"]; 
	$cond = true;
} 

if($cond)
	$sql .= " WHERE ";

$sql .= implode(' AND ', $qArray); 
echo $sql;

	
$con = mysql_connect($server,$user,$password);

if (!$con)
	die('Could not connect: ' . mysql_error());
	
mysql_select_db($database, $con);

$result = mysql_query($sql);

while($row = mysql_fetch_array($result))
{
  var_dump($row);
}
	
?>