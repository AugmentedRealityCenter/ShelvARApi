<?php 

/**
* Takes a JSON-formatted string as input and attempts to parse the call number
*
* A JSON-formatted string object is submitted using POST (to the class) and multiple methods are called
* in order to parse this string into the 10 fields of a call number.
* The way the string is parsed is by keeping track of how many characters
* were in each of the previous sections, how many spaces there have been, and
* by using the guidelines previously described.
*
* @param lcNum
* This represents the input previously recevied via POST and is the JSON-formatted string,
* completely unparsed, unchanged. If the user is only parsing a single call number, the object 
* should be formatted like "{"lcNum" : "AB121.12 .A45 2000 A65"}". However, if the user is parsing
* multiple numbers, the object should be formatted like "{"0" : "AB121.12 .A45 2000 A65", "1" : "CD343.34 .B56"}".
* 
* @return
* An associative array that contains the information held in the parsedArr variable. 
* If you are posting to the class, then you will receive a JSON-object back.
*
**/
function parseToJSON($lcNum, $version=0)
{		
	switch($version){
		case 0:
		default:
			include_once('parseLibrary_v0.php');
	}
	$delegateres = parseToJSON_delegate($lcNum);
	
	return $delegateres;
}

