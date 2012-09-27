<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

/**
 * @file
 * @author Bo Brinkman
 * @date 2011-11-20
 *
 * Generate a PNG image that prints as a sheet of 5160 labels.
 * 
 * @param $tag1 First tag, in base 64 format
 * @param $tag2 ... $tag30 Remaining tags, in base 64 format
 */
include '5160_lib.php';


header("Content-type: image/png");
echo make_5160($_GET);
?>
