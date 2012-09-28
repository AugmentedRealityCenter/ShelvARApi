<?php
/**
 * @file
 * @author Andrew Bair
 * @date 27SEPT2012
 *
 * A function set used to convert a human-readable LC call number to a binary string.
 *
 */

include_once "LC_Converter_lib.php";
include_once "../../tagmaker/base64_lib.php";

$JSONin = urldecode($_GET["call_number"]);
$JSONin = stripslashes($JSONin);
$JSONin = json_decode($JSONin,true);

$binret = LC2Bin($JSONin);

echo json_encode(array("book_tag"=>bin2base64($binret['Bin'])));

 ?>