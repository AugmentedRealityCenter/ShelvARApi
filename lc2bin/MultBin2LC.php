<?php

include("Bin2LC.php");

/**
 * @file
 * @author Brian Stincer
 * @date 2011-10-24
 *
 *  A function set used to convert Multiple binary strings to LC call numbers.
 * 
 */
include_once "LC_Converter_lib.php";
$JSONin = stripslashes($_POST["BINs"]);
$JSONin = json_decode($JSONin,true);
echo MultBin2LC($JSONin);


	
?>
