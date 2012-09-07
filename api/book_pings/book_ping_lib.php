<?php

  /** 
   * @file
   * Add an array of book_pings to MySQL database.
   *
   * Copyright 2011 by ShelvAR Team.
   *
   * @version Jan 9, 2012
   * @author Bo Brinkman
   */

  /** 
   * Add an array of book_pings to the MySQL database.
   *
   * @param $jsoninput 
   *   The data should be a JSON array of entries, each entry consists of
   *   the following fields:
   *   - "book_tag" - REQUIRED - A string of up to 40 characters,
   *     which is the base-64 representation of the binary tag
   *   - "book_call" - REQUIRED - A string of up to 240 characters,
   *     which is the human-readable call number
   *   - "neighbor1_tag" - OPTIONAL - VARCHAR(40), base-64 tag of the
   *     left neighbor
   *   - "neighbor1_call" - OPTIONAL - VARCHAR(240), human-readable call #
   *     of left neighbor
   *   - "neighbor2_tag" - OPTIONAL - VARCHAR(40), base-64 tag of the
   *     right neighbor
   *   - "neighbor2_call" - OPTIONAL - VARCHAR(240), human-readable call #
   *     of right neighbor
   *   - "ping_time" - REQUIRED - DATETIME. Should be in the 
   *     "0000-00-00 00:00:00" format
   *
   * @return
   *   A string. "SUCCESS" if everything worked, "ERRORS" if not. If the return
   *   value is "ERRORS," information about the error is printed in HTML format.
   *
   * Example input:
   * @code
   *   [{"book_tag":"0cV2w09ApwQbT8000000000M", "book_call":"QH585 .S56 2009", "neighbor1_tag":"", "neighbor1_call":"","neighbor2_tag":"0eF2w09Agh99RLR8g00000nM", "neighbor2_call":"QH585.2 .B375 2005", "ping_time":"2011-10-08 09:10:15"}, {"book_tag":"0eF2w09Agh99RLR8g00000nM", "book_call":"QH585.2 .B375 2005",  "neighbor1_tag":"0cV2w09ApwQbT8000000000M", "neighbor1_call":"QH585 .S56 2009", "neighbor2_tag":"0eF2w09Aghe0yfR0000000hM", "neighbor2_call":"QH585.2 .L32 2004", "ping_time":"2011-10-08 09:10:15"}, 
{"book_tag":"0eF2w09Aghe0yfR0000000hM", "book_call":"QH585.2 .L32 2004",  "neighbor1_tag":"0eF2w09Agh99RLR8g00000nM", "neighbor1_call":"QH585.2 .B375 2005", "neighbor2_tag":"", "neighbor2_call":"", "ping_time":"2011-10-08 09:10:16"}]
   * @endcode
   * See http://json.org
   */

    function do_book_ping($jsoninput,$institution=""){
  //TODO should also take $institution as input, and add that to the record in the database
  $decoded = json_decode($jsoninput,true);
  $success = false; //Assume JSON decoding failed.
  
  //Check for errors in JSON decoding
  switch (json_last_error()) {
  case JSON_ERROR_NONE:
    $success = true; //JSON decoding succeeded after all
    break;
  case JSON_ERROR_DEPTH:
    echo 'JSON parse - Maximum stack depth exceeded';
    break;
  case JSON_ERROR_STATE_MISMATCH:
    echo 'JSON parse - Underflow or the modes mismatch';
    break;
  case JSON_ERROR_CTRL_CHAR:
    echo 'JSON parse - Unexpected control character found';
    break;
  case JSON_ERROR_SYNTAX:
    echo 'JSON parse - Syntax error, malformed JSON';
    break;
  case JSON_ERROR_UTF8:
    echo 'JSON parse - Malformed UTF-8 characters, possibly incorrectly encoded';
    break;
  default:
    echo 'JSON parse - Unknown error';
    break;
  }
  
  if(!$success){
    die('JSON error stopped script');
  }
  
  include_once "../../db_info.php";
  $con = mysql_connect($server,$user,$password);
  if (!$con){
    die('Could not connect: ' . mysql_error());
  }
  
  mysql_select_db($database, $con);
  
  foreach($decoded as &$book_ping_entry){
    //First, check that all required fields are present.
    // If not, report the error, but process the rest of the
    // pings anyway.
    if(strlen($book_ping_entry["book_tag"]) < 1){
      Print "<pre>";
      Print "Error - Missing book_tag <br />";
      var_dump($book_ping_entry);
      Print "</pre><br />";
      $success = false;
      continue;
    }
    if(strlen($book_ping_entry["book_call"]) < 1){
      Print "<pre>";
      Print "Error - Missing book_call<br />";
      var_dump($book_ping_entry);
      Print "</pre><br />";
      $success = false;
      continue;
    }
    if(strlen($book_ping_entry["ping_time"]) < 1){
      Print "<pre>";
      Print "Error - Missing ping_time<br />";
      var_dump($book_ping_entry);
      Print "</pre><br />";
      $success = false;
      continue;
    }
    
    //If we reached this point we should have all of the required fields.
    // No guarantee that
    // they are the correct format, though.
    if(!mysql_query("INSERT INTO book_pings (book_tag, book_call, neighbor1_tag, neighbor1_call," .
		    " neighbor2_tag, neighbor2_call, ping_time, institution) VALUES ('".
		    $book_ping_entry["book_tag"] . "', '" .
		    $book_ping_entry["book_call"] . "', '" .
		    $book_ping_entry["neighbor1_tag"] . "', '" .
		    $book_ping_entry["neighbor1_call"] . "', '" .
		    $book_ping_entry["neighbor2_tag"] . "', '" .
		    $book_ping_entry["neighbor2_call"] . "', '" .
		    $book_ping_entry["ping_time"] . "', '".
		    $institution .
		    "')")){
      Print '<pre>SQL Insert failed' . mysql_error();
      Print '<br />';
      var_dump($book_ping_entry);
      Print '<br /></pre>';
      $success = false;
    } else {
      //Print "SQL Insert succeeded.<br />";
    }
  }
  
  if($success){
    return 'SUCCESS';
  } else {
    return 'ERRORS';
  }
  
  mysql_close($con);
}

?>
