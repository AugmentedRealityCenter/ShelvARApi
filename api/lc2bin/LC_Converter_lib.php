<?php

//include_once "../api_ref_call.php";


ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
/**
 * @file
 * @author Brian Stincer, John Mannix
 * @date 2011-11-4
 *
 * A function set used to convert an LC call number to a binary string.
 * 
 */

/**
 * LC2Bin - Converts a LC call number to binary
 *
 * This function compliments Bin2LC.
 *
 * @param $jsonFields - This is a Json object that should be set up 
 * as an assosiative array. The names for the elements of the array 
 * should be 'fld1', 'fld2' up to 'fld7'
 * @return - binary string represented as a Json object
 */
	function LC2Bin($jsonFields, $version=0){
		switch($version){
			case 0:
			default:
				include_once('LC_Converter_lib_v0.php');
		}
  		return LC2Bin_delegate($jsonFields);
	}
	
	/**
 * Bin2LC - Converts a binary number to LC call number
 *
 * This function compliments LC2Bin. 
 *
 * @param $json_bits - binary string represented by a JSON object
 * @return - LC call number as a Json object where the Call Number fields
 * 		are represented as "version", "alphabetic", "wholeClass", "decClass", 
 * 		"date1", "cutter1", "date2", "cutter2". Elements 8-10 are not yet supported.
 */
	function Bin2LC($bits, $version=0){
		switch($version){
			case 0:
			default:
				include_once('LC_Converter_lib_v0.php');
		}	
		return Bin2LC_delegate($bits);
	}
	
	/**
 * MultBin2LC - Converts a multiple Binary numbers to LC numbers
 *
 * This function compliments MultLC2Bin.
 *
 * @param $arr - JSon array of binary numbers
 * @return - Json array of LC call numbers
 */
	function MultBin2LC($arr, $version=0){
		switch($version){
			case 0:
			default:
				include_once('LC_Converter_lib_v0.php');
		}
		return MultiBin2LC_delegate($arr);
	}
	
	/**
 * MultLC2Bin - Converts a multiple LC call Numbers to Binary
 *
 * This function compliments MultBin2LC.
 *
 * @param $arr - Json array of LC call numbers
 * @return - Json array of binary numbers
 */
	function MultLC2Bin($arr, $version =0){
		switch($version){
			case 0:
			default:
				include_once('LC_Converter_lib_v0.php');
		}
		return MultLC2Bin_delegate($arr);
	}

 ?>
