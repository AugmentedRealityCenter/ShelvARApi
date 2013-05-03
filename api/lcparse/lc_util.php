<?php
include_once('../lc2bin/LC2B64.php');
include_once("parseLibrary.php");
//include_once('../api_ref_call.php');

$call_number = url_decode($_GET["call_number"]);
$call_number = substr($call_number, 0, strlen($call_number) - 5);

$reuslt = array();

result[] = parseToAssocArray($call_number)

include_once "LC_Converter_lib.php";
include_once "../base64_lib.php";
$binret = LC2Bin($JSONin);
//echo $binret['Bin'];
if(substr($binret['Bin'],0,1) == 'E') echo json_encode(array("base64"=>$binret['Bin'])); //For debugging
else result[] = array("base64"=>bin2base64($binret['Bin']));

return json_encode(result);
?>