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

$GLOBALS['LC2Bin_version'] = '0000000'; //Binary representation of the version number

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
	function LC2Bin_delegate($jsonFields){
		global $LC2Bin_version;
		$fields = $jsonFields;
		
		$one= $fields['alphabetic'];
		
		$two= $fields['wholeClass'];
		
		$three= $fields['decClass'];
		
		$four= $fields['date1'];
		
		$five= $fields['cutter1'];
		
		$six= $fields['date2'];
		
		$seven= $fields['cutter2'];
		
		$element8meaning = $fields['element8meaning'];
		$element8 = $fields['element8'];
	
		$one1=$one2=$one3='';
  		$two1=$three1=$four1=$six1='';
  		$five1= $five2='';
  		$seven1= $seven2='';
  
  		$zero= '00000000';//First 8 bits,
  
  
  		if($one!='' && strlen($one) <= 3){ // If one is given
  			$zero[0]='1'; //This turns to 1 when field is not blank
  			$one = str_pad($one, 3, ' ', STR_PAD_RIGHT);// Adds buffer space for max ot three letters
  			if($one[0] != ' ')
  				$one1 = decbin(ord(strtoupper($one[0]))-64); //turns letter to uppercase --> then to an ascii number and subtract 64 --> turns into binary number
  			if($one[1] != ' ')
  				$one2 = decbin(ord(strtoupper($one[1]))-64); //same as above for second letter(zero if no letter)
  			if($one[2] != ' ')
  				$one3 = decbin(ord(strtoupper($one[2]))-64); // same as above

  		} else {
			//This is an error: LC class letters are required
			return array("Bin"=>'E1');
		}
  		$one1 = str_pad($one1, 5, '0', STR_PAD_LEFT); // pads binary nums so that there are always 5 bits
  		$one2 = str_pad($one2, 5, '0', STR_PAD_LEFT);
  		$one3 = str_pad($one3, 5, '0', STR_PAD_LEFT);
  
  
  		//Turns field 2 to a 13-bit binary num
  		if($two != '' && intval($two) <= 9999){
  			$zero[1]='1';
  			$two1 = str_pad(decbin($two), 13, '0',STR_PAD_LEFT);
  		}
  		else if($two != ''){
			//This is an error, value out of range
			return array("Bin"=>'E2');
		} 
		else
  			$two1 = str_pad('', 13, '0',STR_PAD_LEFT);
  	
  		//Turns field 3 to a 10-bit binary num
  		if($three !='' && intval($three) <= 999){
  			$zero[2]='1';
  			$three1 = str_pad(decbin($three), 10, '0',STR_PAD_LEFT);
  		}
  		else if($three != ''){
			//This is an error, field 3 out of range
			return array("Bin"=>'E3');
		}
		else
  			$three1 = str_pad('', 10, '0',STR_PAD_LEFT);
  	
  		//Turns field 4 to a 12-bit binary num
  		if($four != '' && intval($four) <= 4095){
  			$zero[3]='1';
  			$four1 = str_pad(decbin($four), 12, '0',STR_PAD_LEFT);
  		}
  		else if(intval($four) > 4095 || strlen($four) > 4) {
			//Error, bad input.
  			return array("Bin"=>'E4');
		}
  	
  		//five1 is the letter and five2 is the 1-3 digit num
  		if($five != ''){
			if(ctype_alpha($five[0]) && intval(substr($five, 1)) <= 99999){
  				$zero[4]='1';
  				$five1 = decbin(ord(strtoupper($five[0]))-64); //to upper-->to ascii -64 --> to bin
  				$five2 = decbin(substr($five, 1));	//changes to bin num
  			
  				$five1 = str_pad($five1, 5, "0", STR_PAD_LEFT); //always make sure max bits are there
  				$five2 = str_pad($five2, 17, "0", STR_PAD_LEFT);
  				if(is_numeric(substr($five, -1)))
  					$five2 = $five2."0";	//changes to bin num
  				else
  					$five2 = $five2."1";
			} else {
				//Error bad input
				return array("Bin"=>'E5');
			}
  		}
  		$five1 = str_pad($five1, 5, "0", STR_PAD_LEFT); //always make sure max bits are there
  		$five2 = str_pad($five2, 18, "0", STR_PAD_LEFT);
  
  		//Turns field 6 to a 12-bit binary num
  		if($six != '' && intval($six) <= 4095){
  			$zero[5]='1';
  			$six1 = str_pad(decbin($six), 12, '0',STR_PAD_LEFT);
  		}
  		else if(intval($six) > 4095 || strlen($six) > 4) {
			//Error, bad input.
  			return array("Bin"=>'E6');
		}
  	
  		//same as field 5
  		if($seven != ''){
			if(ctype_alpha($seven[0]) && intval(substr($seven, 1)) <= 99999){
  				$zero[6]='1';
  				$seven1 = decbin(ord(strtoupper($seven[0]))-64); //to upper-->to ascii -64 --> to bin
  				$seven2 = decbin(substr($seven, 1));	//changes to bin num
  			
  				$seven1 = str_pad($seven1, 5, "0", STR_PAD_LEFT); //always make sure max bits are there
  				$seven2 = str_pad($seven2, 17, "0", STR_PAD_LEFT);
  				if(is_numeric(substr($seven, -1)))
  					$seven2 = $seven2."0";	//changes to bin num
  				else
  					$seven2 = $seven2."1";
			} else {
				//Error bad input
				return array("Bin"=>'E5');
			}
  		}
  		$seven1 = str_pad($seven1, 5, "0", STR_PAD_LEFT); //always make sure max bits are there
  		$seven2 = str_pad($seven2, 18, "0", STR_PAD_LEFT);
  		
		$eight1 = '';
		$hasLetter = false;
  		$letterPlacement = -1;
  		for($i = 0; $i < strlen($element8); $i++){
  			if(!is_numeric($element8[$i])){
  				$hasLetter = true;
  				$letterPlacement = $i;
  				break;
  			}
  		}
  		if(!$hasLetter){
			if($element8 != '' && $element8meaning == 'year' && intval($element8) <= 4095){
				$zero[7] = '1';
				//This is a year, between 0 and 4095. Convert to 12-bit number
				$eight1 = str_pad(decbin($element8), 12, '0',STR_PAD_LEFT);
				$eight1 = $eight1."00";
  			}
  			else if($element8meaning == 'year' && (intval($element8) > 4095 || strlen($element8) > 4)) {
				//Error, bad input.
  				return array("Bin"=>'E8');
			}
		}
		else{
			$numeric = substr($element8, 0, $letterPlacement);
  			$letters = substr($element8, $letterPlacement);
  			
  			if($element8 != '' && $element8meaning == 'year' && intval($numeric) <= 4095){
  				$zero[7]='1';
  				$eight1 = str_pad(decbin($numeric), 12, '0',STR_PAD_LEFT);
  				$let = '00';
  				if(strtolower($letters) == "x") $let = "01";
  				else if(strtolower($letters) == "b") $let = "10";
  				else if(strtolower($letters) == "ax") $let = "11";
  				$eight1 = $eight1.$let;
  			}
  			else if($element8meaning == 'year' && (intval($element8) > 4095 || strlen($element8) > 4)) {
				//Error, bad input.
  				return array("Bin"=>'E8');
			}
  			
		}
		
  
  		//$str = join("", array($zero, $one1, $one2, $one3, $two1, $three1, $four1, $five1, $five2, $six1, $seven1, $seven2));
			
		$str = $LC2Bin_version.$zero.$one1.$one2.$one3.$two1 ; //First two fields are required
		if($zero[2] == '1'){
			$str .= $three1;
		}
		if($zero[3] == '1'){
			$str .= $four1;
		}		
		if($zero[4] == '1'){
			$str .= $five1.$five2;
		}
		if($zero[5] == '1'){
			$str .= $six1;
		}
		if($zero[6] == '1'){
			$str .= $seven1.$seven2;
		}
		if($zero[7] == '1'){
			$str .= $eight1;
		}

		//There are 18 rows of content, each with 6 bits. .. TODO: not sure why
		// this is 18, not 19.
		$str = str_pad($str,18*6,'0',STR_PAD_RIGHT);
		$str = AddErrorBits_delegate($str);
  		$arr = array("Bin"=>$str);
  		/*$arr = array("alphabetic" => $one1.$one2.$one3, 
  					"wholeClass" => $two1,
  					"decClass" => $three1,
  					"date1" => $four1,
  					"cutter1" => $five1.$five2,
  					"date2" => $six1,
  					"cutter2" => $seven1.$seven2,
  					"element8" => $eight1);*/
  		
  		return $arr;
  
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
	function Bin2LC_delegate($bits){
		//$bits = $json_bits;
		global $LC2Bin_version;
		$bits = StripErrorBits_delegate($bits);

		$in_version = substr($bits,0,7);
		if($in_version != $LC2Bin_version){
			//TODO Error handling
		}
		$bits = substr($bits,7);
		//TODO: Don't hard-code the offsets. Peel off the front, like
		// I do with the version number. That way if we change
		// the length of one of the fields, we don't have to redo it all.
		$bits0 = substr($bits, 0, 8);
		$bits  = substr($bits,8);

		if($bits0[0] == '1'){
			$bits1 = substr($bits, 0, 15);
			$bits = substr($bits, 15);
		}

		if($bits0[1] == '1'){
			$bits2 = substr($bits, 0, 13);
			$bits = substr($bits, 13);
		}

		if($bits0[2] == '1'){
			$bits3 = substr($bits, 0, 10);
			$bits = substr($bits, 10);
		}
		if($bits0[3] == '1'){
			$bits4 = substr($bits, 0, 12);
			$bits = substr($bits, 12);
		}
		if($bits0[4] == '1'){
			$bits5 = substr($bits, 0, 23);
			$bits = substr($bits, 23);
		}
		if($bits0[5] == '1'){
			$bits6 = substr($bits, 0, 12);
			$bits = substr($bits, 12);
		}
		if($bits0[6] == '1'){
			$bits7 = substr($bits, 0, 23);
			$bits = substr($bits, 23);
		}
		
		$bits8 = "";
		if($bits0[7] == '1'){
			$bits8 = substr($bits, 0, 14);
			$bits = substr($bits, 14);
		}

		$fld1 = "";
		$fld2 = "";
		$fld3 = "";
		$fld4 = "";
		$fld5 = "";
		$fld6 = "";
		$fld7 = "";
		$fld8 = "";
		
		if($bits0[0] == 1){
			//if(substr($bits1, 0, 5)!= 0)
				$dec0 = (substr($bits1, 0, 5)!= 0) ? strtoupper(chr( bindec(substr($bits1, 0, 5))+64)) : "";
			//if(substr($bits1, 5, 5)!=0)
				$dec1 = (substr($bits1, 5, 5)!=0) ? strtoupper(chr( bindec(substr($bits1, 5, 5))+64)):"";
			//if(substr($bits1, 10, 5)!=0)
				$dec2 = (substr($bits1, 10, 5)!=0) ? strtoupper(chr( bindec(substr($bits1, 10, 5))+64)) : "";
			$fld1 = $dec0.$dec1.$dec2;
		}
		if($bits0[1] == 1){
			$fld2 = bindec($bits2); 
		}
		if($bits0[2] == 1){
			$fld3 = bindec($bits3); 
		}
		if($bits0[3] == 1){
			$fld4 = bindec($bits4); 
		}
		//cutter1
		if($bits0[4] == 1){
			if(substr($bits5, 0, 5)!=0)
				$fld51 = strtoupper(chr( bindec(substr($bits5, 0, 5))+64));
			$fld52 = bindec(substr($bits5, 5, 17));
			if($bits5[22] == "1") 
				$fld52 = $fld52."x";
			$fld5 = $fld51.$fld52;
		}
		if($bits0[5] == 1){
			$fld6 = bindec($bits6); 
		}
		if($bits0[6] == 1){
			if(substr($bits7, 0, 5)!=0)$fld71 = strtoupper(chr( bindec(substr($bits7, 0, 5))+64));
			$fld72 = bindec(substr($bits7, 5, 17));
			if($bits7[22] == "1") $fld72 = $fld72."x";
			$fld7 = $fld71.$fld72;
		}
		$fld8meaning = 'unknown';
		if($bits0[7] == 1){
			$num = bindec(substr($bits8, 0, -2));
			$letter = "";
			$sub = substr($bits8, -2);
			if($sub == "01") $letter = "x";
			else if($sub == "10") $letter = "b";
			else if($sub == "11") $letter = "ax";
			$fld8 =  $num.$letter;
			$fld8meaning = 'year';
		}
		
		$arr = array($bits0, $fld1, $fld2, $fld3, $fld4,$fld5,$fld6,$fld7);

		
		$parsed_call = array('version' => $in_version, 'fieldflags' => $arr[0], 'alphabetic' => $arr[1], 'wholeClass' => $arr[2], 'decClass' => $arr[3], 'date1' => $arr[4], 'cutter1' => $arr[5], 'date2' => $arr[6], 'cutter2' => $arr[7], 'element8' => $fld8, 'element8meaning' => $fld8meaning);
		
		//Should make $c_num_arr = "AAA123.456 B78 2012 C90 1992 FLD8"
		$c_num_arr = array( ($arr[1].$arr[2]) );
		if(strlen($arr[3])>0) array_push($c_num_arr, (".".$arr[3]));
		if(strlen($arr[4])>0) array_push($c_num_arr, (" ".$arr[4]) );
		if(strlen($arr[5])>0) array_push($c_num_arr, (" .".$arr[5]) );
		if(strlen($arr[6])>0) array_push($c_num_arr, (" ".$arr[6]) );
		if(strlen($arr[7])>0) array_push($c_num_arr, (" ".$arr[7]) );
		if(strlen($fld8)>0) array_push($c_num_arr, (" ".$fld8) );
		
		$call_number = implode($c_num_arr);
		
		$full_result = array('call_number' => $call_number, 'parsed_call_number' => $parsed_call, 'result' => 'SUCCESS');
		return $full_result;
	}
	
	/**
 * MultBin2LC - Converts a multiple Binary numbers to LC numbers
 *
 * This function compliments MultLC2Bin.
 *
 * @param $arr - JSon array of binary numbers
 * @return - Json array of LC call numbers
 */
	function MultBin2LC_delegate($arr){
		$result = array();
		for($i = 0; $i< count($arr);$i++){
			$bin = Bin2LC_delegate($arr[$i]);
			$result[$i] = $bin;
		}
		return $result;
	}
	
	/**
 * MultLC2Bin - Converts a multiple LC call Numbers to Binary
 *
 * This function compliments MultBin2LC.
 *
 * @param $arr - Json array of LC call numbers
 * @return - Json array of binary numbers
 */
	function MultLC2Bin_delegate($arr){
		$result = array();
		for($i = 0; $i< count($arr);$i++){
			$bin = LC2Bin_delegate($arr[$i][0],$arr[$i][1],$arr[$i][2],$arr[$i][3],$arr[$i][4],$arr[$i][5],$arr[$i][6]);
			$result[$i] = $bin;
		}
		return $result;
	}

	function AddErrorBits_delegate($bits){
		//First, make sure the string is a multiple of 6 in length
		$len = strlen($bits);
		$len = $len + (6-($len % 6));
		$bits = str_pad($bits, $len, "0", STR_PAD_RIGHT);
		$retbits = "";
		$cols = array(0 => 0, 1 => 0, 2 => 0, 3=> 0, 4 => 0, 5 => 0, 6 => 0, 7=>0);
		while(strlen($bits) > 0){
			$row = substr($bits,0,6);
			$bits = substr($bits,6);
			$parity = 0;
			for($i=0;$i<6;$i++){
				if($row[$i] == '1') {
					$cols[$i]++;
					$cols[6]++;
					$parity++;
				}
			}
			if($parity % 2 == 0){
				$row .= '0';
			} else {
				$row .= '1';
			}
			$retbits .= $row;
		}
		$row = "";
		for($i=0;$i<7;$i++){
			if($cols[$i]%2 == 0){
				$row .= '0';
			} else {
				$row .= '1';
			}
		}
		$retbits .= $row;

		return $retbits;
	}

	function StripErrorBits_delegate($bits){
		$retbits = "";
		while(strlen($bits) > 7){
			$retbits .= substr($bits,0,6);
			$bits = substr($bits,7);
		}

		return $retbits;
	}

 ?>
