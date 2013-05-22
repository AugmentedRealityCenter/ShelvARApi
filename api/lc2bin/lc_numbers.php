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

include_once "lc_numbers_lib.php";

$call_number_in = urldecode($_GET["call_number"]);

$result = "";
$book_tag = lc_to_tag($call_number_in);
if(strlen($book_tag) == 0){
  $result = "ERROR: Call number too long ";
 }

$call_number_out = tag_to_lc($book_tag);
if(strlen($result) == 0 && strcmp(trim($call_number_in),$call_number_out) != 0){
  $result = "ERROR: Call number in doesn't match call number out. This is a bug, report to the developers ";
 }

if(strlen($result) == 0){
  $result = "SUCCESS";
 }

echo json_encode(array("book_tag" => $book_tag, 
		       "call_number" => $call_number_out,
		       "result" => $result));

 ?>
