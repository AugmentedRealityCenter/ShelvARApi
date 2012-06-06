<?php
/**
 * @file
 * @author John Mannix
 * @date 2011-09-23
 *
 * A function set used to convert a binary string to a base64 string,
 * as well as the reverse.
 * 
 * When converting from binary to base64 (and the reverse), we first
 * convert to an intermediate int value. When doing this, the following
 * conventions are used:
 * '0' to '9' correspond to 0 to 9 
 * 'a' to 'z' correspond to 10 to 35 
 * 'A' to 'Z' correspond to 36 to 61 
 * - is 62 
 * _ is 63
 */

/**
 * int2char - Converts a given int to the proper base64 char.
 *
 * This function compliments char2int.
 *
 * @param $int - the given int
 * @return - the resulting base64 char
 */
function int2char($int){
	if(($int > -1) && ($int < 10)) //0-9
		return $int;
	else if (($int > 9) && ($int < 36)) //10-35, a-z
		return chr($int + 87); //87 is the lowercase ASCII offset for our numbers
	else if (($int > 35) && ($int < 62)) //36-61, A-Z
		return chr($int + 29); //29 is the uppercase ASCII offset for our numbers
	else if ($int == 62) //62, -
		return '-';
	else if ($int == 63) //63, _
		return '_';
	else
		return ' '; //Unknown value, return a space!
}

/**
 * char2int - Converts a given base64 char to the proper int.
 *
 * This function compliments int2char. It uses ord() to great effect.
 *
 * @param $char - the given base64 char
 * @return - the resulting int
 */
function char2int($char){
	$int = ord($char); //convert to ASCII number.
	if(($int > 47) && ($int < 58))
		return $int - 48; //47 is the ASCII number offset.
	if(($int > 96) && ($int < 123)) //a-z
		return $int - 87; //87 is the ASCII offset.
	else if (($int > 64) && ($int < 91)) //A-Z
		return $int - 29; //29 is the ASCII offset
	else if ($int == 45) // -
		return 62;
	else if ($int == 95) // _
		return 63;
	else
		return -1; //Unknown char, return a -1.
}

/**
 * bin2base64 - Convert a binary string to base64
 *
 * This fucntion converts a binary string of any size to base 64.
 * If the given binary string is not divisible evenly by 6, the
 * converter pads the last segment with '0's from the end of the
 * segment and then converts that segment.
 *
 * @param $bin - the binary string to convert
 * @return $output - the converted base64 string.
 */
function bin2base64($bin){
	$output = "";
	$length = floor(strlen($bin) / 6);
	$overflow = strlen($bin) % 6;
	for($i = 0; $i < $length; $i++){
		$buffer = substr($bin,($i * 6),6);
		$output .= int2char(bindec($buffer));
	}
	if($overflow > 0){
		$buffer = substr($bin, (($length * 6)));
		$buffer = str_pad($buffer, 6, '0');
		$output .= int2char(bindec($buffer));
	}
	
	return $output;
}

/**
 * base642bin - Converts a given base64 string to its binary representation.
 *
 * WARNING: This function is NOT an exact compliment of bin2base64. This 
 * function does not remove any padded 0s from the end of the base64 string 
 * given by bin2base64. It is up to the user of this function to know where
 * the binary string is supposed to end.
 * 
 * This function takes each char and converts it to a 6 bit binary string,
 * adding 0s to the left if needed.
 *
 * @param $base - The given base64 string.
 * @return $output - The resulting binary string.
 */
function base642bin($base){
	$output = "";
	for($i = 0; $i < strlen($base); $i++){
		$buffer = decbin(char2int($base{$i}));
		$buffer = str_pad($buffer, 6, "0", STR_PAD_LEFT);
		$output .= $buffer;
	}
	return $output;
}

?>