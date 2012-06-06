<?php
include 'base64_lib.php';

/**
 * @file
 * @author John Mannix
 * @date 2011-11-04
 *
 * Wrapper for the base642bin function from base64_lib.php
 *
 * @param input - Base64 string wrapped in JSON. Expected JSON name: "binary"
 *
 * @return Converted binary string wrapped in JSON.
 */

$JSONin = stripslashes($_POST["input"]); //!< Base64 string in JSON.
$JSONin = json_decode($JSONin,true);

if(count($JSONin) == 1)
	echo json_encode(base642bin($JSONin["binary"]));
?>
