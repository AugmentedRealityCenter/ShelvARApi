<?php

/**
 * @file
 * Get a list of the days on which this book was seen.
 *
 * Copyright 2011 by ShelvAR Team.
 * 
 * @version Oct 6, 2011
 * @author Bo Brinkman
 */

  /** 
   * Get a list of the dates when there was a shelf-read, and whether or not
   *    this particular book was seen on that day.
   *
   * @param $book_tag
   *    The base64 string for the book you are searching for.
   * @param $start_date
   *    Exclude all book pings before this date. "0000-00-00 00:00:00" format. If unset, 
   *    returns all book pings that come on or before end_date
   * @param $end_date
   *    Exclude all book pings after this date. "0000-00-00 00:00:00" format. If unset,
   *    defaults to current day and time.
   *
   * @return
   *   An associative array of integers, keyed by date (no hours/minutes/seconds, year-month-day only). 
   *   0 indicates that the book was not seen, but a neighbor was (indicating a shelf read on that day).
   *   1 indicates that the book was seen.
   */

function book_seen($book_tag,$start_date,$end_date){
  include "../db_info.php";
  $con = mysql_connect($server,$user,$password);
  if (!$con){
    die('Could not connect: ' . mysql_error());
  }
  
  mysql_select_db($database, $con);
  
  if($end_date == 0){
    $end_date = date("Y-m-d H:i:s",time());//Current datetime
  }

  //First, build up a list of the neighbors of the book we are looking for
  //TODO: Increase performance, possibly by using COUNT and UNIQUE
  $resource = mysql_query("SELECT * FROM book_pings " . 
	          "WHERE book_tag = '" . $book_tag . "'" .
		  "AND ping_time >= '" . $start_date . "'" .
		  "AND ping_time <= '" . $end_date . "'");

    if($resource == FALSE){
      //Print 'SQL Select failed' . mysql_error();
    } else if(mysql_num_rows($resource) == 0) {
      //Print 'No rows seleted';
    } else {  
      while ($row = mysql_fetch_assoc($resource)) {
	$arr = explode(" ",$row["ping_time"]);
	$days_seen[$arr[0]] = 1;
        $days_neighbor_seen[$arr[0]] = 1;
        $neighbors[$row["neighbor1_tag"]] = 1;
        $neighbors[$row["neighbor2_tag"]] = 1;
      }
      mysql_free_result($resource);


      /* Then calculate which days there was a shelf read
       * based on the days when a neighbor was seen */
      /* TODO Improve performance by selecting just unique dates */
      foreach($neighbors as $key => $value){
        $res2 = mysql_query("SELECT * FROM book_pings " .
                  "WHERE book_tag = '" . $key . "'" .
		  "AND ping_time >= '" . $start_date . "'" .
		  "AND ping_time <= '" . $end_date . "'");
        if($res2 == FALSE){
           //Print 'SQL Select failed' . mysql_error();
        } else {  
          while ($row = mysql_fetch_assoc($res2)) {
	    $arr = explode(" ",$row["ping_time"]);
	    $days_neighbor_seen[$arr[0]] = 1;
          }
          mysql_free_result($res2);
        }
      }

      /* Finally, check to see if the book in question was
       * seen on all the shelf-read days */
      ksort($days_neighbor_seen);    
      foreach($days_neighbor_seen as $key => $value){
	if($days_seen[$key] != 1){
          $seen_days[$key] = 0;
        } else {
          $seen_days[$key] = 1; 
        }       
      }  
    }
  
  mysql_close($con);

  return $seen_days;
}
//Very important to not have whitespace after the closing tag, since using
// in generating image files!
?>
