<?php
  //Include the associative array that contains the Huffman code
require("huffman-v1.php");

function huffman_encode($input){
  global $huffman1;

  $ret = "";
  $input = trim($input);

  for($i=0;$i<strlen($input);$i++){
    $ret .= $huffman1[ord(substr($input,$i))];
  }
  $ret .= $huffman1[0];//Strings should be null terminated when encoded
  return $ret;
}

//Find the first Huffman codeword that has the search string
// as a prefix
//TODO: This is stupidly inefficient. Improve using a trie-based
// decoder.
function huffmanSearch($input){
  global $huffman1;
  for($i=0;$i<256;$i++){
    if(strpos($huffman1[$i],$input)===0){
      return $i;
    }
  }

  return -1;
}

function huffman_decode($input){
  $ret="";
  while(strlen($input) > 0){
    //What is the length of the longest prefix that is in the table?
    $prefixLen=0;
    for($prefixLen=1; $prefixLen<strlen($input);$prefixLen++){
      $which = huffmanSearch(substr($input,0,$prefixLen));
      if($which == -1){
	$prefixLen--;
	break;
      }
    }

    //If prefixLen is 0, it means we are not making progress. Better abort.
    if($prefixLen <= 0) {
      return "";
    }

    $which = huffmanSearch(substr($input,0,$prefixLen));
    //Reached the null terminator, so we can return the result.
    if($which == 0) return $ret;

    $ret .= chr($which);
    $input = substr($input,$prefixLen);
  }

  //Looks like the string wasn't null terminated. Better abort.
  return "";
}

/*
//=====================================================================
//Tester. Comment out when using this file in production code.

//Does encode and decode work for all characters from a US keyboard?
function test1(){
  $toTest = "`1234567890-=qwertyuiop[]\\asdfghjkl;'zxcvbnm,./ ~!@#$%^&*()_+QWERTYUIOP{}|ASDFGHJKL:\"ZXCVBNM<>?";
  $encoded = encode($toTest);
  $decoded = decode($encoded);
  $passed = (strcmp($toTest,$decoded) == 0);
  if($passed){
    print("test1 passed<br/ >");
  } else {
    print("test1 failed<br/ >");
  }
}

//Test to see if whitespace trimming is working
function test2(){
  $toTest = " cow \t\n\t";
  $e = encode($toTest);
  $d = decode($e);
  $passed = (strcmp($d,"cow") == 0);
  if($passed){
    print("test2 passed<br/ >");
  } else {
    print("test2 failed<br/ >");
  }
}

//Main
test1();
test2();
*/
?>