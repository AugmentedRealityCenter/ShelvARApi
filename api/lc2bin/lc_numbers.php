<?php
/**
 * @file
 * @author Bo Brinkman
 * @date 2013-03-29
 *
 * A function set used to convert a human-readable LC call number to
 * a base-64 string, used in a ShelvAR tag.
 *
 */

include_once "../base64_lib.php";
include_once "../HuffmanEncoder.php";

$call_number_in = urldecode($_GET["call_number"]);
$huffman_code = huffman_encode($call_number_in);
$call_number_out = huffman_decode($huffman_code);

$result = "SUCCESS";
if(strcmp($call_number_out,trim($call_number_in)) != 0){
  $result = "ERROR";
 }

echo json_encode(array("book_tag"=>$huffman_code, 
		       "call_number"=>$call_number_out, 
		       "result"=>$result));

 ?>