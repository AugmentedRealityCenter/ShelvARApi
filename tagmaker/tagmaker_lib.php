<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

/**
 * @file
 * @author John Mannix, Travis Boettjer
 * @date 2011-11-11
 *
 * Image generation based on binary interpretation of LC call numbers.
 * Supports PNG and SVG.
 */

include_once 'base64_lib.php'; //Base64 encoding and decoding
include_once '../api/lc2bin/LC_Converter_lib.php'; //The Binary to LC encoder.

//Global Variables.
$blocksize = 7; //!< Block size in pixels (Should not change)
$width = 165;	//!< Width of image in pixels (Should not change)
$height = 0; //!< Height of AR tag in pixels (Variable)
$iheight = (7 * 15);	//!< Full height of image in pixels (constant base, changes based on $height)


/**
 * Translates the given base64 tag to binary and sets the global variables
 * based on it.
 * 
 * All functions in tagmaker_lib need to call this function at start.
 *
 * @param $tag - The given tag in base64.
 *
 * @return $btag - The tag converted to binary.
 */
function init($tag){
	global $blocksize, $height, $iheight;
        $iheight = (7*15); //Need to reinitialize the globals, 15 is old blocksize
	$btag = base642bin($tag);
	//Tags are now guaranteed to be a multiple of 7 in length, so trim the btag
	$btaglen = strlen($btag);
	$btagtotrim = $btaglen % 7;
	$btag = substr($btag,0,$btaglen-$btagtotrim);
	
	//$height = $blocksize * (4 + ceil(strlen($btag) / 7)); //!< Height of AR tag in pixels
	$height = 15 * (4 + 20); //!< Height of AR tag in pixels -- fixed height for now (15 is old $blocksize)
	$iheight += $height;	//Add the height to the image height.

	return $btag;
}

/**
 * Translate a given binary LC Call Number string and format it properly for the tag.
 *
 * Current maximum of 6 lines.
 * 
 * @param $btag - LC Call Number encoded in Binary.
 *
 * @return $output - image of the LC call number
 */

function lcFormat($btag){
  $lcTag = Bin2LC($btag);
  $lcTag = $lcTag['parsed_call_number'];
	
	//Step 1 is to build up our call number.
	// goal is 6 characters per line.
	$goal_width = 6;
	$line_count = 0;
	$chars_this_line = 0;
	$tag_string = array("","","","","","","","","",""); //10 lines max
	
	$tag_string[$line_count] .= $lcTag['alphabetic'];
	$chars_this_line += strlen($lcTag['alphabetic']);

	//if($chars_this_line + strlen($lcTag['wholeClass']) > $goal_width){
		$line_count += 1;
		//$tag_string[$line_count] .= ' '; //need an indent if we are breaking the class
		$chars_this_line = 1;
	//}
	$tag_string[$line_count] .= $lcTag['wholeClass'];
	$chars_this_line += strlen($lcTag['wholeClass']);
	
	if(strlen($lcTag['decClass']) != 0){
		if($chars_this_line + strlen($lcTag['decClass']) > $goal_width){
			$line_count += 1;
			$tag_string[$line_count] += ' ';
			$chars_this_line = 1;
		}
		$tag_string[$line_count] .= '.';
		$tag_string[$line_count] .= $lcTag['decClass'];
		$chars_this_line += 1+strlen($lcTag['decClass']);
	}
	
	if(strlen($lcTag['date1']) != 0){
		if(1 + $chars_this_line + strlen($lcTag['date1']) > $goal_width){
			$line_count += 1;
			$chars_this_line = 0;
		} else {
			$tag_string[$line_count] .= ' ';
			$chars_this_line += 1;
		}
		$tag_string[$line_count] .= $lcTag['date1'];
		$chars_this_line += strlen($lcTag['date1']);
	}

	if(strlen($lcTag['cutter1']) != 0){
		if(2 + $chars_this_line + strlen($lcTag['cutter1']) > $goal_width){
			$line_count += 1;
			$chars_this_line = 0;
		} else {
			$tag_string[$line_count] .= ' ';
			$chars_this_line += 1;
		}
		
		$tag_string[$line_count] .= ('.' . $lcTag['cutter1']);
		$chars_this_line += (1 + strlen($lcTag['cutter1']));
	}

	if(strlen($lcTag['date2']) != 0){
		if(1 + $chars_this_line + strlen($lcTag['date2']) > $goal_width){
			$line_count += 1;
			$chars_this_line = 0;
		} else {
			$tag_string[$line_count] .= ' ';
			$chars_this_line += 1;
		}
		$tag_string[$line_count] .= $lcTag['date2'];
		$chars_this_line += strlen($lcTag['date2']);
	}

	if(strlen($lcTag['cutter2']) != 0){
		if(1 + $chars_this_line + strlen($lcTag['cutter2']) > $goal_width){
			$line_count += 1;
			$chars_this_line = 0;
		} else {
			$tag_string[$line_count] .= ' ';
			$chars_this_line += 1;
		}
		
		$tag_string[$line_count] .= $lcTag['cutter2'];
		$chars_this_line += (strlen($lcTag['cutter2']));
	}
	
	if(strcmp($lcTag['element8meaning'], "year") == 0){ //If meaning is "year", assume data is there
		if(1 + $chars_this_line + strlen($lcTag['element8']) > $goal_width){
			$line_count += 1;
			$chars_this_line = 0;
		} else {
			$tag_string[$line_count] .= ' ';
			$chars_this_line += 1;
		}
		
		$tag_string[$line_count] .= $lcTag['element8'];
		$chars_this_line += (strlen($lcTag['element8']));
	}
	//TODO: Add support for elements 8-10 when available.


	$tag_image = imagecreatetruecolor(imagefontwidth(5)*6,imagefontheight(5)*($line_count+1));
	$black = imagecolorallocate($tag_image,0,0,0);
	$white = imagecolorallocate($tag_image,255,255,255);
	imagefill($tag_image,0,0,$white);
	$y = 0;
	for($i=0;$i<=$line_count;$i++){
		imagestring($tag_image,5,0,$y,$tag_string[$i],$black);
		$y += imagefontheight(5);
	}
	return $tag_image;
}

