<?php
/**
 * @file
 * @author Brian Stincer
 * @date 2011-10-24
 *
 * A function set used to convert a base64 number to a LC call number.
 * 
 * Not currently used, but kept because its inverse (LC2B64) is used.
 */

include_once "LC_Converter_lib.php";
include_once "../../tagmaker/base64_lib.php";

$JSONin = stripslashes($_GET["B64"]);
echo json_encode(array("LC"=>Bin2LC(base642bin($JSONin))));

 ?>
