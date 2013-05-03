<?php
/**
 * @file
 * @author Bo Brinkman
 * @date 2013-03-29
 *
 * A function set used to convert a human-readable LC call number to
 * a base-64 string, used in a ShelvAR tag.
 *
 */

include_once "../base64_lib.php";
include_once "../HuffmanEncoder.php";
include_once "../HammingCode.php";
//include_once "../api_ref_call.php";

function lc_to_tag($call_number_in){
  //TODO: Use result for something...
  $result = "SUCCESS";

  $huffman_code = huffman_encode($call_number_in);
  
  //The call number will be divided into blocks of 26 bits,
  // and encoded using Hamming(32,26)
  $num_call_bits = 32*ceil(strlen($huffman_code)/26.0);
  $num_call_rows = ceil($num_call_bits/7.0);

  //Bottom row is tag type and size
  //Next row up is information about the call number encoding
  //So we need two rows for that stuff, the rest is data
  $num_tag_rows = $num_call_rows + 2;
  
  $tag_type = "00"; //Library call number type
  $tag_size_bits = "00"; //4 blocks, which is 147 bits 
  $num_tag_bits = 147;
  if($num_tag_rows > 21){
    $tag_size_bits = "01";//6 blocks, which is 210 bits
    $num_tag_bits = 210;
  }
  if($num_tag_rows > 30){
    $tag_size_bits = "10";//8 blocks, 273 bits
    $num_tag_bits = 273;
  }
  if($num_tag_rows > 39){
    $tag_size_bits = "11";//10 blocks, 336 bits
    $num_tag_bits = 336;
  }
  if($num_tag_rows > 48){
    $result = "ERROR. Call number is too long to be represented as a tag.";
    return "";
  }
  
  $tag_binary = encode_7_4($tag_type . $tag_size_bits);
  //Call number encoding info. Right now "0000" is the only valid option,
  // which is a Huffman-encoded LC number
  $tag_binary .= encode_7_4("0000");
  
  while(strlen($huffman_code) >= 26){
    $tag_binary .= encode_32_26(substr($huffman_code,0,26));
    $huffman_code = substr($huffman_code,26);
  }

  if(strlen($huffman_code) != 0){
    while(strlen($huffman_code) < 26){
      $huffman_code .= "0";
    }

    $tag_binary .= encode_32_26(substr($huffman_code,0,26));
  }

  while(strlen($tag_binary) < $num_tag_bits){
    $tag_binary .= "0";
  }

  return bin2base64($tag_binary);
}

function tag_to_lc($b64Tag){
  $binaryTag = base642bin($b64Tag);
  $type_and_size = decode_7_4(substr($binaryTag,0,7));
  if(strlen($type_and_size) != 4){
    return "";
  }

  $binaryTag = substr($binaryTag,7);
  $encoding = decode_7_4(substr($binaryTag,0,7));

  if(strlen($encoding) != 4){
    return "";
  }

  $binaryTag = substr($binaryTag,7);
  if(strcmp(substr($type_and_size,0,2),"00") != 0){
    return "";
  }

  $num_blocks = 4;
  if(strcmp(substr($type_and_size,2,2),"01") == 0){
    $num_blocks = 6;
  } else if(strcmp(substr($type_and_size,2,2),"10") == 0){
    $num_blocks = 8;
  } else if(strcmp(substr($type_and_size,2,2),"11") == 0){
    $num_blocks = 10;
  }

  if(strcmp($encoding,"0000") != 0){
    return "";
  }

  $huffman_string = "";
  for($i=0;$i<$num_blocks;$i++){
    $huffman_string .= decode_32_26(substr($binaryTag,0,32));
    $binaryTag = substr($binaryTag,32);
  }
  if(strlen($huffman_string) != 26*$num_blocks){
    return "";
  }

  return huffman_decode($huffman_string);
}

 ?>