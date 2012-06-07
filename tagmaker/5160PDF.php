<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

/**
 * @file
 * @author Bo Brinkman
 * @date 2011-11-20
 *
 * Generate a PNG image that prints as a sheet of 5160 labels.
 * 
 * @param $tag1 First tag, in base 64 format
 * @param $tag2 ... $tag30 Remaining tags, in base 64 format
 */
require 'mem_image.php';
include_once 'tagmaker_lib.php';

/* 5160 info:
 * Size: 2.625" x 1"
 * Labels per sheet: 30
 * Margins: Top 0.5", Bottom 0.5", Left 0.21975", Right 0.21975"
 */	

//Stream handler to read from global variables
$JSONin;
if (!isset($_POST['TagList']))
	//echo "NO POST DATA";
	$JSONin = Array('0cOa620ZMw0ro_r000000060');
else {
	$JSONin2 = $_POST["TagList"];
	//$JSONin = json_decode($JSONin,true);
	$JSONin = str_getcsv($JSONin2);
}

$num_tags = count($JSONin);
$np = $num_tags / 30;
$num_pages = ceil($np);
$tagpos = 0;
$image;
//$pages = 0;

//$pdf = new FPDF('L','in','Letter');
$pdf = new MEM_IMAGE('L','in','Letter');
$URL = '';
$qstring;
$tagTracker = 0;
//for($i = 0; i < $num_pages; $i++)
while($tagTracker == 0)
{
	$URL = '';
	$pdf->AddPage();
	$qstring = '';
	for($j = 0; $j < 30; $j++){
		if($tagpos < $num_tags){
			if($j != 0)
				$qstring .= '&';
			$qstring .= 'tag'.$j.'='.$JSONin[$tagpos];
			$tagpos += 1;
		}
	}
	$tagTracker = ($tagpos % 30);
	if ($_SERVER["SERVER_PORT"] != "80") {
		$URL .= 'http://'.$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$URL .= 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	$URL = dirname($URL);
	$URL .= '/5160.png?' . $qstring;
	//print $URL . '<BR>';
	//Load an image in a global variable
	$image=file_get_contents($URL);
	//Output it (requires PHP>=4.3.2 and FPDF>=1.52)
	$pdf->Image('var://image',0,0,11,8.5,'PNG');
	//$pdf->Image($URL,0,0,11,8.5,'PNG');
	//$pages++;
	unset($image);
}
	
//Someday it would be better to use vector-based graphics


//echo json_encode(array("output"=>$pdf->Output()));
$pdf->Output('','I');
?>
