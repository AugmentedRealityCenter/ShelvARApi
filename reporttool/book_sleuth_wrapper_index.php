<?php
include_once("book_seen.php");
include_once("lc_utils.php");

$JSONin = null;

if(isset($_POST["callNumInput"])){
	$JSONin = stripslashes($_POST["callNumInput"]);
	$JSONin = json_decode($JSONin,true);
}

if($JSONin == null)
    $JSONin = $_GET["book_tag"];
$seenArr = book_seen($JSONin,0,0);

include "../db_info.php";
$con = mysql_connect($server,$user,$password);
  if (!$con){
    die('Could not connect: ' . mysql_error());
  }
  
  mysql_select_db($database, $con);
	  
foreach ($seenArr as $key => $curItem) {
    $tempArr = array('start' => $key);
    
    if($curItem == 1)
    {
        $resource = mysql_query("SELECT book_call FROM book_pings " . 
                                "WHERE book_tag = '" . $JSONin . "'" .
                                "limit 1");
        
        $curcall = mysql_fetch_assoc($resource);
        $curcall = $curcall["book_call"];
        
        $tempArr['icon'] = '../timeline/timeline_js/images/dull-green-circle.png';
        $resource = mysql_query("SELECT * FROM book_pings " . 
                            "WHERE book_tag = '" . $JSONin . "'" .
                            "AND ping_time <= date_add('" . $key . "', interval 1 day)" .
                            "AND ping_time >= '" . $key . "'");
    
		  
		while ($row = mysql_fetch_assoc($resource)) {
			$neighbor1[] = $row["neighbor1_tag"];
			$neighbor1call[] = $row["neighbor1_call"];
			$neighbor2[] = $row["neighbor2_tag"];
			$neighbor2call[] = $row["neighbor2_call"];
		}
    
        if($resource == FALSE){
        //echo 'SQL Select failed' . mysql_error();
        } else if(mysql_num_rows($resource) == 0) {
        //echo 'No rows seleted';
        } else {
			$current_book_tag = $JSONin;
            $neighbor1_tag = $neighbor1[0];
            $neighbor2_tag = $neighbor2[0];
			$neighbor1_call = $neighbor1call[0];
			$neighbor2_call = $neighbor2call[0];
			
			if(!lessthan($current_book_tag, $neighbor1_tag) &&
				(lessthan($current_book_tag, $neighbor2_tag)) || strlen($neighbor2_tag) == 0)
				{
					$tempArr['icon'] = '../timeline/timeline_js/images/dull-green-circle.png';
				}
            
            else $tempArr['icon'] = '../timeline/timeline_js/images/gray-circle.png';

            $tempArr['description'] = 'left = ' . $neighbor1_call . '<br><br> right = ' . $neighbor2_call;
        }
    }
    else {
        $tempArr['icon'] = '../timeline/timeline_js/images/dull-red-circle.png';
        $tempArr['description'] = "The book was not found on the shelf today.";
    }
    $events[] = $tempArr;
}


$result = array("events" => $events);
echo json_encode($result);
?>
