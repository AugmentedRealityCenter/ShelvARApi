<?php 

/**
 * DEPRECATED: Marked for deletion!
 **/

/*
require_once("parseLibrary.php");
/**
* @file
* Parses multiple LC Call Numbers passed to 10 fields and then outputs it as an array of JSON objects.
*
* \b Required \b input \b guidelines:
* \arg An array of LC Call Numbers represented as strings
* \arg No whitespace before the first character will be accepted.
* \arg There MUST be a space between the first cutter number and the first date field
* 
* \b Recommended \b input \b guidelines:
* \arg A space and a period should precede the second cutter number
* \arg There need not be a space between the whole number portion of the 
* class number and the decimal portion
*
*
* \b Output:
* An array of JSON objects with the following 10 fields:
* \arg alphabetic
* \arg wholeClass
* \arg decClass
* \arg date1
* \arg cutter1
* \arg date2
* \arg cutter2
* \arg element8
* \arg element9
* \arg element10
*
* 
* @see http://www.oclc.org/bibformats/en/0xx/050.shtm
*
* @version September 26, 2011
* @author Eliot Fowler
*/
include_once('../api_ref_call.php');

$JSONin = stripslashes($_POST["callNumInput"]);
$JSONin = json_decode($JSONin,true);
echo json_encode(parseMultipleNums($JSONin));

/*
* Array of Parsed Objects
*/
$returnArr;

function parseMultipleNums($arrOfNums)
{
	global $returnArr;
	for($i=0; $i < (count($arrOfNums)); $i++)
	{
		$returnArr[$i] = parseToAssocArray($arrOfNums[$i]);
	}
	
	return $returnArr;
}

*/
?>
