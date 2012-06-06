<?php

/**
 * @file
 * Display a graph showing when a book was seen, or not seen, during
 * shelf read.
 *
 * Copyright 2011 by ShelvAR Team.
 * 
 * @version Oct 6, 2011
 * @author Bo Brinkman
 */

//Prevent caching
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

//Tell the browser this is a PNG
header("Content-type: image/png");

//Get list of books seen, and not seen, during given time period
include 'book_seen.php';
$ret = book_seen($_GET["book_tag"],$_GET["start_date"],$_GET["end_date"]);

//Get start and end dates. If not given, use reasonable defaults.
$start_date = $_GET["start_date"];
if($start_date == FALSE){
  $keys = array_keys($ret);
  $start_date = $keys[0]; //Default start time is date of first result.
}
if($start_date == FALSE){
  //TODO what if there are no results? Need another default case. Default to 30.5 days
}

$end_date = $_GET["end_date"];
if($end_date == FALSE){
  $end_date = date("Y-m-d",time());//Default to current datetime
}

//Convert all dates to timestamp format, seconds since Jan 1, 1970
$end_time = strtotime($end_date);
$start_time = strtotime($start_date);
$mid1_time = ((2*$start_time) + $end_time)/3;
$mid2_time = (($start_time) + (2*$end_time))/3;

//Get string version of the two mid-point dates
$mid1_date = date("Y-m-d",$mid1_time);
$mid2_date = date("Y-m-d",$mid2_time);

//Create image
$im = imagecreatetruecolor(800,160);

//Define color and size "constants"
$black = imagecolorallocate($im,0,0,0);
$gray = imagecolorallocate($im,192,192,192);
$white = imagecolorallocate($im,255,255,255);
$on = imagecolorallocate($im,0,128,0);
$offc = imagecolorallocate($im,255,0,0);
$el_size = 8;

//Draw background and border
imagefill($im,0,0,$white);
imagerectangle($im,0,0,799,159,$black);

//Draw dates as vertical labels
imagestring($im,2,100,145,$start_date,$black);
imagestring($im,2,700,145,$end_date,$black);
//TODO: The positioning of the two mid labels is not precisely centered on the day
imagestring($im,2,300,145,$mid1_date,$black);
imagestring($im,2,500,145,$mid2_date,$black);

//Draw horizontal labels and hash lines
imagestring($im,2,5,42,"Off shelf",$black);
imageline($im,80,50,780,50,$black);
imagestring($im,2,5,92,"On shelf",$black);
imageline($im,80,100,780,100,$black);

//Draw vertical hash lines. There should be at most 11 vertical hash lines,
// but no more than 1 vertical hash line per day. Vertical hash lines
// should be evenly spaced, and correspond to actual days.
$days_in_graph = ($end_time - $start_time)/(24*60*60);
//This actually gives 11 hash lines, because we loop from 0 to 10, INCLUSIVE
$days_per_vertical_hash = round($days_in_graph / 10);
if($days_per_vertical_hash < 1){
  $days_per_vertical_hash = 1;
}
$num_hashes = round($days_in_graph / $days_per_vertical_hash);
$x_per_day = 600 / $days_in_graph;
for($i = 0; $i <= $num_hashes; $i++){
  $x = 130 + ($x_per_day * $days_per_vertical_hash * $i);
  imageline($im,$x,20,$x,130,$gray);
}

//For each item we get back from book_seen, plot on graph.
foreach($ret as $key => $value){
  $ping_time = strtotime($key);
  $x_pct = ($ping_time - $start_time)/($end_time - $start_time);

  //x range is 130 to 730
  $x = 130 + 600*$x_pct;
  if($value == 1){
    imagefilledellipse($im,$x,100,$el_size,$el_size,$on);
  } else {
    imagefilledellipse($im,$x,50,$el_size,$el_size,$offc);
  }
}



imagepng($im);
imagedestroy($im);

?>
