<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
/**
 * @file
 * @author Brian Stincer
 * @date 2011-10-24
 *
 * A function set used to convert an LC call number to a binary string.
 * 
 */

include_once "LC_Converter_lib.php";
//$JSONin = "{\"alphabetic\":\"QA\",\"wholeClass\":\"76\",\"decClass\":\"7\",\"date1\":\"\",\"cutter1\":\"A321\",\"date2\":\"2002\",\"cutter2\":\"\",\"element8\":\"\",\"element8meaning\":\"unknown\",\"element9\":\"\",\"element10\":\"\"}";
$JSONin = stripslashes($_POST["LC"]);
//echo $JSONin;
$JSONin = json_decode($JSONin,true);
$bin = LC2Bin($JSONin);
echo json_encode($bin);

 ?>
