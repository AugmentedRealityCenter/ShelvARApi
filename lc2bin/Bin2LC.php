<?php
/**
 * @file
 * @author Brian Stincer
 * @date 2011-10-24
 *
 * A function set used to convert a binary string to an LC call number.
 * 
 */
include_once "LC_Converter_lib.php";
$JSONin = stripslashes($_POST["Bin"]);
$JSONin = json_decode($JSONin,true);
echo Bin2LC($JSONin);
 


?>
