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
include_once "../base64_lib.php";
include_once "../lcparse/parseLibrary.php";
//include_once "../api_ref_call.php";

$JSONparsed = urldecode($_GET["call_number"]);

$JSONparsedArr = parseToAssocArray($JSONparsed);

$binret = LC2Bin($JSONparsedArr["lcNum"]);

$result = ($JSONparsedArr["allow"]=="true") ? "SUCCESS" : "ERROR";

$c_num_arr = array( ($JSONparsedArr["lcNum"]["alphabetic"].$JSONparsedArr["lcNum"]["wholeClass"]) );
if(strlen($JSONparsedArr["lcNum"]["decClass"])>0) array_push($c_num_arr, (".".$JSONparsedArr["lcNum"]["decClass"]));
if(strlen($JSONparsedArr["lcNum"]["date1"])>0) array_push($c_num_arr, (" ".$JSONparsedArr["lcNum"]["date1"]) );
if(strlen($JSONparsedArr["lcNum"]["cutter1"])>0) array_push($c_num_arr, (" .".$JSONparsedArr["lcNum"]["cutter1"]) );
if(strlen($JSONparsedArr["lcNum"]["date2"])>0) array_push($c_num_arr, (" ".$JSONparsedArr["lcNum"]["date2"]) );
if(strlen($JSONparsedArr["lcNum"]["cutter2"])>0) array_push($c_num_arr, (" ".$JSONparsedArr["lcNum"]["cutter2"]) );
if(strlen($JSONparsedArr["lcNum"]["element8"])>0) array_push($c_num_arr, (" ".$JSONparsedArr["lcNum"]["element8"]) );
if(strlen($JSONparsedArr["lcNum"]["element9"])>0) array_push($c_num_arr, (" ".$JSONparsedArr["lcNum"]["element9"]) );
if(strlen($JSONparsedArr["lcNum"]["element10"])>0) array_push($c_num_arr, (" ".$JSONparsedArr["lcNum"]["element10"]) );

$call_number = implode($c_num_arr);

echo json_encode(array("book_tag"=>bin2base64($binret['Bin']), 
						"call_number"=>$call_number, 
						"parsed_call_number"=>$JSONparsedArr["lcNum"],
						"parser_feedback"=>$JSONparsedArr["arrOfConflicts"], "result"=>$result));

 ?>