/**
 * Generates a PNG AR tag image based on a given LC tag binary (encoded in base64).
 *
 * This function takes in a bit string and translates it to the image. 0 is
 * represented by a white box, 1 is represented by a black box. The image
 * generated is a 10 box wide tag, and the height of the tag is based on the
 * input bit length. Below that is the LC Tag itself, if needed.
 *
 * The height of the full image is different if the nonum paramater is set
 * to 0 or 1. $height is used if 1, $iheight if 0.
 *
 * @param $tag - The binary tag to be encoded, sent as base64.
 * @param $nonum - Flag for if the user wants the LC string under the tag.
 * 		0 for yes, 1 for no.
 *
 * @return $image - The generated Image.
 */
function tagGenPNG($tag, $nonum){
	global $blocksize, $width, $height, $iheight;
	$btag = init($tag);	

	//Generate base image, set full image height based on tag necessity.
	$image = ImageCreate($width, ($nonum == 0) ? $iheight : $height);

	//Generate the tag image.
	$twidth = $blocksize * 11;
	$theight = $blocksize * (4 + 20);
	$timage = imagecreatetruecolor($twidth, $theight);

	//Set colors.
	$black = ImageColorAllocate($timage, 0, 0, 0);
	$white = ImageColorAllocate($timage, 255, 255, 255);
	$iwhite = ImageColorAllocate($image, 255, 255, 255);

	//Set the background, and the L shaped border.
	imagefill($image,0,0, $iwhite);
	imagefill($timage, 0, 0, $white);
	imagefilledrectangle($timage, 0, 0, $twidth, $theight, $black);
	imagefilledrectangle($timage, $blocksize, $blocksize, $twidth-$blocksize, $theight - $blocksize, $white);
	imagefilledrectangle($timage, 2*$blocksize, 2*$blocksize, $twidth-2*$blocksize, $theight - 2*$blocksize, $black);
	
	//Fill the appropriate blocks in the timage.
	$i = 0;
	for($y = $theight - (3 * $blocksize); $y >= 10; $y -= $blocksize) {
		for ($x = 2 * $blocksize; $x < $twidth - 2*$blocksize; $x += $blocksize) {
			if ($i < strlen($btag)) {
				if ($btag{$i} == "0"){ //0 is white, 1 is black.
					imagefilledrectangle($timage, $x, $y, $x + $blocksize, $y + $blocksize, $white);
				}
				$i++;
			} else //No bits left, white box.
				imagefilledrectangle($timage, $x, $y, $x + $blocksize, $y + $blocksize, $white);
		}
	}
	$tscalefactor = 1;//($width-27)/imagesx($timage);
	//TODO: Clean up vertical positioning of the tag.
	imagecopyresampled($image,$timage,($width-$twidth)/2,($height-$theight)/2,0,0,$tscalefactor*imagesx($timage),$tscalefactor*imagesy($timage),imagesx($timage),imagesy($timage));
	
	if($nonum == 0){
		/*$lcTag = Bin2LC($btag);
		imagestring($image, 5, 15, $height + 5, $lcTag['alphabetic'], $black);
		imagestring($image, 5, 15, $height + 20, $lcTag['wholeClass'].".".$lcTag['decClass'], $black);
		imagestring($image, 5, 15, $height + 35, $lcTag['date1'].$lcTag['cutter1'], $black);
		imagestring($image, 5, 15, $height + 50, $lcTag['date2'].$lcTag['cutter2'], $black); */

		$lcTagFormatted = lcFormat($btag);
		/*$length = count($lcTagFormatted);
		for($i = 0; $i < $length; $i++)
			imagestring($image, 5, 15, ($height + 5 + ($i * 15)), $lcTagFormatted{$i}, $black);*/
		$scalefactor = ($twidth)/imagesx($lcTagFormatted);
		imagecopyresampled($image,$lcTagFormatted,($width-$twidth)/2,($height+$theight)/2+$blocksize,0,0,$scalefactor*imagesx($lcTagFormatted),$scalefactor*imagesy($lcTagFormatted),imagesx($lcTagFormatted),imagesy($lcTagFormatted));
	}

	return $image;
}

