<?php

//Print get_ping_count_since(0);
//Print "<br />";
//Print get_ping_count_since(time()-(7*24*60*60)); //one week

function get_ping_count_since($date){
  include_once "../db_info.php";
  $con = mysql_connect($server,$user,$password);
  if (!$con){
    die('Could not connect: ' . mysql_error());
  }

  mysql_select_db($database, $con);

  $date_formatted = date("Y-m-d H:i:s",$date);

    $resource = mysql_query("SELECT * FROM book_pings WHERE ping_time>='". $date_formatted ."'");
    if($resource == FALSE){
      Print '<pre>SQL select failed' . mysql_error();
      Print '<br />';
    } else {
      //Print "SQL Insert succeeded.<br />";
	//Print mysql_num_rows($resource);
	return mysql_num_rows($resource);
    }
  
  
}

?>
