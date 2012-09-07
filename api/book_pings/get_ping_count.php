<?php

  /** 
   * @file
   * Get count of number of book_pings since a particular date
   *
   * Copyright 2011 by ShelvAR Team.
   *
   * @version Jan 24, 2012
   * @author Bo Brinkman
   */

  /** 
   * Get number of book pings since a certain date
   *
   * @param[in] $date
   *   The date at which we want to start counting. This should be an
   *   integer, which is seconds 
   *   since 1970. This corresponds to the time() method of PHP. The
   *   function will count ping times 
   *   that are >= the specified date. Also note that this the SQL
   *   COUNT command for efficiency.
   *
   * @return
   *   An integer, which is the number of book pings since the given date.
   *
   */

function get_ping_count_since($date){
  include_once "../../db_info.php";
  $con = mysql_connect($server,$user,$password);
  if (!$con){
    die('Could not connect: ' . mysql_error());
  }

  mysql_select_db($database, $con);

  $date_formatted = date("Y-m-d H:i:s",$date);

  $resource = mysql_query("SELECT COUNT(*) as TOTALFOUND FROM book_pings WHERE ping_time>='". $date_formatted ."'");
  if($resource == FALSE){
    Print '<pre>SQL select failed' . mysql_error();
    Print '<br />';
  } else {
    //Print "SQL Insert succeeded.<br />";
    //Print mysql_num_rows($resource);
    return mysql_result($resource,0,"TOTALFOUND");
  }
  
  
}

?>
