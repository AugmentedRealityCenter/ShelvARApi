<?php

/**
 * @file
 * @author Travis Boettjer
 * @date 2011-10-17
 *
 * SVG Image generation based on binary interpretation of LC call numbers.
 *
 */

include 'base64.php'; //Base64 encoding and decoding
// The next include will be added once the Bin2LC is available to use.
//include '../Bin2LC/ParseBinary.php'; //The Binary to LC encoder.

/**
 * block - Generates one block equal to a '1' in the incoming tag 
 *	displayed using x and y coords given to it.
 */
function block($x,$y){
	$blocksize = 15;
	
	echo'
  	<Rect x="'.$x.'" y="'.$y.'" width="'.$blocksize.'" height="'.$blocksize.'"
 	 fill="black"/>';
}

 
/**
 * imageGen - Generates an AR tag image based on a given LC tag binary.
 *
 * This function takes in a bit string and translates it to the image. 0 is
 * represented by a black box, 1 is represented by a white box. The image
 * generated is a 10 box wide tag, and the height of the tag is based on the
 * input bit length. Below that is the LC Tag itself.
 *
 * @param $tag - the binary tag to be encoded
 * @return $image - The generated image.
 */
function imageGen($tag){
	$blocksize = 15;
	$quietZoneSize = 1;
	$width = 150;
	$height = $blocksize * (3 + ceil(strlen($tag) / 7));
	$iheight = $blocksize *(ceil(strlen($tag)/7));
	
	
	//Lshape -left
	for($i=0; $i<$iheight; $i++){
		$y = 15 + $i;
		$x = 15;
		block($x, $y);
	}
	//Lshape -bottom
	for($j=0; $j<$width-45; $j++){
		$x = $j +15;
		$y = $iheight+15;
		block($x, $y);
	}

	// create block if there is a bit, for each bit
	$i = 0;
	for ($y = 15; $y < $height - 30; $y += $blocksize) {
		for ($x = 30; $x < $width - 15; $x += $blocksize) {
			if ($i < strlen($tag)) {
				if ($tag{$i} == "0"){
					block($x, $y);
				}
				$i++;
			} //If no bits, white box.
		}
	}
	
	
	echo'</svg>';
}

header('Content-type: image/svg+xml');

echo '<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
 
<svg width="100%" height="100%" version="1.1"
xmlns="http://www.w3.org/2000/svg">';
 
// This part of code done by John Mannix
if(isset($_GET["tag"])) {
	$btag = $_GET["tag"];
	$tag = base642bin($btag);
} else {
	mt_srand();
	$itag = mt_rand();
	$tag = decbin($itag);

	if (strlen($tag) > 100)
		$tag = substr($tag, 0, 100);
	else if (strlen($tag) < 35) {
		$tag = str_pad($tag, 35, "0", STR_PAD_RIGHT);
	}
}

// call function to create svg img given the tag's string
imageGen($tag);


?>
