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
include_once "../base64_lib.php";
include_once "../../header_include.php";
//include_once "../api_ref_call.php";

$JSONin = stripslashes($_GET["B64"]);
if(strlen($JSONin) != 24){
  $result = array('call_number' => "", 'parsed_call_number' => "", 'result' => "ERROR Tag was wrong length. Got ".strlen($JSONin)." characters of data, expected 24. ".$JSONin);
  echo json_encode($result);
 } else {
  echo json_encode(Bin2LC(base642bin($JSONin)));
 }

 ?>
