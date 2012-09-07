<?php
include_once "../lc2bin/LC_Converter_lib.php";
include_once "../tagmaker/base64_lib.php";

function lessthan($base641, $base642)
{
	$lc1 = array('LC'=>Bin2LC(base642bin($base641)));
	$lc2 = array('LC'=>Bin2LC(base642bin($base642)));
	
	$lc1 = $lc1["LC"];
	$lc2 = $lc2["LC"];
	
	if(strcmp($lc1['alphabetic'],$lc2['alphabetic']) < 0) return true;
	if(strcmp($lc1['alphabetic'],$lc2['alphabetic']) > 0) return false;
	
	if($lc1['wholeClass'] < $lc2['wholeClass']) return true;
	if($lc1['wholeClass'] > $lc2['wholeClass']) return false;
	
	if($lc1['decClass'] < $lc2['decClass']) return true;
	if($lc1['decClass'] > $lc2['decClass']) return false;
	
	if($lc1['date1'] < $lc2['date1']) return true;
	if($lc1['date1'] > $lc2['date1']) return false;
	
	if($lc1['cutter1'] < $lc2['cutter1']) return true;
	if($lc1['cutter1'] > $lc2['cutter1']) return false;
	
	if($lc1['date2'] < $lc2['date2']) return true;
	if($lc1['date2'] > $lc2['date2']) return false;
	
	if($lc1['cutter2'] < $lc2['cutter2']) return true;
	if($lc1['cutter2'] > $lc2['cutter2']) return false;
	
	if(strcmp('year', $lc1['element8meaning']) != 0 && strcmp('year', $lc2['element8meaning']) != 0) return true;
	
	if($lc1['element8'] < $lc2['element8']) return true;
	if($lc1['element8'] > $lc2['element8']) return false;
	
	return false;
}
?>