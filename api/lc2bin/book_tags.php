<?php
/**
 * @file
 * @author Bo Brinkman
 * @date 2013-03-30
 *
 * A function set used to convert a base64 number to a LC call number.
 */
$root = $_SERVER['DOCUMENT_ROOT']."/";
include_once $root."header_include.php";
include_once $root."api/lc2bin/lc_numbers_lib.php";

/*foreach($_SERVER as $key => $value){
  if(strpos($key,"REDIRECT_") !== FALSE 
     && strpos($key,"REDIRECT_STATUS") === FALSE
     && strpos($key,"REDIRECT_URL") === FALSE){
    $newkey = substr($key,9);
    $_GET[$newkey] = $value;
  }
}*/
print_r($_SERVER);
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
