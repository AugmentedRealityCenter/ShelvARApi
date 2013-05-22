<?php

//Generator matrix for Hamming(7,4), row-major order
$G_7_4 = 
  "1110000".
  "1001100".
  "0101010".
  "1101001";
//Parity-check matrix for Hamming(7,4), row-major order
$H_7_4 =
  "0001111".
  "0110011".
  "1010101";

$G_31_26 =
  "1110000000000000000000000000000"		.
  "1001100000000000000000000000000"		.
  "0101010000000000000000000000000"		.
  "1101001000000000000000000000000"		.
  "1000000110000000000000000000000"		.
  "0100000101000000000000000000000"		.
  "1100000100100000000000000000000"		.
  "0001000100010000000000000000000"		.
  "1001000100001000000000000000000"		.
  "0101000100000100000000000000000"		.
  "1101000100000010000000000000000"		.
  "1000000000000001100000000000000"		.
  "0100000000000001010000000000000"		.
  "1100000000000001001000000000000"		.
  "0001000000000001000100000000000"		.
  "1001000000000001000010000000000"		.
  "0101000000000001000001000000000"		.
  "1101000000000001000000100000000"		.
  "0000000100000001000000010000000"		.
  "1000000100000001000000001000000"		.
  "0100000100000001000000000100000"		.
  "1100000100000001000000000010000"		.
  "0001000100000001000000000001000"		.
  "1001000100000001000000000000100"		.
  "0101000100000001000000000000010"		.
  "1101000100000001000000000000001";


$H_31_26 =
  "0000000000000001111111111111111".
  "0000000111111110000000011111111".
  "0001111000011110000111100001111".
  "0110011001100110011001100110011".
  "1010101010101010101010101010101";




function encode_7_4($input){
  global $G_7_4;
  if(strlen($input) != 4) return "";

  $ret="";
  for($x=0;$x<7;$x++){
    $parity=0;
    for($y=0;$y<4;$y++){
      if(substr($input,$y,1)=="1" && substr($G_7_4,$y*7+$x,1) == "1"){
	$parity = ($parity+1)%2;
      }
    }
    if($parity == 0){
      $ret .= "0";
    } else {
      $ret .= "1";
    }
  }
  return $ret;
}

function decode_7_4($input){
  global $H_7_4;

  if(strlen($input) != 7) return "";

  $syndrome=0;
  //First, correct errors, if possible
  for($y=0;$y<3;$y++){
    $parity=0;
    for($x=0;$x<7;$x++){
      if(substr($input,$x,1)=="1" && substr($H_7_4,$y*7+$x,1) == "1"){
	$parity = ($parity+1)%2;
      }
    }
    $syndrome = $syndrome*2;
    if($parity == 0){
      $syndrome += 0;
    } else {
      $syndrome += 1;
    }
  }

  //If there is a bad bit, flip it.
  if($syndrome != 0){
    $syndrome -= 1;
    if(substr($input,$syndrome,1) == "1"){
      $input = substr_replace($input,"0",$syndrome,1);
    } else {
      $input = substr_replace($input,"1",$syndrome,1);
    }
  }

  //The encoded work is bits 2, 4, 5 and 6.
  $ret = substr($input,2,1) . substr($input,4,3);
  return $ret;
}

function encode_31_26($input){
  global $G_31_26;
  if(strlen($input) != 26) return "";

  $ret="";
  for($x=0;$x<31;$x++){
    $parity=0;
    for($y=0;$y<26;$y++){
      if(substr($input,$y,1)=="1" && substr($G_31_26,$y*31+$x,1) == "1"){
	$parity = ($parity+1)%2;
      }
    }
    if($parity == 0){
      $ret .= "0";
    } else {
      $ret .= "1";
    }
  }
  return $ret;
}

function decode_31_26($input){
  global $H_31_26;

  if(strlen($input) != 31) return "";

  $syndrome=0;
  //First, correct errors, if possible
  for($y=0;$y<5;$y++){
    $parity=0;
    for($x=0;$x<31;$x++){
      if(substr($input,$x,1)=="1" && substr($H_31_26,$y*31+$x,1) == "1"){
	$parity = ($parity+1)%2;
      }
    }
    $syndrome = $syndrome*2;
    if($parity == 0){
      $syndrome += 0;
    } else {
      $syndrome += 1;
    }
  }

  //If there is a bad bit, flip it.
  if($syndrome != 0){
    $syndrome -= 1;
    if(substr($input,$syndrome,1) == "1"){
      $input = substr_replace($input,"0",$syndrome,1);
    } else {
      $input = substr_replace($input,"1",$syndrome,1);
    }
  }

  //The encoded work is bits 2, 4,5,6, and 8,9,10,11,12,13,14, and 16-31 
  $ret = substr($input,2,1) . substr($input,4,3) . substr($input,8,7) . substr($input,16,15);
  return $ret;
}

