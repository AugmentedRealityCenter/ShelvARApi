<?php

include("LC2Bin.php");

/**
 * @file
 * @author Brian Stincer
 * @date 2011-10-24
 *
 * A function set used to convert Multiple LC call numbers to binary strings.
 * 
 */
include_once "LC_Converter_lib.php";
$JSONin = stripslashes($_POST["LCs"]);
$JSONin = json_decode($JSONin,true);
echo MultLC2Bin($JSONin);


	
?>
