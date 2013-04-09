<?php
/**
 * @file
 * @author Bo Brinkman
 * @date 2013-03-30
 *
 * A function set used to convert a base64 number to a LC call number.
 */

include_once "../../header_include.php";
include_once "lc_numbers_lib.php";
//include_once "../api_ref_call.php";

$b64_in = stripslashes($_GET["B64"]);
$decoded = tag_to_lc($b64_in);

if(strlen($decoded) == 0){
  $result = array('call_number' => "", 
		  'result' => "ERROR. Tag decode failed.");
  echo json_encode($result);
 } else {
  $result = array('call_number' => $decoded, 
		  'result' => "SUCCESS");
  echo json_encode($result);
 }

 ?>
