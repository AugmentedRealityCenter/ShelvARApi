<?php
/**
 * @file
 * @author Brian Stincer
 * @date 2011-10-24
 *
 * A function set used to convert an LC call number to a binary string.
 * 
 * Used by: librariantagger.js, book_sleuth
 */

include_once "LC_Converter_lib.php";
include_once "../base64_lib.php";
//include_once "../api_ref_call.php";

$JSONin = stripslashes($_GET["LC"]);
$JSONin = json_decode($JSONin,true);
//echo print_r($JSONin);
$binret = LC2Bin($JSONin);
//echo $binret['Bin'];
if(substr($binret['Bin'],0,1) == 'E') echo json_encode(array("base64"=>$binret['Bin'])); //For debugging
else echo json_encode(array("base64"=>bin2base64($binret['Bin'])));

 ?>
