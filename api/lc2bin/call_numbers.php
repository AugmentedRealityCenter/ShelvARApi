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
include_once "../lcparse/parseLibrary.php";

$JSONparsed = urldecode($_GET["call_number"]);

$JSONparsedArr = parseToAssocArray($JSONparsed);

$binret = LC2Bin($JSONparsedArr["lcNum"]);

echo json_encode(array("book_tag"=>bin2base64($binret['Bin']), "parsed_call_number"=>$JSONparsed,
						"parser_feedback"=>$JSONparsedArr["arrOfConflicts"], "result"=>"Also TODO"));

 ?>