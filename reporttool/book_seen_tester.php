<html>
<head>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
</head>
<body>

<?php

/**
 * @file
 * Get a list of the days on which tis book was seen.
 *
 * Copyright 2011 by ShelvAR Team. Note: I'm following
 * the Drupal commenting conventions to the best of my
 * ability.
 *
 * @see http://drupal.org/node/1354
 * 
 * @version Oct 6, 2011
 * @author Bo Brinkman
 */

  /**
   * Placeholder variable for main function return value
   */

require_once 'book_seen.php';

$ret = book_seen($_GET["book_tag"],$_GET["start_date"],$_GET["end_date"]);

Print '<table border=1><tr><td>Book on shelf, dates</td><td>Book off shelf, dates</td></tr>';
foreach($ret as $key => $value){
  Print '<tr>';
  if($ret[$key] != 1){
    Print '<td>&nbsp;</td>';
  } 
  Print '<td>' . $key . '</td>';
  if($ret[$key] == 1){
    Print '<td>&nbsp;</td>';
  } 
  Print '</tr>';
}
Print '</table>';

?>

</body>
</html>