/**
 * Generates an SVG AR tag image based on a given LC tag binary (encoded in base64).
 *
 * This function takes in a bit string and translates it to the image. 0 is
 * represented by a white box, 1 is represented by a black box. The image
 * generated is a 10 box wide tag, and the height of the tag is based on the
 * input bit length. Below that is the LC Tag itself, if needed.
 *
 * This function does not return a value.
 *
 * @param $tag - The binary tag to be encoded, sent as base64.
 * @param $nonum - Flag for if the user wants the LC string under the tag.
 * 		0 for yes, 1 for no.
 */
function tagGenSVG($tag, $nonum){
	global $blocksize, $width, $height, $iheight;
	$btag = init($tag);	
	
	echo '<?xml version="1.0" standalone="no"?>
	<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
	"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
	 
	<svg width="100%" height="100%" version="1.1"s
	xmlns="http://www.w3.org/2000/svg">';

	//Create L shaped border, White on black
	echo '<Rect x="0" y="0" width="'.$width.'" height="'.$height.'" fill="black"/>';
	echo '<Rect x="15" y="15" width="'.$blocksize.'" height="'.($height- 30).'" fill="white"/>'; //Left bar
	echo '<Rect x="15" y="'.($height- 30).'" width="'.($width- 30).'" height="'.$blocksize.'" fill="white"/>'; //Bottom bar.


	//Create block if there is a bit, for each bit
	$i = 0;
	for($y = $height - 45; $y >= 15; $y -= $blocksize) {
		for ($x = 30; $x < $width - 15; $x += $blocksize) {
			if ($i < strlen($btag)) {
				if ($btag{$i} == "0"){
					echo '<Rect x="'.$x.'" y="'.$y.'" width="'.$blocksize.'" height="'.$blocksize.'" fill="white"/>';
				}
				$i++;
			} else //If no more bits, white box instead for the remander of the tag.
				echo '<Rect x="'.$x.'" y="'.$y.'" width="'.$blocksize.'" height="'.$blocksize.'" fill="white"/>';
		}
	}

	//Add LC Number.
	if($nonum == 0){
		/*$lcTag = Bin2LC($btag);
		echo '<text x="15" y="'.($height+20).'">'.$lcTag["alphabetic"].'</text>';
		echo '<text x="15" y="'.($height+35).'">'.$lcTag["wholeClass"].$lcTag["decClass"].'</text>';
		echo '<text x="15" y="'.($height+50).'">'.$lcTag["date1"].$lcTag["cutter1"].'</text>';
		echo '<text x="15" y="'.($height+65).'">'.$lcTag["date2"].$lcTag["cutter2"].'</text>';*/

		$lcTagFormatted = lcFormat($btag);
		$length = count($lcTagFormatted);
		for($i = 0; $i < $length; $i++)
			echo '<text x="15" y="'.($height+20+($i * 15)).'">'.$lcTagFormatted[$i].'</text>';

	}

	echo'</svg>';
}

?>
