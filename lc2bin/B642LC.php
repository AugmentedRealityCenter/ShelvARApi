<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
/**
 * @file
 * @author Brian Stincer
 * @date 2011-10-24
 *
 * A function set used to convert a base64 number to a LC call number.
 * 
 */

include_once "LC_Converter_lib.php";
include_once "../tagmaker/base64_lib.php";
$JSONin = stripslashes($_POST["B64"]);
//echo $JSONin;
//echo '<br/>';
//$JSONin = json_decode($JSONin,true);
echo json_encode(array("LC"=>Bin2LC(base642bin($JSONin))));

 ?>
