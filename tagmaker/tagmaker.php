<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

/**
 * @file
 * @author John Mannix
 * @date 2011-11-11
 *
 * Wrapper for image generation based on binary interpretation of LC call numbers.
 * Supports PNG and SVG. Can be flagged for no LC number or not.
 * 
 * @param $tag - Base64 encoded binary LC number
 * @param $type - Flags for type of image. 0 is PNG. 1 is SVG.
 * @param $nonum - Flags for inclusion of LC Number at bottom of tag.
 * 			0 is "Yes, I want a tag." 1 is "No, I do not want a tag."
 * 			[Optional, defaults to 0]
 */
include 'tagmaker_lib.php';

$tag; //!< Binary tag encoded in Base64.
$type; //!< Type of image.
$nonum; //!< No LC Number flag.

//Input handlers.
if(isset($_GET["tag"])) { //Checks for a provided tag.
	$tag = $_GET["tag"]; //Gets the tag as base64.
} else
	echo "No tag given!<BR>";

if(isset($_GET["type"])) //Gets if type is specified.
	$type = $_GET["type"];
else	//If no type specified, you get PNG.
	$type = 0;

if(isset($_GET["nonum"])) //Checks if the notag flag is enabled/given.
	$nonum = $_GET["nonum"];
else		//Defaults to "yes I want a tag"
	$nonum = 0;

//$file_name = time() . mt_rand(1000000,9999999) . '.png';
	
//Determine what type we want, and execute.
switch($type){
default:
case 0: //PNG
	header("Content-type: image/png");
	$image = tagGenPNG($tag, $nonum);
	imagePNG($image);//, 'imgcache/'.$file_name); //Display the image.
	imagedestroy($image);
	//echo $image;
	break;
case 1: //SVG
	//header('Content-type: image/svg+xml');
	tagGenSVG($tag, $nonum);
	break;
}

?>
