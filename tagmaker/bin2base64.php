<?php
include 'base64_lib.php';

/**
 * @file
 * @author John Mannix
 * @date 2011-11-04
 *
 * Wrapper for the bin2base64 function from base64_lib.php
 *
 * @param input - Binary string wrapped in JSON. Expected JSON name: "Bin"
 *
 * @return converted base64 string wrapped in JSON (named base64).
 */

$JSONin = stripslashes($_POST["input"]); //!< Binary string in JSON.
$JSONin = json_decode($JSONin,true);

if(count($JSONin) == 1)
	echo json_encode(array("base64"=>bin2base64($JSONin["Bin"]));
?>