function encode_32_26($input){
  if(strlen($input) != 26) return "";

  $enc = encode_31_26($input);

  if(strlen($enc) != 31) return "";

  $parity = 0;
  for($i=0; $i<31;$i++){
    if(substr($enc,$i,1) == "1"){
      $parity = ($parity+1)%2;
    }
  }
  if($parity == 0){
    $enc = $enc . "0";
  } else {
    $enc = $enc . "1";
  }
  return $enc;
}

function decode_32_26($input){
  if(strlen($input) != 32) return "";

  $parity = 0;
  if(substr($input,31,1) == "0"){
    $parity = 0;
  } else {
    $parity = 1;
  }

  $dec = decode_31_26(substr($input,0,31));
  if(strlen($dec) != 26) return "";

  $enc = encode_31_26($dec);
  if(strlen($enc) != 31) return "";

  for($i=0; $i<31;$i++){
    if(substr($enc,$i,1) == "1"){
      $parity = ($parity+1)%2;
    }
  }

  if($parity == 0){
    return $dec;
  }else if(strcmp($enc,substr($input,0,31)) == 0){
    return $dec;
  }else {
    return "";
  }
}

//=============Testing code. Comment out in released code=======


/*
//should give 0000000
print(encode_7_4("0000")."<br/>");
//should give 1111111
print(encode_7_4("1111")."<br/>");
//should give 1000011
print(encode_7_4("0011")."<br/>");

//All of these should give 0011
print(decode_7_4("1000011")."<br/>");
print(decode_7_4("1000001")."<br/>");
print(decode_7_4("1010011")."<br/>");

//all of these should give 1111
print(decode_7_4("0111111")."<br/>");
print(decode_7_4("1011111")."<br/>");
print(decode_7_4("1101111")."<br/>");
print(decode_7_4("1110111")."<br/>");
print(decode_7_4("1111011")."<br/>");
print(decode_7_4("1111101")."<br/>");
print(decode_7_4("1111110")."<br/>");

//These should return empty string
print("empties<br/>");
print(decode_7_4("111111")."<br/>");
print(decode_7_4("11111111")."<br/>");

print("31_26<br/>");
  
print(encode_31_26("00000000000000000000000000")."<br/>");
print(encode_31_26("11111111111111111111111111")."<br/>");

print(decode_31_26("0000000000000000000000000000000")."<br/>");
print(decode_31_26("1111111111111111111111111111111")."<br/>");
print(decode_31_26("1111111111111111111111111111110")."<br/>");
print(decode_31_26("1111111111111111111111111111101")."<br/>");

print("32_26<br/>");

print(encode_32_26("00000000000000000000000000")."<br/>");
print(encode_32_26("11111111111111111111111111")."<br/>");

print(decode_32_26("11111111111111111111111111111110")."<br/>");
print(decode_32_26("11111111111111111111111111101111")."<br/>");
print(decode_32_26("11111111111111111111111111011111")."<br/>");
print(decode_32_26("11111111111111111111111110111111")."<br/>");
print(decode_32_26("11111111111111111111111101111111")."<br/>");
print(decode_32_26("11111111111111111111111011111111")."<br/>");
print(decode_32_26("11111111111111111111110111111111")."<br/>");
print(decode_32_26("11111111111111111111101111111111")."<br/>");
print(decode_32_26("11111111111111111111011111111111")."<br/>");
print(decode_32_26("11111111111111111110111111111111")."<br/>");
print(decode_32_26("11111111111111111101111111111111")."<br/>");
print(decode_32_26("11111111111111111011111111111111")."<br/>");
print(decode_32_26("11111111111111110111111111111111")."<br/>");
print(decode_32_26("11111111111111101111111111111111")."<br/>");
print(decode_32_26("11111111111111011111111111111111")."<br/>");
print(decode_32_26("11111111111110111111111111111111")."<br/>");
print(decode_32_26("11111111111101111111111111111111")."<br/>");
print(decode_32_26("11111111111011111111111111111111")."<br/>");
print(decode_32_26("11111111110111111111111111111111")."<br/>");
print(decode_32_26("11111111101111111111111111111111")."<br/>");
print(decode_32_26("11111111011111111111111111111111")."<br/>");
print(decode_32_26("11111110111111111111111111111111")."<br/>");
print(decode_32_26("11111101111111111111111111111111")."<br/>");
print(decode_32_26("11111011111111111111111111111111")."<br/>");
print(decode_32_26("11110111111111111111111111111111")."<br/>");
print(decode_32_26("11101111111111111111111111111111")."<br/>");
print(decode_32_26("11011111111111111111111111111111")."<br/>");
print(decode_32_26("10111111111111111111111111111111")."<br/>");
print(decode_32_26("01111111111111111111111111111111")."<br/>");

print("empties<br/>");
print(decode_32_26("00111111111111111111111111111111")."<br/>");
print(decode_32_26("11111111111111111111111111111100")."<br/>");
*/

?>
